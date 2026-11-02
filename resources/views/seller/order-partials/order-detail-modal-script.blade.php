/* Dedicated modal behavior, referenced from the main Orders controller. */
function createSellerOrderModalController(modal, modalContent) {
    let lastTrigger = null;

    function closeOrderModal({ restoreFocus = true } = {}) {
        if (!modal || !modalContent) return;
        modal.dataset.openTemplate = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        modalContent.replaceChildren();
        document.documentElement.classList.remove('overflow-hidden');
        if (restoreFocus) {
            const focusTarget = lastTrigger?.isConnected
                ? lastTrigger
                : document.querySelector('[data-order-template]');
            focusTarget?.focus({ preventScroll: true });
        }
    }

    function openOrderModal(templateId, preserveFocus = false, opener = null) {
        if (!modal || !modalContent) return;
        const template = document.getElementById(templateId);
        if (!(template instanceof HTMLTemplateElement)) return;

        if (opener) lastTrigger = opener;
        modal.dataset.openTemplate = templateId;
        modalContent.replaceChildren(template.content.cloneNode(true));
        // Lazy-load only the selected order's address map. Other orders never contact the map provider.
        modalContent.querySelectorAll('iframe[data-order-map-src]').forEach((frame) => {
            frame.src = frame.dataset.orderMapSrc;
        });
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('overflow-hidden');

        if (!preserveFocus) {
            window.requestAnimationFrame(() => {
                modalContent.querySelector('[data-close-order-modal]')?.focus({ preventScroll: true });
            });
        }
    }

    function trapModalFocus(event) {
        if (event.key !== 'Tab' || !modal || modal.classList.contains('hidden')) return;
        const focusables = [...modalContent.querySelectorAll('button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])')]
            .filter((element) => element.getClientRects().length > 0);
        if (!focusables.length) { event.preventDefault(); return; }
        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        if (event.shiftKey && (document.activeElement === first || !modalContent.contains(document.activeElement))) {
            event.preventDefault(); last.focus();
        } else if (!event.shiftKey && (document.activeElement === last || !modalContent.contains(document.activeElement))) {
            event.preventDefault(); first.focus();
        }
    }

    return { closeOrderModal, openOrderModal, trapModalFocus };
}
