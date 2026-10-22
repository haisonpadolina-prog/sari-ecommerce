<script>
(function () {
    function initRegistrationReview() {
        window.__SARI_REGISTRATION_REVIEW_ABORT__?.abort();
        document.body.classList.remove('reg-review-open');
        const drawer = document.getElementById('reviewDrawer');
        const backdrop = document.getElementById('reviewBackdrop');
        if (!drawer || !backdrop) return;
        const controller = new AbortController();
        const signal = controller.signal;
        window.__SARI_REGISTRATION_REVIEW_ABORT__ = controller;
        drawer.querySelectorAll('[data-registration-avatar]').forEach(image => {
            listenAvatarError(image);
        });
        function listenAvatarError(image) {
            image.addEventListener('error', () => { image.hidden = true; }, { signal });
            image.addEventListener('load', () => { image.hidden = false; }, { signal });
        }
        let selectedDetail = null;
        let returnFocus = null;
        const listen = (element, type, handler) => element?.addEventListener(type, handler, { signal });
        const focusable = () => Array.from(drawer.querySelectorAll('button:not([disabled]), a[href], textarea:not([disabled]), [tabindex="0"]')).filter(element => !element.closest('[hidden]') && element.getClientRects().length > 0);
        function showTab(name) {
            if (!selectedDetail) return;
            selectedDetail.querySelectorAll('[data-detail-tab]').forEach(tab => {
                const active = tab.dataset.detailTab === name;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-pressed', String(active));
            });
            selectedDetail.querySelectorAll('[data-detail-tab-panel]').forEach(panel => {
                panel.hidden = panel.dataset.detailTabPanel !== name;
            });
            if (name === 'documents') {
                selectedDetail.querySelectorAll('[data-registration-preview-src]').forEach(image => {
                    if (!image.getAttribute('src')) image.src = image.dataset.registrationPreviewSrc;
                });
            }
            selectedDetail.querySelector('.registration-modal-body')?.scrollTo({ top: 0 });
        }
        function openReview(key, trigger) {
            selectedDetail = Array.from(drawer.querySelectorAll('[data-application-detail]')).find(detail => detail.dataset.applicationDetail === String(key));
            if (!selectedDetail) return;
            returnFocus = trigger;
            drawer.querySelectorAll('[data-application-detail]').forEach(detail => { detail.hidden = detail !== selectedDetail; });
            document.querySelectorAll('[data-application-row]').forEach(row => row.classList.toggle('is-selected', row.dataset.applicationRow === String(key)));
            selectedDetail.querySelectorAll('[data-registration-avatar-src]').forEach(image => {
                if (!image.getAttribute('src')) image.src = image.dataset.registrationAvatarSrc;
            });
            showTab('overview');
            drawer.inert = false;
            drawer.setAttribute('aria-labelledby', 'registration-review-title-' + key);
            drawer.setAttribute('aria-hidden', 'false');
            drawer.classList.add('is-open');
            backdrop.classList.add('is-open');
            document.body.classList.add('reg-review-open');
            selectedDetail.querySelector('[data-close-review]')?.focus({ preventScroll: true });
        }
        function closeReview() {
            if (!drawer.classList.contains('is-open')) return;
            drawer.classList.remove('is-open');
            backdrop.classList.remove('is-open');
            document.body.classList.remove('reg-review-open');
            if (returnFocus?.isConnected) returnFocus.focus({ preventScroll: true });
            drawer.setAttribute('aria-hidden', 'true');
            drawer.inert = true;
            document.querySelectorAll('[data-application-row]').forEach(row => row.classList.remove('is-selected'));
            selectedDetail = null;
        }
        document.querySelectorAll('[data-application-row]').forEach(row => {
            listen(row, 'click', event => {
                if (!event.target.closest('a, button, input, textarea, select, form')) openReview(row.dataset.applicationRow, row);
            });
            listen(row, 'keydown', event => {
                if (event.target === row && ['Enter', ' '].includes(event.key)) {
                    event.preventDefault();
                    openReview(row.dataset.applicationRow, row);
                }
            });
        });
        document.querySelectorAll('[data-open-review]').forEach(button => listen(button, 'click', event => {
            event.preventDefault();
            event.stopPropagation();
            openReview(button.dataset.openReview, button);
        }));
        drawer.querySelectorAll('[data-close-review]').forEach(button => listen(button, 'click', closeReview));
        drawer.querySelectorAll('[data-detail-tab]').forEach(button => listen(button, 'click', () => showTab(button.dataset.detailTab)));
        drawer.querySelectorAll('[data-review-action]').forEach(button => listen(button, 'click', () => {
            showTab('decision');
            const prefix = button.dataset.reviewAction === 'decline' ? 'reject-note-' : 'approve-note-';
            document.getElementById(prefix + button.dataset.detailOwner)?.focus({ preventScroll: true });
        }));
        listen(backdrop, 'click', closeReview);
        listen(document, 'keydown', event => {
            if (!drawer.classList.contains('is-open')) return;
            if (event.key === 'Escape') { event.preventDefault(); closeReview(); }
            if (event.key !== 'Tab') return;
            const controls = focusable();
            const first = controls[0];
            const last = controls[controls.length - 1];
            if (!first) { event.preventDefault(); drawer.focus(); return; }
            if (event.shiftKey && (document.activeElement === first || !drawer.contains(document.activeElement))) {
                event.preventDefault(); last.focus();
            } else if (!event.shiftKey && (document.activeElement === last || !drawer.contains(document.activeElement))) {
                event.preventDefault(); first.focus();
            }
        });
        listen(document, 'focusin', event => {
            if (drawer.classList.contains('is-open') && !drawer.contains(event.target)) focusable()[0]?.focus();
        });
        listen(document, 'livewire:navigating', closeReview);
    }
    window.__SARI_INIT_REGISTRATION_REVIEW__ = initRegistrationReview;
    if (!window.__SARI_REGISTRATION_REVIEW_LIVEWIRE__) {
        window.__SARI_REGISTRATION_REVIEW_LIVEWIRE__ = true;
        document.addEventListener('livewire:navigated', () => window.__SARI_INIT_REGISTRATION_REVIEW__?.());
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initRegistrationReview, { once: true });
    else initRegistrationReview();
})();
</script>
