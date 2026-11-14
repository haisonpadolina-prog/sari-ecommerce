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
                <button id="sariBuyerAiExpand" type="button" class="sari-buyer-ai-icon-button" aria-label="Expand conversation" title="Expand conversation" aria-pressed="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 3H3v5m13-5h5v5M3 16v5h5m13-5v5h-5"/></svg>
                </button>
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
                        Ask about outfit ideas, colors for your skin tone, seller size charts and sizes,
                        your cart, orders, notifications, and Buyer pages.
                        You can optionally share measurements for size suggestions. These are not saved to your Buyer profile; if AI is configured, your chat is sent to its provider.
                    </p>
                </div>

                <div id="sariBuyerAiWelcomeActions" class="sari-buyer-ai-welcome-actions"></div>
            </section>
        </div>

        <div class="sari-buyer-ai-composer-wrap">
            <details id="sariBuyerAiFitDetails" class="sari-buyer-ai-fit-details">
                <summary>Optional fit &amp; style details <span aria-hidden="true">⌄</span></summary>
                <div class="sari-buyer-ai-fit-content">
                    <p>Share only what you want. Used for this chat; if AI is configured, these details are also sent to its provider.</p>
                    <div class="sari-buyer-ai-fit-grid">
                        <label class="sari-buyer-ai-fit-wide">Body measurement unit
                            <select id="sariBuyerAiMeasureUnit"><option value="cm">Centimeters (cm)</option><option value="in">Inches (in)</option></select>
                        </label>
                        <label class="sari-buyer-ai-fit-wide">What are you shopping for?
                            <select id="sariBuyerAiClothing">
                                <option value="">Any clothing / help me choose</option>
                                <option value="jeans">Jeans</option>
                                <option value="shorts">Shorts</option>
                                <option value="t-shirt">T-shirt</option>
                                <option value="shirt or polo">Shirt / Polo</option>
                                <option value="blouse or top">Blouse / Top</option>
                                <option value="trousers or pants">Trousers / Pants</option>
                                <option value="dress or skirt">Dress / Skirt</option>
                                <option value="jacket or hoodie">Jacket / Hoodie</option>
                                <option value="complete outfit">Complete outfit</option>
                            </select>
                        </label>
                        <label class="sari-buyer-ai-fit-wide">Other preferences <small>Optional</small>
                            <input id="sariBuyerAiPreferences" type="text" maxlength="160" placeholder="e.g. black jeans, casual style, under ₱1,000">
                        </label>

                        <label>Skin tone <select id="sariBuyerAiTone"><option value="">Prefer not to say</option><option value="fair">Fair/light</option><option value="medium">Medium</option><option value="morena">Morena/tan</option><option value="deep">Deep</option></select></label>
                        <label>Preferred fit <select id="sariBuyerAiFit"><option value="">No preference</option><option value="regular">Regular</option><option value="relaxed">Relaxed</option><option value="slim">Slim</option></select></label>
                        <label>Height (cm) <input id="sariBuyerAiHeight" type="number" inputmode="decimal" min="120" max="229" step="0.1" placeholder="Optional"></label>
                        <label>Weight (kg) <input id="sariBuyerAiWeight" type="number" inputmode="decimal" min="25" max="300" step="0.1" placeholder="Optional"></label>
                        <label><span data-body-measure-label="Chest circumference">Chest circumference (cm)</span> <input id="sariBuyerAiChest" type="number" inputmode="decimal" min="1" max="250" step="0.1" placeholder="Optional"></label>
                        <label><span data-body-measure-label="Waist circumference">Waist circumference (cm)</span> <input id="sariBuyerAiWaist" type="number" inputmode="decimal" min="1" max="250" step="0.1" placeholder="Optional"></label>
                        <label><span data-body-measure-label="Hips circumference">Hips circumference (cm)</span> <input id="sariBuyerAiHips" type="number" inputmode="decimal" min="1" max="250" step="0.1" placeholder="Optional"></label>
                        <label><span data-body-measure-label="Foot length">Foot length (cm)</span><input id="sariBuyerAiFoot" type="number" inputmode="decimal" min="1" max="250" step="0.1" placeholder="Optional"></label>
                        <label class="sari-buyer-ai-fit-wide sari-ai-measure-confirm"><input id="sariBuyerAiMeasureConfirmed" type="checkbox"> These are full body circumferences, not laid-flat garment widths. Foot is measured in length.</label>
                        <label>Specific size to browse (optional)
                            <select id="sariBuyerAiPreferredSize"><option value="">Use my measurements</option><option value="XS">XS</option><option value="Small">Small</option><option value="Medium">Medium</option><option value="Large">Large</option><option value="XL">XL</option><option value="XXL">XXL</option></select>
                        </label>
                    </div>
                    <button id="sariBuyerAiUseFit" type="button">Ask AI with these details</button>
                </div>
            </details>
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
.sari-buyer-ai-fit-details{border-bottom:1px solid #f0f1f3;}
.sari-buyer-ai-fit-details summary{display:flex;align-items:center;justify-content:space-between;padding:8px 13px;font-size:11px;font-weight:600;color:#926006;cursor:pointer;list-style:none;}
.sari-buyer-ai-fit-details summary::-webkit-details-marker{display:none;}
.sari-buyer-ai-fit-details[open] summary span{transform:rotate(180deg);}
.sari-buyer-ai-fit-content{max-height:min(42vh,265px);overflow-y:auto;padding:0 12px 11px;}
.sari-buyer-ai-fit-content p{margin:0 0 8px;font-size:10px;line-height:1.4;color:#758196;}
.sari-buyer-ai-fit-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px;}
.sari-buyer-ai-fit-grid label{display:grid;gap:4px;min-width:0;color:#667085;font-size:10.5px;}
.sari-buyer-ai-fit-grid select,.sari-buyer-ai-fit-grid input{min-width:0;width:100%;padding:6px 7px;border:1px solid #dfe4ea;border-radius:7px;background:#fff;color:#344054;font:inherit;font-size:11px;}
.sari-buyer-ai-fit-grid select:focus-visible,.sari-buyer-ai-fit-grid input:focus-visible{outline:2px solid #d59617;outline-offset:1px;}
#sariBuyerAiUseFit{width:100%;margin-top:9px;padding:8px 10px;border:0;border-radius:8px;background:#d59617;color:#fff;font-size:11px;font-weight:600;cursor:pointer;}
#sariBuyerAiUseFit:disabled{opacity:.55;cursor:wait;}
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
/* Buyer AI presentation and movement only; messaging behavior stays unchanged. */
#sariBuyerAi { --sari-ai-gold:#C88912; --sari-ai-ink:#2F3742; --sari-ai-muted:#7B8794; --sari-ai-border:#E5E7EB; }
#sariBuyerAiLauncher { touch-action:none; cursor:grab; user-select:none; -webkit-user-select:none; }
#sariBuyerAiLauncher:hover { transform:none; }
#sariBuyerAi.is-dragging #sariBuyerAiLauncher { cursor:grabbing; }
#sariBuyerAi.is-dragging .sari-buyer-ai-launcher-callout { opacity:0; }
#sariBuyerAi[data-callout-side="right"] .sari-buyer-ai-launcher-callout { left:84px; right:auto; }
#sariBuyerAiPanel { position:fixed; width:min(420px,calc(100vw - 24px)); height:min(660px,calc(100dvh - 24px)); border-radius:20px; border-color:#E5E7EB; box-shadow:0 8px 24px rgba(15,23,42,.08),0 24px 64px rgba(15,23,42,.15); transform:none; }
#sariBuyerAi.is-open #sariBuyerAiPanel { transform:none; }
#sariBuyerAi .sari-buyer-ai-header { min-height:76px; padding:14px 16px; gap:12px; cursor:grab; touch-action:none; }
#sariBuyerAi.is-panel-dragging .sari-buyer-ai-header { cursor:grabbing; }
#sariBuyerAi .sari-buyer-ai-header-actions { cursor:default; }
#sariBuyerAi .sari-buyer-ai-identity-copy > strong { font-size:16px; font-weight:750; letter-spacing:-.025em; }
#sariBuyerAi .sari-buyer-ai-identity-copy > small { font-size:10.5px; line-height:1.5; }
#sariBuyerAi .sari-buyer-ai-icon-button { width:36px; height:36px; border-radius:10px; }
#sariBuyerAi .sari-buyer-ai-messages { background:#F8FAFC; padding:18px 16px; }
#sariBuyerAi .sari-buyer-ai-welcome { padding:12px 0 18px; }
#sariBuyerAi .sari-buyer-ai-welcome-kicker { color:#A87316; font-size:9.5px; letter-spacing:.1em; font-weight:700; }
#sariBuyerAi .sari-buyer-ai-welcome-copy h2 { font-size:21px; font-weight:750; letter-spacing:-.035em; }
#sariBuyerAi .sari-buyer-ai-welcome-copy p { font-size:12px; line-height:1.7; }
#sariBuyerAi .sari-buyer-ai-welcome-actions { gap:9px; margin-top:18px; }
#sariBuyerAi .sari-buyer-ai-welcome-actions button { min-height:44px; padding:11px 13px; border-radius:12px; border-color:#E5E7EB; font-size:12px; box-shadow:none; }
#sariBuyerAi .sari-buyer-ai-welcome-actions button:hover,
#sariBuyerAi .sari-buyer-ai-suggestions button:hover { background:#F8FAFC; border-color:#D9BA78; }
#sariBuyerAi .sari-buyer-ai-bubble { padding:12px 14px; border-radius:14px; font-size:13px; line-height:1.7; }
#sariBuyerAi .sari-buyer-ai-message--user > .sari-buyer-ai-bubble { background:#2F3742; color:white; border-bottom-right-radius:5px; }
#sariBuyerAi .sari-buyer-ai-message--assistant > .sari-buyer-ai-bubble { border-bottom-left-radius:5px; }
#sariBuyerAi .sari-buyer-ai-suggestions { gap:8px; padding:10px 14px 4px; }
#sariBuyerAi .sari-buyer-ai-suggestions button { background:#fff; border-color:#E5E7EB; font-size:11px; line-height:1.45; }
#sariBuyerAi .sari-buyer-ai-fit-details summary { padding:10px 16px; color:#667383; }
#sariBuyerAi .sari-buyer-ai-composer { padding:10px 14px 14px; gap:9px; }
#sariBuyerAi .sari-buyer-ai-composer textarea { border-radius:13px; background:#fff; border-color:#DCE2E8; font-size:13px; }
#sariBuyerAi .sari-buyer-ai-send { border-radius:12px; }
#sariBuyerAi .sari-buyer-ai-send:hover { background:#B47B0E; border-color:#B47B0E; }
#sariBuyerAi .sari-buyer-ai-product { background:#fff; border-color:#E5E7EB; border-radius:13px; }
#sariBuyerAi .sari-buyer-ai-product-photo { background:white; }
#sariBuyerAi .sari-buyer-ai-variant { background:#F8FAFC; border-color:#E5E7EB; color:#59636E; }
#sariBuyerAi :is(button,a,summary):focus-visible { outline:2px solid #C88912; outline-offset:3px; }
@media(max-width:640px) {
    #sariBuyerAiPanel { border-radius:18px; }
    #sariBuyerAi .sari-buyer-ai-header { padding:12px; min-height:68px; }
    #sariBuyerAi .sari-buyer-ai-messages { padding:14px 12px; }
    #sariBuyerAi .sari-buyer-ai-composer textarea { font-size:16px; }
}
@media(max-height:520px) {
    #sariBuyerAi .sari-buyer-ai-suggestions { display:none; }
    #sariBuyerAi .sari-buyer-ai-header { min-height:54px; padding:7px 12px; }
    #sariBuyerAi .sari-buyer-ai-composer textarea { min-height:44px; }
}


/* Premium fit details and warm gold conversation palette. */
#sariBuyerAi .sari-buyer-ai-fit-details summary {
    padding:13px 16px; font-size:12px; font-weight:650; color:#475569;
    background:#fff;
}
#sariBuyerAi .sari-buyer-ai-fit-details[open] summary { color:#996508; }
#sariBuyerAi .sari-buyer-ai-fit-content {
    max-height:min(48dvh,340px); padding:2px 16px 16px; background:white;
    scrollbar-width:thin; scrollbar-color:#DCE2E8 transparent;
}
#sariBuyerAi .sari-buyer-ai-fit-content > p {
    margin:0 0 14px; font-size:11px; line-height:1.65; color:#8793A1;
}
#sariBuyerAi .sari-buyer-ai-fit-grid { gap:12px; }
#sariBuyerAi .sari-buyer-ai-fit-grid label {
    display:flex; flex-wrap:wrap; align-content:start; gap:6px;
    font-size:11px; font-weight:600; line-height:1.5; color:#596675;
}
#sariBuyerAi .sari-buyer-ai-fit-grid label small { margin-left:auto; color:#8B96A4; font-size:10px; font-weight:400; }
#sariBuyerAi .sari-buyer-ai-fit-wide { grid-column:1 / -1; }
#sariBuyerAi .sari-buyer-ai-fit-grid input,
#sariBuyerAi .sari-buyer-ai-fit-grid select {
    flex:0 0 100%; width:100%; min-height:42px; padding:9px 11px;
    border:1px solid #DCE2E8; border-radius:10px; background:#fff;
    font-size:12px; line-height:1.5; font-weight:400; color:#344256;
}
#sariBuyerAi .sari-buyer-ai-fit-grid input::placeholder { color:#9AA5B3; }
#sariBuyerAi #sariBuyerAiUseFit {
    min-height:44px; margin-top:16px; padding:10px 14px;
    border:1px solid #C88912; border-radius:11px;
    background:#C88912; color:white; font-size:12px; font-weight:700;
}
#sariBuyerAi #sariBuyerAiUseFit:hover:not(:disabled) { background:#B47B0E; border-color:#B47B0E; }
#sariBuyerAi .sari-buyer-ai-message--user > .sari-buyer-ai-bubble {
    background:#9B650B; border:1px solid #9B650B; color:#fff;
    box-shadow:0 3px 8px rgba(155,101,11,.10);
}
#sariBuyerAi .sari-buyer-ai-message--assistant > .sari-buyer-ai-bubble {
    background:#FFFCF5; border-color:#EADDBF; color:#4D4639;
}
@media(max-width:640px) {
    #sariBuyerAi .sari-buyer-ai-fit-grid input,
    #sariBuyerAi .sari-buyer-ai-fit-grid select { font-size:16px; }
}


/* Custom white option menus; native selects remain the data source. */
#sariBuyerAi .sari-ai-select-wrap { display:block; flex:0 0 100%; width:100%; position:relative; }
#sariBuyerAi .sari-ai-native-select {
    position:absolute !important; width:1px !important; height:1px !important; min-height:0 !important;
    padding:0 !important; border:0 !important; overflow:hidden !important;
    clip-path:inset(50%); opacity:0; pointer-events:none;
}
#sariBuyerAi .sari-ai-select-trigger {
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    width:100%; min-height:44px; padding:10px 12px; border:1px solid #DCE2E8;
    border-radius:11px; background:white; color:#344256; text-align:left;
    font:500 12px/1.5 Poppins,ui-sans-serif,system-ui,sans-serif; cursor:pointer;
}
#sariBuyerAi .sari-ai-select-label { min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
#sariBuyerAi .sari-ai-select-trigger svg { width:16px; height:16px; flex:0 0 16px; fill:none; stroke:#7B8794; stroke-width:1.8; }
#sariBuyerAi .sari-ai-select-trigger:hover { background:#F8FAFC; border-color:#CFD6DE; }
#sariBuyerAi .sari-ai-select-trigger[aria-expanded="true"] { border-color:#C88912; box-shadow:0 0 0 2px rgba(200,137,18,.10); }
#sariBuyerAi .sari-ai-select-trigger[aria-expanded="true"] svg { transform:rotate(180deg); }
#sariBuyerAi .sari-ai-select-menu {
    position:fixed; z-index:100; overflow-y:auto; overscroll-behavior:contain;
    border:1px solid #E2E8F0; border-radius:13px; padding:6px; background:white;
    box-shadow:0 16px 32px rgba(15,23,42,.12),0 2px 6px rgba(15,23,42,.05);
    scrollbar-width:thin; scrollbar-color:#DCE2E8 transparent;
}
#sariBuyerAi .sari-ai-select-menu[hidden] { display:none; }
#sariBuyerAi .sari-ai-select-option {
    display:block; width:100%; min-height:38px; padding:9px 12px; border:0;
    border-radius:8px; background:white; color:#475569; text-align:left;
    font:500 12px/1.5 Poppins,ui-sans-serif,system-ui,sans-serif; cursor:pointer;
}
#sariBuyerAi .sari-ai-select-option:hover,#sariBuyerAi .sari-ai-select-option:focus-visible {
    background:#F8FAFC; color:#996508; outline:none;
}
#sariBuyerAi .sari-ai-select-option[aria-selected="true"] { background:#FFF8E8; color:#8C5B08; font-weight:650; }
#sariBuyerAi.is-expanded .sari-buyer-ai-messages { padding:24px 28px; }
#sariBuyerAi.is-expanded .sari-buyer-ai-message { max-width:860px; margin-left:auto; margin-right:auto; }
#sariBuyerAi.is-expanded .sari-buyer-ai-message--assistant { grid-template-columns:32px minmax(0,1fr); }
#sariBuyerAi.is-expanded .sari-buyer-ai-bubble { font-size:14px; line-height:1.75; }
#sariBuyerAi.is-expanded .sari-buyer-ai-fit-grid { grid-template-columns:repeat(4,minmax(0,1fr)); }
#sariBuyerAi.is-expanded .sari-buyer-ai-fit-wide { grid-column:span 2; }
@media(max-width:640px) {
    #sariBuyerAi.is-expanded .sari-buyer-ai-messages { padding:14px 12px; }
    #sariBuyerAi.is-expanded .sari-buyer-ai-fit-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    #sariBuyerAi.is-expanded .sari-buyer-ai-fit-wide { grid-column:1 / -1; }
}


#sariBuyerAi .sari-buyer-ai-size-pick { display:grid; gap:5px; padding:12px 14px; margin:5px 0; border:1px solid #E7CE96; border-radius:11px; background:#FFF9EB; }
#sariBuyerAi .sari-buyer-ai-size-pick small { color:#96620A; font-size:9px; font-weight:750; letter-spacing:.06em; }
#sariBuyerAi .sari-buyer-ai-size-pick > strong { color:#80520A; font-size:16px; font-weight:750; line-height:1.4; }
#sariBuyerAi .sari-buyer-ai-size-pick > span { color:#776A53; font-size:11px; line-height:1.6; }
#sariBuyerAi .sari-buyer-ai-size-pick.is-pending { border-color:#E5E7EB; background:#F8FAFC; }
#sariBuyerAi .sari-buyer-ai-size-pick.is-pending > strong { font-size:13px; color:#475569; }
#sariBuyerAi .sari-buyer-ai-other-variants summary { padding:6px 0; color:#778397; font-size:11px; cursor:pointer; }

#sariBuyerAi .sari-buyer-ai-fit-grid .sari-ai-measure-confirm { display:flex; flex-wrap:nowrap; align-items:flex-start; gap:9px; font-size:10.5px; font-weight:400; line-height:1.65; }
#sariBuyerAi .sari-buyer-ai-fit-grid .sari-ai-measure-confirm input { flex:0 0 17px; width:17px; height:17px; min-height:17px; margin:2px 0 0; padding:0; accent-color:#C88912; }


#sariBuyerAi[data-small-viewport="true"] .sari-buyer-ai-fit-content { max-height:80px; }
#sariBuyerAi[data-small-viewport="true"] .sari-buyer-ai-suggestions { display:none; }
#sariBuyerAi[data-small-viewport="true"] .sari-buyer-ai-header { min-height:54px; padding:7px 12px; }
#sariBuyerAi[data-small-viewport="true"] .sari-buyer-ai-composer textarea { min-height:44px; }
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
    const fitDetails = document.getElementById('sariBuyerAiFitDetails');
    const fitSend = document.getElementById('sariBuyerAiUseFit');
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
        { key: 'product', test: p => /^\/buyer\/products\/\d+$/.test(p), title: 'Product Details', prompts: ['Check this product size guide and ask me for measurements if needed.', 'Recommend similar clothing.', 'How do product variants work?', 'Where do I see product reviews?'] },
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
        { key: 'home', test: p => ['/buyer/home', '/buyer/dashboard'].includes(p), title: 'Buyer Home', prompts: ['Help me choose clothing sizes using seller size charts; ask me for optional measurements.', 'Show trending products and prices.', 'Summarize my orders and notifications.', 'What is my total spending?'] },
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
            const photo = safeUrl(product.suggested_variant?.image || product.image, true);
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
            const suggested = product.suggested_variant;
            const basis = product.size_recommendation_basis;
            const hasChoice = suggested && suggested.size && ['requested_label', 'structured_chart'].includes(basis);
            const needed = (Array.isArray(product.needed_measurements) ? product.needed_measurements : [])
                .map(key => String(key).replace('_cm', ' (cm)')).join(' and ');
            const specific = hasChoice
                ? `<div class="sari-buyer-ai-size-pick"><small>${basis === 'requested_label' ? 'YOUR REQUESTED SIZE' : 'SUGGESTED SIZE · MEASUREMENT MATCH'}</small><strong>${escapeHtml(suggested.size)}${suggested.color ? ` · ${escapeHtml(suggested.color)}` : ''}</strong><span>${basis === 'requested_label' ? 'Available in stock. Body fit has not been checked.' : escapeHtml(product.size_match_note || 'Matched to seller measurements. Verify fit before buying.')}</span></div>`
                : `<div class="sari-buyer-ai-size-pick is-pending"><small>SIZE CHECK NEEDED</small><strong>${suggested?.color ? `Color idea: ${escapeHtml(suggested.color)}` : 'Choose the right fit'}</strong><span>${product.size_match_note ? escapeHtml(product.size_match_note) : (needed ? `Share ${escapeHtml(needed)} so I can compare the seller chart.` : 'Seller measurements are needed before choosing a size.')}</span></div>`;
            return `<article class="sari-buyer-ai-product">
                ${photo ? `<img class="sari-buyer-ai-product-photo" src="${escapeHtml(photo)}" loading="lazy" alt="${escapeHtml(product.name)}">` : '<span class="sari-buyer-ai-product-photo" aria-hidden="true"></span>'}
                <div class="sari-buyer-ai-product-info">
                    <strong class="sari-buyer-ai-product-name">${escapeHtml(product.name)}</strong>
                    <span class="sari-buyer-ai-product-meta"><strong>Category:</strong> ${escapeHtml(product.category || 'General')}</span>
                    <span class="sari-buyer-ai-product-meta"><strong>Seller:</strong> ${escapeHtml(product.seller || 'SARI Seller')}</span>
                    <span class="sari-buyer-ai-product-price">${money(product.price)}</span>
                    ${product.size_recommendation_basis !== 'not_applicable' && (product.outfit_role || /fashion|apparel|clothing|shirt|shorts|jeans|pants|shoes|footwear/i.test(`${product.category || ''} ${product.name || ''}`)) ? specific : ''}
                    ${variants ? `<details class="sari-buyer-ai-other-variants"><summary>View available options</summary><div class="sari-buyer-ai-product-variants" aria-label="Available colors and sizes">${variants}</div></details>` : '<span class="sari-buyer-ai-product-meta">Seller has not listed size/color variants.</span>'}
                    ${url && product.size_guide_available ? `<span class="sari-buyer-ai-product-meta">Seller size chart available on product page</span>` : ''}
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
        history = []; saveHistory(); setError(''); render();
        for (const field of root.querySelectorAll('.sari-buyer-ai-fit-grid input, .sari-buyer-ai-fit-grid select')) { if (field.type === 'checkbox') field.checked = false; else field.value = ''; }
        const measurementUnit = document.getElementById('sariBuyerAiMeasureUnit');
        if (measurementUnit) { measurementUnit.value='cm'; measurementUnit.dispatchEvent(new Event('change',{bubbles:true})); }
        if (fitDetails) fitDetails.open = false;
        input?.focus();
    });
    fitSend?.addEventListener('click', () => {
        if (busy) return;
        const items = [];
        const optional = [
            ['sariBuyerAiClothing', 'clothing wanted', null, null, ''],
            ['sariBuyerAiPreferences', 'shopping preferences', null, null, ''],
            ['sariBuyerAiTone', 'skin tone', null, null, ''],
            ['sariBuyerAiFit', 'fit preference', null, null, ''],
            ['sariBuyerAiHeight', 'height', 120, 229, ' cm'],
            ['sariBuyerAiWeight', 'weight', 25, 300, ' kg'],
            ['sariBuyerAiChest', 'chest circumference', 1, 250, ' cm'],
            ['sariBuyerAiWaist', 'waist circumference', 1, 250, ' cm'],
            ['sariBuyerAiHips', 'hips circumference', 1, 250, ' cm'],
            ['sariBuyerAiFoot', 'foot length', 1, 250, ' cm'],
            ['sariBuyerAiPreferredSize', 'requested size', null, null, ''],
        ];
        for (const [id, label, min, max, unit] of optional) {
            const field = document.getElementById(id);
            const raw = field?.value?.trim() || '';
            if (!raw) continue;
            const bodyField = ['sariBuyerAiChest','sariBuyerAiWaist','sariBuyerAiHips','sariBuyerAiFoot'].includes(id);
            const inInches = bodyField && document.getElementById('sariBuyerAiMeasureUnit')?.value === 'in';
            const numeric = Number(raw) * (inInches ? 2.54 : 1);
            if (min !== null && (!Number.isFinite(numeric) || numeric < min || numeric > max)) {
                setError(`Please enter a valid ${label} (${min}–${max}${unit}).`);
                field.focus(); return;
            }
            items.push(`${label}: ${raw}${inInches ? ' inches' : unit}`);
        }
        if (document.getElementById('sariBuyerAiMeasureConfirmed')?.checked) items.push('measurement basis: body circumference confirmed');
        if (!items.length) { setError('Add one optional detail or ask a question in the chat.'); return; }
        if (fitDetails) fitDetails.open = false;
        ask(`Please recommend available clothing for me and help with seller size charts if relevant. My optional self-reported details: ${items.join('; ')}. Ask for other measurements only if needed.`);
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

<script data-navigate-once>
(function () {
    const root = document.getElementById('sariBuyerAi');
    if (!root || root.dataset.movementBound === '1') return;
    const launcher = root.querySelector('#sariBuyerAiLauncher');
    const panel = root.querySelector('#sariBuyerAiPanel');
    const header = root.querySelector('.sari-buyer-ai-header');
    if (!launcher || !panel || !header) return;
    root.dataset.movementBound = '1';
    const key = 'sari:buyer-ai:position:v1';
    const margin = 12;
    const clamp = (value, min, max) => Math.max(min, Math.min(Math.max(min, max), value));
    const unit = value => typeof value === 'number' && Number.isFinite(value) && value >= 0 && value <= 1;
    let state = { launcher: { x:1, y:1 }, panel:null };
    try {
        const saved = JSON.parse(localStorage.getItem(key) || 'null');
        if (saved && unit(saved.launcher?.x) && unit(saved.launcher?.y)) {
            state.launcher = saved.launcher;
            if (unit(saved.panel?.x) && unit(saved.panel?.y)) state.panel = saved.panel;
        }
    } catch (_) {}
    function viewport() {
        const v = window.visualViewport;
        return { left:v?.offsetLeft || 0, top:v?.offsetTop || 0, width:v?.width || window.innerWidth, height:v?.height || window.innerHeight };
    }
    function limits(width, height) {
        const v = viewport();
        return { x:v.left+margin, y:v.top+margin, w:Math.max(0,v.width-width-margin*2), h:Math.max(0,v.height-height-margin*2) };
    }
    function point(normalized, box) { return { x:box.x+normalized.x*box.w, y:box.y+normalized.y*box.h }; }
    function normalize(x, y, box) { return { x:box.w ? clamp((x-box.x)/box.w,0,1) : 0, y:box.h ? clamp((y-box.y)/box.h,0,1) : 0 }; }
    function save() { try { localStorage.setItem(key,JSON.stringify(state)); } catch (_) {} }
    function fit() {
        const v = viewport();
        root.dataset.smallViewport = v.height < 520 ? 'true' : 'false';
        const launchBox = launcher.getBoundingClientRect();
        const launch = point(state.launcher,limits(launchBox.width,launchBox.height));
        Object.assign(root.style,{left:launch.x+'px',top:launch.y+'px',right:'auto',bottom:'auto'});
        root.dataset.calloutSide = launch.x-v.left < 210 ? 'right' : 'left';
        panel.style.width = Math.min(root.classList.contains('is-expanded')?980:440,Math.max(1,v.width-margin*2))+'px';
        panel.style.height = Math.min(root.classList.contains('is-expanded')?Math.max(1,v.height-margin*2):720,Math.max(1,v.height-margin*2))+'px';
        const r = panel.getBoundingClientRect();
        const bounds = limits(r.width,r.height);
        let p;
        if (state.panel) p = point(state.panel,bounds);
        else {
            const above = launch.y-r.height-12;
            p = { x:clamp(launch.x+launchBox.width-r.width,bounds.x,bounds.x+bounds.w), y:clamp(above >= bounds.y ? above : launch.y+launchBox.height+12,bounds.y,bounds.y+bounds.h) };
        }
        Object.assign(panel.style,{left:p.x+'px',top:p.y+'px',right:'auto',bottom:'auto'});
    }
    let frame = 0;
    function schedule() { if (!frame) frame = requestAnimationFrame(() => { frame=0; fit(); }); }
    let drag = null, suppressUntil = 0;
    function start(event, kind) {
        if (event.button !== 0 || event.isPrimary === false) return;
        if (kind === 'panel' && event.target.closest('button,a,input,select,textarea,summary')) return;
        const element = kind === 'launcher' ? launcher : panel;
        const rect = element.getBoundingClientRect();
        drag = { id:event.pointerId, kind, handle:event.currentTarget, startX:event.clientX, startY:event.clientY, x:rect.left, y:rect.top, width:rect.width, height:rect.height, moved:false };
    }
    launcher.addEventListener('pointerdown',event => start(event,'launcher'));
    header.addEventListener('pointerdown',event => start(event,'panel'));
    window.addEventListener('pointermove',event => {
        if (!drag || event.pointerId !== drag.id) return;
        const dx=event.clientX-drag.startX, dy=event.clientY-drag.startY;
        if (!drag.moved && Math.hypot(dx,dy)<6) return;
        if (!drag.moved) {
            drag.moved=true;
            try { drag.handle.setPointerCapture(drag.id); } catch (_) {}
            root.classList.add(drag.kind==='launcher'?'is-dragging':'is-panel-dragging');
            if (drag.kind==='launcher') state.panel=null;
        }
        event.preventDefault();
        const box=limits(drag.width,drag.height);
        state[drag.kind]=normalize(drag.x+dx,drag.y+dy,box);
        fit();
    },{passive:false});
    function end(event) {
        if (!drag || event.pointerId !== drag.id) return;
        const done=drag; drag=null;
        if (done.moved) {
            suppressUntil=Date.now()+450;
            root.classList.remove('is-dragging','is-panel-dragging');
            save();
            try { if (done.handle.hasPointerCapture(done.id)) done.handle.releasePointerCapture(done.id); } catch (_) {}
        }
    }
    window.addEventListener('pointerup',end);
    window.addEventListener('pointercancel',end);
    launcher.addEventListener('lostpointercapture',end);
    header.addEventListener('lostpointercapture',end);
    root.addEventListener('click',event => {
        if (event.detail !== 0 && Date.now()<suppressUntil) { event.preventDefault(); event.stopImmediatePropagation(); }
    },true);
    launcher.setAttribute('title','Drag to move. Alt + arrow keys to reposition; Alt + Home to reset.');
    header.setAttribute('title','Drag this header to move the chat window.');
    launcher.addEventListener('keydown',event => {
        if (!event.altKey || !['ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Home'].includes(event.key)) return;
        event.preventDefault();
        if (event.key==='Home') state={launcher:{x:1,y:1},panel:null};
        else {
            const r=launcher.getBoundingClientRect(), box=limits(r.width,r.height), step=event.shiftKey?40:16;
            state.launcher=normalize(r.left+(event.key==='ArrowRight'?step:event.key==='ArrowLeft'?-step:0),r.top+(event.key==='ArrowDown'?step:event.key==='ArrowUp'?-step:0),box);
            state.panel=null;
        }
        fit(); save();
    });
    const observer=new MutationObserver(() => {
        schedule();
        if (!root.classList.contains('is-open') && panel.contains(document.activeElement)) launcher.focus({preventScroll:true});
    });
    observer.observe(root,{attributes:true,attributeFilter:['class']});
    window.addEventListener('resize',schedule);
    window.addEventListener('pageshow',schedule);
    window.visualViewport?.addEventListener('resize',schedule);
    window.visualViewport?.addEventListener('scroll',schedule);
    document.addEventListener('livewire:navigated',schedule);
    fit();
})();

</script>

<script data-navigate-once>
(function () {
    const root=document.getElementById('sariBuyerAi');
    if(!root || root.dataset.selectUiBound==='1') return;
    root.dataset.selectUiBound='1';
    const controls=[];
    let active=null;
    const viewport=()=>{const v=window.visualViewport;return {left:v?.offsetLeft||0,top:v?.offsetTop||0,width:v?.width||innerWidth,height:v?.height||innerHeight};};
    function position() {
        if(!active) return;
        const r=active.trigger.getBoundingClientRect(),v=viewport(),gap=6,pad=12;
        const below=Math.max(0,v.top+v.height-pad-r.bottom-gap),above=Math.max(0,r.top-v.top-pad-gap);
        const upward=below<160 && above>below;
        const height=Math.min(260,upward?above:below);
        const width=Math.min(Math.max(r.width,180),v.width-pad*2);
        Object.assign(active.menu.style,{width:width+'px',maxHeight:height+'px',left:Math.max(v.left+pad,Math.min(r.left,v.left+v.width-pad-width))+'px',top:(upward?r.top-gap-Math.min(active.menu.scrollHeight,height):r.bottom+gap)+'px'});
    }
    function sync(c) {
        c.label.textContent=c.select.options[c.select.selectedIndex]?.textContent || 'Select';
        c.options.forEach((option,index)=>option.setAttribute('aria-selected',String(index===c.select.selectedIndex)));
    }
    function close(focus=false) {
        if(!active) return;
        const old=active;active=null;old.menu.hidden=true;old.trigger.setAttribute('aria-expanded','false');
        if(focus) old.trigger.focus({preventScroll:true});
    }
    function open(c,last=false) {
        close();active=c;sync(c);c.menu.hidden=false;c.trigger.setAttribute('aria-expanded','true');position();
        const index=last?c.options.length-1:Math.max(0,c.select.selectedIndex);
        c.options[index]?.focus({preventScroll:true});
    }
    root.querySelectorAll('.sari-buyer-ai-fit-grid select').forEach(select=>{
        const wrapper=document.createElement('span');wrapper.className='sari-ai-select-wrap';
        select.before(wrapper);wrapper.append(select);select.classList.add('sari-ai-native-select');select.tabIndex=-1;select.setAttribute('aria-hidden','true');
        const trigger=document.createElement('button');trigger.type='button';trigger.className='sari-ai-select-trigger';
        trigger.setAttribute('aria-haspopup','listbox');trigger.setAttribute('aria-expanded','false');
        trigger.setAttribute('aria-label',select.closest('label')?.childNodes[0]?.textContent.trim() || 'Choose option');
        const label=document.createElement('span');label.className='sari-ai-select-label';trigger.append(label);
        const icon=document.createElementNS('http://www.w3.org/2000/svg','svg');icon.setAttribute('viewBox','0 0 24 24');icon.setAttribute('aria-hidden','true');
        const path=document.createElementNS(icon.namespaceURI,'path');path.setAttribute('d','m7 10 5 5 5-5');icon.append(path);trigger.append(icon);wrapper.append(trigger);
        const menu=document.createElement('div');menu.className='sari-ai-select-menu';menu.id=select.id+'Menu';menu.hidden=true;menu.setAttribute('role','listbox');menu.setAttribute('aria-label',trigger.getAttribute('aria-label'));trigger.setAttribute('aria-controls',menu.id);root.append(menu);
        const c={select,trigger,label,menu,options:[]};controls.push(c);
        Array.from(select.options).forEach((native,index)=>{
            const option=document.createElement('button');option.type='button';option.className='sari-ai-select-option';option.textContent=native.textContent;option.setAttribute('role','option');option.tabIndex=-1;menu.append(option);c.options.push(option);
            option.addEventListener('click',event=>{event.preventDefault();select.selectedIndex=index;select.dispatchEvent(new Event('change',{bubbles:true}));close(true);});
            option.addEventListener('keydown',event=>{
                let next=index;
                if(event.key==='ArrowDown') next=(index+1)%c.options.length;
                else if(event.key==='ArrowUp') next=(index-1+c.options.length)%c.options.length;
                else if(event.key==='Home') next=0;
                else if(event.key==='End') next=c.options.length-1;
                else if(event.key==='Escape'){event.preventDefault();close(true);return;}
                else if(event.key==='Tab'){close();return;}
                else return;
                event.preventDefault();c.options[next].focus({preventScroll:true});c.options[next].scrollIntoView({block:'nearest'});
            });
        });
        trigger.addEventListener('click',event=>{event.preventDefault();active===c?close():open(c);});
        trigger.addEventListener('keydown',event=>{if(['ArrowDown','ArrowUp'].includes(event.key)){event.preventDefault();open(c,event.key==='ArrowUp');}});
        select.addEventListener('change',()=>sync(c));sync(c);
    });
    document.addEventListener('pointerdown',event=>{if(active&&!active.menu.contains(event.target)&&!active.trigger.contains(event.target))close();});
    root.querySelector('#sariBuyerAiClear')?.addEventListener('click',()=>{close();setTimeout(()=>controls.forEach(sync),0);});
    root.querySelector('#sariBuyerAiFitDetails')?.addEventListener('toggle',event=>{if(!event.target.open)close();});
    root.querySelector('#sariBuyerAiClose')?.addEventListener('click',()=>close());
    root.querySelector('#sariBuyerAiExpand')?.addEventListener('click',event=>{
        close();const expanded=root.classList.toggle('is-expanded');
        const button=event.currentTarget;button.setAttribute('aria-pressed',String(expanded));
        button.setAttribute('aria-label',expanded?'Restore compact chat':'Expand conversation');button.title=expanded?'Restore compact chat':'Expand conversation';
    });
    window.addEventListener('resize',()=>close());
    window.visualViewport?.addEventListener('resize',()=>close());
    document.addEventListener('scroll',event=>{if(active&&!active.menu.contains(event.target))close();},true);
    document.addEventListener('livewire:navigating',()=>close());
})();

</script>

<script data-navigate-once>
(() => {
    const root=document.getElementById('sariBuyerAi'), unit=document.getElementById('sariBuyerAiMeasureUnit');
    if(!root||!unit||unit.dataset.conversionBound)return;unit.dataset.conversionBound='1';let previous=unit.value||'cm';
    function update(){
        const current=unit.value||'cm';
        if(current!==previous){const factor=previous==='in'?2.54:1/2.54;for(const name of ['Chest','Waist','Hips','Foot']){const input=document.getElementById('sariBuyerAi'+name);if(input?.value&&Number.isFinite(Number(input.value)))input.value=String(Math.round(Number(input.value)*factor*100)/100);}}
        previous=current;
        root.querySelectorAll('[data-body-measure-label]').forEach(span=>span.textContent=span.dataset.bodyMeasureLabel+' ('+current+')');
        for(const name of ['Chest','Waist','Hips','Foot']){const input=document.getElementById('sariBuyerAi'+name);if(input){input.min=current==='in'?'0.4':'1';input.max=current==='in'?'98.42':'250';input.step='0.01';}}
    }
    unit.addEventListener('change',update);update();
})();
</script>
