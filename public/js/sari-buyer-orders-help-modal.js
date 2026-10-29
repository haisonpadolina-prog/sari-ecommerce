/* My Orders Need Help: modal-only support and report submission. */
(() => {
    'use strict';
    let cleanup = null;
    function init() {
        if (cleanup) {cleanup();cleanup=null;}
        const root = document.getElementById('sariBuyerHelpModal');
        if (!root) return;
        const ac = new AbortController();
        const signal = ac.signal;
        const $ = (selector) => root.querySelector(selector);
        const dialog = $('.sari-help-dialog');
        const supportForm = $('#sariHelpSupportForm');
        const reportForm = $('#sariHelpReportForm');
        const feedback = $('#sariHelpFeedback');
        const success = $('#sariHelpSuccess');
        const successLink = $('#sariHelpOpenConversation');
        let mode = 'support';
        let opener = null;
        let submitting = false;
        let prevOverflow = '';
        // The buyer sidebar and header are outside #buyerMainContent.
        // Portal the fixed overlay directly to <body> so it covers both.
        const originalParent = root.parentNode;
        const originalNext = root.nextSibling;
        function restoreModal() {
            if (root.parentNode === originalParent) return;
            if (originalParent?.isConnected) {
                const before = originalNext?.parentNode === originalParent ? originalNext : null;
                originalParent.insertBefore(root, before);
            } else {
                root.remove();
            }
        }
        function close() {
            if (root.hidden || submitting) return;
            root.hidden = true;
            document.body.style.overflow = prevOverflow;
            restoreModal();
            if (opener?.isConnected) opener.focus();
        }
        function open(kind, trigger) {
            mode = kind;
            opener = trigger;
            feedback.hidden = true;
            feedback.textContent = '';
            success.hidden = true;
            supportForm.hidden = kind !== 'support';
            reportForm.hidden = kind !== 'report';
            $('#sariHelpModeIcon').textContent = kind === 'report' ? 'report_problem' : 'support_agent';
            $('#sariHelpTitle').textContent = kind === 'report' ? 'Report an Issue' : 'Chat with Support';
            $('#sariHelpDescription').textContent = kind === 'report'
                ? 'Submit a concern to SARI Admin without leaving My Orders.'
                : 'Send a private message to SARI Admin without leaving My Orders.';
            prevOverflow = document.body.style.overflow;
            // Covers the entire viewport, including persistent layout regions.
            if (root.parentNode !== document.body) document.body.appendChild(root);
            document.body.style.overflow = 'hidden';
            root.hidden = false;
            const field = kind === 'report' ? $('#sariHelpIssueSubject') : $('#sariHelpSupportText');
            field.focus();
        }
        function errorMessage(message) {
            feedback.textContent = message;
            feedback.hidden = false;
        }
        function makeUrl(template, uuid) {
            return String(template).replace('__CONVERSATION__',encodeURIComponent(uuid));
        }
        function conversationUrl(type, uuid) {
            const url = new URL(root.dataset.messagesUrl, window.location.href);
            url.searchParams.set('channel', type);
            url.searchParams.set('conversation', uuid);
            return url.toString();
        }
        async function request(endpoint, {method='GET', body=null}={}) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const headers = {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'};
            if (method !== 'GET') headers['X-CSRF-TOKEN']=csrf;
            const response = await fetch(endpoint,{method,body,headers,credentials:'same-origin',signal});
            const data = await response.json().catch(()=>({}));
            if (!response.ok) {
                const details = data?.errors ? Object.values(data.errors).flat()[0] : null;
                if (response.status === 419) throw new Error('Your session expired. Refresh this page, then try again.');
                throw new Error(details || data.message || `Request failed (${response.status}).`);
            }
            return data;
        }
        function done(title, message, type, uuid) {
            supportForm.hidden = true;
            reportForm.hidden = true;
            feedback.hidden = true;
            $('#sariHelpSuccessTitle').textContent = title;
            $('#sariHelpSuccessText').textContent = message;
            successLink.href = conversationUrl(type, uuid);
            success.hidden = false;
            successLink.focus();
        }
        async function submit(event, kind) {
            event.preventDefault();
            if (submitting) return;
            const form = kind === 'report' ? reportForm : supportForm;
            if (!form.reportValidity()) return;
            feedback.hidden = true;
            const button=form.querySelector('button[type="submit"]');
            submitting=true;
            button.disabled=true;
            try {
                if (kind === 'support') {
                    const body=$('#sariHelpSupportText').value.trim();
                    if (!body) throw new Error('Please enter a message.');
                    const opened = await request(root.dataset.supportUrl,{method:'POST'});
                    const uuid=String(opened.conversation?.uuid||'');
                    if (!uuid) throw new Error('Support conversation could not be opened.');
                    const payload = new FormData(); payload.set('body',body);
                    await request(makeUrl(root.dataset.sendTemplate,uuid),{method:'POST',body:payload});
                    supportForm.reset();
                    done('Message sent', 'Your message is saved in Buyer Messages and is available to SARI Admin.', 'support',uuid);
                } else {
                    const subject=$('#sariHelpIssueSubject').value.trim();
                    const description=$('#sariHelpIssueDescription').value.trim();
                    if (!subject || description.length < 10) throw new Error('Enter a subject and at least 10 characters describing the issue.');
                    const payload=new FormData();payload.set('subject',subject);payload.set('description',description);
                    const created = await request(root.dataset.complaintUrl,{method:'POST',body:payload});
                    const uuid=String(created.conversation?.uuid||'');
                    if (!uuid) throw new Error('The report was saved but no conversation link was returned. Open Buyer Messages to check your report.');
                    reportForm.reset();
                    done('Issue report submitted','SARI Admin received your complaint. A linked conversation is now available in Buyer Messages.','report',uuid);
                }
            } catch (err) {if (!signal.aborted) errorMessage(err.message||'Unable to submit. Please try again.');}
            finally {submitting=false;button.disabled=false;}
        }
        document.querySelectorAll('[data-sari-help-open]').forEach(trigger=>{
            trigger.addEventListener('click',event=>{event.preventDefault();open(trigger.dataset.sariHelpOpen,trigger);},{signal});
        });
        root.querySelectorAll('[data-sari-help-close]').forEach(btn=>btn.addEventListener('click',close,{signal}));
        document.addEventListener('keydown',event=>{
            if (root.hidden) return;
            if (event.key==='Escape') {event.preventDefault();close();return;}
            if (event.key!=='Tab') return;
            const elements=[...dialog.querySelectorAll('button:not([disabled]):not([hidden]),a[href]:not([hidden]),input:not([disabled]),textarea:not([disabled])')]
                .filter(el=>el.getClientRects().length>0 && !el.closest('[hidden]'));
            if (!elements.length) return;
            const first=elements[0],last=elements[elements.length-1];
            if (event.shiftKey && document.activeElement===first){event.preventDefault();last.focus();}
            else if(!event.shiftKey && document.activeElement===last){event.preventDefault();first.focus();}
        },{signal});
        supportForm.addEventListener('submit',event=>submit(event,'support'),{signal});
        reportForm.addEventListener('submit',event=>submit(event,'report'),{signal});
        cleanup=()=>{
            ac.abort();
            if(!root.hidden){document.body.style.overflow=prevOverflow;root.hidden=true;}
            restoreModal();
            cleanup=null;
        };
    }
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init,{once:true});else init();
    document.addEventListener('livewire:navigated',init);
    document.addEventListener('livewire:navigating',()=>cleanup?.());
    window.addEventListener('pagehide',()=>cleanup?.());
})();
