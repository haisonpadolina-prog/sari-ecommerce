(function () {
    if (window.__SARI_PRODUCTS_ASSET_BOOTSTRAPPED__) {
        if (typeof window.__SARI_SCHEDULE_PRODUCTS_PAGE__ === 'function') {
            window.__SARI_SCHEDULE_PRODUCTS_PAGE__();
        }
        return;
    }

    window.__SARI_PRODUCTS_ASSET_BOOTSTRAPPED__ = true;

function initSariProductsPage() {
    const currentProductsGrid = document.getElementById('productsGrid');
    if (!currentProductsGrid) return;

    /*
     * Avoid duplicate initialization on the same DOM node.
     * Livewire can fire its navigation event immediately after a normal load.
     */
    if (
        window.__SARI_PRODUCTS_INIT_GRID__ === currentProductsGrid &&
        window.__SARI_PRODUCTS_PAGE_API__
    ) {
        return;
    }

    window.__SARI_PRODUCTS_INIT_GRID__ = currentProductsGrid;

    window.__SARI_PRODUCTS_PAGE_ABORT__?.abort();
    const pageAbort = new AbortController();
    window.__SARI_PRODUCTS_PAGE_ABORT__ = pageAbort;
    const signal = pageAbort.signal;

    const config = window.__SARI_PRODUCTS_CONFIG__ || {};
    const libraryUrl = String(config.libraryUrl || window.__SARI_PRODUCTS_LIBRARY_URL__ || '');
    const productsBaseUrl = String(config.productsBaseUrl || '/seller/products');
    const productDraftUrl = String(config.productDraftUrl || '');
    const productDraftSaveUrl = String(config.productDraftSaveUrl || '');
    const productDraftDeleteUrl = String(config.productDraftDeleteUrl || '');
    const requestedCategory = String(config.requestedCategory || '');

    const grid = document.getElementById('productsGrid');
    const empty = document.getElementById('productsEmpty');
    const resultCount = document.getElementById('productsResultCount');
    const search = document.getElementById('productsSearch');
    const searchSuggestions = document.getElementById('productsSearchSuggestions');
    const status = document.getElementById('productsStatus');
    const stock = document.getElementById('productsStock');
    const category = document.getElementById('productsCategory');
    const applyFilterButton = document.getElementById('productsApplyFilters');
    const clear = document.getElementById('productsClearFilters');

    const premiumDropdowns = Array.from(document.querySelectorAll('[data-products-dropdown]'));

    function premiumDropdownForSelect(select) {
        if (!select?.id) return null;
        return premiumDropdowns.find((dropdown) => dropdown.dataset.selectId === select.id) || null;
    }

    function premiumDropdownOptionMarkup(option) {
        const value = String(option.value ?? '');
        const label = String(option.textContent ?? '').trim();

        return `
            <button
                type="button"
                class="seller-premium-dropdown-option"
                data-products-dropdown-option
                data-value="${escapeHtml(value)}"
                role="option"
                tabindex="-1"
                aria-selected="false"
            >
                <span class="seller-premium-dropdown-option-dot" aria-hidden="true"></span>
                <span class="seller-premium-dropdown-option-copy">${escapeHtml(label)}</span>
                <svg class="seller-premium-dropdown-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="m6 12 4 4 8-9"></path>
                </svg>
            </button>
        `;
    }

    function buildPremiumDropdown(select) {
        const dropdown = premiumDropdownForSelect(select);
        if (!dropdown || !select) return;

        const menu = dropdown.querySelector('[data-products-dropdown-menu]');
        if (!menu) return;

        menu.innerHTML = Array.from(select.options).map(premiumDropdownOptionMarkup).join('');
        syncPremiumDropdown(select);
    }

    function syncPremiumDropdown(select) {
        const dropdown = premiumDropdownForSelect(select);
        if (!dropdown || !select) return;

        const selectedOption = select.options[select.selectedIndex] || select.options[0];
        const label = dropdown.querySelector('[data-products-dropdown-label]');
        const selectedValue = String(select.value ?? '');

        if (label) {
            label.textContent = selectedOption?.textContent?.trim() || '';
        }

        dropdown.querySelectorAll('[data-products-dropdown-option]').forEach((option) => {
            const active = String(option.dataset.value ?? '') === selectedValue;
            option.classList.toggle('is-selected', active);
            option.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    }

    function closePremiumDropdown(dropdown) {
        if (!dropdown) return;
        dropdown.classList.remove('is-open');
        dropdown.querySelector('[data-products-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
    }

    function closeAllPremiumDropdowns(except = null) {
        premiumDropdowns.forEach((dropdown) => {
            if (dropdown !== except) closePremiumDropdown(dropdown);
        });
    }

    function openPremiumDropdown(dropdown) {
        if (!dropdown) return;
        closeAllPremiumDropdowns(dropdown);
        dropdown.classList.add('is-open');
        dropdown.querySelector('[data-products-dropdown-trigger]')?.setAttribute('aria-expanded', 'true');
    }

    function syncAllPremiumDropdowns() {
        [status, stock, category].forEach((select) => syncPremiumDropdown(select));
    }

    function premiumDropdownOptions(dropdown) {
        return Array.from(dropdown?.querySelectorAll('[data-products-dropdown-option]') || []);
    }

    function focusPremiumDropdownOption(dropdown, mode = 'selected') {
        const options = premiumDropdownOptions(dropdown);
        if (!options.length) return;

        let target = null;
        if (mode === 'first') target = options[0];
        else if (mode === 'last') target = options[options.length - 1];
        else target = dropdown.querySelector('.seller-premium-dropdown-option.is-selected') || options[0];

        target?.focus({ preventScroll: true });
    }

    function movePremiumDropdownFocus(dropdown, step) {
        const options = premiumDropdownOptions(dropdown);
        if (!options.length) return;

        let index = options.indexOf(document.activeElement);
        if (index < 0) index = 0;

        const nextIndex = (index + step + options.length) % options.length;
        options[nextIndex]?.focus({ preventScroll: true });
    }

    function setFilterApplyDirty(dirty = true) {
        applyFilterButton?.classList.toggle('is-dirty', Boolean(dirty));
    }

    function setPremiumFilterValue(select, value) {
        if (!select) return;

        const normalized = String(value ?? '');
        const exists = Array.from(select.options).some((option) => String(option.value) === normalized);

        select.value = exists ? normalized : '';
        syncPremiumDropdown(select);
        closePremiumDropdown(premiumDropdownForSelect(select));

        // Dropdown choices are staged until the seller presses Apply Filters.
        setFilterApplyDirty(true);
    }

    premiumDropdowns.forEach((dropdown) => {
        const select = document.getElementById(dropdown.dataset.selectId || '');
        const trigger = dropdown.querySelector('[data-products-dropdown-trigger]');

        if (select) buildPremiumDropdown(select);

        trigger?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (dropdown.classList.contains('is-open')) {
                closePremiumDropdown(dropdown);
            } else {
                openPremiumDropdown(dropdown);
            }
        }, { signal });

        dropdown.addEventListener('click', (event) => {
            const option = event.target.closest('[data-products-dropdown-option]');
            if (!option || !dropdown.contains(option) || !select) return;

            event.preventDefault();
            event.stopPropagation();

            setPremiumFilterValue(select, option.dataset.value);
            trigger?.focus({ preventScroll: true });
        }, { signal });

        trigger?.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                openPremiumDropdown(dropdown);
                window.requestAnimationFrame(() => focusPremiumDropdownOption(dropdown, 'selected'));
                return;
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                openPremiumDropdown(dropdown);
                window.requestAnimationFrame(() => focusPremiumDropdownOption(dropdown, 'last'));
                return;
            }

            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();

                if (dropdown.classList.contains('is-open')) {
                    closePremiumDropdown(dropdown);
                } else {
                    openPremiumDropdown(dropdown);
                    window.requestAnimationFrame(() => focusPremiumDropdownOption(dropdown, 'selected'));
                }
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closePremiumDropdown(dropdown);
            }
        }, { signal });

        dropdown.addEventListener('keydown', (event) => {
            if (!dropdown.classList.contains('is-open')) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                movePremiumDropdownFocus(dropdown, 1);
                return;
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                movePremiumDropdownFocus(dropdown, -1);
                return;
            }

            if (event.key === 'Home') {
                event.preventDefault();
                focusPremiumDropdownOption(dropdown, 'first');
                return;
            }

            if (event.key === 'End') {
                event.preventDefault();
                focusPremiumDropdownOption(dropdown, 'last');
                return;
            }

            if (event.key === 'Enter' || event.key === ' ') {
                const option = event.target.closest('[data-products-dropdown-option]');
                if (!option || !select) return;

                event.preventDefault();
                setPremiumFilterValue(select, option.dataset.value);
                trigger?.focus({ preventScroll: true });
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closePremiumDropdown(dropdown);
                trigger?.focus({ preventScroll: true });
            }
        }, { signal });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-products-dropdown]')) {
            closeAllPremiumDropdowns();
        }
    }, { signal });

    window.addEventListener('blur', () => closeAllPremiumDropdowns(), { signal });
    window.addEventListener('resize', () => closeAllPremiumDropdowns(), { passive: true, signal });

    const totalCount = document.getElementById('productsTotalCount');
    const approvedCount = document.getElementById('productsApprovedCount');
    const lowStockCount = document.getElementById('productsLowStockCount');
    const outStockCount = document.getElementById('productsOutStockCount');

    const catalogHealthScore = document.getElementById('productsCatalogHealthScore');
    const catalogHealthLabel = document.getElementById('productsCatalogHealthLabel');
    const catalogHealthBar = document.getElementById('productsCatalogHealthBar');
    const catalogAttention = document.getElementById('productsCatalogAttention');

    const healthApproved = document.getElementById('productsHealthApproved');
    const healthLow = document.getElementById('productsHealthLow');
    const healthOut = document.getElementById('productsHealthOut');
    const healthReview = document.getElementById('productsHealthReview');

    const quickAll = document.getElementById('productsQuickAll');
    const quickApproved = document.getElementById('productsQuickApproved');
    const quickPending = document.getElementById('productsQuickPending');
    const quickLow = document.getElementById('productsQuickLow');
    const quickOut = document.getElementById('productsQuickOut');
    const quickFilterButtons = Array.from(document.querySelectorAll('[data-products-quick-filter]'));

    const actionModal = document.getElementById('productsActionModal');
    const actionForm = document.getElementById('productsActionForm');
    const actionTitle = document.getElementById('productsActionTitle');
    const actionText = document.getElementById('productsActionText');
    const actionSubmit = document.getElementById('productsActionSubmit');
    const actionCancel = document.getElementById('productsActionCancel');

    let products = [];

    let appliedFilterState = {
        status: String(status?.value || ''),
        stock: String(stock?.value || ''),
        category: String(category?.value || ''),
    };

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function peso(value) {
        return '₱' + Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    let serverClockOffsetMs = 0;
    let flashSaleClockId = null;

    function serverNowMs() {
        return Date.now() + serverClockOffsetMs;
    }

    function isoToLocalDateTimeValue(value) {
        if (!value) return '';

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';

        const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
        return localDate.toISOString().slice(0, 16);
    }

    function localDateTimeValueToIso(value) {
        if (!value) return '';

        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? '' : date.toISOString();
    }

    function minimumLocalFlashSaleValue() {
        return isoToLocalDateTimeValue(new Date(Date.now() + 60000).toISOString());
    }

    function formatFlashSaleCountdown(milliseconds) {
        const totalSeconds = Math.max(0, Math.ceil(milliseconds / 1000));
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        const hh = String(hours).padStart(2, '0');
        const mm = String(minutes).padStart(2, '0');
        const ss = String(seconds).padStart(2, '0');

        return days > 0 ? `${days}d ${hh}:${mm}:${ss}` : `${hh}:${mm}:${ss}`;
    }

    function statusClass(value) {
        const map = {
            approved: 'border-[#d8e9df] bg-[#edf7f1] text-[#4f8065]',
            pending: 'border-[#d8e5f1] bg-[#eef5fc] text-[#537a9f]',
            flagged: 'border-[#f0d4d4] bg-[#fff0f0] text-[#b45b5b]',
            rejected: 'border-[#eed2d2] bg-[#fff0f0] text-[#a85858]',
            removed: 'border-[#e2ddd6] bg-[#f4f2ef] text-[#746d64]',
        };
        return map[String(value || '').toLowerCase()] || 'border-[#e2ddd6] bg-[#f4f2ef] text-[#746d64]';
    }

    function updateSummary() {
        const total = products.length;
        const approved = products.filter((p) => String(p.moderation_status || '').toLowerCase() === 'approved').length;
        const pending = products.filter((p) => String(p.moderation_status || '').toLowerCase() === 'pending').length;
        const low = products.filter((p) => String(p.stock_state || '') === 'low-stock').length;
        const out = products.filter((p) => Number(p.stock || 0) <= 0).length;

        if (totalCount) totalCount.textContent = total.toLocaleString('en-PH');
        if (approvedCount) approvedCount.textContent = approved.toLocaleString('en-PH');
        if (lowStockCount) lowStockCount.textContent = low.toLocaleString('en-PH');
        if (outStockCount) outStockCount.textContent = out.toLocaleString('en-PH');

        if (healthApproved) healthApproved.textContent = approved.toLocaleString('en-PH');
        if (healthLow) healthLow.textContent = low.toLocaleString('en-PH');
        if (healthOut) healthOut.textContent = out.toLocaleString('en-PH');
        if (healthReview) healthReview.textContent = pending.toLocaleString('en-PH');

        if (quickAll) quickAll.textContent = total.toLocaleString('en-PH');
        if (quickApproved) quickApproved.textContent = approved.toLocaleString('en-PH');
        if (quickPending) quickPending.textContent = pending.toLocaleString('en-PH');
        if (quickLow) quickLow.textContent = low.toLocaleString('en-PH');
        if (quickOut) quickOut.textContent = out.toLocaleString('en-PH');

        /*
         * Readiness is intentionally derived only from data already present
         * on this page: approved + currently in-stock listings.
         */
        const ready = products.filter((p) => (
            String(p.moderation_status || '').toLowerCase() === 'approved'
            && Number(p.stock || 0) > 0
        )).length;

        const score = total ? Math.round((ready / total) * 100) : 0;
        const attention = products.filter((p) => (
            String(p.moderation_status || '').toLowerCase() !== 'approved'
            || (Number(p.stock || 0) > 0 && Number(p.stock || 0) <= Number(p.low_stock_threshold ?? 5))
        )).length;

        if (catalogHealthScore) catalogHealthScore.textContent = score.toLocaleString('en-PH');
        if (catalogAttention) {
            catalogAttention.textContent = attention === 1
                ? '1 listing needs attention'
                : `${attention.toLocaleString('en-PH')} listings need attention`;
        }

        const scoreColor = score >= 80 ? '#4f8065' : (score >= 50 ? '#c48a13' : '#b65e5e');
        const scoreLabel = score >= 80 ? 'Strong' : (score >= 50 ? 'Needs Attention' : 'Priority Review');

        if (catalogHealthBar) {
            catalogHealthBar.style.width = `${score}%`;
            catalogHealthBar.style.backgroundColor = scoreColor;
        }

        if (catalogHealthScore) catalogHealthScore.style.color = scoreColor;

        if (catalogHealthLabel) {
            catalogHealthLabel.textContent = scoreLabel;
            catalogHealthLabel.style.color = scoreColor;
            catalogHealthLabel.style.borderColor = score >= 80 ? '#cfe4d7' : (score >= 50 ? '#ead9b2' : '#ebcaca');
            catalogHealthLabel.style.backgroundColor = score >= 80 ? '#f3faf5' : (score >= 50 ? '#fff8e9' : '#fff4f4');
        }
    }

    function populateCategories() {
        if (!category) return;

        const categories = [...new Set(products.map((p) => String(p.category || 'Uncategorized').trim() || 'Uncategorized'))]
            .sort((a, b) => a.localeCompare(b));

        category.innerHTML = '<option value="">All Categories</option>' + categories.map((item) => (
            `<option value="${escapeHtml(item)}">${escapeHtml(item)}</option>`
        )).join('');

        if (requestedCategory && categories.includes(requestedCategory)) {
            category.value = requestedCategory;
        }

        buildPremiumDropdown(category);
    }

    function cardMarkup(product, cardIndex = 0) {
        const stockValue = Number(product.stock || 0);
        const discount = Math.min(100, Math.max(0, Number(product.discount || 0)));
        const backendEffectiveDiscount = Math.min(100, Math.max(0, Number(product.effective_discount ?? discount)));
        const flashSaleEndsAtMs = Date.parse(product.flash_sale_ends_at || '');

        /*
         * Pending/flagged listings keep the Flash Sale visible as scheduled,
         * but they do not run a countdown. The live countdown is rendered only
         * after the administrator changes moderation_status to approved.
         */
        const moderationStatus = String(product.moderation_status || '').toLowerCase();
        const awaitingApproval = moderationStatus === 'pending' || moderationStatus === 'flagged';
        const flashSaleScheduled = Boolean(
            awaitingApproval
            && discount > 0
            && Number.isFinite(flashSaleEndsAtMs)
        );
        const flashSaleActive = Boolean(
            moderationStatus === 'approved'
            && discount > 0
            && Number.isFinite(flashSaleEndsAtMs)
            && flashSaleEndsAtMs > serverNowMs()
        );
        const effectiveDiscount = (flashSaleActive || flashSaleScheduled)
            ? discount
            : backendEffectiveDiscount;
        const displayedSalePrice = (flashSaleActive || flashSaleScheduled)
            ? Math.round((Number(product.price || 0) * (1 - discount / 100)) * 100) / 100
            : Number(product.sale_price ?? product.price ?? 0);
        const threshold = Number(product.low_stock_threshold ?? 5);

        const stockTextClass = stockValue <= 0
            ? 'seller-stock-text--out'
            : (stockValue <= threshold ? 'seller-stock-text--low' : 'seller-stock-text--normal');

        let primaryBadge = {
            label: product.status_label || product.moderation_status || 'Listing',
            classes: 'seller-product-status--neutral'
        };

        if (moderationStatus === 'approved') {
            primaryBadge = {
                label: 'Active',
                classes: 'seller-product-status--active'
            };
        } else if (moderationStatus === 'pending') {
            primaryBadge = {
                label: 'Pending Review',
                classes: 'seller-product-status--pending'
            };
        } else if (moderationStatus === 'flagged' || moderationStatus === 'rejected') {
            primaryBadge = {
                label: moderationStatus === 'flagged' ? 'Flagged' : 'Rejected',
                classes: 'seller-product-status--danger'
            };
        } else if (moderationStatus === 'removed') {
            primaryBadge = {
                label: 'Removed',
                classes: 'seller-product-status--neutral'
            };
        }

        let secondaryBadge = '';

        if (flashSaleActive || flashSaleScheduled) {
            secondaryBadge = `⚡ -${effectiveDiscount.toLocaleString('en-PH', { maximumFractionDigits: 2 })}%`;
        } else if (product.on_trend) {
            secondaryBadge = 'Featured';
        } else if (product.mall_badge) {
            secondaryBadge = 'Mall';
        } else if (effectiveDiscount > 0) {
            secondaryBadge = `-${effectiveDiscount.toLocaleString('en-PH', { maximumFractionDigits: 2 })}%`;
        }

        const flashSaleMarkup = flashSaleActive
            ? `
                <div
                    class="seller-product-flash-sale"
                    data-flash-sale
                    data-product-id="${escapeHtml(product.id)}"
                    data-flash-sale-ends-at="${escapeHtml(product.flash_sale_ends_at)}"
                >
                    <span class="seller-product-flash-sale-label">Flash sale</span>
                    <span class="seller-product-flash-sale-bolt" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"></path>
                        </svg>
                    </span>
                    <span class="seller-product-flash-sale-countdown" data-flash-sale-countdown role="timer" aria-label="Flash Sale time remaining">--:--:--</span>
                </div>
            `
            : (flashSaleScheduled
                ? `
                    <div class="seller-product-flash-sale seller-product-flash-sale--scheduled" title="Countdown starts after administrator approval">
                        <span class="seller-product-flash-sale-label">Flash sale</span>
                        <span class="seller-product-flash-sale-bolt" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"></path>
                            </svg>
                        </span>
                        <span class="seller-product-flash-sale-countdown seller-product-flash-sale-countdown--scheduled">Starts on approval</span>
                    </div>
                `
                : '');

        const isInitialViewportCard = cardIndex < 6;
        const isTopPriorityCard = cardIndex < 3;

        const image = product.image_url
            ? `<img
                    src="${escapeHtml(product.image_url)}"
                    alt="${escapeHtml(product.name || 'Product')}"
                    class="seller-product-image h-full w-full object-cover"
                    loading="${isInitialViewportCard ? 'eager' : 'lazy'}"
                    fetchpriority="${isTopPriorityCard ? 'high' : 'auto'}"
                    decoding="async"
               >`
            : `<div class="grid h-full w-full place-items-center bg-[#f2f0ec] text-[#aaa198]">
                    <svg viewBox="0 0 24 24" class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.45" aria-hidden="true">
                        <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                        <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                    </svg>
               </div>`;

        return `
            <article
                data-product-item
                data-product-id="${escapeHtml(product.id)}"
                data-search="${escapeHtml([product.name, product.category, product.brand, product.sku].filter(Boolean).join(' ').toLowerCase())}"
                data-status="${escapeHtml(product.moderation_status || '')}"
                data-stock="${escapeHtml(product.stock_state || '')}"
                data-category="${escapeHtml(product.category || 'Uncategorized')}"
                class="seller-products-card seller-product-card--reference border bg-white"
            >
                <div class="seller-product-card-media">
                    ${image}

                    <div class="seller-product-card-badges">
                        <span class="seller-product-card-badge ${primaryBadge.classes}">
                            ${escapeHtml(primaryBadge.label)}
                        </span>

                        ${secondaryBadge
                            ? `<span class="seller-product-card-badge seller-product-card-badge--dark">${escapeHtml(secondaryBadge)}</span>`
                            : ''
                        }
                    </div>
                </div>

                <div class="seller-product-card-body">
                    <div class="seller-product-card-heading">
                        <div class="min-w-0">
                            <h3 class="seller-product-card-title">
                                ${escapeHtml(product.name || 'Untitled product')}
                            </h3>

                            <p class="seller-product-card-category">
                                ${escapeHtml(product.category || 'Uncategorized')}${product.brand ? ' · ' + escapeHtml(product.brand) : ''}
                            </p>
                        </div>

                        <div class="seller-product-card-price-wrap">
                            <strong class="seller-product-card-price">
                                ${peso(displayedSalePrice)}
                            </strong>

                            ${effectiveDiscount > 0
                                ? `<span class="seller-product-card-original-price">${peso(product.price)}</span>`
                                : ''
                            }
                        </div>
                    </div>

                    ${flashSaleMarkup}

                    <div class="seller-product-card-divider"></div>

                    <div class="seller-product-card-footer">
                        <div class="seller-product-card-stock-copy">
                            <p class="seller-product-card-stock ${stockTextClass}">
                                ${stockValue.toLocaleString('en-PH')} in stock
                            </p>

                            <p class="seller-product-card-updated">
                                ${escapeHtml(product.created_at_human || '')}
                            </p>
                        </div>

                        <div class="seller-product-actions seller-product-card-actions">
                            <button
                                type="button"
                                data-page-view-product
                                data-id="${escapeHtml(product.id)}"
                                title="View product"
                                aria-label="View product"
                                class="seller-product-action seller-product-action--view"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>

                            <button
                                type="button"
                                data-page-edit-product
                                data-id="${escapeHtml(product.id)}"
                                title="Edit product"
                                aria-label="Edit product"
                                class="seller-product-action seller-product-action--edit"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>
                                </svg>
                            </button>

                            <button
                                type="button"
                                data-page-product-action="archive"
                                data-id="${escapeHtml(product.id)}"
                                data-name="${escapeHtml(product.name || '')}"
                                title="Archive product"
                                aria-label="Archive product"
                                class="seller-product-action seller-product-action--archive"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" aria-hidden="true">
                                    <rect x="4" y="7" width="16" height="13" rx="2"></rect>
                                    <path d="M3 4h18v3H3z"></path>
                                    <path d="M10 11h4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        `;
    }

    function syncFlashSaleCountdowns() {
        let needsRerender = false;

        document.querySelectorAll('[data-flash-sale]').forEach((saleNode) => {
            const endValue = saleNode.dataset.flashSaleEndsAt || '';
            const endMs = Date.parse(endValue);
            const countdownNode = saleNode.querySelector('[data-flash-sale-countdown]');

            if (!Number.isFinite(endMs) || !countdownNode) return;

            const remainingMs = endMs - serverNowMs();

            if (remainingMs <= 0) {
                const productId = String(saleNode.dataset.productId || '');
                const product = products.find((item) => String(item?.id ?? '') === productId);

                if (product?.flash_sale_active) {
                    product.flash_sale_active = false;
                    product.effective_discount = 0;
                    product.sale_price = Number(product.price || 0);
                    needsRerender = true;
                }

                countdownNode.textContent = '00:00:00';
                return;
            }

            countdownNode.textContent = formatFlashSaleCountdown(remainingMs);
        });

        if (needsRerender) {
            renderProducts();
        }
    }

    function startFlashSaleClock() {
        if (flashSaleClockId !== null) {
            window.clearInterval(flashSaleClockId);
        }

        syncFlashSaleCountdowns();
        flashSaleClockId = window.setInterval(syncFlashSaleCountdowns, 1000);

        signal.addEventListener('abort', () => {
            if (flashSaleClockId !== null) {
                window.clearInterval(flashSaleClockId);
                flashSaleClockId = null;
            }
        }, { once: true });
    }

    function syncQuickFilterState() {
        const statusValue = String(status?.value || '').toLowerCase();
        const stockValue = String(stock?.value || '').toLowerCase();

        let active = 'all';

        if (statusValue === 'approved' && !stockValue) {
            active = 'approved';
        } else if (statusValue === 'pending' && !stockValue) {
            active = 'pending';
        } else if (stockValue === 'low-stock' && !statusValue) {
            active = 'low';
        } else if (stockValue === 'out-of-stock' && !statusValue) {
            active = 'out';
        } else if (statusValue || stockValue) {
            active = '';
        }

        quickFilterButtons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.productsQuickFilter === active);
        });
    }

    function applyFilters({ commitDropdowns = true } = {}) {
        const q = String(search?.value || '').trim().toLowerCase();

        if (commitDropdowns) {
            appliedFilterState = {
                status: String(status?.value || ''),
                stock: String(stock?.value || ''),
                category: String(category?.value || ''),
            };
        }

        const statusValue = String(appliedFilterState.status || '').toLowerCase();
        const stockValue = String(appliedFilterState.stock || '').toLowerCase();
        const categoryValue = String(appliedFilterState.category || '');

        const cards = Array.from(grid?.querySelectorAll('[data-product-item]') || []);
        let visible = 0;

        cards.forEach((card) => {
            const matches =
                (!q || String(card.dataset.search || '').includes(q)) &&
                (!statusValue || String(card.dataset.status || '').toLowerCase() === statusValue) &&
                (!stockValue || String(card.dataset.stock || '').toLowerCase() === stockValue) &&
                (!categoryValue || String(card.dataset.category || '') === categoryValue);

            card.classList.toggle('seller-filter-hidden', !matches);
            card.setAttribute('aria-hidden', matches ? 'false' : 'true');
            if (matches) visible++;
        });

        if (resultCount) {
            resultCount.textContent = `Showing ${visible.toLocaleString('en-PH')} of ${cards.length.toLocaleString('en-PH')} products`;
        }

        empty?.classList.toggle('hidden', visible !== 0 || cards.length === 0);
        syncAllPremiumDropdowns();
        syncQuickFilterState();
        setFilterApplyDirty(false);
    }


    let activeSearchSuggestionIndex = -1;

    function normalizedProductSearchText(product) {
        return [
            product?.name,
            product?.category,
            product?.brand,
            product?.sku,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();
    }

    function searchSuggestionStatus(product) {
        const value = String(product?.moderation_status || '').toLowerCase();

        if (value === 'approved') {
            return { label: 'Approved', className: 'seller-product-search-status--approved' };
        }

        if (value === 'pending') {
            return { label: 'Pending', className: 'seller-product-search-status--pending' };
        }

        if (['flagged', 'rejected', 'removed'].includes(value)) {
            return {
                label: product?.status_label || value.charAt(0).toUpperCase() + value.slice(1),
                className: 'seller-product-search-status--danger'
            };
        }

        return {
            label: product?.status_label || 'Listing',
            className: 'seller-product-search-status--neutral'
        };
    }

    function getProductSearchSuggestions(query) {
        const q = String(query || '').trim().toLowerCase();
        if (!q) return [];

        return products
            .map((product) => {
                const name = String(product?.name || '').toLowerCase();
                const categoryName = String(product?.category || '').toLowerCase();
                const brand = String(product?.brand || '').toLowerCase();
                const sku = String(product?.sku || '').toLowerCase();
                const haystack = normalizedProductSearchText(product);

                let score = 99;

                if (name === q) score = 0;
                else if (name.startsWith(q)) score = 1;
                else if (name.includes(q)) score = 2;
                else if (sku.startsWith(q)) score = 3;
                else if (brand.startsWith(q)) score = 4;
                else if (categoryName.startsWith(q)) score = 5;
                else if (haystack.includes(q)) score = 6;

                return { product, score };
            })
            .filter((item) => item.score < 99)
            .sort((a, b) => {
                if (a.score !== b.score) return a.score - b.score;
                return String(a.product?.name || '').localeCompare(String(b.product?.name || ''));
            })
            .slice(0, 6)
            .map((item) => item.product);
    }

    function closeProductSearchSuggestions() {
        if (!searchSuggestions) return;

        searchSuggestions.classList.add('hidden');
        searchSuggestions.innerHTML = '';
        activeSearchSuggestionIndex = -1;
        search?.setAttribute('aria-expanded', 'false');
    }

    function highlightSearchSuggestion(index) {
        if (!searchSuggestions) return;

        const items = Array.from(
            searchSuggestions.querySelectorAll('[data-product-search-suggestion]')
        );

        if (!items.length) {
            activeSearchSuggestionIndex = -1;
            return;
        }

        activeSearchSuggestionIndex = Math.max(0, Math.min(index, items.length - 1));

        items.forEach((item, itemIndex) => {
            item.classList.toggle('is-keyboard-active', itemIndex === activeSearchSuggestionIndex);
        });

        items[activeSearchSuggestionIndex]?.scrollIntoView({ block: 'nearest' });
    }

    function selectProductSearchSuggestion(productId) {
        const product = products.find(
            (item) => String(item?.id) === String(productId)
        );

        if (!product || !search) return;

        search.value = product.name || '';
        applyFilters();
        closeProductSearchSuggestions();

        requestAnimationFrame(() => {
            const selector = `[data-product-item][data-product-id="${CSS.escape(String(product.id))}"]`;
            const card = grid?.querySelector(selector);

            if (!card) return;

            card.classList.remove('hidden');
            card.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
            });

            card.classList.add('seller-product-search-hit');
            window.setTimeout(() => {
                card.classList.remove('seller-product-search-hit');
            }, 1500);
        });
    }

    function renderProductSearchSuggestions() {
        if (!search || !searchSuggestions) return;

        const query = String(search.value || '').trim();

        if (!query) {
            closeProductSearchSuggestions();
            return;
        }

        const matches = getProductSearchSuggestions(query);
        activeSearchSuggestionIndex = -1;

        if (!matches.length) {
            searchSuggestions.innerHTML = `
                <div class="seller-product-search-empty">
                    <strong>No matching products</strong>
                    <span>Try another product name, category, brand, or SKU.</span>
                </div>
            `;
            searchSuggestions.classList.remove('hidden');
            search.setAttribute('aria-expanded', 'true');
            return;
        }

        searchSuggestions.innerHTML = matches.map((product) => {
            const statusInfo = searchSuggestionStatus(product);
            const displayPrice = Number(product?.sale_price ?? product?.price ?? 0);

            const imageMarkup = product?.image_url
                ? `
                    <img
                        src="${escapeHtml(product.image_url)}"
                        alt="${escapeHtml(product.name || 'Product')} thumbnail"
                        loading="lazy"
                        decoding="async"
                    >
                `
                : `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                        <circle cx="8" cy="9" r="1.5"></circle>
                        <path d="m4 17 5-5 4 4 2-2 5 4"></path>
                    </svg>
                `;

            const metaParts = [
                product?.category || 'Uncategorized',
                product?.brand || '',
                product?.sku || '',
            ].filter(Boolean);

            return `
                <button
                    type="button"
                    class="seller-product-search-suggestion"
                    data-product-search-suggestion
                    data-product-id="${escapeHtml(product.id)}"
                    role="option"
                    aria-label="Select ${escapeHtml(product.name || 'product')}"
                >
                    <span class="seller-product-search-thumb">
                        ${imageMarkup}
                    </span>

                    <span class="seller-product-search-copy">
                        <span class="seller-product-search-name">
                            ${escapeHtml(product.name || 'Untitled product')}
                        </span>

                        <span class="seller-product-search-meta">
                            ${metaParts.map((part, index) => `
                                ${index > 0 ? '<i class="seller-product-search-meta-dot" aria-hidden="true"></i>' : ''}
                                <span>${escapeHtml(part)}</span>
                            `).join('')}
                        </span>
                    </span>

                    <span class="seller-product-search-side">
                        <span class="seller-product-search-price">
                            ${peso(displayPrice)}
                        </span>
                        <span class="seller-product-search-status ${statusInfo.className}">
                            ${escapeHtml(statusInfo.label)}
                        </span>
                    </span>
                </button>
            `;
        }).join('');

        searchSuggestions.classList.remove('hidden');
        search.setAttribute('aria-expanded', 'true');
    }

    function productLibrarySignature(data) {
        const items = Array.isArray(data?.products) ? data.products : [];

        return items.map((product) => [
            product?.id ?? '',
            product?.updated_at ?? product?.created_at ?? '',
            product?.moderation_status ?? '',
            product?.stock ?? '',
            product?.price ?? '',
            product?.sale_price ?? '',
            product?.discount ?? '',
            product?.image_url ?? '',
        ].join(':')).join('|');
    }

    function cacheProductLibrary(data) {
        if (!data || !Array.isArray(data?.products)) return;

        window.__SARI_PRODUCTS_LIBRARY_CACHED__ = data;

        const key = window.__SARI_PRODUCTS_LIBRARY_CACHE_KEY__;
        if (!key) return;

        try {
            window.sessionStorage.setItem(
                key,
                JSON.stringify({
                    saved_at: Date.now(),
                    data: data
                })
            );
        } catch (_) {
            // Cache is a performance enhancement only.
        }
    }

    function warmVisibleProductImages(items) {
        if (!Array.isArray(items)) return;

        items.slice(0, 3).forEach((product) => {
            const source = String(product?.image_url || '').trim();
            if (!source) return;

            const preload = new Image();
            preload.decoding = 'async';

            try {
                preload.fetchPriority = 'high';
            } catch (_) {}

            preload.src = source;
        });
    }

    function applyProductLibraryData(data, forceRender = false) {
        if (!data || !Array.isArray(data?.products)) return false;

        const nextSignature = productLibrarySignature(data);
        const previousSignature = window.__SARI_PRODUCTS_LIBRARY_RENDERED_SIGNATURE__ || '';

        const serverNow = Date.parse(data?.server_now || '');
        serverClockOffsetMs = Number.isFinite(serverNow)
            ? serverNow - Date.now()
            : serverClockOffsetMs;

        products = data.products;

        /*
         * Inventory controls belong to the current DOM, not to the product-card
         * render lifecycle. Livewire may replace this page while the cached
         * product signature stays unchanged, so always hydrate the new summary
         * nodes even when rebuilding the cards is intentionally skipped.
         */
        updateSummary();

        /*
         * Begin fetching first-screen images before building the card DOM.
         */
        warmVisibleProductImages(products);

        if (forceRender || nextSignature !== previousSignature) {
            window.__SARI_PRODUCTS_LIBRARY_RENDERED_SIGNATURE__ = nextSignature;
            renderProducts();
        } else {
            /*
             * The product cards are already current, but these controls are part
             * of the newly navigated DOM and still need to be rehydrated.
             */
            populateCategories();
            applyFilters();
            syncFlashSaleCountdowns();
        }

        startFlashSaleClock();
        cacheProductLibrary(data);

        return true;
    }

    function renderProducts() {
        if (!grid) return;

        grid.innerHTML = products.map((product, index) => cardMarkup(product, index)).join('');
        updateSummary();
        populateCategories();
        applyFilters();
        syncFlashSaleCountdowns();

        if (!products.length) {
            grid.innerHTML = `
                <div class="col-span-full rounded-[16px] border border-dashed border-[#ded5c8] bg-[#fcfaf7] px-6 py-14 text-center sm:col-span-1 lg:col-span-2">
                    <p class="text-[11px] font-semibold text-[#514a42]">No active products yet.</p>
                    <p class="mt-1 text-[9px] text-[#91887d]">Use Add New Product to create your first listing.</p>
                </div>
            `;
            if (resultCount) resultCount.textContent = '0 active products';
        }
    }

    async function loadProducts(options = {}) {
        const preferCache = options.preferCache !== false;
        const forceNetwork = options.forceNetwork === true;

        let renderedSomething = false;

        /*
         * Instant path: render the last good library response immediately.
         * A fresh request still runs below, so stale data self-corrects.
         */
        if (preferCache) {
            const cached = window.__SARI_PRODUCTS_LIBRARY_CACHED__;

            if (cached && Array.isArray(cached?.products)) {
                renderedSomething = applyProductLibraryData(
                    cached,
                    !window.__SARI_PRODUCTS_FAST_PAINTED__
                );
            }
        }

        try {
            let data = null;

            /*
             * On the initial page load, reuse the request that was started near
             * the TOP of the Blade file while the rest of the page was parsing.
             */
            if (!forceNetwork && window.__SARI_PRODUCTS_LIBRARY_PROMISE__) {
                data = await window.__SARI_PRODUCTS_LIBRARY_PROMISE__;
            }

            /*
             * If early prefetch failed, or this is a post-edit/action refresh,
             * make a fresh request.
             */
            if (!data) {
                const response = await fetch(libraryUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    cache: forceNetwork ? 'no-store' : 'default',
                    signal,
                });

                if (!response.ok) {
                    throw new Error('Unable to load Product Library.');
                }

                data = await response.json();
            }

            applyProductLibraryData(data, !renderedSomething);
        } catch (error) {
            if (error?.name === 'AbortError') return;

            console.error(error);

            /*
             * If cached products are already visible, keep them on screen
             * instead of replacing the whole catalog with an error panel.
             */
            if (renderedSomething) {
                return;
            }

            if (grid) {
                grid.innerHTML = `
                    <div class="col-span-full rounded-[16px] border border-[#efd7d7] bg-[#fff8f8] px-6 py-16 text-center">
                        <p class="text-[11px] font-semibold text-[#a55b5b]">Unable to load your products.</p>
                        <p class="mt-1 text-[9px] text-[#987777]">Refresh the page and try again.</p>
                    </div>
                `;
            }

            if (resultCount) {
                resultCount.textContent = 'Product Library unavailable';
            }
        }
    }

    [status, stock, category].forEach((element) => {
        element?.addEventListener('change', () => {
            syncPremiumDropdown(element);
            setFilterApplyDirty(true);
        }, { signal });
    });

    applyFilterButton?.addEventListener('click', () => {
        closeAllPremiumDropdowns();
        closeProductSearchSuggestions();
        applyFilters();
    }, { signal });

    search?.addEventListener('input', () => {
        applyFilters({ commitDropdowns: false });
        renderProductSearchSuggestions();
    }, { signal });

    search?.addEventListener('focus', () => {
        if (String(search.value || '').trim()) {
            renderProductSearchSuggestions();
        }
    }, { signal });

    search?.addEventListener('keydown', (event) => {
        if (!searchSuggestions || searchSuggestions.classList.contains('hidden')) {
            if (event.key === 'ArrowDown' && String(search.value || '').trim()) {
                renderProductSearchSuggestions();
            }
            return;
        }

        const suggestionItems = Array.from(
            searchSuggestions.querySelectorAll('[data-product-search-suggestion]')
        );

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            highlightSearchSuggestion(
                activeSearchSuggestionIndex < suggestionItems.length - 1
                    ? activeSearchSuggestionIndex + 1
                    : 0
            );
            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            highlightSearchSuggestion(
                activeSearchSuggestionIndex > 0
                    ? activeSearchSuggestionIndex - 1
                    : suggestionItems.length - 1
            );
            return;
        }

        if (event.key === 'Enter' && activeSearchSuggestionIndex >= 0) {
            event.preventDefault();
            const selected = suggestionItems[activeSearchSuggestionIndex];
            if (selected) {
                selectProductSearchSuggestion(selected.dataset.productId);
            }
            return;
        }

        if (event.key === 'Escape') {
            closeProductSearchSuggestions();
        }
    }, { signal });

    searchSuggestions?.addEventListener('click', (event) => {
        const suggestion = event.target.closest('[data-product-search-suggestion]');
        if (!suggestion) return;

        selectProductSearchSuggestion(suggestion.dataset.productId);
    }, { signal });

    document.addEventListener('click', (event) => {
        if (
            searchSuggestions?.contains(event.target) ||
            search?.contains(event.target)
        ) {
            return;
        }

        closeProductSearchSuggestions();
    }, { signal });

    quickFilterButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const mode = String(button.dataset.productsQuickFilter || 'all');

            if (status) status.value = '';
            if (stock) stock.value = '';

            if (mode === 'approved' && status) {
                status.value = 'approved';
            } else if (mode === 'pending' && status) {
                status.value = 'pending';
            } else if (mode === 'low' && stock) {
                stock.value = 'low-stock';
            } else if (mode === 'out' && stock) {
                stock.value = 'out-of-stock';
            }

            closeAllPremiumDropdowns();
            syncAllPremiumDropdowns();
            applyFilters();
        }, { signal });
    });

    clear?.addEventListener('click', function () {
        if (search) search.value = '';
        if (status) status.value = '';
        if (stock) stock.value = '';
        if (category) category.value = '';
        syncAllPremiumDropdowns();
        closeAllPremiumDropdowns();
        closeProductSearchSuggestions();
        applyFilters();
        search?.focus();
    }, { signal });


    /*
    |--------------------------------------------------------------------------
    | VIEW / EDIT — LOCAL PRODUCT MANAGEMENT
    |--------------------------------------------------------------------------
    | No Dashboard query-string bridge. The already-loaded Product Library
    | object is used directly by this page.
    */
    const viewProductModal = document.getElementById('sellerViewProductModal');
    const editProductModal = document.getElementById('sellerEditProductModal');
    const editProductForm = document.getElementById('sellerEditProductForm');
    const editProductPriceInput = document.getElementById('editProductPrice');
    const editProductDiscountInput = document.getElementById('editProductDiscount');
    const editProductSalePreview = document.getElementById('editProductSalePreview');
    const editProductFlashSaleLocalInput = document.getElementById('editProductFlashSaleEndsAtLocal');
    const editProductFlashSaleInput = document.getElementById('editProductFlashSaleEndsAt');

    function syncEditFlashSaleEnd() {
        if (!editProductFlashSaleInput) return;
        editProductFlashSaleInput.value = localDateTimeValueToIso(editProductFlashSaleLocalInput?.value || '');
    }

    function getProductById(id) {
        return products.find((product) => String(product?.id ?? '') === String(id ?? '')) || null;
    }

    function showProductPageModal(element) {
        if (!element) return;

        if (element.parentElement !== document.body) {
            document.body.appendChild(element);
        }

        element.classList.remove('hidden');
        element.classList.add('flex', 'seller-modal-visible');

        element.style.setProperty('display', 'flex', 'important');
        element.style.setProperty('opacity', '1', 'important');
        element.style.setProperty('visibility', 'visible', 'important');
        element.style.setProperty('pointer-events', 'auto', 'important');
        element.style.setProperty('z-index', '2147483000', 'important');

        document.body.classList.add('overflow-hidden');
    }

    function hideProductPageModal(element) {
        if (!element) return;

        element.classList.remove('seller-modal-visible');
        element.classList.add('hidden');
        element.classList.remove('flex');

        element.style.setProperty('display', 'none', 'important');
        element.style.removeProperty('opacity');
        element.style.removeProperty('visibility');
        element.style.removeProperty('pointer-events');
        element.style.removeProperty('z-index');

        if (
            !viewProductModal?.classList.contains('flex') &&
            !editProductModal?.classList.contains('flex') &&
            !actionModal?.classList.contains('flex')
        ) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function discountedProductPrice(price, discount) {
        const original = Math.max(0, Number(price || 0));
        const percent = Math.min(100, Math.max(0, Number(discount || 0)));
        return original * (1 - percent / 100);
    }

    function formatProductVariantOptions(options) {
        if (!options || typeof options !== 'object') return 'Default';
        const entries = Object.entries(options)
            .filter(([, value]) => String(value ?? '').trim() !== '');

        return entries.length
            ? entries.map(([key, value]) => `${key}: ${value}`).join(' · ')
            : 'Default';
    }

    let activeViewedProduct = null;

    function viewStatusStyle(status) {
        const value = String(status || '').toLowerCase();

        if (value === 'approved') {
            return {
                classes: 'border-[#cce2d4] bg-[#f1faf4] text-[#3f7d59]',
                label: 'Approved'
            };
        }

        if (value === 'pending') {
            return {
                classes: 'border-[#cedfec] bg-[#f1f7fc] text-[#527797]',
                label: 'Under Review'
            };
        }

        if (value === 'flagged') {
            return {
                classes: 'border-[#efd1d1] bg-[#fff3f3] text-[#a95656]',
                label: 'Flagged'
            };
        }

        if (value === 'rejected') {
            return {
                classes: 'border-[#edcccc] bg-[#fff1f1] text-[#a04f4f]',
                label: 'Rejected'
            };
        }

        return {
            classes: 'border-[#e1ddd6] bg-[#f7f5f2] text-[#71695f]',
            label: status ? String(status) : 'Unknown'
        };
    }

    function openLocalViewProduct(product) {
        if (!product || !viewProductModal) return;

        activeViewedProduct = product;

        const originalPrice = Math.max(0, Number(product.price || 0));
        const discount = Math.min(100, Math.max(0, Number(product.discount || 0)));
        const salePrice = Number(product.sale_price ?? discountedProductPrice(originalPrice, discount));
        const stockValue = Number(product.stock || 0);
        const variants = Array.isArray(product.variants) ? product.variants : [];
        const specifications = Array.isArray(product.specifications) ? product.specifications : [];
        const isVariantProduct = Boolean(product.has_variants || variants.length);
        const freeShipping = Boolean(product.free_shipping);

        const productName = product.name || 'Product';
        const categoryName = product.category || 'Uncategorized';
        const brandName = product.brand || 'No Brand';
        const listingType = isVariantProduct ? 'With Variants' : 'Simple Product';
        const moderationLabel = product.status_label || product.moderation_status || 'Unknown';

        document.getElementById('viewProductName').textContent = productName;
        document.getElementById('viewProductCategory').textContent = categoryName;
        document.getElementById('viewProductCategoryChip').textContent = categoryName;
        document.getElementById('viewProductBrand').textContent = brandName;
        document.getElementById('viewProductBrandLine').textContent = `${brandName} · ${product.sku || 'No SKU'}`;
        document.getElementById('viewProductSku').textContent = product.sku || '—';
        document.getElementById('viewProductStock').textContent = stockValue.toLocaleString('en-PH');
        document.getElementById('viewProductType').textContent = listingType;
        document.getElementById('viewProductTypeChip').textContent = listingType;
        document.getElementById('viewProductStatus').textContent = moderationLabel;
        document.getElementById('viewProductDescription').textContent =
            product.description || 'No description has been added for this product yet.';

        const conditionLabels = { new: 'New', like_new: 'Like New', used: 'Used' };
        document.getElementById('viewProductCondition').textContent =
            conditionLabels[String(product.condition || '')] || 'Not specified';

        const packageParts = [];
        if (product.package_weight !== null && product.package_weight !== undefined) packageParts.push(`${Number(product.package_weight)} kg`);
        const dimensions = [product.package_length, product.package_width, product.package_height];
        if (dimensions.every(value => value !== null && value !== undefined && value !== '')) {
            packageParts.push(`${Number(dimensions[0])} × ${Number(dimensions[1])} × ${Number(dimensions[2])} cm`);
        }
        document.getElementById('viewProductPackage').textContent = packageParts.join(' · ') || 'Not specified';
        document.getElementById('viewProductPreparation').textContent = product.preparation_days !== null && product.preparation_days !== undefined
            ? `${Number(product.preparation_days)} day${Number(product.preparation_days) === 1 ? '' : 's'}`
            : 'Not specified';
        document.getElementById('viewProductLowStock').textContent = `${Number(product.low_stock_threshold ?? 5).toLocaleString('en-PH')} units`;

        document.getElementById('viewProductSalePrice').textContent = peso(salePrice);

        const originalPriceNode = document.getElementById('viewProductOriginalPrice');
        const discountBadge = document.getElementById('viewProductDiscountBadge');
        const discountText = document.getElementById('viewProductDiscountText');

        if (discount > 0) {
            originalPriceNode.textContent = peso(originalPrice);
            originalPriceNode.classList.remove('hidden');

            discountBadge.textContent = `${discount.toLocaleString('en-PH', { maximumFractionDigits: 2 })}% OFF`;
            discountBadge.classList.remove('hidden');

            discountText.textContent = `${discount.toLocaleString('en-PH', { maximumFractionDigits: 2 })}% off`;
        } else {
            originalPriceNode.classList.add('hidden');
            discountBadge.classList.add('hidden');
            discountText.textContent = 'No active discount';
        }

        const voucher = String(product.voucher_code || '').trim();
        document.getElementById('viewProductVoucher').textContent = voucher || 'None';

        const shippingChip = document.getElementById('viewProductShippingChip');
        document.getElementById('viewProductShippingText').textContent =
            freeShipping ? 'Free Shipping enabled' : 'Standard shipping';

        shippingChip.classList.toggle('hidden', !freeShipping);

        document.getElementById('viewProductCreated').textContent =
            product.created_at_human || '—';

        const ratingCount = Number(product.rating_count || 0);
        const ratingValue = Number(product.rating || 0);
        document.getElementById('viewProductRating').textContent =
            ratingCount > 0
                ? `★ ${ratingValue.toFixed(1)} (${ratingCount.toLocaleString('en-PH')})`
                : 'No ratings';

        const statusBadge = document.getElementById('viewProductStatusBadge');
        const statusVisual = viewStatusStyle(product.moderation_status);

        statusBadge.className =
            `absolute left-3 top-3 inline-flex min-h-[28px] items-center rounded-full border px-3 text-[8px] font-semibold shadow-sm backdrop-blur ${statusVisual.classes}`;
        statusBadge.textContent = product.status_label || statusVisual.label;

        const gallerySection = document.getElementById('viewProductGallerySection');
        const galleryGrid = document.getElementById('viewProductGallery');
        const galleryCount = document.getElementById('viewProductGalleryCount');
        const galleryItems = Array.isArray(product.gallery) ? product.gallery : [];

        if (galleryItems.length) {
            galleryGrid.innerHTML = galleryItems.map((item, index) => `
                <div class="aspect-square overflow-hidden rounded-[9px] border border-[#e6dfd5] bg-[#f8f5f0]">
                    <img src="${escapeHtml(item?.url || '')}" alt="Gallery image ${index + 1}" class="h-full w-full object-cover">
                </div>
            `).join('');
            galleryCount.textContent = `${galleryItems.length} image${galleryItems.length === 1 ? '' : 's'}`;
            gallerySection.classList.remove('hidden');
        } else {
            galleryGrid.innerHTML = '';
            gallerySection.classList.add('hidden');
        }

        const specSection = document.getElementById('viewProductSpecificationsSection');
        const specGrid = document.getElementById('viewProductSpecifications');
        const specCount = document.getElementById('viewProductSpecificationCount');

        if (specifications.length) {
            specGrid.innerHTML = specifications.map((spec) => `
                <div data-view-spec-card class="rounded-[13px] border border-[#ebe4da] bg-white px-3.5 py-3">
                    <p class="text-[7.5px] font-medium text-[#958c80]">
                        ${escapeHtml(spec?.name || 'Specification')}
                    </p>
                    <p class="mt-1.5 text-[9.5px] font-semibold text-[#40382f]">
                        ${escapeHtml(spec?.value || '—')}${spec?.unit ? ' ' + escapeHtml(spec.unit) : ''}
                    </p>
                </div>
            `).join('');

            specCount.textContent = `${specifications.length} ${specifications.length === 1 ? 'detail' : 'details'}`;
            specSection.classList.remove('hidden');
        } else {
            specGrid.innerHTML = '';
            specSection.classList.add('hidden');
        }

        const variantSection = document.getElementById('viewProductVariantsSection');
        const variantBody = document.getElementById('viewProductVariants');
        const variantCount = document.getElementById('viewProductVariantCount');

        if (variants.length) {
            variantBody.innerHTML = variants.map((variant) => {
                const variantStock = Number(variant?.stock || 0);
                const threshold = Number(product.low_stock_threshold ?? 5);
                const stockClass = variantStock <= 0
                    ? 'text-[#b65e5e]'
                    : (variantStock <= threshold ? 'text-[#b87912]' : 'text-[#4f7d61]');

                return `
                    <tr>
                        <td class="px-5 py-3.5 font-semibold text-[#51483f]">
                            ${escapeHtml(formatProductVariantOptions(variant?.options))}
                        </td>
                        <td class="px-4 py-3.5">
                            ${variant?.image_url
                                ? `<img src="${escapeHtml(variant.image_url)}" alt="Variation image" class="h-9 w-9 rounded-[8px] border border-[#e6dfd5] object-cover">`
                                : '<span class="text-[#aaa198]">—</span>'}
                        </td>
                        <td class="px-4 py-3.5 text-[#81786e]">
                            ${escapeHtml(variant?.sku || '—')}
                        </td>
                        <td class="px-4 py-3.5 font-bold text-[#a8731f]">
                            ${peso(variant?.price || 0)}
                        </td>
                        <td class="px-4 py-3.5 font-semibold ${stockClass}">
                            ${variantStock.toLocaleString('en-PH')}
                        </td>
                    </tr>
                `;
            }).join('');

            variantCount.textContent = `${variants.length} ${variants.length === 1 ? 'variant' : 'variants'}`;
            variantSection.classList.remove('hidden');
        } else {
            variantBody.innerHTML = '';
            variantSection.classList.add('hidden');
        }

        const image = document.getElementById('viewProductImage');
        const imagePlaceholder = document.getElementById('viewProductImagePlaceholder');

        if (product.image_url) {
            image.src = product.image_url;
            image.alt = `${productName} product image`;
            image.classList.remove('hidden');
            imagePlaceholder.classList.add('hidden');
        } else {
            image.src = '';
            image.alt = '';
            image.classList.add('hidden');
            imagePlaceholder.classList.remove('hidden');
        }

        showProductPageModal(viewProductModal);
    }

    function updateLocalEditSalePreview() {
        if (!editProductSalePreview) return;

        const original = Math.max(0, Number(editProductPriceInput?.value || 0));
        const discount = Math.min(100, Math.max(0, Number(editProductDiscountInput?.value || 0)));
        const sale = discountedProductPrice(original, discount);

        const flashEndIso = editProductFlashSaleInput?.value || '';
        const flashEndDate = flashEndIso ? new Date(flashEndIso) : null;
        const flashText = flashEndDate && !Number.isNaN(flashEndDate.getTime())
            ? ' · Flash Sale countdown starts after admin approval'
            : '';

        editProductSalePreview.textContent = discount > 0
            ? `Buyer price: ${peso(sale)} · original ${peso(original)}${flashText}`
            : `Buyer price: ${peso(original)}`;
    }

    function openLocalEditProduct(product) {
        if (!product || !editProductModal || !editProductForm) return;

        editProductForm.action = productsBaseUrl + '/' + encodeURIComponent(product.id);

        document.getElementById('editProductName').value = product.name || '';

        const categorySelect = document.getElementById('editProductCategory');
        const requested = product.category || 'Others';

        if (categorySelect && !Array.from(categorySelect.options).some((option) => option.value === requested)) {
            categorySelect.add(new Option(requested, requested));
        }
        if (categorySelect) categorySelect.value = requested;

        document.getElementById('editProductBrand').value = product.brand || '';
        document.getElementById('editProductSku').value = product.sku || '';
        document.getElementById('editProductPrice').value = Number(product.price || 0);
        document.getElementById('editProductStock').value = Number(product.stock || 0);
        document.getElementById('editProductDiscount').value = Number(product.discount || 0);

        if (editProductFlashSaleInput) {
            editProductFlashSaleInput.value = product.flash_sale_ends_at || '';
        }
        if (editProductFlashSaleLocalInput) {
            editProductFlashSaleLocalInput.value = isoToLocalDateTimeValue(product.flash_sale_ends_at || '');
        }
        syncEditFlashSaleEnd();

        document.getElementById('editProductVoucher').value = product.voucher_code || '';
        document.getElementById('editProductDescription').value = product.description || '';

        const freeShipping = document.getElementById('editProductFreeShipping');
        if (freeShipping) freeShipping.checked = Boolean(product.free_shipping);

        const currentImage = document.getElementById('editCurrentImage');
        const currentImageWrap = document.getElementById('editCurrentImageWrap');

        if (product.image_url) {
            currentImage.src = product.image_url;
            currentImageWrap.classList.remove('hidden');
        } else {
            currentImage.src = '';
            currentImageWrap.classList.add('hidden');
        }

        updateLocalEditSalePreview();
        showProductPageModal(editProductModal);
    }

    editProductPriceInput?.addEventListener('input', updateLocalEditSalePreview, { signal });
    editProductDiscountInput?.addEventListener('input', updateLocalEditSalePreview, { signal });
    editProductFlashSaleLocalInput?.addEventListener('change', () => {
        syncEditFlashSaleEnd();
        updateLocalEditSalePreview();
    }, { signal });
    editProductForm?.addEventListener('submit', syncEditFlashSaleEnd, { signal });

    document.querySelectorAll('[data-close-view]').forEach((button) => {
        button.addEventListener('click', () => hideProductPageModal(viewProductModal), { signal });
    });

    document.querySelectorAll('[data-close-edit]').forEach((button) => {
        button.addEventListener('click', () => hideProductPageModal(editProductModal), { signal });
    });

    document.getElementById('viewProductEditButton')?.addEventListener('click', function () {
        if (!activeViewedProduct) return;

        const product = activeViewedProduct;
        hideProductPageModal(viewProductModal);
        openLocalEditProduct(product);
    }, { signal });

    [viewProductModal, editProductModal].forEach((modalElement) => {
        modalElement?.addEventListener('click', (event) => {
            if (event.target === modalElement) hideProductPageModal(modalElement);
        }, { signal });
    });


    /*
    | Keep Update inside Product Management even if the existing controller
    | returns a Dashboard redirect after success.
    */
    editProductForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const submit = editProductForm.querySelector('button[type="submit"]');
        const originalHtml = submit?.innerHTML || '';

        if (submit) {
            submit.disabled = true;
            submit.textContent = 'Saving...';
        }

        try {
            const response = await fetch(editProductForm.action, {
                method: 'POST',
                body: new FormData(editProductForm),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                redirect: 'manual',
                signal,
            });

            if (response.status === 422) {
                const payload = await response.json().catch(() => ({}));
                const firstError = Object.values(payload?.errors || {}).flat().find(Boolean);
                throw new Error(firstError || payload?.message || 'Please check the product fields.');
            }

            if (!(response.ok || response.type === 'opaqueredirect' || (response.status >= 300 && response.status < 400))) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(payload?.message || 'Unable to update this product.');
            }

            hideProductPageModal(editProductModal);
            await loadProducts({ preferCache: false, forceNetwork: true });

            const notice = document.getElementById('productsClientNotice');
            if (notice) {
                notice.textContent = 'Product updated successfully.';
                notice.classList.remove('hidden');
                window.setTimeout(() => notice.classList.add('hidden'), 5000);
            }
        } catch (error) {
            if (error?.name !== 'AbortError') {
                window.alert(error?.message || 'Unable to update this product.');
            }
        } finally {
            if (submit) {
                submit.disabled = false;
                submit.innerHTML = originalHtml;
            }
        }
    });


    function closeActionModal() {
        if (!actionModal) return;

        actionModal.classList.add('hidden');
        actionModal.classList.remove('flex');

        actionModal.style.setProperty('display', 'none', 'important');
        actionModal.style.removeProperty('opacity');
        actionModal.style.removeProperty('visibility');
        actionModal.style.removeProperty('pointer-events');
        actionModal.style.removeProperty('z-index');

        document.body.classList.remove('overflow-hidden');
    }

    function openActionModal(button) {
        const action = String(button.dataset.pageProductAction || 'archive');
        const id = String(button.dataset.id || '');
        const name = String(button.dataset.name || 'Product');
        if (!actionForm || !id) return;

        actionForm.action = productsBaseUrl + '/' + encodeURIComponent(id) + '/' + action;

        /*
         * Product Management exposes a single removal action: Archive.
         * Historical/compliance records stay intact and the product can be
         * restored from Archived Products.
         */
        if (action !== 'archive') return;

        actionTitle.textContent = 'Archive Product?';
        actionText.textContent = `“${name}” will move to Archived Products and can be restored later.`;
        actionSubmit.textContent = 'Archive Product';
        actionSubmit.className = 'h-10 rounded-xl bg-[#c99128] px-5 text-[9px] font-semibold text-white hover:bg-[#b47e1e]';

        if (actionModal.parentElement !== document.body) {
            document.body.appendChild(actionModal);
        }

        actionModal.classList.remove('hidden');
        actionModal.classList.add('flex');

        actionModal.style.setProperty('display', 'flex', 'important');
        actionModal.style.setProperty('opacity', '1', 'important');
        actionModal.style.setProperty('visibility', 'visible', 'important');
        actionModal.style.setProperty('pointer-events', 'auto', 'important');
        actionModal.style.setProperty('z-index', '2147483000', 'important');

        document.body.classList.add('overflow-hidden');
    }




    actionForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const originalText = actionSubmit?.textContent || '';
        if (actionSubmit) {
            actionSubmit.disabled = true;
            actionSubmit.textContent = 'Processing...';
        }

        try {
            const response = await fetch(actionForm.action, {
                method: 'POST',
                body: new FormData(actionForm),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                redirect: 'manual',
                signal,
            });

            if (response.status === 422) {
                const payload = await response.json().catch(() => ({}));
                const firstError = Object.values(payload?.errors || {}).flat().find(Boolean);
                throw new Error(firstError || payload?.message || 'Unable to process this product action.');
            }

            if (!(response.ok || response.type === 'opaqueredirect' || (response.status >= 300 && response.status < 400))) {
                const payload = await response.json().catch(() => ({}));
                throw new Error(payload?.message || 'Unable to process this product action.');
            }

            closeActionModal();
            await loadProducts({ preferCache: false, forceNetwork: true });

            const notice = document.getElementById('productsClientNotice');
            if (notice) {
                notice.textContent = 'Product catalog updated successfully.';
                notice.classList.remove('hidden');
                window.setTimeout(() => notice.classList.add('hidden'), 5000);
            }
        } catch (error) {
            if (error?.name !== 'AbortError') {
                window.alert(error?.message || 'Unable to process this product action.');
            }
        } finally {
            if (actionSubmit) {
                actionSubmit.disabled = false;
                actionSubmit.textContent = originalText;
            }
        }
    }, { signal });

    actionCancel?.addEventListener('click', closeActionModal, { signal });
    actionModal?.addEventListener('click', function (event) {
        if (event.target === actionModal) closeActionModal();
    }, { signal });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        closeActionModal();
        hideProductPageModal(viewProductModal);
        hideProductPageModal(editProductModal);
    }, { signal });

    document.addEventListener('livewire:navigating', function () {
        pageAbort.abort();

        if (window.__SARI_PRODUCTS_INIT_GRID__ === grid) {
            window.__SARI_PRODUCTS_INIT_GRID__ = null;
        }
    }, { once: true });



    /*
    |--------------------------------------------------------------------------
    | INSTANT PRODUCT ACTION API
    |--------------------------------------------------------------------------
    | Expose the small View / Edit / Archive API before the much
    | larger Add Product builder initializes.
    */
    window.__SARI_PRODUCTS_PAGE_API__ = {
        getProductById,
        openLocalViewProduct,
        openLocalEditProduct,
        openActionModal,
    };

    if (!window.__SARI_PRODUCTS_GLOBAL_ACTION_ROUTER__) {
        window.__SARI_PRODUCTS_GLOBAL_ACTION_ROUTER__ = true;

        document.addEventListener('click', function (event) {
            const button = event.target.closest(
                '[data-page-view-product], [data-page-edit-product], [data-page-product-action]'
            );

            if (!button) return;

            const api = window.__SARI_PRODUCTS_PAGE_API__;
            if (!api) return;

            event.preventDefault();
            event.stopPropagation();

            const id = button.dataset.id;

            if (button.hasAttribute('data-page-view-product')) {
                const product = api.getProductById(id);
                if (product) api.openLocalViewProduct(product);
                return;
            }

            if (button.hasAttribute('data-page-edit-product')) {
                const product = api.getProductById(id);
                if (product) api.openLocalEditProduct(product);
                return;
            }

            if (button.hasAttribute('data-page-product-action')) {
                api.openActionModal(button);
            }
        }, true);
    }

    /*
    | Show a success message after the local Add Product flow refreshes
    | Product Management.
    */
    const productsClientNotice = document.getElementById('productsClientNotice');
    const savedProductsNotice = window.sessionStorage.getItem('sariProductsNotice');

    if (productsClientNotice && savedProductsNotice) {
        productsClientNotice.textContent = savedProductsNotice;
        productsClientNotice.classList.remove('hidden');
        window.sessionStorage.removeItem('sariProductsNotice');

        window.setTimeout(function () {
            productsClientNotice.classList.add('hidden');
        }, 5500);
    }


    const productsOpenRequest = new URLSearchParams(window.location.search).get('open');
    if (productsOpenRequest === 'add-product') {
        window.location.assign(String((window.__SARI_PRODUCTS_CONFIG__ || {}).createUrl || '/seller/products/create'));
        return;
    }

    loadProducts();
}

/*
|--------------------------------------------------------------------------
| PRODUCT MANAGEMENT BOOTSTRAP
|--------------------------------------------------------------------------
| Run on BOTH a normal initial page load and Livewire navigation.
*/
function scheduleSariProductsPage() {
    const run = () => initSariProductsPage();

    if (window.__SARI_SELLER_AFTER_PAINT__) {
        window.__SARI_SELLER_AFTER_PAINT__(run);
        return;
    }

    window.requestAnimationFrame(() => window.requestAnimationFrame(run));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scheduleSariProductsPage, { once: true });
} else {
    scheduleSariProductsPage();
}

document.addEventListener('livewire:navigated', scheduleSariProductsPage);


    window.__SARI_SCHEDULE_PRODUCTS_PAGE__ = scheduleSariProductsPage;
})();
