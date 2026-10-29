/* Buyer Admin Support: existing PlatformMessagingService APIs; no new messaging tables. */
(() => {
    'use strict';
    let cleanup = null;
    function initAdminInbox() {
        const nav = document.getElementById('sariBmAdminThreads');
        if (!nav || nav.dataset.loaded === '1') return;
        nav.dataset.loaded = '1';
        const endpoint = nav.dataset.inboxUrl;
        if (!endpoint) return;
        const fetchController = new AbortController();
        fetch(endpoint, {credentials:'same-origin', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}, signal:fetchController.signal})
            .then(response => {if (!response.ok) throw new Error('Inbox unavailable');return response.json();})
            .then(data => {
                if (!nav.isConnected) return;
                const threads = (Array.isArray(data.conversations) ? data.conversations : [])
                    .filter(item => ['admin_support','report_support'].includes(String(item.type)) && item.uuid);
                nav.replaceChildren();
                for (const thread of threads) {
                    const link = document.createElement('a');
                    link.className = 'sari-bm-seller';
                    const target = new URL(nav.dataset.messagesUrl, window.location.href);
                    target.searchParams.set('channel', thread.type === 'report_support' ? 'report' : 'support');
                    target.searchParams.set('conversation', String(thread.uuid));
                    link.href = target.toString();
                    link.dataset.storeSearch = String(thread.subject || 'admin support').toLowerCase();
                    const avatar = document.createElement('span');
                    avatar.className = 'sari-bm-avatar';
                    avatar.textContent = thread.type === 'report_support' ? '!' : 'A';
                    avatar.setAttribute('aria-hidden','true');
                    const copy = document.createElement('span');copy.className='sari-bm-seller-copy';
                    const title = document.createElement('strong');
                    title.textContent = thread.type === 'report_support' ? 'Admin · '+String(thread.subject || 'Issue report').slice(0,70) : 'SARI Admin Support';
                    const subtitle = document.createElement('small');
                    subtitle.textContent = thread.last_message?.body || (thread.type === 'report_support' ? 'Issue report conversation' : 'Support conversation');
                    copy.append(title,subtitle);
                    link.append(avatar,copy);
                    if (Number(thread.unread_count || 0) > 0) {
                        const badge = document.createElement('span');badge.className='sari-bm-unread';
                        badge.textContent = String(thread.unread_count);link.append(badge);
                    }
                    nav.append(link);
                }
                nav.hidden = threads.length === 0;
            }).catch(() => { nav.hidden = true; });
    }
    function init() {
        if (cleanup) { cleanup(); cleanup = null; }
        const root = document.getElementById('sariBuyerAdminSupport');
        if (!root) { initAdminInbox(); return; }
        const ctl = new AbortController();
        const signal = ctl.signal;
        let uuid = '';
        let polling = null;
        let revision = 0;
        const $ = (selector) => root.querySelector(selector);
        const messages = $('#sariAsMessages');
        const replyForm = $('#sariAsReplyForm');
        const replyText = $('#sariAsReplyText');
        const feedback = $('#sariAsFeedback');
        const history = $('#sariAsReportHistory');
        const complaintForm = $('#sariAsComplaintForm');
        const mode = root.dataset.mode;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || complaintForm?.querySelector('input[name="_token"]')?.value || '';
        const url = (template, key, value) => String(template || '').replace(key, encodeURIComponent(String(value)));
        function notify(message, error = false) {
            feedback.textContent = message || '';
            feedback.classList.toggle('is-error', error);
            feedback.hidden = !message;
        }
        async function call(endpoint, options={}) {
            const response = await fetch(endpoint, {credentials:'same-origin', signal, ...options, headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest', ...(options.method && options.method !== 'GET' ? {'X-CSRF-TOKEN':csrf} : {}), ...(options.headers || {})}});
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const validation = data.errors ? Object.values(data.errors).flat()[0] : null;
                throw new Error(validation || data.message || `Request failed (${response.status})`);
            }
            return data;
        }
        function empty(message) {
            messages.replaceChildren();
            const hint = document.createElement('p');
            hint.className = 'sari-as-hint';
            hint.textContent = message;
            messages.append(hint);
        }
        function render(entries) {
            const wasAtBottom = messages.scrollHeight - messages.scrollTop - messages.clientHeight < 85;
            messages.replaceChildren();
            if (!entries.length) { empty('Start the conversation by sending your message.'); return; }
            for (const entry of entries) {
                const bubble = document.createElement('div');
                const mine = ['buyer','social_buyer'].includes(String(entry.sender_role).toLowerCase());
                bubble.className = 'sari-as-bubble' + (mine ? ' is-mine' : '');
                bubble.textContent = entry.body || (entry.attachment ? 'Attachment sent' : 'Message');
                if (entry.attachment?.url) {
                    const attachment = document.createElement('a');
                    attachment.href = entry.attachment.url;
                    attachment.textContent = ' View attachment';
                    attachment.target = '_blank';
                    attachment.rel = 'noopener noreferrer';
                    bubble.append(attachment);
                }
                const meta = document.createElement('small');
                const date = entry.created_at ? new Date(entry.created_at).toLocaleString() : '';
                meta.textContent = `${mine ? 'You' : (entry.sender || 'SARI Admin')}${date ? ' · '+date : ''}`;
                bubble.append(meta);
                messages.append(bubble);
            }
            if (wasAtBottom) messages.scrollTop = messages.scrollHeight;
        }
        async function load(conversationUuid, quiet=false) {
            const current = ++revision;
            try {
                const data = await call(url(root.dataset.detailsTemplate, '__CONVERSATION__', conversationUuid));
                if (signal.aborted || current !== revision || uuid !== conversationUuid) return;
                $('#sariAsThreadTitle').textContent = data.conversation?.subject || 'SARI Admin Support';
                render(data.messages || []);
                replyForm.hidden = false;
                if (!quiet) notify('');
            } catch (error) { if (!signal.aborted && !quiet) notify(error.message, true); }
        }
        async function openReport(id) {
            const data = await call(url(root.dataset.openReportTemplate,'__COMPLAINT__',id), {method:'POST'});
            uuid = String(data.conversation?.uuid || '');
            if (!uuid) throw new Error('The complaint conversation could not be opened.');
            complaintForm?.setAttribute('hidden','');
            await load(uuid);
            highlightHistory(id);
        }
        function highlightHistory(id) {
            history?.querySelectorAll('button').forEach(button => button.classList.toggle('is-selected', button.dataset.complaint === String(id)));
        }
        async function loadReports() {
            const data = await call(root.dataset.list);
            history.replaceChildren();
            const reports = Array.isArray(data.reports) ? data.reports : [];
            if (!reports.length) {
                const p = document.createElement('p');p.className = 'sari-as-subtitle';p.textContent = 'No reports submitted yet.';history.append(p);return;
            }
            for (const report of reports) {
                const button = document.createElement('button');button.type='button';button.dataset.complaint=String(report.id);
                button.textContent=`#${report.id} · ${report.subject || 'Issue'}`;
                const state=document.createElement('small');state.textContent=report.status || 'Open';button.append(state);
                button.addEventListener('click', () => {notify(''); openReport(report.id).catch(err => notify(err.message,true));}, {signal});
                history.append(button);
            }
        }
        replyForm.addEventListener('submit', async event => {
            event.preventDefault();const body=replyText.value.trim();if(!uuid || !body)return;
            const button=replyForm.querySelector('button[type="submit"]');button.disabled=true;
            try {const data=new FormData();data.append('body',body);await call(url(root.dataset.sendTemplate,'__CONVERSATION__',uuid),{method:'POST',body:data});replyText.value='';await load(uuid);messages.scrollTop=messages.scrollHeight;}
            catch(error){notify(error.message,true);}finally{button.disabled=false;}
        },{signal});
        if (complaintForm) complaintForm.addEventListener('submit',() => {const button=complaintForm.querySelector('button[type="submit"]');if(button)button.disabled=true;},{signal});
        (async () => {
            try {
                await loadReports();
                const submitted = Number(root.dataset.newReport || 0);
                const selectedConversation = String(root.dataset.conversation || '').trim();
                if (submitted > 0) {await openReport(submitted);}
                else if (selectedConversation) {
                    uuid = selectedConversation;
                    await load(uuid);
                }
                else if (mode === 'support') {
                    const data=await call(root.dataset.openSupport,{method:'POST'});
                    uuid=String(data.conversation?.uuid || '');
                    if(!uuid)throw new Error('Support conversation unavailable.');
                    await load(uuid);
                } else {empty('Submit your issue above, or open a previous report to continue chatting with Admin.');}
            } catch(error){if(!signal.aborted){notify(error.message,true);empty('Could not load Admin Support. Check your connection and try again.');}}
        })();
        polling=setInterval(()=>{if(uuid&&!signal.aborted&&document.visibilityState==='visible')load(uuid,true);},15000);
        cleanup=()=>{ctl.abort();clearInterval(polling);cleanup=null;};
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',init,{once:true});
    else init();
    document.addEventListener('livewire:navigated',init);
    document.addEventListener('livewire:navigating',()=>{if(cleanup)cleanup();});
    window.addEventListener('pagehide',()=>{if(cleanup)cleanup();});
})();
