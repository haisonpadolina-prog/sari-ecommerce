function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function showBuyerToast(message, tone = 'success') {
    let toast = document.getElementById('buyerGlobalToast');

    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'buyerGlobalToast';
        toast.className = 'fixed bottom-5 right-5 z-[300] max-w-[360px] rounded-2xl border bg-white px-4 py-3 text-[10px] font-medium shadow-[0_18px_55px_rgba(40,32,20,.18)] transition';
        document.body.appendChild(toast);
    }

    toast.classList.remove('border-[#cfe4d7]', 'text-[#4F7D63]', 'border-[#efcece]', 'text-[#a65353]');
    toast.classList.add(tone === 'error' ? 'border-[#efcece]' : 'border-[#cfe4d7]');
    toast.classList.add(tone === 'error' ? 'text-[#a65353]' : 'text-[#4F7D63]');
    toast.textContent = message;
    toast.style.opacity = '1';

    clearTimeout(window.__buyerToastTimer);
    window.__buyerToastTimer = setTimeout(() => {
        toast.style.opacity = '0';
    }, 2600);
}

async function refreshCartCount() {
    const url = document.body.dataset.buyerCartSummaryUrl;
    if (!url) return;

    try {
        const response = await fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            cache: 'no-store',
        });
        if (!response.ok) return;
        const data = await response.json();
        const count = Number(data.count || 0);

        ['buyerCartCount', 'buyerSidebarCartCount'].forEach((id) => {
            const badge = document.getElementById(id);
            if (!badge) return;
            badge.textContent = String(count);
            badge.classList.toggle('hidden', count <= 0);
            if (count > 0) badge.classList.add('grid');
            else badge.classList.remove('grid');
        });
    } catch (_) {}
}

async function addProductToCart(productId, quantity = 1, variantId = null) {
    const url = document.body.dataset.buyerCartStoreUrl;
    if (!url) throw new Error('Cart route is unavailable.');

    const body = new URLSearchParams();
    body.set('product_id', String(productId));
    body.set('quantity', String(quantity));
    if (variantId) body.set('variant_id', String(variantId));

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: body.toString(),
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const firstError = data?.errors ? Object.values(data.errors).flat()[0] : null;
        throw new Error(firstError || data.message || 'Could not add this item to your cart.');
    }

    await refreshCartCount();
    return data;
}

window.SariBuyerCart = { add: addProductToCart, refresh: refreshCartCount };

function initBuyerShell() {
    const body = document.body;
    const sidebar = document.getElementById('buyerSidebar');
    const overlay = document.getElementById('buyerSidebarOverlay');
    const main = document.getElementById('buyerMainContent');
    const mobileOpen = document.getElementById('buyerMobileMenuButton');
    const mobileClose = document.getElementById('buyerSidebarClose');
    const desktopToggle = document.getElementById('buyerSidebarToggle');

    const openMobile = () => {
        if (!sidebar) return;
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay?.classList.remove('hidden');
        body.classList.add('overflow-hidden');
    };

    const closeMobile = () => {
        if (!sidebar || window.innerWidth >= 1024) return;
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        body.classList.remove('overflow-hidden');
    };

    mobileOpen?.addEventListener('click', openMobile);
    mobileClose?.addEventListener('click', closeMobile);
    overlay?.addEventListener('click', closeMobile);

    const setCollapsed = (collapsed) => {
        if (!sidebar || !main) return;
        sidebar.classList.toggle('lg:w-[82px]', collapsed);
        sidebar.classList.toggle('lg:w-[275px]', !collapsed);
        main.classList.toggle('lg:pl-[82px]', collapsed);
        main.classList.toggle('lg:pl-[275px]', !collapsed);
        sidebar.querySelectorAll('.buyer-sidebar-label, .buyer-sidebar-extra').forEach((el) => el.classList.toggle('lg:hidden', collapsed));
        localStorage.setItem('sariBuyerSidebarCollapsed', collapsed ? '1' : '0');
    };

    setCollapsed(localStorage.getItem('sariBuyerSidebarCollapsed') === '1');
    desktopToggle?.addEventListener('click', () => {
        setCollapsed(!sidebar?.classList.contains('lg:w-[82px]'));
    });

    const logoutForm = document.getElementById('buyerLogoutForm');
    const logoutModal = document.getElementById('buyerLogoutModal');
    const cancelLogout = document.getElementById('buyerCancelLogout');
    const confirmLogout = document.getElementById('buyerConfirmLogout');

    logoutForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        logoutModal?.classList.remove('hidden');
        logoutModal?.classList.add('flex');
    });
    cancelLogout?.addEventListener('click', () => {
        logoutModal?.classList.add('hidden');
        logoutModal?.classList.remove('flex');
    });
    confirmLogout?.addEventListener('click', () => logoutForm?.submit());

    const submitSearch = (input) => {
        const query = String(input?.value || '').trim();
        const productsUrl = body.dataset.buyerProductsUrl || '/buyer/products';
        window.location.href = query ? `${productsUrl}?search=${encodeURIComponent(query)}` : productsUrl;
    };

    ['buyerHeaderSearch', 'buyerMobileSearch'].forEach((id) => {
        const input = document.getElementById(id);
        input?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                submitSearch(input);
            }
        });
    });

    document.addEventListener('click', async (event) => {
        const quickAdd = event.target.closest('[data-quick-add]');
        if (quickAdd) {
            event.preventDefault();
            event.stopPropagation();
            const card = quickAdd.closest('[data-product-card]');
            const productId = Number(quickAdd.dataset.quickAdd || card?.dataset.productId || 0);
            const hasVariants = card?.dataset.productHasVariants === '1';
            const productUrl = card?.dataset.productUrl;

            if (hasVariants) {
                if (productUrl) window.location.href = productUrl;
                return;
            }

            try {
                quickAdd.disabled = true;
                await addProductToCart(productId, 1, null);
                showBuyerToast('Item added to your cart.');
            } catch (error) {
                showBuyerToast(error.message || 'Unable to add item.', 'error');
            } finally {
                quickAdd.disabled = false;
            }
            return;
        }

        const openProduct = event.target.closest('[data-open-product]');
        if (openProduct) {
            const card = openProduct.closest('[data-product-card]');
            const productUrl = card?.dataset.productUrl;
            if (productUrl) {
                event.preventDefault();
                window.location.href = productUrl;
            }
        }
    });

    refreshCartCount();
}

document.addEventListener('DOMContentLoaded', initBuyerShell);
