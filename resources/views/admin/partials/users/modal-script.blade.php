<script>
(function () {
    const normalize = value => (value || "").toString().trim().toLowerCase();
    const drawer = document.getElementById('profileDrawer');
    const backdrop = document.getElementById('profileBackdrop');
    const closeProfile = document.getElementById('closeProfile');
    const tabs = Array.from(document.querySelectorAll('[data-drawer-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-drawer-panel]'));

    function switchTab(name) {
        tabs.forEach(tab => {
            const active = tab.dataset.drawerTab === name;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', String(active));
            tab.tabIndex = active ? 0 : -1;
        });
        panels.forEach(panel => panel.hidden = panel.dataset.drawerPanel !== name);
    }

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => switchTab(tab.dataset.drawerTab));
        tab.addEventListener('keydown', event => {
            let next;
            if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
            if (event.key === 'ArrowLeft') next = (index + tabs.length - 1) % tabs.length;
            if (event.key === 'Home') next = 0;
            if (event.key === 'End') next = tabs.length - 1;
            if (next === undefined) return;
            event.preventDefault();
            switchTab(tabs[next].dataset.drawerTab);
            tabs[next].focus();
        });
    });
    document.querySelectorAll('[data-jump-manage]').forEach(button => button.addEventListener('click', () => switchTab('manage')));
    document.querySelector('[data-jump-activity]')?.addEventListener('click', () => switchTab('activity'));

    function initialsFromName(name) {
        return (name || 'U').split(/\s+/).filter(Boolean).slice(0,2).map(value => value.charAt(0).toUpperCase()).join('') || 'U';
    }

    function renderActivity(items, actor = {}) {
        const timeline = document.getElementById('activityTimeline');
        if (!timeline) return;
        timeline.setAttribute('role', 'list');
        if (!Array.isArray(items) || items.length === 0) {
            timeline.innerHTML = '<p class="account-activity-empty" role="status">No recorded activity yet.</p>';
            return;
        }
        timeline.innerHTML = items.map(item => `
            <div class="account-activity-row" role="listitem">
                <span class="account-activity-avatar">${escapeHtml(initialsFromName(actor.name))}</span>
                <div class="account-activity-content">
                    <div class="account-activity-user"><strong>${escapeHtml(actor.name || "User")}</strong><span>${escapeHtml(actor.reference || "")}</span></div>
                    <p class="account-activity-description">${escapeHtml(item.description || 'Account activity')}</p>
                    <p class="account-activity-meta">${escapeHtml((item.action || 'activity').replaceAll('_', ' '))}<span aria-hidden="true"> · </span>${escapeHtml(item.time || '')}</p>
                </div>
                <span class="account-activity-relative">${escapeHtml(item.relative || '')}</span>
            </div>`).join('');
        if (actor.photoUrl) {
            timeline.querySelectorAll('.account-activity-avatar').forEach(avatar => {
                const photo = document.createElement('img');
                photo.alt = 'Profile photo of ' + (actor.name || 'user');
                photo.hidden = true;
                photo.addEventListener('load', () => { photo.hidden = false; }, { once: true });
                photo.addEventListener('error', () => { photo.remove(); }, { once: true });
                avatar.appendChild(photo);
                photo.src = actor.photoUrl;
            });
        }
    }

    function escapeHtml(value) {
        return String(value ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#039;');
    }

    function renderProfile(profile) {
        const sectionsRoot = document.getElementById('drawerProfileSections');
        const documentsWrap = document.getElementById('drawerDocumentsWrap');
        const documentsRoot = document.getElementById('drawerDocuments');
        if (!sectionsRoot || !documentsWrap || !documentsRoot) return;

        const sections = Array.isArray(profile?.sections) ? profile.sections : [];
        if (sections.length === 0) {
            sectionsRoot.innerHTML = '<div class="enterprise-detail-section"><div class="enterprise-detail-section-heading"><div><p>No additional account data</p><span>No extended profile fields are available for this record.</span></div></div></div>';
        } else {
            sectionsRoot.innerHTML = sections.map(section => {
                const fields = Array.isArray(section?.fields) ? section.fields : [];
                const fieldMarkup = fields.map(field => {
                    const value = field?.value === null || field?.value === undefined || field?.value === '' ? '—' : String(field.value);
                    const emptyClass = value === '—' ? ' is-empty' : '';
                    return `<div class="enterprise-detail-item"><dt class="enterprise-detail-label">${escapeHtml(field?.label || 'Field')}</dt><dd class="enterprise-detail-value${emptyClass}">${escapeHtml(value)}</dd></div>`;
                }).join('');

                return `<section class="enterprise-detail-section"><div class="enterprise-detail-section-heading"><div><p>${escapeHtml(section?.title || 'Account Information')}</p></div></div><dl class="enterprise-detail-grid">${fieldMarkup}</dl></section>`;
            }).join('');
        }

        const documents = Array.isArray(profile?.documents) ? profile.documents : [];
        documentsWrap.classList.toggle('hidden', documents.length === 0);
        documentsRoot.innerHTML = documents.map(document => `
            <a class="enterprise-document-link" href="${escapeHtml(document?.url || '#')}" target="_blank" rel="noopener">
                <span>${escapeHtml(document?.label || 'Registration document')}</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14 5h5v5"></path><path d="m10 14 9-9"></path><path d="M19 13v6H5V5h6"></path></svg>
            </a>`).join('');
    }

    let returnFocus = null;
    function openProfile(row, targetTab = 'overview', focusTarget = null) {
        returnFocus = row.querySelector('[popovertarget]') || document.activeElement;
        const name = row.dataset.displayName || 'User';
        const role = row.dataset.role || 'User';
        const status = row.dataset.status || 'active';
        document.getElementById('drawerName').textContent = 'Account management';
        document.getElementById('drawerProfileName').textContent = name;
        const avatar = document.getElementById('drawerInitials');
        avatar.textContent = initialsFromName(name);
        avatar.classList.add('user-account-avatar');
        const photoState = document.getElementById('drawerPhotoState');
        photoState.textContent = row.dataset.photoUrl ? 'Loading photo…' : 'No uploaded photo';
        if (row.dataset.photoUrl) {
            const image = document.createElement('img');
            image.alt = 'Profile photo of ' + name;
            image.addEventListener('error', () => { if (!avatar.contains(image)) return; image.hidden = true; photoState.textContent = 'Photo unavailable'; }, { once: true });
            image.addEventListener('load', () => { if (avatar.contains(image)) photoState.textContent = ''; }, { once: true });
            avatar.appendChild(image);
            image.src = row.dataset.photoUrl;
        }
        document.getElementById('drawerRole').textContent = role;
        document.getElementById('drawerEmail').textContent = row.dataset.emailDisplay || '—';
        document.getElementById('drawerContact').textContent = row.dataset.contact || 'No contact number';
        document.getElementById('drawerJoined').textContent = 'Joined ' + (row.dataset.joined || '—');
        document.getElementById('drawerId').textContent = row.dataset.accountReference || '—';
        document.getElementById('drawerAccess').textContent = status === 'active' ? 'Access enabled' : (status === 'banned' ? 'Email and account banned' : 'Access suspended');
        document.getElementById('drawerAccess').className = 'mt-1.5 text-[9px] font-semibold ' + (status === 'active' ? 'text-[#45805b]' : 'text-[#a95d45]');

        const badge = document.getElementById('drawerStatusBadge');
        badge.textContent = status === 'active' ? 'Active' : (status === 'banned' ? 'Banned' : 'Suspended');
        badge.className = 'inline-flex rounded-full border px-2.5 py-1 text-[8px] font-semibold ' + (status === 'active' ? 'border-[#d6e9dc] bg-[#eef8f1] text-[#36805a]' : 'border-[#ead5d0] bg-[#fff4f1] text-[#a95d45]');

        document.getElementById('noteForm').action = row.dataset.noteUrl;
        document.getElementById('suspendForm').action = row.dataset.suspendUrl;
        document.getElementById('restoreForm').action = row.dataset.restoreUrl;
        document.getElementById('banForm').action = row.dataset.banUrl;
        document.getElementById('unbanForm').action = row.dataset.unbanUrl;
        document.getElementById('deleteUserForm').action = row.dataset.deleteUrl;
        document.getElementById('suspendForm').classList.toggle('hidden', status !== 'active');
        document.getElementById('restoreForm').classList.toggle('hidden', status === 'active' || status === 'banned');
        document.getElementById('banForm').classList.toggle('hidden', status === 'banned');
        document.getElementById('unbanForm').classList.toggle('hidden', status !== 'banned');

        const messageLink = document.getElementById('sellerMessageLink');
        if (row.dataset.messageUrl) {
            messageLink.href = row.dataset.messageUrl;
            messageLink.classList.remove('hidden');
            messageLink.classList.add('flex');
        } else {
            messageLink.classList.add('hidden');
            messageLink.classList.remove('flex');
        }

        let profile = {};
        try { profile = JSON.parse(row.dataset.profile || '{}'); } catch (_) {}
        renderProfile(profile);

        let activity = [];
        try { activity = JSON.parse(row.dataset.activity || '[]'); } catch (_) {}
        renderActivity(activity, {
            name,
            reference: row.dataset.accountReference || '',
            photoUrl: row.dataset.photoUrl || '',
        });
        switchTab(targetTab);
        drawer?.classList.add('is-open');
        backdrop?.classList.add('is-open');
        drawer?.setAttribute('aria-hidden', 'false');
        backdrop?.setAttribute('aria-hidden', 'false');
        document.body.classList.add('user-drawer-open');

        window.setTimeout(() => {
            if (!focusTarget) closeProfile?.focus({ preventScroll: true });
        }, 60);

        if (focusTarget) {
            window.setTimeout(() => {
                document.querySelector(focusTarget)?.focus({ preventScroll: false });
                document.querySelector(focusTarget)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 120);
        }
    }

    function closeDrawer() {
        if (!drawer?.classList.contains('is-open')) return;
        drawer?.classList.remove('is-open');
        backdrop?.classList.remove('is-open');
        drawer?.setAttribute('aria-hidden', 'true');
        backdrop?.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('user-drawer-open');
        returnFocus?.focus({ preventScroll: true });
    }

    document.querySelectorAll('[data-view-user]').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('[data-user-row]');
            if (row) openProfile(row, 'overview');
        });
    });

    document.querySelectorAll('[data-activity-user]').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('[data-user-row]');
            if (row) openProfile(row, 'activity');
        });
    });

    document.querySelectorAll('[data-note-user]').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('[data-user-row]');
            if (row) openProfile(row, 'manage', '#adminNoteInput');
        });
    });

    document.querySelectorAll('[data-access-user]').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('[data-user-row]');
            if (!row) return;

            const mode = button.dataset.accessMode;
            openProfile(
                row,
                'manage',
                mode === 'restore' ? '#restoreAccessButton' : '#suspendReasonInput'
            );
        });
    });

    document.querySelectorAll('[data-ban-user]').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('[data-user-row]');
            if (!row) return;
            openProfile(row, 'manage', button.dataset.banMode === 'unban' ? '#unbanAccessButton' : '#banReasonInput');
        });
    });

    document.querySelectorAll('[data-delete-user]').forEach(button => {
        button.addEventListener('click', () => {
            const row = button.closest('[data-user-row]');
            if (row) openProfile(row, 'manage', '#deleteAccountButton');
        });
    });
    closeProfile?.addEventListener('click', closeDrawer); backdrop?.addEventListener('click', closeDrawer); document.addEventListener('keydown', event => { if (event.key === 'Escape') closeDrawer(); });

    document.querySelectorAll('[data-close-user-modal]').forEach(button => button.addEventListener('click', closeDrawer));
    document.addEventListener('keydown', event => {
        if (event.key !== 'Tab' || !drawer?.classList.contains('is-open')) return;
        const controls = Array.from(drawer.querySelectorAll('button, a[href], input, textarea, select, [tabindex="0"]')).filter(element => !element.disabled && element.getClientRects().length);
        const first = controls[0], last = controls[controls.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
    });
})();
</script>
