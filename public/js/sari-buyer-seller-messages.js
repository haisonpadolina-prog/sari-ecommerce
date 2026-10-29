(function () {
    function start() {
        const root = document.getElementById('sariBuyerSellerChat');
        if (!root || root.dataset.booted === '1') return;
        root.dataset.booted = '1';

        const threadEl = document.getElementById('sariBuyerSellerMessageList');
        const form = document.getElementById('sariBuyerSellerSendForm');
        const input = document.getElementById('sariBmMessageInput');
        const sendButton = document.getElementById('sariBmSend');
        const imageInput = document.getElementById('sariBmImageInput');
        const fileInput = document.getElementById('sariBmFileInput');
        const attachmentPreview = document.getElementById('sariBmAttachmentPreview');
        const feedback = document.getElementById('sariBmFeedback');
        const status = document.getElementById('sariBmStatus');
        const search = document.getElementById('sariBuyerSellerSearch');
        const url = root.dataset.threadUrl;
        const confirmationUrl = root.dataset.confirmTemplate;
        const chat = window.SariOrderCard;
        // Use the already-rendered, escaped seller avatar markup for live message rows.
        const sellerAvatarMarkup = root.querySelector('.sari-bm-header-copy .sari-bm-avatar')?.innerHTML || 'S';
        const controller = new AbortController();
        const signal = controller.signal;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || form?.querySelector('input[name="_token"]')?.value;
        let lastIds = '';
        let loading = false;
        let sending = false;
        let realtime = null;
        let pollTimer = null;
        let reconcileTimer = null;

        const setError = (message = '') => {
            if (!feedback) return;
            feedback.textContent = message;
            feedback.hidden = !message;
        };
        const escapeHtml = chat?.escapeHtml || (value => String(value ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'));
        const nearBottom = () => !threadEl || (threadEl.scrollHeight - threadEl.scrollTop - threadEl.clientHeight < 150);
        let pendingAttachment = null;
        const formatBytes = value => { const size = Number(value) || 0; if (size < 1024) return size + ' B'; if (size < 1048576) return (size / 1024).toFixed(1).replace(/\.0$/,'') + ' KB'; return (size / 1048576).toFixed(1).replace(/\.0$/,'') + ' MB'; };
        function clearPendingAttachment(){ pendingAttachment = null; if (imageInput) imageInput.value=''; if (fileInput) fileInput.value=''; if (attachmentPreview){ attachmentPreview.innerHTML=''; attachmentPreview.hidden=true; }}
        function renderPendingAttachment(){ if(!attachmentPreview) return; if(!pendingAttachment){ attachmentPreview.innerHTML=''; attachmentPreview.hidden=true; return; } const isImage=/^image\//.test(pendingAttachment.type||''); const thumb=isImage?`<span class="sari-bm-attachment-thumb"><img src="${escapeHtml(URL.createObjectURL(pendingAttachment))}" alt=""></span>`:`<span class="sari-bm-attachment-thumb"><svg viewBox="0 0 24 24"><path d="M14 3H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9l-4-6Z"></path><path d="M14 3v6h4"></path></svg></span>`; attachmentPreview.hidden=false; attachmentPreview.innerHTML=`<div class="sari-bm-attachment-chip">${thumb}<span class="sari-bm-attachment-meta"><strong>${escapeHtml(pendingAttachment.name||'Attachment')}</strong><span>${escapeHtml(formatBytes(pendingAttachment.size))}</span></span><button type="button" class="sari-bm-attachment-remove" aria-label="Remove attachment"><svg viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></svg></button></div>`; attachmentPreview.querySelector('.sari-bm-attachment-remove')?.addEventListener('click', clearPendingAttachment,{once:true}); }
        function attachmentHtml(attachment){ if(!attachment?.url) return ''; const name=escapeHtml(attachment.name||'Attachment'); if(attachment.is_image){ return `<div class="sari-bm-attachment-message"><a class="sari-bm-attachment-image" href="${escapeHtml(attachment.url)}" target="_blank" rel="noopener noreferrer"><img src="${escapeHtml(attachment.url)}" alt="${name}"></a></div>`; } const ext=escapeHtml((attachment.extension||'FILE').toUpperCase()); return `<div class="sari-bm-attachment-message"><a class="sari-bm-attachment-link" href="${escapeHtml(attachment.url)}" target="_blank" rel="noopener noreferrer"><span class="sari-bm-attachment-file-icon"><svg viewBox="0 0 24 24"><path d="M14 3H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9l-4-6Z"></path><path d="M14 3v6h4"></path></svg></span><span class="sari-bm-attachment-file-copy"><strong>${name}</strong><span>${ext}</span></span></a></div>`; }

        function messageHtml(msg) {
            const mine = msg.sender_role === 'buyer';
            const time = msg.created_at ? new Date(msg.created_at) : null;
            const dateLabel = time && !Number.isNaN(time.getTime()) ? time.toLocaleString('en-PH', {month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true}) : '';
            return `<div class="sari-bm-message ${mine ? 'is-mine' : ''}" data-message-id="${Number(msg.id)}">
                ${mine ? '' : `<span class="sari-bm-message-avatar" aria-hidden="true">${sellerAvatarMarkup}</span>`}
                <div class="sari-bm-message-content">
                    <span class="sari-bm-sender">${mine ? 'You' : (msg.message_type === 'order_confirmation' ? 'Automatic seller confirmation' : 'Seller')}</span>
                    ${attachmentHtml(msg.attachment)}
                    ${msg.body ? `<p class="sari-bm-bubble">${escapeHtml(msg.body)}</p>` : ''}
                    ${chat?.render(msg.order_confirmation, true, confirmationUrl) || ''}
                    <time class="sari-bm-time">${escapeHtml(dateLabel)}</time>
                </div>
            </div>`;
        }
        async function json(response) {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const detail = Object.values(data.errors || {})?.[0]?.[0];
                throw new Error(detail || data.message || 'Request failed. Please try again.');
            }
            return data;
        }
        function syncUnread(total) {
            document.dispatchEvent(new CustomEvent('sari:buyer-chat-unread', {detail:{total: Number(total) || 0}}));
            const active = root.querySelector('.sari-bm-seller.is-active .sari-bm-unread');
            active?.remove();
        }
        async function refresh({initial = false} = {}) {
            if (!url || loading || signal.aborted || (!initial && document.hidden)) return;
            loading = true;
            try {
                const response = await fetch(url, {credentials:'same-origin', cache:'no-store', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}, signal});
                const data = await json(response);
                const messages = Array.isArray(data.messages) ? data.messages : [];
                // Re-render when confirmation status changes, even if IDs did not.
                const fingerprint = JSON.stringify(messages.map(msg => [msg.id,msg.body,msg.order_confirmation?.confirmed]));
                if (fingerprint !== lastIds && threadEl) {
                    const stick = initial || nearBottom();
                    threadEl.innerHTML = messages.length ? messages.map(messageHtml).join('') :
                        '<div class="sari-bm-empty" id="sariBmEmpty"><span>✉</span><h3>Start your conversation</h3><p>Message the seller about your order.</p></div>';
                    lastIds = fingerprint;
                    if (stick) threadEl.scrollTop = threadEl.scrollHeight;
                }
                syncUnread(data.unread_total);
                setError('');
                if (status) status.textContent = realtime ? 'Live chat' : 'Auto-updating';
                status?.classList.add('is-live');
            } catch (error) {
                if (error?.name !== 'AbortError') {
                    setError(error.message || 'Connection unavailable. Retrying…');
                    if (status) status.textContent = 'Reconnecting…';
                }
            } finally {
                loading = false;
            }
        }
        function startPolling() {
            if (pollTimer) return;
            pollTimer = setInterval(() => refresh(), 3000);
            if (status) status.textContent = 'Auto-updating';
        }
        if (url) {
            refresh({initial:true});
            const channel = root.dataset.privateChannel;
            if (window.Echo && channel) {
                try {
                    realtime = window.Echo.private(channel)
                        .listen('.buyer-seller.message', event => {
                            if (Number(event?.seller_id) === Number(root.dataset.sellerId)) refresh();
                            document.dispatchEvent(new Event('sari:refresh-chat-unread'));
                        })
                        .error(() => startPolling());
                } catch (_) { realtime = null; startPolling(); }
            } else { startPolling(); }
            reconcileTimer = setInterval(() => refresh(), 15000);
        }
        form?.addEventListener('submit', async event => {
            event.preventDefault();
            if (sending) return;
            const body = input?.value.trim() || '';
            if (!body && !pendingAttachment) { input?.focus(); return; }
            sending = true; sendButton.disabled = true; setError('');
            try {
                const payload = new FormData();
                payload.append('_token', csrf);
                payload.append('body', body);
                if (pendingAttachment) payload.append('attachment', pendingAttachment, pendingAttachment.name);
                const response = await fetch(root.dataset.sendUrl, {
                    method:'POST', credentials:'same-origin', headers:{
                        'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':csrf
                    }, body:payload, signal
                });
                await json(response);
                input.value = ''; input.style.height = '';
                clearPendingAttachment();
                await refresh({initial:true});
                window.SariBuyerToast?.success('Message sent.');
                input.focus();
            } catch (error) {
                if (error?.name !== 'AbortError') {
                    setError(error.message);
                    window.SariBuyerToast?.error(error.message || 'Unable to send message.');
                }
            }
            finally { sending = false; sendButton.disabled = false; }
        }, {signal});
        input?.addEventListener('keydown', event => {
            if (event.isComposing) return;
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault(); form?.requestSubmit();
            }
        }, {signal});
        input?.addEventListener('input', () => {
            input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 115) + 'px';
        }, {signal});
        const acceptAttachment = file => { if (!file) return; pendingAttachment = file; renderPendingAttachment(); setError(''); };
        imageInput?.addEventListener('change', () => acceptAttachment(imageInput.files?.[0] || null), {signal});
        fileInput?.addEventListener('change', () => acceptAttachment(fileInput.files?.[0] || null), {signal});
        threadEl?.addEventListener('submit', async event => {
            const confirmation = event.target.closest('form[data-confirm-address-form]');
            if (!confirmation) return;
            event.preventDefault();
            const button = confirmation.querySelector('button');
            if (button?.disabled) return;
            button.disabled = true; setError('');
            try {
                const response = await fetch(confirmation.action, {method:'POST', credentials:'same-origin', headers:{
                    'Accept':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'
                },signal});
                await json(response);
                await refresh({initial:true});
                window.SariBuyerToast?.success('Delivery details confirmed.');
            } catch (error) {
                if (error?.name !== 'AbortError') {
                    setError(error.message);
                    window.SariBuyerToast?.error(error.message || 'Unable to confirm delivery details.');
                }
                button.disabled = false;
            }
        }, {signal});
        search?.addEventListener('input', () => {
            const q = search.value.trim().toLowerCase();
            root.querySelectorAll('[data-store-search]').forEach(a => {
                a.hidden = q !== '' && !a.dataset.storeSearch.includes(q);
            });
        }, {signal});
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) refresh();
        }, {signal});
        window.addEventListener('online', () => refresh(), {signal});

        const cleanup = () => {
            controller.abort();
            clearInterval(pollTimer); clearInterval(reconcileTimer);
            if (realtime && window.Echo) {
                try { window.Echo.leave(root.dataset.privateChannel); } catch (_) {}
            }
            root.dataset.booted = '0';
        };
        document.addEventListener('livewire:navigating', cleanup, {once:true,signal});
        window.addEventListener('beforeunload', cleanup, {once:true,signal});
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, {once:true});
    } else { start(); }
    document.addEventListener('livewire:navigated', start);
})();
