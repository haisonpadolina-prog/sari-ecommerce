(function () {
    function initBuyerShop() {
        const grid = document.getElementById('shopProductGrid');
        if (!grid) return;

        window.__SARI_BUYER_SHOP_ABORT__?.abort();

        const controller = new AbortController();
        window.__SARI_BUYER_SHOP_ABORT__ = controller;
        const signal = controller.signal;

        const search = document.getElementById('shopProductSearch');
        const empty = document.getElementById('shopProductsEmpty');
        const feedback = document.getElementById('shopVoucherFeedback');
        const cards = Array.from(grid.querySelectorAll('[data-product-card]'));

        const apply = () => {
            const query = String(search?.value || '').trim().toLowerCase();
            let visible = 0;

            cards.forEach((card) => {
                const haystack = `${card.dataset.productName || ''} ${card.dataset.productCategory || ''}`.toLowerCase();
                const show = !query || haystack.includes(query);

                card.classList.toggle('hidden', !show);
                if (show) visible++;
            });

            empty?.classList.toggle('hidden', visible > 0);
        };

        search?.addEventListener('input', apply, { signal });

        document.querySelectorAll('[data-copy-voucher]').forEach((button) => {
            button.addEventListener('click', async function () {
                const code = String(this.dataset.copyVoucher || '').trim();
                if (!code) return;

                try {
                    await navigator.clipboard.writeText(code);
                } catch (_) {
                    // The code remains visible in the feedback when the
                    // Clipboard API is unavailable (for example over HTTP).
                }

                if (feedback) {
                    feedback.textContent = `Voucher ${code} is ready to use at checkout.`;
                    feedback.classList.remove('hidden');
                }
            }, { signal });
        });

        apply();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBuyerShop, { once: true });
    } else {
        initBuyerShop();
    }

    document.addEventListener('livewire:navigated', initBuyerShop);
})();
