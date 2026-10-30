import './echo';
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function showBuyerToast(message, tone = 'success') {
    window.SariBuyerToast?.show(message, tone);
}

// Livewire can dispatch its initial and subsequent navigation events close together.
// Share in-flight badge requests instead of requesting identical summaries twice.
let cartSummaryRequest = null;
let notificationSummaryRequest = null;
let cartRevision = 0;
let cartRequestGeneration = 0;
let notificationRequestGeneration = 0;
let displayedNotificationCount = null;
let lastFocusSync = 0;

// No cross-user count data is stored in localStorage. Other tabs receive an
// invalidation message and fetch their own authenticated summary.
const BADGE_SYNC_KEY = 'sari:buyer-badges-invalidated-v1';
let badgeChannel = null;
try {
    if (typeof window.BroadcastChannel === 'function') {
        badgeChannel = new window.BroadcastChannel(BADGE_SYNC_KEY);
    }
} catch (_) {
    // Storage-based invalidation is the fallback for unsupported browsers.
}

function invalidateOtherTabs(kind) {
    if (badgeChannel) {
        try {
            badgeChannel.postMessage({ kind });
            return;
        } catch (_) {
            // Fall back to the storage event.
        }
    }
    try {
        window.localStorage?.setItem(BADGE_SYNC_KEY, JSON.stringify({ kind, at: Date.now() }));
    } catch (_) {
        // Browser storage can be disabled without breaking this page.
    }
}

function applyAuthoritativeCartCount(value, broadcast = false) {
    const count = Number(value);
    if (!Number.isFinite(count) || count < 0) return false;
    cartRevision++;
    cartRequestGeneration++; // Ignore older summary responses after a mutation.
    displayCartCount(count);
    if (broadcast) invalidateOtherTabs('cart');
    return true;
}


function displayCartCount(value) {
    const count = Math.max(0, Number(value) || 0);
    ['buyerCartCount', 'buyerSidebarCartCount'].forEach((id) => {
        const badge = document.getElementById(id);
        if (!badge) return;
        badge.textContent = String(count);
        badge.classList.toggle('hidden', count <= 0);
        badge.classList.toggle('grid', count > 0);
    });
}

function displayNotificationCount(value) {
    const count = Math.max(0, Number(value) || 0);
    ['buyerNotificationCount', 'buyerSidebarNotificationCount'].forEach((id) => {
        const badge = document.getElementById(id);
        if (!badge) return;
        badge.textContent = count > 99 ? '99+' : String(count);
        badge.classList.toggle('hidden', count <= 0);
        badge.classList.toggle('grid', count > 0);
    });
}

function refreshCartCount(force = false) {
    const url = document.body?.dataset.buyerCartSummaryUrl;
    if (!url) return Promise.resolve();
    if (cartSummaryRequest && !force) return cartSummaryRequest;
    const requestGeneration = ++cartRequestGeneration;

    // A successful cart mutation may update the badge before this GET completes.
    // Never overwrite its authoritative count with an earlier response.
    const requestedAtRevision = cartRevision;
    const request = fetch(url, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
        cache: 'no-store',
    }).then(async (response) => {
        if (!response.ok) return;
        const data = await response.json();
        if (requestedAtRevision === cartRevision && requestGeneration === cartRequestGeneration) {
            displayCartCount(data.count);
        }
    }).catch(() => {
        // Counts can be refreshed again on a subsequent navigation.
    }).finally(() => {
        if (cartSummaryRequest === request) cartSummaryRequest = null;
    });

    cartSummaryRequest = request;
    return request;
}

function refreshNotificationCount(force = false) {
    const url = document.body?.dataset.buyerNotificationSummaryUrl;
    if (!url) return Promise.resolve();
    if (notificationSummaryRequest && !force) return notificationSummaryRequest;
    const requestGeneration = ++notificationRequestGeneration;

    const request = fetch(url, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
        cache: 'no-store',
    }).then(async (response) => {
        if (!response.ok) return;
        const data = await response.json();
        if (requestGeneration === notificationRequestGeneration) {
            const next = Number(data.unread_count);
            if (!Number.isFinite(next) || next < 0) return;
            const previous = displayedNotificationCount;
            displayedNotificationCount = next;
            displayNotificationCount(next);
            if (previous !== null && next !== previous) {
                document.dispatchEvent(new CustomEvent('sari:buyer-notification-count-changed', {
                    detail: { previous, count: next },
                }));
            }
        }
    }).catch(() => {
        // A failed badge fetch must never break normal Buyer navigation.
    }).finally(() => {
        if (notificationSummaryRequest === request) notificationSummaryRequest = null;
    });

    notificationSummaryRequest = request;
    return request;
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

    // The POST returns the cart line count (one line per product/variant).
    // Reuse it immediately instead of making a second blocking GET.
    if (!applyAuthoritativeCartCount(data.cart_count, true)) {
        await refreshCartCount(true);
        invalidateOtherTabs('cart');
    }
    return data;
}

// Explicit refresh calls follow user actions and must fetch fresh counts,
// even when a navigation-initiated request is still in flight.
window.SariBuyerCart = { add: addProductToCart, refresh: () => refreshCartCount(true) };
window.SariBuyerNotifications = {
    refresh: () => refreshNotificationCount(true),
    changed: () => {
        invalidateOtherTabs('notifications');
        return refreshNotificationCount(true);
    },
};

// Cart/Details already emit this event on successful mutations. Previously the
// shared header/sidebar never listened for it, so badges stayed stale.
document.addEventListener('sari:cart-updated', (event) => {
    if (!applyAuthoritativeCartCount(event.detail?.count, true)) {
        refreshCartCount(true);
        invalidateOtherTabs('cart');
    }
});

document.addEventListener('sari:notification-updated', () => {
    window.SariBuyerNotifications.changed();
});

function handleRemoteBadgeChange(kind) {
    if (kind === 'cart') refreshCartCount(true);
    if (kind === 'notifications') refreshNotificationCount(true);
}

if (badgeChannel) {
    badgeChannel.onmessage = (event) => handleRemoteBadgeChange(event.data?.kind);
} else {
    window.addEventListener('storage', (event) => {
        if (event.key !== BADGE_SYNC_KEY || !event.newValue) return;
        try {
            handleRemoteBadgeChange(JSON.parse(event.newValue)?.kind);
        } catch (_) {
            // Ignore invalid data from storage.
        }
    });
}

// Order notifications currently have no Buyer-wide broadcast event. While the
// page is visible, reconcile the count periodically and immediately on focus.
// Keep one request in flight and never poll in a background tab.
window.setInterval(() => {
    if (document.visibilityState === 'hidden') return;
    refreshNotificationCount();
}, 20000);

function syncAfterReturnToPage() {
    if (document.visibilityState === 'hidden') return;
    const now = Date.now();
    if (now - lastFocusSync < 4000) return;
    lastFocusSync = now;
    refreshCartCount();
    refreshNotificationCount();
}
window.addEventListener('focus', syncAfterReturnToPage);
document.addEventListener('visibilitychange', syncAfterReturnToPage);
window.addEventListener('pageshow', (event) => {
    if (event.persisted) syncAfterReturnToPage();
});

function visitBuyerPage(url) {
    const target = new URL(url, window.location.href);
    const isSafeBuyerPage = target.origin === window.location.origin &&
        /^\/buyer\/(?:products|products\/\d+|cart)$/.test(target.pathname);
    if (isSafeBuyerPage && typeof window.Livewire?.navigate === 'function') {
        window.Livewire.navigate(target.href);
    } else {
        window.location.assign(target.href);
    }
}

function initBuyerShell() {
    // Sidebar, mobile drawer and overlay are owned by the persistent shell in
    // layouts/buyer.blade.php. Do not bind a second, conflicting set of handlers.

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
        const productsUrl = document.body?.dataset.buyerProductsUrl || '/buyer/products';
        visitBuyerPage(query ? `${productsUrl}?search=${encodeURIComponent(query)}` : productsUrl);
    };

    // The header is replaced during Livewire navigation. Delegate once at the
    // document level so Enter-to-search keeps working on every Buyer page.
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter') return;
        const input = event.target.closest?.('#buyerHeaderSearch, #buyerMobileSearch');
        if (!input) return;
        event.preventDefault();
        submitSearch(input);
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
                if (productUrl) visitBuyerPage(productUrl);
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
                visitBuyerPage(productUrl);
            }
        }
    });

    refreshCartCount();
    refreshNotificationCount();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBuyerShell, { once: true });
} else {
    initBuyerShell();
}
document.addEventListener('livewire:navigated', () => {
    refreshCartCount();
    refreshNotificationCount();
});
