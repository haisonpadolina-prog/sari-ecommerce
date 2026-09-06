document.addEventListener('DOMContentLoaded', () => {
    let revision = null;
    const url = '/buyer/orders-live-state';

    async function sync() {
        if (document.hidden) return;
        try {
            const response = await fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', cache: 'no-store' });
            if (!response.ok) return;
            const data = await response.json();
            if (revision && data.revision && String(revision) !== String(data.revision)) {
                window.location.reload();
                return;
            }
            revision = data.revision || revision;
        } catch (_) {}
    }

    sync();
    window.setInterval(sync, 3500);
});
