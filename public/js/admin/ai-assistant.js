(() => {
    'use strict';
    if (window.__SARI_ADMIN_AI__) return;
    window.__SARI_ADMIN_AI__ = true;
    function init() {
        const root = document.getElementById('sariAdminAi');
        if (!root || root.dataset.bound) return;
        root.dataset.bound = '1';
        const panel = root.querySelector('.sai-panel');
        const launcher = root.querySelector('.sai-launch');
        const feed = root.querySelector('[data-ai-feed]');
        const form = root.querySelector('[data-ai-form]');
        const input = root.querySelector('textarea');
        const status = root.querySelector('[data-ai-status]');
        let allowedActions = [], thread = null, busy = false, checked = false, translating = false;
        const languageSelect = root.querySelector('[data-ai-language]');
        const languageOverlay = root.querySelector('[data-ai-language-overlay]');
        const languageTrigger = root.querySelector('[data-ai-language-open]');
        const languageDialog = root.querySelector('#saiLanguageDialog');
        const languageBackground = [...panel.children].filter(node => node !== languageOverlay);
        const catalogs = window.SariAdminAiLocale || { en: {}, tl: {} };
        let autoLocale = 'en', translationGeneration = 0, chatBlockedUntil = 0, cooldownTimer = null;
        try {
            const saved = localStorage.getItem('sari:admin-ai:language');
            if (['auto', 'English', 'Tagalog', 'Taglish'].includes(saved)) languageSelect.value = saved;
        } catch (_) {}
        const locale = () => languageSelect.value === 'Tagalog' || languageSelect.value === 'Taglish' ? 'tl' : languageSelect.value === 'English' ? 'en' : autoLocale;
        function t(key, values = {}) {
            let text = catalogs[locale()]?.[key] || catalogs.en?.[key] || key.replaceAll('_', ' ');
            Object.entries(values).forEach(([name, value]) => { text = text.replaceAll('{' + name + '}', String(value)); });
            return text;
        }
        function syncBusy() {
            root.querySelector('[data-ai-send]').disabled = busy || translating || Date.now() < chatBlockedUntil;
            root.querySelector('[data-ai-clear]').disabled = busy || translating;
            languageSelect.disabled = busy;
            languageTrigger.disabled = busy || translating;
        }
        function applyLanguage() {
            root.lang = locale() === 'tl' ? 'fil' : 'en';
            root.querySelector('[data-ai-language-label]').textContent = t('language_' + languageSelect.value.toLowerCase());
            for (const [attribute, property] of [['data-i18n', 'textContent'], ['data-i18n-aria', 'aria-label'], ['data-i18n-title', 'title'], ['data-i18n-placeholder', 'placeholder']]) {
                root.querySelectorAll('[' + attribute + ']').forEach(node => {
                    const value = t(node.getAttribute(attribute));
                    if (property === 'textContent') node.textContent = value; else node.setAttribute(property, value);
                });
            }
            if (status.dataset.key) status.textContent = t(status.dataset.key, JSON.parse(status.dataset.values || '{}'));
            root.querySelectorAll('.sai-chart').forEach(chart => {
                if (chart._chartData && window.SariAdminAiChart) chart.replaceWith(makeChart(chart._chartData));
            });
        }
        const welcomeTemplate = feed.querySelector('.sai-welcome')?.cloneNode(true);
        const el = (tag, text, cls) => {
            const node = document.createElement(tag);
            if (text !== undefined) node.textContent = String(text);
            if (cls) node.className = cls;
            return node;
        };
        const setStatus = (key, error = false, values = {}) => { status.dataset.key = key; status.dataset.values = JSON.stringify(values); status.textContent = t(key, values); status.dataset.error = String(error); status.classList.toggle('sai-sr', !error && !['working', 'checking', 'translating'].includes(key)); };
        function startChatCooldown(seconds) {
            chatBlockedUntil = Date.now() + seconds * 1000;
            if (cooldownTimer) clearInterval(cooldownTimer);
            const tick = () => {
                const remaining = Math.max(0, Math.ceil((chatBlockedUntil - Date.now()) / 1000));
                syncBusy();
                if (remaining) setStatus('rate_wait', true, { seconds: remaining });
                else { clearInterval(cooldownTimer); cooldownTimer = null; if (!busy && !translating) setStatus('ready'); }
            };
            tick(); cooldownTimer = setInterval(tick, 1000);
        }
        const ln = (tag, key, cls) => { const node = el(tag, t(key), cls); node.dataset.i18n = key; return node; };
        const metaLabel = (card, key, value) => { const line = el('p', undefined, 'sai-meta'); line.append(ln('span', key), document.createTextNode(' · ' + value)); card.append(line); };
        const scroll = () => { feed.scrollTop = feed.scrollHeight; };
        const safePath = (value) => {
            try {
                const url = new URL(value, location.origin);
                return url.origin === location.origin && url.pathname.startsWith('/admin/') ? url.href : null;
            } catch (_) { return null; }
        };
        async function request(path, body, method) {
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 115000);
            try {
                const response = await fetch(root.dataset.base + path, {
                    method: method || (body ? 'POST' : 'GET'), credentials: 'same-origin', signal: controller.signal,
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                    body: body ? JSON.stringify(body) : undefined,
                });
                let payload;
                try { payload = await response.json(); } catch (_) {
                    if (response.status !== 429) throw new Error(t('session_error'));
                    payload = {};
                }
                if (response.status === 429) {
                    const retry = Number(response.headers?.get?.('Retry-After') || payload.retry_after || 60);
                    const seconds = Number.isFinite(retry) ? Math.max(1, Math.min(3600, Math.ceil(retry))) : 60;
                    const error = new Error(t('rate_wait', { seconds })); error.status = 429; error.retryAfter = seconds;
                    throw error;
                }
                if (!response.ok) {
                    const fallback = t(response.status === 429 ? 'rate_error' : 'request_error');
                    // Only render user-safe validation/configuration errors, never a server stack trace.
                    throw new Error(response.status >= 500 && ![502, 503].includes(response.status)
                        ? t('server_error') : payload.message || fallback);
                }
                return payload;
            } catch (error) {
                if (error.name === 'AbortError') throw new Error(t('timeout'));
                throw error;
            } finally { clearTimeout(timer); }
        }
        function avatar(cls = 'sari-admin-ai-mini-avatar') {
            const wrap = el('span', undefined, cls), img = el('img');
            img.src = root.dataset.robot; img.alt = ''; wrap.setAttribute('aria-hidden', 'true'); wrap.append(img); return wrap;
        }
        function paintBubble(bubble, text) {
            bubble.replaceChildren();
            String(text).split(/(\*\*[^*\n]+\*\*)/g).forEach(piece => bubble.append(
                piece.startsWith('**') && piece.endsWith('**') ? el('strong', piece.slice(2, -2)) : document.createTextNode(piece)));
        }
        function message(role, text) {
            const node = el('article', undefined, 'sai-message'); node.dataset.role = role;
            node.dataset.original = String(text); node._translations = new Map();
            if (role !== 'user') node.append(role === 'error' ? ln('span', 'request_status', 'sari-admin-ai-message-label') : el('span', 'SARI AI', 'sari-admin-ai-message-label'));
            const line = el('div', undefined, 'sari-admin-ai-message sari-admin-ai-message--' + (role === 'user' ? 'user' : 'assistant'));
            if (role !== 'user') line.append(avatar());
            const bubble = el('div', undefined, 'sai-text sari-admin-ai-bubble'); paintBubble(bubble, text);
            line.append(bubble); node.append(line);
            const tools = el('div', undefined, 'sai-message-tools');
            if (role === 'assistant') tools.append(button('copy', async event => {
                const control = event.currentTarget;
                try { await navigator.clipboard.writeText(bubble.textContent); control.textContent = t('copied'); }
                catch (_) { control.textContent = t('copy_error'); }
            }));
            node.append(tools); feed.append(node); scroll(); return node;
        }
        async function translateConversation() {
            const generation = ++translationGeneration, language = languageSelect.value;
            const nodes = [...feed.querySelectorAll('.sai-message')].filter(n => ['user', 'assistant'].includes(n.dataset.role));
            if (language === 'auto') {
                nodes.forEach(n => { paintBubble(n.querySelector('.sai-text'), n.dataset.original); n.querySelector('[data-original-toggle]')?.remove(); });
                translating = false; syncBusy(); setStatus('ready'); return;
            }
            if (!nodes.length) { setStatus('ready'); return; }
            translating = true; syncBusy(); setStatus('translating');
            try {
                const fragments = [], layouts = new Map();
                nodes.forEach(node => {
                    if (node._translations.has(language)) return;
                    const chars = Array.from(node.dataset.original), ids = [];
                    for (let start = 0; start < chars.length; start += 3000) {
                        const id = fragments.length; ids.push(id); fragments.push({ id, text: chars.slice(start, start + 3000).join('') });
                    }
                    layouts.set(node, ids);
                });
                const translated = new Map();
                for (let start = 0; start < fragments.length; start += 4) {
                    if (generation !== translationGeneration) return;
                    const result = await request('/translate', { language, messages: fragments.slice(start, start + 4) });
                    (result.messages || []).forEach(item => translated.set(item.id, item.text));
                }
                if (generation !== translationGeneration) return;
                layouts.forEach((ids, node) => {
                    if (ids.some(id => !translated.has(id))) throw new Error(t('translation_error'));
                    node._translations.set(language, ids.map(id => translated.get(id)).join(''));
                });
                nodes.forEach(node => {
                    const value = node._translations.get(language) || node.dataset.original;
                    paintBubble(node.querySelector('.sai-text'), value);
                    node.querySelector('[data-original-toggle]')?.remove();
                    if (value !== node.dataset.original) {
                        let original = false;
                        const toggle = button('show_original', () => {
                            original = !original; paintBubble(node.querySelector('.sai-text'), original ? node.dataset.original : value);
                            toggle.dataset.i18n = original ? 'show_translation' : 'show_original'; toggle.textContent = t(toggle.dataset.i18n);
                        });
                        toggle.dataset.originalToggle = '1'; node.querySelector('.sai-message-tools').append(toggle);
                    }
                });
                setStatus('translated');
            } catch (_) { if (generation === translationGeneration) setStatus('translation_error', true); }
            finally { if (generation === translationGeneration) { translating = false; syncBusy(); } }
        }
        function showTyping() {
            const node = el('div', undefined, 'sai-typing'); node.setAttribute('aria-label', t('thinking'));
            const dots = el('div', undefined, 'sai-typing-dots');
            for (let n = 0; n < 3; n++) dots.append(el('span'));
            node.append(avatar(), dots, ln('span', 'checking')); feed.append(node); scroll(); return node;
        }
        function download(blob, name) {
            const url = URL.createObjectURL(blob), a = el('a'); a.href = url; a.download = name; a.click();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        }
        function csv(rows) {
            const keys = [...new Set(rows.flatMap(row => Object.keys(row)))];
            const cell = value => {
                let text = typeof value === 'object' && value !== null ? JSON.stringify(value) : String(value ?? '');
                if (/^\s*[=+\-@\t\r]/.test(text)) text = "'" + text;
                return '"' + text.replaceAll('"', '""') + '"';
            };
            return '\uFEFF' + [keys.map(cell).join(','), ...rows.map(row => keys.map(key => cell(row[key])).join(','))].join('\r\n');
        }
        function button(label, fn, cls = 'sai-small') {
            const b = catalogs.en?.[label] ? ln('button', label, cls) : el('button', label, cls); b.type = 'button'; b.addEventListener('click', fn); return b;
        }
        function table(card, rows) {
            if (!rows.length) { card.append(ln('p', 'empty')); return; }
            const keys = [...new Set(rows.flatMap(row => Object.keys(row)))].filter(k => !['image_url', 'url'].includes(k));
            const wrap = el('div', undefined, 'sai-scroll'), t = el('table'), head = el('thead'), h = el('tr');
            keys.forEach(key => h.append(ln('th', key))); head.append(h); t.append(head);
            const body = el('tbody');
            rows.forEach(row => {
                const tr = el('tr');
                keys.forEach(key => {
                    const value = row[key], td = el('td');
                    const text = typeof value === 'object' && value !== null ? JSON.stringify(value) : String(value ?? '—');
                    if (key === 'id' && safePath(row.url)) {
                        const link = el('a', text); link.href = safePath(row.url); td.append(link);
                    } else td.textContent = text;
                    td.title = text; tr.append(td);
                }); body.append(tr);
            }); t.append(body); wrap.append(t); card.append(wrap);
            card.append(button('csv', () => download(new Blob([csv(rows)], { type: 'text/csv;charset=utf-8' }), 'sari-records.csv')));
        }
        function makeChart(data) {
            const view = window.SariAdminAiChart.render(data, { t, locale: locale(), download });
            view._chartData = data; return view;
        }
        function chart(card, data) {
            if (!window.SariAdminAiChart) { card.append(ln('p', 'chart_unavailable', 'sai-meta')); return; }
            card.append(makeChart(data));
        }
        function map(card, rows) {
            const points = rows.filter(r => r.latitude !== null && r.longitude !== null
                && Number.isFinite(Number(r.latitude)) && Math.abs(Number(r.latitude)) <= 90
                && Number.isFinite(Number(r.longitude)) && Math.abs(Number(r.longitude)) <= 180);
            if (!points.length) { card.append(ln('p', 'no_coords', 'sai-meta')); return; }
            const controls = el('div', undefined, 'sai-actions');
            let frame;
            points.forEach(point => controls.append(button(t('map') + ': ' + point.name, () => {
                const lat = Number(point.latitude), lon = Number(point.longitude), delta = .025;
                const bounds = [Math.max(-180, lon - delta), Math.max(-90, lat - delta), Math.min(180, lon + delta), Math.min(90, lat + delta)].join(',');
                if (!frame) { frame = el('iframe'); frame.title = t('map_title'); frame.referrerPolicy = 'no-referrer'; frame.loading = 'lazy'; card.append(frame); }
                frame.src = 'https://www.openstreetmap.org/export/embed.html?' + new URLSearchParams({ bbox: bounds, layer: 'mapnik', marker: lat + ',' + lon });
            })));
            card.append(ln('p', 'map_note', 'sai-meta'), controls);
        }
        function proposal(card, data) {
            card.append(el('h4', t('review') + ' · ' + t(data.type)));
            const details = el('div', undefined, 'sai-detail');
            const d = data.details;
            details.textContent = [
                ...data.targets.map(t => '#' + t.id + ' · ' + t.name),
                d.ai_role ? t('permissions') + ': ' + d.ai_role : '',
                d.role ? t('account_role') + ': ' + d.role : '',
                d.reason ? t('reason') + ': ' + d.reason : '',
                d.title ? t('title') + ': ' + d.title : '', d.message ? t('message') + ': ' + d.message : '',
                d.audiences ? t('audiences') + ': ' + d.audiences.join(', ') : '',
                data.type === 'announcement' ? t('mode') + ': ' + t(d.publish ? 'publish' : 'draft') : '',
                t('expires') + ': ' + new Date(data.expires_at).toLocaleString(),
            ].filter(Boolean).join('\n');
            card.append(details);
            if (data.effect) card.append(el('p', data.effect, 'sai-meta'));
            card.append(el('p', data.warning, 'sai-meta'));
            const controls = el('div', undefined, 'sai-actions');
            async function decide(confirm) {
                controls.querySelectorAll('button').forEach(b => { b.disabled = true; });
                try {
                    const result = await request('/actions/' + data.id, { confirm });
                    card.append(el('p', result.message, 'sai-meta'));
                    (result.results || []).forEach(r => card.append(el('p', '#' + r.id + ': ' + r.message, 'sai-meta')));
                    // A provider reply never marks completion; only this confirmed server result can.
                    message('assistant', t(confirm ? 'completed' : 'cancelled'));
                } catch (error) {
                    card.append(el('p', error.message, 'sai-meta'));
                    // The same proposal is idempotent; a transport retry cannot execute twice.
                    controls.querySelectorAll('button').forEach(b => { b.disabled = false; });
                }
                scroll();
            }
            controls.append(button('confirm', () => decide(true), 'sai-confirm'), button('cancel', () => decide(false)));
            card.append(controls);
        }
        function bulkReview(card, rows) {
            const candidates = rows.filter(row => !row.archived && ['pending', 'flagged'].includes(row.status));
            if (!candidates.length) return;
            const selection = el('fieldset', undefined, 'sai-bulk-review');
            selection.append(ln('legend', 'select_products'));
            candidates.forEach(row => {
                const label = el('label'), checkbox = el('input'); checkbox.type = 'checkbox'; checkbox.value = row.id;
                label.append(checkbox, el('span', '#' + row.id + ' · ' + row.name)); selection.append(label);
            });
            const reason = el('textarea'); reason.rows = 2; reason.maxLength = 500; reason.placeholder = t('reject_reason'); reason.setAttribute('data-i18n-placeholder', 'reject_reason'); reason.setAttribute('aria-label', t('reject_reason')); reason.dataset.i18nAria = 'reject_reason';
            const error = el('p', '', 'sai-meta'); error.setAttribute('role', 'status');
            const actions = el('div', undefined, 'sai-actions');
            async function prepare(type) {
                const ids = [...selection.querySelectorAll('input:checked')].map(input => Number(input.value));
                if (!ids.length || ids.length > 10) { error.textContent = t('select_ten'); return; }
                if (type === 'reject_product' && reason.value.trim().length < 5) { error.textContent = t('reason_required'); reason.focus(); return; }
                actions.querySelectorAll('button').forEach(button => { button.disabled = true; }); error.textContent = '';
                try {
                    artifact(await request('/prepare', { type, ids, ...(type === 'reject_product' ? { reason: reason.value.trim() } : {}) }));
                    error.textContent = t('preview_created');
                } catch (failure) { error.textContent = failure.message; }
                finally { actions.querySelectorAll('button').forEach(button => { button.disabled = false; }); }
            }
            actions.append(button('preview_approve', () => prepare('approve_product')), button('preview_reject', () => prepare('reject_product')));
            selection.append(reason, error, actions); card.append(selection);
        }
        const dateText = date => new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit' }).format(date);
        const dateTo = root.querySelector('[data-ai-to]'), dateFrom = root.querySelector('[data-ai-from]');
        dateTo.value = dateText(new Date()); dateFrom.value = dateText(new Date(Date.now() - 29 * 86400000));
        root.querySelectorAll('[data-ai-operation]').forEach(control => control.addEventListener('click', async () => {
            const operation = control.dataset.aiOperation;
            const filters = operation === 'products' ? { status: 'review' } : ['seller_performance', 'cod_remittances'].includes(operation) ? { from: dateFrom.value, to: dateTo.value } : {};
            if (['seller_performance', 'cod_remittances'].includes(operation) && (!filters.from || !filters.to || filters.from > filters.to)) { setStatus('invalid_dates', true); return; }
            control.disabled = true;
            try { artifact(await request('/read', { operation, filters })); root.querySelector('[data-ai-tools]').open = false; }
            catch (error) { message('error', error.message); }
            finally { control.disabled = false; }
        }));
        function artifact(data) {
            const card = el('section', undefined, 'sai-artifact');
            if (data.proposal) { proposal(card, data.proposal); feed.append(card); return; }
            card.append(ln('h4', data.source || 'records'));
            if (data.as_of) metaLabel(card, 'as_of', new Date(data.as_of).toLocaleString());
            const sourceDetails = el('details', undefined, 'sai-source-details');
            sourceDetails.append(ln('summary', 'source_details'));
            if (data.filters) metaLabel(sourceDetails, 'filters', JSON.stringify(data.filters));
            if (Number.isFinite(data.total)) metaLabel(card, 'total', data.total + (data.limit ? ' · ' + t('displaying') + ' ' + data.rows.length : ''));
            if (data.definition || data.note) sourceDetails.append(el('p', data.definition || data.note, 'sai-meta'));
            if (data.filters || data.definition || data.note) card.append(sourceDetails);
            if (data.chart) chart(card, data.chart);
            if (data.source === 'products') (data.rows || []).forEach(row => {
                const product = el('div', undefined, 'sai-product'), image = el('img');
                image.alt = row.name; image.loading = 'lazy';
                const path = safePath(row.image_url);
                if (path) image.src = path;
                image.addEventListener('error', () => { image.remove(); product.style.gridTemplateColumns = '1fr'; });
                const text = el('div'); text.append(el('strong', '#' + row.id + ' · ' + row.name), el('p', row.seller + ' · ₱' + Number(row.price).toLocaleString()), el('p', t('stock') + ' ' + row.stock + ' · ' + row.status));
                product.append(image, text); card.append(product);
            });
            if (data.summary) table(card, [data.summary]);
            if (data.source === 'products' && data.rows?.length && allowedActions.includes('approve_product')) bulkReview(card, data.rows);
            if (data.rows && data.source !== 'products') table(card, data.rows);
            if (data.source === 'products' && data.rows?.length) card.append(button('csv', () => download(new Blob([csv(data.rows)], { type: 'text/csv;charset=utf-8' }), 'sari-products.csv')));
            if (data.source === 'products' && !data.rows?.length) card.append(ln('p', 'empty'));
            if (data.events) { card.append(ln('h4', 'journey_title')); table(card, data.events); }
            if (data.stale_orders) { card.append(ln('h4', 'stale_title')); table(card, data.stale_orders); }
            if (data.workflows) table(card, data.workflows);
            if (data.limitations) card.append(el('p', data.limitations, 'sai-meta'));
            if (data.source === 'locations') map(card, data.rows || []);
            if (data.has_more) card.append(button('next', async event => {
                event.currentTarget.disabled = true;
                try { artifact(await request('/read', { operation: data.source, filters: { ...data.filters, offset: data.offset + data.limit } })); }
                catch (error) { message('error', error.message); event.currentTarget.disabled = false; }
            }));
            feed.append(card); scroll();
        }
        async function send(text) {
            if (busy || translating || !text.trim()) return;
            if (Date.now() < chatBlockedUntil) { setStatus('rate_wait', true, { seconds: Math.ceil((chatBlockedUntil - Date.now()) / 1000) }); return; }
            if (languageSelect.value === 'auto') {
                autoLocale = /\b(ako|ikaw|ang|mga|yung|iyon|ito|paano|ano|bakit|sino|saan|ipakita|pakita|gawin|kailangan|tulong|meron|wala|mayroon|namin|natin|naman|po|paki|araw|ngayon|hanapin|ilang|kumusta|salamat|magandang|pwede|puwede|patingin|magkano|sige|hindi|nakaraan)\b/i.test(text) ? 'tl' : 'en';
                applyLanguage();
            }
            busy = true; syncBusy();
            root.querySelector('.sai-welcome')?.remove(); message('user', text); input.value = '';
            feed.setAttribute('aria-busy', 'true'); setStatus('working');
            const typing = showTyping();
            try {
                const result = await request('/chat', { message: text, thread_id: thread, language: languageSelect.value });
                thread = result.thread_id; typing.remove(); message('assistant', result.message);
                (result.artifacts || []).forEach(artifact); setStatus('connected');
            } catch (error) {
                message('error', error.message);
                if (error.status === 429) { input.value = text; startChatCooldown(error.retryAfter); }
                else setStatus('request_error', true);
            }
            finally { typing.remove(); busy = false; feed.removeAttribute('aria-busy'); syncBusy(); }
        }
        function close() { panel.hidden = true; root.classList.remove('is-open'); launcher.tabIndex = 0; launcher.setAttribute('aria-expanded', 'false'); launcher.focus(); }
        launcher.addEventListener('click', async () => {
            panel.hidden = !panel.hidden; root.classList.toggle('is-open', !panel.hidden); launcher.tabIndex = panel.hidden ? 0 : -1; launcher.setAttribute('aria-expanded', String(!panel.hidden));
            if (panel.hidden) return;
            input.focus();
            if (!checked) {
                try {
                    const data = await request('/status'); checked = data.configured;
                    allowedActions = data.allowed_actions || [];
                    if (data.read_operations) root.querySelectorAll('[data-ai-operation]').forEach(button => { button.hidden = !data.read_operations.includes(button.dataset.aiOperation); });
                    root.querySelector('.sari-admin-ai-launcher-online')?.setAttribute('data-offline', String(!data.configured));
                    setStatus(data.configured ? (data.actions_enabled ? 'confirm_status' : 'read_status') : 'setup', !data.configured);
                } catch (error) { setStatus(error.message, true); }
            }
        });
        function closeLanguage() {
            languageOverlay.hidden = true;
            languageBackground.forEach(node => { node.inert = false; });
            languageTrigger.setAttribute('aria-expanded', 'false');
            languageTrigger.focus();
        }
        languageTrigger.addEventListener('click', () => {
            if (busy || translating) return;
            root.querySelectorAll('[name="saiLanguageChoice"]').forEach(radio => { radio.checked = radio.value === languageSelect.value; });
            languageOverlay.hidden = false;
            languageBackground.forEach(node => { node.inert = true; });
            languageTrigger.setAttribute('aria-expanded', 'true');
            languageDialog.querySelector('input:checked').focus();
        });
        root.querySelectorAll('[data-ai-language-cancel]').forEach(button => button.addEventListener('click', closeLanguage));
        languageOverlay.addEventListener('click', event => { if (event.target === languageOverlay) closeLanguage(); });
        root.querySelector('[data-ai-language-save]').addEventListener('click', () => {
            const value = languageDialog.querySelector('input:checked')?.value;
            closeLanguage();
            if (value && value !== languageSelect.value) {
                languageSelect.value = value;
                languageSelect.dispatchEvent(new Event('change'));
            }
        });
        languageSelect.addEventListener('change', () => {
            try { localStorage.setItem('sari:admin-ai:language', languageSelect.value); } catch (_) {}
            applyLanguage(); translateConversation();
        });
        root.querySelector('[data-ai-expand]')?.addEventListener('click', event => {
            const expanded = root.classList.toggle('is-expanded');
            event.currentTarget.setAttribute('aria-pressed', String(expanded));
            event.currentTarget.dataset.i18nAria = expanded ? 'collapse' : 'expand';
            event.currentTarget.dataset.i18nTitle = expanded ? 'collapse' : 'expand'; applyLanguage();
        });
        root.querySelector('[data-ai-close]').addEventListener('click', close);
        root.addEventListener('keydown', event => {
            if (!languageOverlay.hidden) {
                if (event.key === 'Escape') { event.preventDefault(); event.stopPropagation(); closeLanguage(); }
                if (event.key === 'Tab') {
                    const stops = [...languageDialog.querySelectorAll('button, input:checked')].filter(node => !node.disabled);
                    const first = stops[0], last = stops[stops.length - 1];
                    if (event.shiftKey && (document.activeElement === first || document.activeElement === languageDialog)) { event.preventDefault(); last.focus(); }
                    else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
                }
            } else if (event.key === 'Escape' && !panel.hidden) close();
        });
        form.addEventListener('submit', event => { event.preventDefault(); send(input.value); });
        input.addEventListener('keydown', event => { if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) { event.preventDefault(); send(input.value); } });
        root.addEventListener('click', event => {
            const prompt = event.target.closest('[data-ai-query]');
            if (prompt && root.contains(prompt)) send(t(prompt.dataset.aiQuery));
        });
        root.querySelector('[data-ai-alerts]').addEventListener('click', async event => {
            event.currentTarget.disabled = true;
            try { artifact(await request('/alerts')); } catch (error) { message('error', error.message); }
            finally { root.querySelector('[data-ai-alerts]').disabled = false; }
        });
        applyLanguage(); setStatus('ready');
        root.querySelector('[data-ai-clear]').addEventListener('click', async () => {
            if (busy || translating) return;
            ++translationGeneration;
            try {
                if (thread) await request('/threads/' + thread, null, 'DELETE');
                thread = null; feed.replaceChildren();
                if (welcomeTemplate) feed.append(welcomeTemplate.cloneNode(true));
                else message('assistant', t('welcome'));
                applyLanguage();
                setStatus('ready'); input.focus();
            } catch (error) { message('error', error.message); }
        });
    }
    document.addEventListener('livewire:navigated', init);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
