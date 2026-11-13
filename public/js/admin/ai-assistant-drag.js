/* Floating launcher movement only. Chat requests, language and admin actions remain in ai-assistant.js. */
(() => {
    'use strict';
    if (window.__SARI_ADMIN_AI_DRAG__) return;
    window.__SARI_ADMIN_AI_DRAG__ = true;
    const KEY = 'sari:admin-ai:launcher-position:v1';
    const EDGE = 12;
    let currentRoot = null;
    let cleanup = () => {};

    function init() {
        const root = document.getElementById('sariAdminAi');
        if (root === currentRoot && root?.isConnected) return;
        cleanup();
        currentRoot = root;
        if (!root) return;
        const launcher = root.querySelector('.sai-launch');
        const panel = root.querySelector('.sai-panel');
        if (!launcher || !panel) return;
        const controller = new AbortController();
        const signal = controller.signal;
        let drag = null, position = null, preferred = null, blockClickUntil = 0;
        root.dataset.aiDraggable = '1';
        const listen = (target, name, callback, options = {}) => target?.addEventListener(name, callback, { ...options, signal });
        const clamp = (value, min, max) => Math.min(Math.max(value, min), Math.max(min, max));
        function viewport() {
            const view = window.visualViewport;
            return { x: view?.offsetLeft || 0, y: view?.offsetTop || 0,
                width: view?.width || window.innerWidth || document.documentElement.clientWidth,
                height: view?.height || window.innerHeight || document.documentElement.clientHeight };
        }
        function limits() {
            const view = viewport();
            const width = launcher.offsetWidth || 72, height = launcher.offsetHeight || 72;
            return { ...view, widthButton: width, heightButton: height,
                minX: view.x + EDGE, minY: view.y + EDGE,
                maxX: Math.max(view.x + EDGE, view.x + view.width - width - EDGE),
                maxY: Math.max(view.y + EDGE, view.y + view.height - height - EDGE) };
        }
        function remember(write = false) {
            const box = limits();
            preferred = { x: (position.x - box.minX) / Math.max(1, box.maxX - box.minX),
                y: (position.y - box.minY) / Math.max(1, box.maxY - box.minY) };
            if (write) {
                try { localStorage.setItem(KEY, JSON.stringify({ version: 1, ...preferred })); } catch (_) {}
            }
        }
        function placePanel() {
            if (panel.hidden || !position) return;
            const view = viewport();
            panel.style.setProperty('max-width', Math.max(0, view.width - EDGE * 2) + 'px', 'important');
            panel.style.setProperty('max-height', Math.max(0, view.height - EDGE * 2) + 'px', 'important');
            const width = panel.offsetWidth || Math.min(root.classList.contains('is-expanded') ? 760 : 480, view.width - EDGE * 2);
            const height = panel.offsetHeight || Math.min(740, view.height - EDGE * 2);
            const buttonWidth = launcher.offsetWidth || 72, buttonHeight = launcher.offsetHeight || 72;
            const leftSide = position.x + buttonWidth / 2 < view.x + view.width / 2;
            const topSide = position.y + buttonHeight / 2 < view.y + view.height / 2;
            const x = leftSide ? position.x : position.x + buttonWidth - width;
            const y = topSide ? position.y + buttonHeight + 12 : position.y - height - 12;
            panel.style.left = clamp(x, view.x + EDGE, view.x + view.width - width - EDGE) + 'px';
            panel.style.top = clamp(y, view.y + EDGE, view.y + view.height - height - EDGE) + 'px';
            panel.style.transformOrigin = `${leftSide ? 'left' : 'right'} ${topSide ? 'top' : 'bottom'}`;
        }
        function place(x, y) {
            const box = limits();
            position = { x: clamp(x, box.minX, box.maxX), y: clamp(y, box.minY, box.maxY) };
            root.style.left = position.x + 'px';
            root.style.top = position.y + 'px';
            root.style.right = 'auto';
            root.style.bottom = 'auto';
            root.dataset.aiDock = position.x + box.widthButton / 2 < box.x + box.width / 2 ? 'left' : 'right';
            placePanel();
        }
        function fitViewport() {
            const box = limits();
            if (preferred) place(box.minX + preferred.x * (box.maxX - box.minX), box.minY + preferred.y * (box.maxY - box.minY));
            else {
                const rect = root.getBoundingClientRect();
                place(rect.width ? rect.left : box.maxX - 12, rect.height ? rect.top : box.maxY - 10);
                remember();
            }
        }
        try {
            const saved = JSON.parse(localStorage.getItem(KEY) || 'null');
            if (saved?.version === 1 && Number.isFinite(saved.x) && Number.isFinite(saved.y)) {
                preferred = { x: clamp(saved.x, 0, 1), y: clamp(saved.y, 0, 1) };
            }
        } catch (_) {}
        fitViewport();

        listen(launcher, 'pointerdown', event => {
            if (event.button !== 0 || event.isPrimary === false || drag || root.classList.contains('is-open')) return;
            drag = { id: event.pointerId, x: event.clientX, y: event.clientY, left: position.x, top: position.y, moved: false };
            try { launcher.setPointerCapture(event.pointerId); } catch (_) {}
        });
        listen(launcher, 'pointermove', event => {
            if (!drag || event.pointerId !== drag.id) return;
            const dx = event.clientX - drag.x, dy = event.clientY - drag.y;
            if (!drag.moved && Math.hypot(dx, dy) < 6) return;
            drag.moved = true;
            root.classList.add('is-dragging');
            event.preventDefault();
            place(drag.left + dx, drag.top + dy);
        });
        function endDrag(event) {
            if (!drag || event.pointerId !== drag.id) return;
            const moved = drag.moved;
            const id = drag.id;
            drag = null;
            root.classList.remove('is-dragging');
            try { launcher.releasePointerCapture(id); } catch (_) {}
            if (moved) { blockClickUntil = Date.now() + 450; remember(true); }
        }
        listen(launcher, 'pointerup', endDrag);
        listen(launcher, 'pointercancel', endDrag);
        listen(launcher, 'lostpointercapture', endDrag);
        // Suppress only the pointer click generated by dragging; keyboard activation stays intact.
        listen(launcher, 'click', event => {
            if (Date.now() < blockClickUntil && (event.detail > 0 || event.pointerType)) {
                event.preventDefault(); event.stopImmediatePropagation();
            }
        }, { capture: true });
        // Optional keyboard positioning: Alt + arrow keys; Alt + Home restores the default corner.
        listen(launcher, 'keydown', event => {
            if (!event.altKey || !['ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home'].includes(event.key)) return;
            event.preventDefault();
            const step = event.shiftKey ? 40 : 12;
            if (event.key === 'Home') { const box = limits(); place(box.maxX - 12, box.maxY - 10); }
            else place(position.x + (event.key === 'ArrowRight' ? step : event.key === 'ArrowLeft' ? -step : 0),
                position.y + (event.key === 'ArrowDown' ? step : event.key === 'ArrowUp' ? -step : 0));
            remember(true);
        });
        listen(window, 'resize', fitViewport);
        listen(window, 'pageshow', fitViewport);
        listen(window.visualViewport, 'resize', fitViewport);
        listen(window.visualViewport, 'scroll', fitViewport);
        const stateObserver = new MutationObserver(placePanel);
        stateObserver.observe(root, { attributes: true, attributeFilter: ['class'] });
        stateObserver.observe(panel, { attributes: true, attributeFilter: ['hidden'] });
        const sizeObserver = window.ResizeObserver ? new ResizeObserver(placePanel) : null;
        sizeObserver?.observe(panel);
        cleanup = () => {
            controller.abort(); stateObserver.disconnect(); sizeObserver?.disconnect();
            if (drag) { try { launcher.releasePointerCapture(drag.id); } catch (_) {} }
            drag = null; root.classList.remove('is-dragging');
        };
    }
    document.addEventListener('livewire:navigated', init);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
