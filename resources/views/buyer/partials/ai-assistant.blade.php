<div
    id="sariBuyerAi"
    class="sari-buyer-ai"
    data-buyer-id="{{ session('buyer_account_id') ? 'account-'.(int) session('buyer_account_id') : 'social-'.(int) session('buyer_social_account_id') }}"
    data-endpoint="{{ \Illuminate\Support\Facades\Route::has('buyer.ai-assistant.message') ? route('buyer.ai-assistant.message') : '' }}"
>
    <button
        id="sariBuyerAiLauncher"
        type="button"
        class="sari-buyer-ai-launcher"
        aria-label="Open SARI Buyer AI"
        aria-expanded="false"
        aria-controls="sariBuyerAiPanel"
    >
        <span class="sari-buyer-ai-launcher-aura" aria-hidden="true"></span>

        <span class="sari-buyer-ai-launcher-shell">
            <img
                src="{{ asset('images/sari-seller-ai-assistant.png') }}"
                alt=""
                class="sari-buyer-ai-launcher-image"
                draggable="false"
            >
        </span>

        <span class="sari-buyer-ai-launcher-online" aria-hidden="true"></span>

        <span class="sari-buyer-ai-launcher-callout" aria-hidden="true">
            <strong>Ask SARI AI</strong>
            <small>Buyer shopping assistant</small>
        </span>
    </button>

    <section
        id="sariBuyerAiPanel"
        class="sari-buyer-ai-panel"
        aria-label="SARI Buyer AI"
        aria-hidden="true"
    >
        <header class="sari-buyer-ai-header">
            <div class="sari-buyer-ai-identity">
                <div class="sari-buyer-ai-avatar">
                    <img
                        src="{{ asset('images/sari-seller-ai-assistant.png') }}"
                        alt=""
                        draggable="false"
                    >
                </div>

                <div class="sari-buyer-ai-identity-copy">
                    <strong>SARI</strong>

                    <small>
                        <span class="sari-buyer-ai-status-dot" aria-hidden="true"></span>
                        {{ \Illuminate\Support\Facades\Route::has('buyer.ai-assistant.message') ? 'Online · Buyer shopping assistant' : 'Setup required · Chat is unavailable' }}
                    </small>
                </div>
            </div>

            <div class="sari-buyer-ai-header-actions">
                <button
                    id="sariBuyerAiClear"
                    type="button"
                    class="sari-buyer-ai-icon-button"
                    aria-label="Start a new AI conversation"
                    title="New conversation"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                </button>

                <button
                    id="sariBuyerAiClose"
                    type="button"
                    class="sari-buyer-ai-icon-button"
                    aria-label="Minimize SARI Buyer AI"
                    title="Minimize"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M6 12h12"></path>
                    </svg>
                </button>
            </div>
        </header>

        <div
            id="sariBuyerAiMessages"
            class="sari-buyer-ai-messages"
            role="log"
            aria-live="polite"
            aria-relevant="additions"
        >
            <section id="sariBuyerAiWelcome" class="sari-buyer-ai-welcome">
                <div class="sari-buyer-ai-welcome-visual">
                    <span class="sari-buyer-ai-welcome-halo" aria-hidden="true"></span>
                    <img
                        src="{{ asset('images/sari-seller-ai-assistant.png') }}"
                        alt=""
                        draggable="false"
                    >
                </div>

                <div class="sari-buyer-ai-welcome-copy">
                    <span class="sari-buyer-ai-welcome-kicker">YOUR SHOPPING COPILOT</span>
                    <h2>How can I help you shop?</h2>
                    <p>
                        Ask about outfit ideas, colors for your skin tone, real product photos and sizes,
                        your cart, orders, notifications, and Buyer pages.
                    </p>
                </div>

                <div id="sariBuyerAiWelcomeActions" class="sari-buyer-ai-welcome-actions"></div>
            </section>
        </div>

        <div class="sari-buyer-ai-composer-wrap">
            <div id="sariBuyerAiSuggestions" class="sari-buyer-ai-suggestions"></div>

            <form id="sariBuyerAiForm" class="sari-buyer-ai-composer">
                @csrf

                <textarea
                    id="sariBuyerAiInput"
                    rows="1"
                    maxlength="1200"
                    placeholder="Ask about products, outfits, or orders..."
                    aria-label="Ask SARI Buyer AI"
                ></textarea>

                <button
                    id="sariBuyerAiSend"
                    type="submit"
                    class="sari-buyer-ai-send"
                    aria-label="Send message"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="m4 4 17 8-17 8 3-8-3-8Z"></path>
                        <path d="M7 12h14"></path>
                    </svg>
                </button>
            </form>

        </div>

        <p id="sariBuyerAiError" class="sari-buyer-ai-error" role="alert"></p>

    </section>
</div>


<style id="sariBuyerAiStyles">
/* SARI Buyer AI: one authoritative stylesheet. All scrolling stays inside messages. */
.sari-buyer-ai {
    --sari-ai-gold:#d59617;
    --sari-ai-ink:#233044;
    --sari-ai-muted:#778397;
    --sari-ai-border:#e6eaf0;
    position:fixed;
    right:24px;
    bottom:22px;
    z-index:8500;
    font-family:Poppins,ui-sans-serif,system-ui,sans-serif;
}
.sari-buyer-ai *, .sari-buyer-ai *::before, .sari-buyer-ai *::after {box-sizing:border-box;}
.sari-buyer-ai button,.sari-buyer-ai textarea {font-family:inherit;}

/* Floating seller robot artwork reused for the buyer. */
.sari-buyer-ai-launcher {position:relative;display:grid;place-items:center;width:72px;height:72px;border:0;padding:0;background:transparent;border-radius:50%;cursor:pointer;isolation:isolate;transition:transform .18s ease;}
.sari-buyer-ai-launcher:hover {transform:translateY(-2px);}
.sari-buyer-ai-launcher:focus-visible {outline:3px solid rgba(213,150,23,.25);outline-offset:3px;}
.sari-buyer-ai-launcher-shell {display:grid;place-items:center;width:72px;height:72px;}
.sari-buyer-ai-launcher-image {display:block;width:72px;height:72px;max-width:72px;object-fit:contain;user-select:none;}
.sari-buyer-ai-launcher-aura {display:none;}
.sari-buyer-ai-launcher-online {position:absolute;right:1px;bottom:6px;width:14px;height:14px;border:3px solid #fff;border-radius:50%;background:#35a568;}
.sari-buyer-ai-launcher-callout {position:absolute;right:84px;top:50%;display:grid;gap:2px;min-width:170px;padding:10px 12px;border:1px solid var(--sari-ai-border);border-radius:12px;background:#fff;text-align:left;box-shadow:0 10px 24px rgba(16,24,40,.1);opacity:0;pointer-events:none;transform:translate(6px,-50%);transition:opacity .15s ease,transform .15s ease;}
.sari-buyer-ai-launcher-callout strong {font-size:12px;font-weight:650;color:var(--sari-ai-ink);}
.sari-buyer-ai-launcher-callout small {font-size:10px;color:var(--sari-ai-muted);line-height:1.4;}
.sari-buyer-ai-launcher:hover .sari-buyer-ai-launcher-callout {opacity:1;transform:translate(0,-50%);}
.sari-buyer-ai.is-open .sari-buyer-ai-launcher {opacity:0;pointer-events:none;}

/* Four-row chat grid: header, scrollable messages, composer, optional error. */
.sari-buyer-ai-panel {
    position:absolute;
    right:0;
    bottom:86px;
    display:grid;
    grid-template-rows:auto minmax(0,1fr) auto auto;
    width:min(412px,calc(100vw - 32px));
    height:min(665px,calc(100dvh - 112px));
    min-height:0;
    overflow:hidden;
    border:1px solid #dfe4ea;
    border-radius:18px;
    background:#fff;
    box-shadow:0 24px 60px rgba(16,24,40,.16),0 4px 12px rgba(16,24,40,.05);
    opacity:0;
    visibility:hidden;
    pointer-events:none;
    transform:translateY(10px) scale(.99);
    transform-origin:bottom right;
    transition:opacity .16s ease,transform .16s ease,visibility .16s;
}
.sari-buyer-ai.is-open .sari-buyer-ai-panel {visibility:visible;opacity:1;pointer-events:auto;transform:translateY(0) scale(1);}
.sari-buyer-ai-panel > * {min-width:0;max-width:100%;}

/* Always-visible chatbot header. */
.sari-buyer-ai-header {display:flex;align-items:center;justify-content:space-between;gap:12px;min-height:70px;padding:10px 14px;background:#fff;border-bottom:1px solid var(--sari-ai-border);}
.sari-buyer-ai-identity {display:flex;align-items:center;gap:10px;min-width:0;flex:1 1 auto;}
.sari-buyer-ai-avatar {display:grid;place-items:center;flex:0 0 42px;width:42px;height:42px;}
.sari-buyer-ai-avatar img {display:block;width:42px;height:42px;object-fit:contain;}
.sari-buyer-ai-avatar > span {display:none;}
.sari-buyer-ai-identity-copy {min-width:0;flex:1;}
.sari-buyer-ai-identity-copy > strong {display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:15px;font-weight:650;line-height:1.35;color:var(--sari-ai-ink);}
.sari-buyer-ai-identity-copy > small {display:flex;align-items:center;gap:6px;min-width:0;margin-top:3px;color:var(--sari-ai-muted);font-size:10.5px;font-weight:450;line-height:1.4;}
.sari-buyer-ai-status-dot {flex:0 0 7px;width:7px;height:7px;border-radius:50%;background:#35a568;}
.sari-buyer-ai-name-row,.sari-buyer-ai-beta {display:none;}
.sari-buyer-ai-header-actions {display:flex;flex:0 0 auto;align-items:center;gap:4px;}
.sari-buyer-ai-icon-button {display:grid;place-items:center;width:33px;height:33px;border:1px solid transparent;border-radius:9px;background:transparent;color:#667085;cursor:pointer;}
.sari-buyer-ai-icon-button:hover,.sari-buyer-ai-icon-button:focus-visible {border-color:var(--sari-ai-border);background:#f8fafc;color:#344054;}
.sari-buyer-ai-icon-button svg {width:16px;height:16px;}

/* The ONLY scrollable panel region. Welcome content starts at the top. */
.sari-buyer-ai-messages {min-width:0;min-height:0;max-width:100%;height:100%;overflow-x:hidden;overflow-y:auto;overscroll-behavior:contain;scrollbar-width:none;scroll-padding-bottom:14px;padding:14px 13px 16px;background:#f8fafb;}
.sari-buyer-ai-messages::-webkit-scrollbar {display:none;width:0;height:0;}
.sari-buyer-ai-welcome {display:grid;justify-items:center;align-content:start;gap:0;min-height:0;padding:9px 6px 14px;text-align:center;}
.sari-buyer-ai-welcome-visual {position:relative;display:grid;place-items:center;width:76px;height:76px;border-radius:50%;}
.sari-buyer-ai-welcome-visual img {display:block;width:76px;height:76px;object-fit:contain;}
.sari-buyer-ai-welcome-halo {display:none;}
.sari-buyer-ai-welcome-copy {width:100%;max-width:328px;margin-top:10px;}
.sari-buyer-ai-welcome-kicker {display:block;color:#b97805;font-size:10px;font-weight:650;line-height:1.35;letter-spacing:.08em;}
.sari-buyer-ai-welcome-copy h2 {margin:6px 0 0;color:#1d2939;font-size:19px;font-weight:650;line-height:1.35;letter-spacing:-.02em;}
.sari-buyer-ai-welcome-copy p {margin:7px 0 0;color:#758196;font-size:12px;font-weight:400;line-height:1.55;}
.sari-buyer-ai-welcome-actions {display:grid;gap:7px;width:100%;max-width:340px;margin-top:16px;}
.sari-buyer-ai-welcome-actions button {display:flex;align-items:center;justify-content:space-between;gap:10px;min-height:42px;width:100%;padding:9px 12px;border:1px solid #e1e7ed;border-radius:11px;background:#fff;color:#405068;font-size:11.5px;font-weight:550;line-height:1.45;text-align:left;cursor:pointer;box-shadow:0 2px 8px rgba(16,24,40,.025);}
.sari-buyer-ai-welcome-actions button:hover,.sari-buyer-ai-welcome-actions button:focus-visible {border-color:#e6ce99;background:#fffaf0;color:#926006;}
.sari-buyer-ai-welcome-actions button span {flex:0 0 auto;font-size:14px;color:#c18a17;}

/* Conversation bubbles grow with their messages but never overflow sideways. */
.sari-buyer-ai-response {min-width:0;max-width:100%;margin-bottom:12px;}
.sari-buyer-ai-message {display:flex;width:100%;min-width:0;margin-top:10px;}
.sari-buyer-ai-message:first-child {margin-top:0;}
.sari-buyer-ai-message--assistant {display:grid;grid-template-columns:29px minmax(0,1fr);align-items:end;gap:8px;}
.sari-buyer-ai-message--user {justify-content:flex-end;}
.sari-buyer-ai-mini-avatar {display:grid;place-items:center;width:29px;height:29px;flex:0 0 29px;}
.sari-buyer-ai-mini-avatar img {width:29px;height:29px;object-fit:contain;}
.sari-buyer-ai-bubble {min-width:0;max-width:100%;padding:10px 12px;border-radius:12px;font-size:13.5px;font-weight:400;line-height:1.57;white-space:normal;overflow-wrap:anywhere;word-break:normal;}
.sari-buyer-ai-message--assistant > .sari-buyer-ai-bubble {width:100%;min-width:0;border:1px solid #e5eaf0;border-bottom-left-radius:5px;background:#fff;color:#46546a;}
.sari-buyer-ai-message--user > .sari-buyer-ai-bubble {width:fit-content;max-width:82%;margin-left:auto;border-bottom-right-radius:5px;background:var(--sari-ai-gold);color:#fff;}
.sari-buyer-ai-bubble p {margin:0;}
.sari-buyer-ai-bubble p + p {margin-top:9px;}
.sari-buyer-ai-bubble strong {font-weight:600;color:inherit;}
.sari-buyer-ai-message-label {display:block;margin:0 0 4px 37px;color:#8691a2;font-size:10px;font-weight:650;line-height:1.4;letter-spacing:.05em;text-transform:uppercase;}
.sari-buyer-ai-typing-wrap {display:flex;align-items:flex-end;gap:8px;margin-top:10px;}
.sari-buyer-ai-typing-content {display:grid;gap:5px;min-width:0;}
.sari-buyer-ai-typing-label {font-size:10px;font-weight:550;color:#8691a2;}
.sari-buyer-ai-typing {display:flex;align-items:center;gap:5px;width:max-content;min-height:35px;padding:0 12px;border:1px solid #e5eaf0;border-radius:12px 12px 12px 5px;background:white;}
.sari-buyer-ai-typing span {width:6px;height:6px;border-radius:50%;background:#98a2b3;animation:sariBuyerAiTyping 1s infinite;}
.sari-buyer-ai-typing span:nth-child(2) {animation-delay:.12s;}
.sari-buyer-ai-typing span:nth-child(3) {animation-delay:.24s;}
@keyframes sariBuyerAiTyping {0%,60%,100% {transform:translateY(0);opacity:.35;} 30% {transform:translateY(-2px);opacity:1;}}

/* Compact composer; no visible scrollbars. Long messages still scroll within the input. */
.sari-buyer-ai-composer-wrap {min-width:0;border-top:1px solid var(--sari-ai-border);background:#fff;}
.sari-buyer-ai-suggestions {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px;padding:9px 12px 3px;}
.sari-buyer-ai-suggestions:empty {display:none;}
.sari-buyer-ai-suggestions button {min-width:0;min-height:36px;overflow:hidden;border:1px solid #e4e8ee;border-radius:9px;background:#fff;padding:7px 9px;color:#5a697d;font-size:10.5px;font-weight:550;line-height:1.3;text-align:left;text-overflow:ellipsis;cursor:pointer;}
.sari-buyer-ai-suggestions button:hover,.sari-buyer-ai-suggestions button:focus-visible {border-color:#e2ca97;background:#fffaf0;color:#996908;}
.sari-buyer-ai-composer {display:grid;grid-template-columns:minmax(0,1fr) 44px;align-items:center;gap:8px;margin:0;padding:8px 12px;}
.sari-buyer-ai-composer textarea {display:block;width:100%;min-width:0;min-height:48px;max-height:104px;margin:0;padding:12px 12px;resize:none;overflow-x:hidden;overflow-y:auto;scrollbar-width:none;border:1px solid #dce2e9;border-radius:11px;background:white;color:#344054;font-size:13.5px;line-height:1.48;font-weight:400;outline:none;box-shadow:none;}
.sari-buyer-ai-composer textarea::-webkit-scrollbar {display:none;width:0;height:0;}
.sari-buyer-ai-composer textarea::placeholder {color:#929eaf;opacity:1;}
.sari-buyer-ai-composer textarea:focus {border-color:var(--sari-ai-gold);box-shadow:0 0 0 3px rgba(213,150,23,.09);}
.sari-buyer-ai-send {display:grid;place-items:center;width:44px;height:44px;min-width:44px;padding:0;border:1px solid var(--sari-ai-gold);border-radius:11px;background:var(--sari-ai-gold);color:#fff;cursor:pointer;}
.sari-buyer-ai-send:hover,.sari-buyer-ai-send:focus-visible {background:#b97f0c;border-color:#b97f0c;}
.sari-buyer-ai-send:disabled {opacity:.6;cursor:wait;}
.sari-buyer-ai-send svg {width:18px;height:18px;stroke:#fff;}
.sari-buyer-ai-error {display:none;min-width:0;margin:0;padding:9px 12px;border-top:1px solid #f2d7d7;background:#fff8f8;color:#a65353;font-size:11.5px;line-height:1.5;}
.sari-buyer-ai-error:not(:empty) {display:block;}

/* Real buyer product recommendation cards. */
.sari-buyer-ai-product-list {display:grid;gap:10px;min-width:0;margin:10px 0 5px;}
.sari-buyer-ai-product {display:grid;grid-template-columns:76px minmax(0,1fr);gap:11px;min-width:0;padding:10px;border:1px solid #eee4d4;border-radius:12px;background:#fff;}
.sari-buyer-ai-product-photo {display:block;width:76px;height:94px;border-radius:9px;object-fit:cover;background:#f3f1ed;}
.sari-buyer-ai-product-info {display:flex;min-width:0;flex-direction:column;gap:4px;}
.sari-buyer-ai-product-name {color:#282e38;font-size:13.5px;line-height:1.38;font-weight:600;overflow-wrap:anywhere;}
.sari-buyer-ai-product-meta {font-size:11.5px;line-height:1.45;color:#778397;overflow-wrap:anywhere;}
.sari-buyer-ai-product-price {color:#ac750b;font-size:15px;line-height:1.4;font-weight:650;}
.sari-buyer-ai-product-variants {display:flex;flex-wrap:wrap;gap:5px;margin:3px 0;}
.sari-buyer-ai-variant {display:inline-flex;align-items:center;gap:4px;max-width:100%;padding:4px 6px;border:1px solid #efe3d2;border-radius:6px;background:#fbf7f0;color:#6b5840;font-size:11px;line-height:1.3;overflow-wrap:anywhere;}
.sari-buyer-ai-variant-img {width:18px;height:18px;border-radius:4px;object-fit:cover;}
.sari-buyer-ai-variant--soldout {opacity:.55;text-decoration:line-through;}
.sari-buyer-ai-product-link {display:inline-flex;align-items:center;justify-content:center;align-self:flex-start;min-height:31px;margin-top:4px;padding:6px 10px;border-radius:8px;background:var(--sari-ai-gold);color:#fff !important;text-decoration:none !important;font-size:11.5px;font-weight:600;}
.sari-buyer-ai-product-link:hover {background:#b97f0c;}

@media (max-width:640px) {
    .sari-buyer-ai {right:12px;bottom:12px;}
    .sari-buyer-ai-launcher,.sari-buyer-ai-launcher-shell,.sari-buyer-ai-launcher-image {width:62px;height:62px;max-width:62px;}
    .sari-buyer-ai-launcher-callout {display:none;}
    .sari-buyer-ai-panel {position:fixed;right:8px;bottom:8px;width:calc(100vw - 16px);height:min(730px,calc(100dvh - 16px));border-radius:16px;transform-origin:bottom center;}
    .sari-buyer-ai-header {min-height:66px;padding:9px 12px;}
    .sari-buyer-ai-messages {padding:12px 11px 15px;}
    .sari-buyer-ai-welcome {padding-top:5px;}
    .sari-buyer-ai-composer {padding:8px 11px;}
    .sari-buyer-ai-composer textarea {min-height:54px;font-size:16px;}
}
/* Small screens may wrap the placeholder onto two lines; keep it fully visible. */
@media (max-width:440px) {
    .sari-buyer-ai-composer textarea {min-height:74px;}
}
@media (max-width:385px) {
    .sari-buyer-ai-product {grid-template-columns:62px minmax(0,1fr);gap:9px;padding:9px;}
    .sari-buyer-ai-product-photo {width:62px;height:79px;}
    .sari-buyer-ai-welcome-copy h2 {font-size:18px;}
    .sari-buyer-ai-welcome-visual,.sari-buyer-ai-welcome-visual img {width:68px;height:68px;}
    .sari-buyer-ai-identity-copy > strong {font-size:14px;}
}
@media (prefers-reduced-motion:reduce) {
    .sari-buyer-ai-launcher,.sari-buyer-ai-launcher-callout,.sari-buyer-ai-panel,.sari-buyer-ai-typing span {transition:none;animation:none;}
}
@media print {.sari-buyer-ai {display:none !important;}}
</style>

<script data-navigate-once>
(function () {
    if (window.__SARI_BUYER_AI_ASSISTANT_V1__) return;
    const root = document.getElementById('sariBuyerAi');
    if (!root) return;
    window.__SARI_BUYER_AI_ASSISTANT_V1__ = true;

    const launcher = document.getElementById('sariBuyerAiLauncher');
    const panel = document.getElementById('sariBuyerAiPanel');
    const closeButton = document.getElementById('sariBuyerAiClose');
    const clearButton = document.getElementById('sariBuyerAiClear');
    const messagesEl = document.getElementById('sariBuyerAiMessages');
    const welcomeEl = document.getElementById('sariBuyerAiWelcome');
    const welcomeActionsEl = document.getElementById('sariBuyerAiWelcomeActions');
    const suggestionsEl = document.getElementById('sariBuyerAiSuggestions');
    const form = document.getElementById('sariBuyerAiForm');
    const input = document.getElementById('sariBuyerAiInput');
    const sendButton = document.getElementById('sariBuyerAiSend');
    const errorEl = document.getElementById('sariBuyerAiError');
    const endpoint = root.dataset.endpoint || '';
    // Use per-tab storage: private Buyer conversations are not retained in localStorage.
    const storageKey = `sari:buyer-ai:v1:${root.dataset.buyerId}:history`;
    const openKey = `sari:buyer-ai:v1:${root.dataset.buyerId}:open`;
    const robotUrl = @json(asset('images/sari-seller-ai-assistant.png'));
    const definitions = [
        { key: 'product', test: p => /^\/buyer\/products\/\d+$/.test(p), title: 'Product Details', prompts: ['How do I choose a size and color?', 'Recommend similar clothing.', 'How do product variants work?', 'Where do I see product reviews?'] },
        { key: 'shop', test: p => p.startsWith('/buyer/shop/'), title: 'Seller Shop', prompts: ['Suggest products from the marketplace.', 'How do I choose a good size?', 'How do I message a seller?', 'Explain this page.'] },
        { key: 'products', test: p => p === '/buyer/products', title: 'Shop Products', prompts: ['Morena ako, 5\'3, 60 kg. Suggest a casual outfit with photos.', 'Show trending products with photos.', 'Show the best books.', 'What is my total spending?'] },
        { key: 'cart', test: p => p === '/buyer/cart', title: 'Shopping Cart', prompts: ['What is in my cart?', 'How do size and color variants work in cart?', 'How do I select items for checkout?', 'Explain this page.'] },
        { key: 'shipping', test: p => p === '/buyer/shipping', title: 'Shipping', prompts: ['Where are my shipments?', 'Check my latest order status.', 'What does in transit mean?', 'How do I track a delivery?'] },
        { key: 'checkout', test: p => p === '/buyer/checkout', title: 'Checkout', prompts: ['Explain the checkout process.', 'How do vouchers apply?', 'How are delivery fees calculated?', 'What payment methods are available?'] },
        { key: 'order-details', test: p => /^\/buyer\/orders\/\d+$/.test(p), title: 'Order Tracking', prompts: ['What is my latest order status?', 'How do I read the tracking route?', 'Can I cancel my order?', 'How do returns work?'] },
        { key: 'orders', test: p => p === '/buyer/orders', title: 'My Orders', prompts: ['Summarize my recent orders.', 'What is my latest order status?', 'How do I track delivery?', 'How can I request a return?'] },
        { key: 'notifications', test: p => p === '/buyer/notifications', title: 'Notifications', prompts: ['How many unread notifications do I have?', 'What updates show here?', 'How do I mark notifications as read?', 'Explain this page.'] },
        { key: 'messages', test: p => p === '/buyer/messages', title: 'Messages', prompts: ['Do I have unread seller messages?', 'How can I message a seller?', 'What can I ask the seller?', 'Explain this page.'] },
        { key: 'rewards', test: p => p === '/buyer/rewards', title: 'Rewards', prompts: ['How do I use a voucher?', 'Where can I find active rewards?', 'How do promotions work?', 'Explain this page.'] },
        { key: 'account', test: p => p === '/buyer/account', title: 'Buyer Account', prompts: ['Summarize my Buyer account activity.', 'How do I manage my addresses?', 'Where can I view recent orders?', 'Explain this page.'] },
        { key: 'recent-orders', test: p => p === '/buyer/recent-orders', title: 'Recent Orders', prompts: ['Summarize my recent orders.', 'Where do I see order tracking?', 'How do returns work?', 'Explain this page.'] },
        { key: 'recent-activity', test: p => p === '/buyer/recent-activity', title: 'Recent Activity', prompts: ['What recent account changes were recorded?', 'What does recent activity mean?', 'How do I manage activity entries?', 'Explain this page.'] },
        { key: 'home', test: p => ['/buyer/home', '/buyer/dashboard'].includes(p), title: 'Buyer Home', prompts: ['Suggest outfit colors for my skin tone and show product photos.', 'Show trending products and prices.', 'Summarize my orders and notifications.', 'What is my total spending?'] },
    ];
    const fallbackPage = {key: 'buyer', title: 'Buyer workspace', prompts: ['Recommend a nice outfit.', 'What is in my cart?', 'Check my latest order.', 'Explain this page.']};
    let currentPage = resolvePage();
    let history = readHistory();
    let busy = false;
    let controller = null;

    function escapeHtml(value) {
        return String(value ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
    }
    function safeUrl(value, image = false) {
        try {
            const url = new URL(String(value || ''), location.origin);
            if (url.origin !== location.origin) return '';
            if (image && !(/^(\/buyer\/(?:products|product-images|product-variants)\/|\/images\/)/.test(url.pathname))) return '';
            if (!image && !/^\/buyer\/products\/\d+$/.test(url.pathname)) return '';
            return url.href;
        } catch (_) { return ''; }
    }
    function money(value) {
        return new Intl.NumberFormat('en-PH', {style: 'currency', currency: 'PHP', maximumFractionDigits: 2})
            .format(Number(value) || 0);
    }
    function readHistory() {
        try {
            const saved = JSON.parse(sessionStorage.getItem(storageKey) || '[]');
            if (!Array.isArray(saved)) return [];
            return saved.filter(row => row && ['user', 'assistant'].includes(row.role) && typeof row.content === 'string')
                .slice(-20).map(row => ({role: row.role, content: row.content.slice(0, 2000), products: Array.isArray(row.products) ? row.products.slice(0, 4) : []}));
        } catch (_) { return []; }
    }
    function saveHistory() {
        try { sessionStorage.setItem(storageKey, JSON.stringify(history.slice(-20))); } catch (_) {}
    }
    function resolvePage() {
        const path = location.pathname || '/buyer/home';
        const page = definitions.find(def => def.test(path)) || fallbackPage;
        return {key: page.key, title: page.title, path, prompts: [...page.prompts]};
    }
    function formatReply(value) {
        const escaped = escapeHtml(value).replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        return escaped.split(/\n{2,}/).filter(Boolean)
            .map(chunk => `<p>${chunk.replace(/\n/g, '<br>')}</p>`).join('');
    }
    function productCards(products) {
        if (!Array.isArray(products) || !products.length) return '';
        return `<div class="sari-buyer-ai-product-list">${products.slice(0, 4).map(product => {
            const url = safeUrl(product.url);
            const photo = safeUrl(product.image, true);
            const variants = (Array.isArray(product.variants) ? product.variants : [])
                .filter(variant => variant && variant.label)
                .slice(0, 5).map(variant => {
                    const thumbnail = safeUrl(variant.image, true);
                    return `<span class="sari-buyer-ai-variant ${Number(variant.stock) <= 0 ? 'sari-buyer-ai-variant--soldout' : ''}"
                        title="${escapeHtml(variant.label)} · ${money(variant.price)}">
                        ${thumbnail ? `<img src="${escapeHtml(thumbnail)}" loading="lazy" alt="" class="sari-buyer-ai-variant-img">` : ''}
                        ${escapeHtml(variant.label)} · ${money(variant.price)}
                    </span>`;
                }).join('');
            return `<article class="sari-buyer-ai-product">
                ${photo ? `<img class="sari-buyer-ai-product-photo" src="${escapeHtml(photo)}" loading="lazy" alt="${escapeHtml(product.name)}">` : '<span class="sari-buyer-ai-product-photo" aria-hidden="true"></span>'}
                <div class="sari-buyer-ai-product-info">
                    <strong class="sari-buyer-ai-product-name">${escapeHtml(product.name)}</strong>
                    <span class="sari-buyer-ai-product-meta"><strong>Category:</strong> ${escapeHtml(product.category || 'General')}</span>
                    <span class="sari-buyer-ai-product-meta"><strong>Seller:</strong> ${escapeHtml(product.seller || 'SARI Seller')}</span>
                    <span class="sari-buyer-ai-product-price">${money(product.price)}</span>
                    ${variants ? `<div class="sari-buyer-ai-product-variants" aria-label="Available colors and sizes">${variants}</div>` : '<span class="sari-buyer-ai-product-meta">Standard option</span>'}
                    ${url ? `<a class="sari-buyer-ai-product-link" href="${escapeHtml(url)}" wire:navigate.hover>View Product ↗</a>` : ''}
                </div>
            </article>`;
        }).join('')}</div>`;
    }
    function messageHtml(message) {
        if (message.role === 'user') return `<div class="sari-buyer-ai-message sari-buyer-ai-message--user"><div class="sari-buyer-ai-bubble">${escapeHtml(message.content)}</div></div>`;
        return `<div class="sari-buyer-ai-response">
            <span class="sari-buyer-ai-message-label">SARI AI</span>
            <div class="sari-buyer-ai-message sari-buyer-ai-message--assistant">
                <span class="sari-buyer-ai-mini-avatar" aria-hidden="true"><img src="${escapeHtml(robotUrl)}" alt=""></span>
                <div class="sari-buyer-ai-bubble">${formatReply(message.content)}</div>
            </div>
            ${productCards(message.products)}
        </div>`;
    }
    function suggestionHtml(text, welcome = false) {
        return `<button type="button" data-sari-buyer-ai-prompt="${escapeHtml(text)}">${escapeHtml(text)}${welcome ? '<span aria-hidden="true">↗</span>' : ''}</button>`;
    }
    function updateSuggestions() {
        if (suggestionsEl) suggestionsEl.innerHTML = history.length ? currentPage.prompts.slice(0, 2).map(text => suggestionHtml(text)).join('') : '';
        if (welcomeActionsEl) welcomeActionsEl.innerHTML = currentPage.prompts.slice(0, 4).map(text => suggestionHtml(text, true)).join('');
    }
    function render() {
        if (!messagesEl) return;
        if (!history.length) {
            messagesEl.innerHTML = '';
            if (welcomeEl) { welcomeEl.hidden = false; messagesEl.appendChild(welcomeEl); }
        } else {
            messagesEl.innerHTML = history.map(messageHtml).join('');
        }
        updateSuggestions();
        requestAnimationFrame(() => { messagesEl.scrollTop = messagesEl.scrollHeight; });
    }
    function resizeInput() {
        if (!input) return;
        input.style.height = 'auto';
        input.style.height = `${Math.min(104, Math.max(42, input.scrollHeight))}px`;
    }
    function setError(text = '') { if (errorEl) errorEl.textContent = text; }
    function showTyping() {
        if (!messagesEl || document.getElementById('sariBuyerAiTyping')) return;
        messagesEl.insertAdjacentHTML('beforeend', `<div id="sariBuyerAiTyping" class="sari-buyer-ai-typing-wrap">
          <span class="sari-buyer-ai-mini-avatar" aria-hidden="true"><img src="${escapeHtml(robotUrl)}" alt=""></span>
          <div class="sari-buyer-ai-typing-content"><span class="sari-buyer-ai-typing-label">Checking your Buyer information</span>
          <div class="sari-buyer-ai-typing" aria-label="SARI Buyer AI is thinking"><span></span><span></span><span></span></div></div>
        </div>`);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }
    function setOpen(open) {
        root.classList.toggle('is-open', Boolean(open));
        panel?.setAttribute('aria-hidden', open ? 'false' : 'true');
        launcher?.setAttribute('aria-expanded', open ? 'true' : 'false');
        try { sessionStorage.setItem(openKey, open ? '1' : '0'); } catch (_) {}
        if (open) {
            currentPage = resolvePage(); updateSuggestions(); render(); input?.focus();
            if (!endpoint) setError('SARI AI chat is not connected yet. The Buyer AI message route needs to be installed.');
        }
    }
    async function ask(raw) {
        const question = String(raw || '').trim();
        if (!question || busy) return;
        if (!endpoint) {
            setError('SARI AI chat is not connected yet. The Buyer AI message route needs to be installed.');
            return;
        }
        busy = true;
        setError('');
        currentPage = resolvePage();
        const prior = history.slice(-8).map(item => ({role: item.role, content: item.content.slice(0, 1200)}));
        history.push({role: 'user', content: question, products: []});
        history = history.slice(-20);
        saveHistory();
        render();
        input.value = '';
        resizeInput();
        sendButton.disabled = true;
        showTyping();
        controller?.abort();
        controller = new AbortController();
        const currentController = controller;
        const timeout = setTimeout(() => currentController.abort(), 22000);
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content || form.querySelector('input[name="_token"]')?.value || '';
            const response = await fetch(endpoint, {
                method: 'POST', credentials: 'same-origin', cache: 'no-store', signal: currentController.signal,
                headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                body: JSON.stringify({message: question, history: prior, page_context: {path: currentPage.path}}),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                if (response.status === 419 || response.status === 403) throw new Error('Your Buyer session may have expired. Please sign in again.');
                const validation = Object.values(data.errors || {})?.[0]?.[0];
                throw new Error(validation || (response.status === 429 ? 'Too many questions right now. Please try again shortly.' : 'SARI AI could not reach the service. Please try again.'));
            }
            const reply = String(data.reply || '').trim();
            if (!reply) throw new Error('SARI AI did not return an answer. Please try again.');
            history.push({role: 'assistant', content: reply, products: Array.isArray(data.products) ? data.products.slice(0, 4) : []});
            history = history.slice(-20);
            saveHistory();
        } catch (error) {
            setError(error?.name === 'AbortError' ? 'SARI AI is taking longer than expected. Please try again.' : (error?.message || 'SARI AI is temporarily unavailable.'));
        } finally {
            clearTimeout(timeout);
            if (controller === currentController) controller = null;
            busy = false;
            sendButton.disabled = false;
            render();
            input?.focus();
        }
    }

    launcher?.addEventListener('click', () => setOpen(true));
    closeButton?.addEventListener('click', () => setOpen(false));
    clearButton?.addEventListener('click', () => {
        if (busy) return;
        history = []; saveHistory(); setError(''); render(); input?.focus();
    });
    form?.addEventListener('submit', event => { event.preventDefault(); ask(input.value); });
    input?.addEventListener('input', resizeInput);
    input?.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
            event.preventDefault(); ask(input.value);
        }
    });
    root.addEventListener('click', event => {
        const prompt = event.target.closest('[data-sari-buyer-ai-prompt]');
        if (prompt && root.contains(prompt)) ask(prompt.dataset.sariBuyerAiPrompt || '');
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && root.classList.contains('is-open')) setOpen(false);
    });
    document.addEventListener('livewire:navigated', () => { currentPage = resolvePage(); updateSuggestions(); });
    currentPage = resolvePage();
    render();
    let shouldOpen = false;
    try { shouldOpen = sessionStorage.getItem(openKey) === '1'; } catch (_) {}
    if (shouldOpen) setOpen(true);
})();
</script>
