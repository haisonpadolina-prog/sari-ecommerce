@php
    $sellerAiAccount = $sellerAiAccount ?? $sellerLayoutAccount ?? null;
@endphp

<div
    id="sariSellerAi"
    class="sari-seller-ai"
    data-seller-id="{{ (int) ($sellerAiAccount?->id ?? 0) }}"
    data-endpoint="{{ route('seller.ai-assistant.message') }}"
>
    <button
        id="sariSellerAiLauncher"
        type="button"
        class="sari-seller-ai-launcher"
        aria-label="Open SARI Seller AI"
        aria-expanded="false"
        aria-controls="sariSellerAiPanel"
    >
        <span class="sari-seller-ai-launcher-aura" aria-hidden="true"></span>

        <span class="sari-seller-ai-launcher-shell">
            <img
                src="{{ asset('images/sari-seller-ai-assistant.png') }}"
                alt=""
                class="sari-seller-ai-launcher-image"
                draggable="false"
            >
        </span>

        <span class="sari-seller-ai-launcher-online" aria-hidden="true"></span>

        <span class="sari-seller-ai-launcher-callout" aria-hidden="true">
            <strong>Ask SARI AI</strong>
            <small>Seller workspace assistant</small>
        </span>
    </button>

    <section
        id="sariSellerAiPanel"
        class="sari-seller-ai-panel"
        aria-label="SARI Seller AI"
        aria-hidden="true"
    >
        <header class="sari-seller-ai-header">
            <div class="sari-seller-ai-identity">
                <div class="sari-seller-ai-avatar">
                    <img
                        src="{{ asset('images/sari-seller-ai-assistant.png') }}"
                        alt=""
                        draggable="false"
                    >
                </div>

                <div class="sari-seller-ai-identity-copy">
                    <strong>SARI Seller AI</strong>

                    <small>
                        <span class="sari-seller-ai-status-dot" aria-hidden="true"></span>
                        Online · Seller workspace assistant
                    </small>
                </div>
            </div>

            <div class="sari-seller-ai-header-actions">
                <button
                    id="sariSellerAiClear"
                    type="button"
                    class="sari-seller-ai-icon-button"
                    aria-label="Start a new AI conversation"
                    title="New conversation"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                </button>

                <button
                    id="sariSellerAiClose"
                    type="button"
                    class="sari-seller-ai-icon-button"
                    aria-label="Minimize SARI Seller AI"
                    title="Minimize"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M6 12h12"></path>
                    </svg>
                </button>
            </div>
        </header>

        <div class="sari-seller-ai-context-bar">
            <span class="sari-seller-ai-context-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 5h14v14H5z"></path>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h5"></path>
                </svg>
            </span>

            <div class="sari-seller-ai-context-copy">
                <small>Current workspace</small>
                <strong id="sariSellerAiPageLabel">Seller workspace</strong>
            </div>

        </div>

        <div
            id="sariSellerAiMessages"
            class="sari-seller-ai-messages"
            role="log"
            aria-live="polite"
            aria-relevant="additions"
        >
            <section id="sariSellerAiWelcome" class="sari-seller-ai-welcome">
                <div class="sari-seller-ai-welcome-visual">
                    <span class="sari-seller-ai-welcome-halo" aria-hidden="true"></span>
                    <img
                        src="{{ asset('images/sari-seller-ai-assistant.png') }}"
                        alt=""
                        draggable="false"
                    >
                </div>

                <div class="sari-seller-ai-welcome-copy">
                    <span class="sari-seller-ai-welcome-kicker">YOUR SELLER COPILOT</span>
                    <h2>How can I help with your store?</h2>
                    <p>
                        Ask about products, inventory, orders, shipping, reviews,
                        vouchers, finance, or reports using your current SARI Seller data.
                    </p>
                </div>

                <div id="sariSellerAiWelcomeActions" class="sari-seller-ai-welcome-actions"></div>
            </section>
        </div>

        <div class="sari-seller-ai-composer-wrap">
            <div id="sariSellerAiSuggestions" class="sari-seller-ai-suggestions"></div>

            <form id="sariSellerAiForm" class="sari-seller-ai-composer">
                @csrf

                <textarea
                    id="sariSellerAiInput"
                    rows="1"
                    maxlength="1200"
                    placeholder="Ask SARI AI about this page or your store..."
                    aria-label="Ask SARI Seller AI"
                ></textarea>

                <button
                    id="sariSellerAiSend"
                    type="submit"
                    class="sari-seller-ai-send"
                    aria-label="Send message"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="m4 4 17 8-17 8 3-8-3-8Z"></path>
                        <path d="M7 12h14"></path>
                    </svg>
                </button>
            </form>

            <div class="sari-seller-ai-composer-meta">
                <span>Enter to send · Shift+Enter for a new line</span>
                <span>AI can make mistakes</span>
            </div>
        </div>

        <p id="sariSellerAiError" class="sari-seller-ai-error" role="alert"></p>

        <footer class="sari-seller-ai-footer">
            <span>
                For account actions or enforcement decisions,
                <a href="{{ route('seller.messages') }}">contact SARI Admin</a>.
            </span>
        </footer>
    </section>
</div>

<style id="sariSellerAiStyles">
    .sari-seller-ai {
        --sari-ai-gold: #d59617;
        --sari-ai-gold-dark: #bb7d08;
        --sari-ai-charcoal: #24282f;
        --sari-ai-gold-soft: #fff9ec;
        --sari-ai-ink: #17202d;
        --sari-ai-text: #475467;
        --sari-ai-muted: #98a2b3;
        --sari-ai-line: #e4e7ec;
        --sari-ai-soft: #f7f9fb;

        position: fixed;
        right: 24px;
        bottom: 22px;
        z-index: 8500;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .sari-seller-ai *,
    .sari-seller-ai *::before,
    .sari-seller-ai *::after {
        box-sizing: border-box;
    }

    /* ----------------------------------------------------------
       Premium floating launcher
       ---------------------------------------------------------- */
    .sari-seller-ai-launcher {
        position: relative;
        display: grid;
        width: 72px;
        height: 72px;
        place-items: center;
        border: 0;
        border-radius: 50%;
        background: transparent;
        padding: 0;
        cursor: pointer;
        outline: none;
        isolation: isolate;
        transition: transform .18s ease;
    }

    .sari-seller-ai-launcher:hover {
        transform: translateY(-2px) scale(1.015);
    }

    .sari-seller-ai-launcher:focus-visible {
        box-shadow: 0 0 0 4px rgba(213, 150, 23, .2);
    }

    .sari-seller-ai-launcher-aura {
        display: none;
    }

    .sari-seller-ai-launcher-shell {
        display: grid;
        width: 72px;
        height: 72px;
        overflow: visible;
        place-items: center;
        border: 0;
        border-radius: 50%;
        background: transparent;
        box-shadow: none;
    }

    .sari-seller-ai-launcher-image {
        width: 72px;
        height: 72px;
        max-width: 72px;
        object-fit: contain;
        user-select: none;
        transform: none;
    }

    .sari-seller-ai-launcher-online {
        position: absolute;
        right: 1px;
        bottom: 6px;
        width: 14px;
        height: 14px;
        border: 3px solid #fff;
        border-radius: 50%;
        background: #35a568;
        box-shadow: none;
    }

    .sari-seller-ai-launcher-callout {
        position: absolute;
        right: 84px;
        display: grid;
        min-width: 132px;
        gap: 1px;
        border: 1px solid #e4e7ec;
        border-radius: 12px;
        background: #fff;
        padding: 8px 10px;
        text-align: left;
        opacity: 0;
        pointer-events: none;
        transform: translateX(6px);
        box-shadow: 0 12px 28px rgba(16, 24, 40, .1);
        transition: opacity .14s ease, transform .14s ease;
    }

    .sari-seller-ai-launcher-callout strong {
        color: #344054;
        font-size: 8.8px;
        font-weight: 700;
    }

    .sari-seller-ai-launcher-callout small {
        color: #98a2b3;
        font-size: 6.9px;
        font-weight: 500;
    }

    .sari-seller-ai-launcher:hover .sari-seller-ai-launcher-callout {
        opacity: 1;
        transform: translateX(0);
    }

    /* ----------------------------------------------------------
       Panel shell
       ---------------------------------------------------------- */
    .sari-seller-ai-panel {
        position: absolute;
        right: 0;
        bottom: 86px;
        display: grid;
        width: min(396px, calc(100vw - 24px));
        height: min(596px, calc(100dvh - 110px));
        grid-template-rows: auto auto minmax(0, 1fr) auto auto auto;
        overflow: hidden;
        border: 1px solid #dfe3e8;
        border-radius: 20px;
        background: #fff;
        box-shadow:
            0 32px 80px rgba(16, 24, 40, .18),
            0 8px 24px rgba(16, 24, 40, .06);
        opacity: 0;
        pointer-events: none;
        transform: translateY(12px) scale(.985);
        transform-origin: bottom right;
        transition:
            opacity .17s ease,
            transform .17s ease;
    }

    .sari-seller-ai.is-open .sari-seller-ai-panel {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0) scale(1);
    }

    .sari-seller-ai.is-open .sari-seller-ai-launcher {
        opacity: 0;
        pointer-events: none;
    }

    /* ----------------------------------------------------------
       Header + context
       ---------------------------------------------------------- */
    .sari-seller-ai-header {
        display: flex;
        min-height: 70px;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid #eaecf0;
        background: #fff;
        padding: 10px 12px;
    }

    .sari-seller-ai-identity {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
    }

    .sari-seller-ai-avatar {
        position: relative;
        display: grid;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        place-items: center;
        overflow: visible;
        border: 0;
        border-radius: 50%;
        background: transparent;
    }

    .sari-seller-ai-avatar img {
        width: 46px;
        height: 46px;
        max-width: 46px;
        object-fit: contain;
        filter: none;
    }

    .sari-seller-ai-avatar span {
        position: absolute;
        right: 0;
        bottom: 1px;
        width: 10px;
        height: 10px;
        border: 2px solid #fff;
        border-radius: 50%;
        background: #35a568;
        box-shadow: none;
    }

    .sari-seller-ai-identity-copy {
        min-width: 0;
    }

    .sari-seller-ai-name-row {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 6px;
    }

    .sari-seller-ai-name-row strong {
        overflow: hidden;
        color: var(--sari-ai-ink);
        font-size: 11px;
        font-weight: 750;
        line-height: 1.3;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-seller-ai-beta {
        display: inline-flex;
        min-height: 18px;
        align-items: center;
        border: 1px solid var(--sari-ai-charcoal);
        border-radius: 999px;
        background: var(--sari-ai-charcoal);
        padding: 0 6px;
        color: #f0bd4c;
        font-size: 5.9px;
        font-weight: 800;
        letter-spacing: .08em;
    }

    .sari-seller-ai-identity-copy small {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 3px;
        color: #7c8592;
        font-size: 7.5px;
        font-weight: 500;
    }

    .sari-seller-ai-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #35a568;
    }

    .sari-seller-ai-header-actions {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .sari-seller-ai-icon-button {
        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;
        border: 1px solid transparent;
        border-radius: 9px;
        background: transparent;
        color: #667085;
        cursor: pointer;
        outline: none;
    }

    .sari-seller-ai-icon-button:hover,
    .sari-seller-ai-icon-button:focus-visible {
        border-color: #e4e7ec;
        background: #f8fafc;
        color: #344054;
    }

    .sari-seller-ai-icon-button svg {
        width: 14px;
        height: 14px;
    }

    .sari-seller-ai-context-bar {
        display: grid;
        grid-template-columns: 30px minmax(0, 1fr) auto;
        gap: 8px;
        align-items: center;
        min-height: 51px;
        border-bottom: 1px solid #eaecf0;
        background: #fcfcfd;
        padding: 8px 12px;
    }

    .sari-seller-ai-context-icon {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 1px solid #e4e7ec;
        border-radius: 9px;
        background: #fff;
        color: #7c8592;
    }

    .sari-seller-ai-context-icon svg {
        width: 13px;
        height: 13px;
    }

    .sari-seller-ai-context-copy {
        min-width: 0;
    }

    .sari-seller-ai-context-copy small {
        display: block;
        color: #98a2b3;
        font-size: 6.7px;
        font-weight: 650;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .sari-seller-ai-context-copy strong {
        display: block;
        overflow: hidden;
        margin-top: 2px;
        color: #475467;
        font-size: 8.6px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-seller-ai-live-pill {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        gap: 5px;
        border: 1px solid #d8eadf;
        border-radius: 999px;
        background: #f4faf6;
        padding: 0 7px;
        color: #4f7d63;
        font-size: 6.7px;
        font-weight: 700;
        white-space: nowrap;
    }

    .sari-seller-ai-live-pill > span {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #35a568;
    }

    /* ----------------------------------------------------------
       Conversation + welcome
       ---------------------------------------------------------- */
    .sari-seller-ai-messages {
        min-height: 0;
        width: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        background: #f7f9fb;
        padding: 11px 10px 14px;
        scrollbar-width: thin;
        scrollbar-color: #d0d5dd transparent;
        scroll-padding-bottom: 14px;
    }

    .sari-seller-ai-welcome {
        display: grid;
        justify-items: center;
        min-height: 100%;
        align-content: center;
        padding: 14px 8px 18px;
        text-align: center;
    }

    .sari-seller-ai-welcome-visual {
        position: relative;
        display: grid;
        width: 96px;
        height: 96px;
        overflow: visible;
        place-items: center;
        border-radius: 50%;
        background: transparent;
    }

    .sari-seller-ai-welcome-visual img {
        position: relative;
        z-index: 1;
        width: 96px;
        height: 96px;
        max-width: 96px;
        object-fit: contain;
        filter: none;
    }

    .sari-seller-ai-welcome-halo {
        display: none;
    }

    .sari-seller-ai-welcome-copy {
        max-width: 310px;
        margin-top: 7px;
    }

    .sari-seller-ai-welcome-kicker {
        color: #b97805;
        font-size: 6.7px;
        font-weight: 800;
        letter-spacing: .13em;
    }

    .sari-seller-ai-welcome-copy h2 {
        margin: 5px 0 0;
        color: #1d2939;
        font-size: 15px;
        font-weight: 750;
        letter-spacing: -.02em;
    }

    .sari-seller-ai-welcome-copy p {
        margin: 6px 0 0;
        color: #7c8592;
        font-size: 8.4px;
        line-height: 1.55;
    }

    .sari-seller-ai-welcome-actions {
        display: grid;
        width: 100%;
        max-width: 318px;
        gap: 6px;
        margin-top: 14px;
    }

    .sari-seller-ai-welcome-actions button {
        display: flex;
        min-height: 37px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border: 1px solid #e4e7ec;
        border-radius: 11px;
        background: #fff;
        padding: 0 10px;
        color: #475467;
        font-family: inherit;
        font-size: 8.3px;
        font-weight: 650;
        text-align: left;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(16, 24, 40, .025);
    }

    .sari-seller-ai-welcome-actions button:hover,
    .sari-seller-ai-welcome-actions button:focus-visible {
        border-color: #ead3a0;
        background: #fffaf0;
        color: #9b6505;
        outline: none;
    }

    .sari-seller-ai-welcome-actions button svg {
        width: 12px;
        height: 12px;
        flex: 0 0 auto;
        color: #b97805;
    }

    .sari-seller-ai-message {
        display: flex;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        margin-top: 9px;
    }

    .sari-seller-ai-message:first-child {
        margin-top: 0;
    }

    .sari-seller-ai-message--user {
        justify-content: flex-end;
    }

    .sari-seller-ai-message--assistant {
        align-items: flex-end;
        justify-content: flex-start;
        gap: 7px;
    }

    .sari-seller-ai-mini-avatar {
        display: grid;
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        overflow: visible;
        place-items: center;
        border: 0;
        border-radius: 50%;
        background: transparent;
    }

    .sari-seller-ai-mini-avatar img {
        width: 28px;
        height: 28px;
        max-width: 28px;
        object-fit: contain;
    }

    .sari-seller-ai-bubble {
        display: block;
        min-width: 0;
        border-radius: 14px;
        padding: 8px 10px;
        font-size: 8.9px;
        line-height: 1.56;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .sari-seller-ai-message--assistant .sari-seller-ai-bubble {
        width: auto;
        max-width: calc(100% - 35px);
        border: 1px solid #e4e7ec;
        border-bottom-left-radius: 5px;
        background: #fff;
        color: #475467;
        box-shadow: 0 4px 12px rgba(16, 24, 40, .035);
    }

    .sari-seller-ai-message--user .sari-seller-ai-bubble {
        width: auto;
        max-width: 82%;
        margin-left: auto;
        border-bottom-right-radius: 5px;
        background: #d59617;
        color: #fff;
        box-shadow: 0 5px 13px rgba(213, 150, 23, .14);
    }

    .sari-seller-ai-bubble p {
        margin: 0;
    }

    .sari-seller-ai-bubble p + p {
        margin-top: 7px;
    }

    .sari-seller-ai-bubble strong {
        font-weight: 700;
        color: inherit;
    }

    .sari-seller-ai-bubble em {
        font-style: italic;
    }

    .sari-seller-ai-message-label {
        display: block;
        margin: 0 0 4px 35px;
        color: #98a2b3;
        font-size: 6.7px;
        font-weight: 750;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-seller-ai-bubble {
        display: block;
        width: auto;
        max-width: 100%;
        min-width: 0;
        border-radius: 14px;
        padding: 8px 10px;
        font-size: 8.9px;
        line-height: 1.56;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .sari-seller-ai-message--user .sari-seller-ai-bubble {
        width: fit-content;
        max-width: 100%;
    }

    .sari-seller-ai-message--assistant .sari-seller-ai-bubble {
        width: 100%;
    }

    .sari-seller-ai-bubble p {
        margin: 0;
    }

    .sari-seller-ai-bubble p + p {
        margin-top: 7px;
    }

    .sari-seller-ai-bubble strong {
        font-weight: 700;
        color: inherit;
    }

    .sari-seller-ai-bubble em {
        font-style: italic;
    }

    .sari-seller-ai-message--assistant .sari-seller-ai-bubble {
        border: 1px solid #e4e7ec;
        border-bottom-left-radius: 5px;
        background: #fff;
        color: #475467;
        box-shadow: 0 4px 12px rgba(16, 24, 40, .035);
    }

    .sari-seller-ai-message--user .sari-seller-ai-bubble {
        border-bottom-right-radius: 5px;
        background: #d59617;
        color: #fff;
        box-shadow: 0 5px 13px rgba(213, 150, 23, .14);
    }

    .sari-seller-ai-typing-wrap {
        display: flex;
        width: 100%;
        align-items: flex-end;
        gap: 7px;
        margin-top: 10px;
    }

    .sari-seller-ai-typing-content {
        display: grid;
        gap: 4px;
        min-width: 0;
        max-width: calc(100% - 35px);
    }

    .sari-seller-ai-typing-label {
        color: #98a2b3;
        font-size: 6.6px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .sari-seller-ai-typing {
        display: inline-flex;
        width: max-content;
        height: 34px;
        align-items: center;
        gap: 4px;
        border: 1px solid #e4e7ec;
        border-radius: 12px 12px 12px 5px;
        background: #fff;
        padding: 0 11px;
    }

    .sari-seller-ai-typing span {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #98a2b3;
        animation: sariSellerAiTyping 1s ease-in-out infinite;
    }

    .sari-seller-ai-typing span:nth-child(2) { animation-delay: .12s; }
    .sari-seller-ai-typing span:nth-child(3) { animation-delay: .24s; }

    @keyframes sariSellerAiTyping {
        0%, 60%, 100% { transform: translateY(0); opacity: .35; }
        30% { transform: translateY(-2px); opacity: 1; }
    }

    /* ----------------------------------------------------------
       Dynamic suggestions + composer
       ---------------------------------------------------------- */
    .sari-seller-ai-composer-wrap {
        border-top: 1px solid #eaecf0;
        background: #fff;
    }

    .sari-seller-ai-suggestions {
        display: flex;
        gap: 5px;
        overflow-x: auto;
        max-width: 100%;
        padding: 7px 10px 2px;
        scrollbar-width: none;
        scroll-snap-type: x proximity;
    }

    .sari-seller-ai-suggestions:empty {
        display: none;
    }

    .sari-seller-ai-suggestions::-webkit-scrollbar {
        display: none;
    }

    .sari-seller-ai-suggestions button {
        min-height: 27px;
        max-width: 190px;
        flex: 0 0 auto;
        overflow: hidden;
        border: 1px solid #e3e6ea;
        border-radius: 999px;
        background: #fff;
        padding: 0 9px;
        color: #667085;
        font-family: inherit;
        font-size: 7.2px;
        font-weight: 650;
        cursor: pointer;
        text-overflow: ellipsis;
        white-space: nowrap;
        scroll-snap-align: start;
    }

    .sari-seller-ai-suggestions button:hover,
    .sari-seller-ai-suggestions button:focus-visible {
        border-color: #e6ce99;
        background: #fffaf0;
        color: #9b6505;
        outline: none;
    }

    .sari-seller-ai-composer {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 42px;
        gap: 7px;
        align-items: end;
        padding: 8px 10px 5px;
    }

    .sari-seller-ai-composer textarea {
        width: 100%;
        min-height: 42px;
        max-height: 104px;
        resize: none;
        overflow-y: auto;
        border: 1px solid #dfe3e8;
        border-radius: 12px;
        background: #fff;
        padding: 11px;
        color: #344054;
        font-family: inherit;
        font-size: 9.4px;
        line-height: 1.45;
        outline: none;
        box-shadow: 0 1px 2px rgba(16,24,40,.015);
    }

    .sari-seller-ai-composer textarea::placeholder {
        color: #98a2b3;
    }

    .sari-seller-ai-composer textarea:focus {
        border-color: #d59617;
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .08);
    }

    .sari-seller-ai-send {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border: 1px solid #d59617;
        border-radius: 12px;
        background: #d59617;
        color: #fff;
        cursor: pointer;
        outline: none;
        box-shadow: 0 6px 14px rgba(213, 150, 23, .16);
    }

    .sari-seller-ai-send:hover,
    .sari-seller-ai-send:focus-visible {
        border-color: #bd810c;
        background: #bd810c;
    }

    .sari-seller-ai-send:disabled {
        cursor: wait;
        opacity: .62;
    }

    .sari-seller-ai-send svg {
        width: 15px;
        height: 15px;
        stroke: #fff;
    }

    .sari-seller-ai-composer-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 11px 6px;
        color: #b0b7c3;
        font-size: 6.1px;
        line-height: 1.3;
    }

    .sari-seller-ai-error {
        display: none;
        margin: 0;
        border-top: 1px solid #f1d7d7;
        background: #fff7f7;
        padding: 7px 10px;
        color: #a65353;
        font-size: 7.6px;
        line-height: 1.45;
    }

    .sari-seller-ai-error:not(:empty) {
        display: block;
    }

    .sari-seller-ai-footer {
        border-top: 1px solid #eaecf0;
        background: #fcfcfd;
        padding: 7px 10px 8px;
        color: #98a2b3;
        font-size: 6.7px;
        line-height: 1.45;
        text-align: center;
    }

    .sari-seller-ai-footer a {
        color: #8f650d;
        font-weight: 650;
        text-decoration: none;
    }

    .sari-seller-ai-footer a:hover,
    .sari-seller-ai-footer a:focus-visible {
        text-decoration: underline;
        outline: none;
    }

    /* ----------------------------------------------------------
       Mobile
       ---------------------------------------------------------- */
    @media (max-width: 640px) {
        .sari-seller-ai {
            right: 14px;
            bottom: 14px;
        }

        .sari-seller-ai-launcher,
        .sari-seller-ai-launcher-shell {
            width: 62px;
            height: 62px;
        }

        .sari-seller-ai-launcher-image {
            width: 62px;
            height: 62px;
            max-width: 62px;
        }

        .sari-seller-ai-launcher-callout {
            display: none !important;
        }

        .sari-seller-ai-panel {
            position: fixed;
            right: 9px;
            bottom: 9px;
            width: calc(100vw - 18px);
            height: min(650px, calc(100dvh - 18px));
            border-radius: 17px;
            transform-origin: bottom center;
        }

        .sari-seller-ai-context-bar {
            grid-template-columns: 30px minmax(0, 1fr);
        }

        .sari-seller-ai-live-pill {
            display: none;
        }

        .sari-seller-ai-message--assistant .sari-seller-ai-bubble {
            max-width: calc(100% - 35px);
        }

        .sari-seller-ai-message--user .sari-seller-ai-bubble {
            max-width: 84%;
        }

        .sari-seller-ai-bubble {
            font-size: 9.4px;
        }

        .sari-seller-ai-composer-meta span:first-child {
            display: none;
        }

        .sari-seller-ai-composer-meta {
            justify-content: flex-end;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-seller-ai-launcher,
        .sari-seller-ai-launcher-aura,
        .sari-seller-ai-launcher-callout,
        .sari-seller-ai-panel,
        .sari-seller-ai-typing span {
            transition: none !important;
            animation: none !important;
        }
    }

    @media print {
        .sari-seller-ai {
            display: none !important;
        }
    }

    /* Final chat-width hardening: direct bubble layout */
    #sariSellerAiMessages .sari-seller-ai-message {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--user {
        justify-content: flex-end !important;
        padding-inline: 0 !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--user > .sari-seller-ai-bubble {
        width: auto !important;
        max-width: 82% !important;
        margin-left: auto !important;
        margin-right: 0 !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--assistant > .sari-seller-ai-bubble {
        width: auto !important;
        max-width: calc(100% - 35px) !important;
    }

    #sariSellerAiMessages .sari-seller-ai-bubble {
        min-width: 0 !important;
        overflow-wrap: anywhere !important;
        word-break: break-word !important;
    }


    /* ==========================================================
       ROOT PANEL OVERFLOW FIX
       The entire assistant panel is width-contained here.
       ========================================================== */

    #sariSellerAiPanel {
        width: min(390px, calc(100vw - 32px)) !important;
        max-width: calc(100vw - 32px) !important;
        min-width: 0 !important;
        overflow: hidden !important;
        contain: layout paint;
    }

    #sariSellerAiPanel > * {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }

    #sariSellerAiPanel .sari-seller-ai-header {
        display: flex !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }

    #sariSellerAiPanel .sari-seller-ai-identity {
        flex: 1 1 0 !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }

    #sariSellerAiPanel .sari-seller-ai-identity-copy,
    #sariSellerAiPanel .sari-seller-ai-name-row {
        min-width: 0 !important;
        max-width: 100% !important;
    }

    #sariSellerAiPanel .sari-seller-ai-header-actions {
        flex: 0 0 auto !important;
        min-width: max-content !important;
        width: auto !important;
    }

    #sariSellerAiPanel .sari-seller-ai-context-bar {
        grid-template-columns: 30px minmax(0, 1fr) auto !important;
        min-width: 0 !important;
        overflow: hidden !important;
    }

    #sariSellerAiPanel .sari-seller-ai-context-copy {
        min-width: 0 !important;
        overflow: hidden !important;
    }

    #sariSellerAiPanel .sari-seller-ai-live-pill {
        max-width: 104px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #sariSellerAiMessages {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }

    /* Assistant row = avatar + a track that is allowed to shrink. */
    #sariSellerAiMessages .sari-seller-ai-message--assistant {
        display: grid !important;
        grid-template-columns: 28px minmax(0, 1fr) !important;
        align-items: end !important;
        gap: 7px !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--assistant > .sari-seller-ai-mini-avatar {
        grid-column: 1 !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--assistant > .sari-seller-ai-bubble {
        grid-column: 2 !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
        word-break: normal !important;
    }

    /* Seller row has one shrinkable track; bubble is aligned to the right. */
    #sariSellerAiMessages .sari-seller-ai-message--user {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) !important;
        justify-items: end !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        padding: 0 !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--user > .sari-seller-ai-bubble {
        width: fit-content !important;
        min-width: 0 !important;
        max-width: min(78%, 276px) !important;
        margin: 0 !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
        word-break: normal !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message-label {
        max-width: calc(100% - 35px) !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    /* Prevent the footer area from establishing a wider intrinsic width. */
    #sariSellerAiPanel .sari-seller-ai-composer-wrap {
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }

    #sariSellerAiSuggestions {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 5px !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
        padding: 7px 10px 2px !important;
    }

    #sariSellerAiSuggestions:empty {
        display: none !important;
    }

    #sariSellerAiSuggestions button {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #sariSellerAiForm {
        display: flex !important;
        align-items: flex-end !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        gap: 7px !important;
        padding: 8px 10px 5px !important;
        overflow: hidden !important;
    }

    #sariSellerAiInput {
        flex: 1 1 0 !important;
        width: 0 !important;
        min-width: 0 !important;
        max-width: none !important;
        margin: 0 !important;
    }

    #sariSellerAiSend {
        flex: 0 0 42px !important;
        width: 42px !important;
        min-width: 42px !important;
        max-width: 42px !important;
        margin: 0 !important;
    }

    #sariSellerAiPanel .sari-seller-ai-composer-meta,
    #sariSellerAiPanel .sari-seller-ai-footer,
    #sariSellerAiError {
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }

    @media (max-width: 640px) {
        #sariSellerAiPanel {
            right: 9px !important;
            left: 9px !important;
            width: auto !important;
            max-width: none !important;
        }

        #sariSellerAiMessages .sari-seller-ai-message--user > .sari-seller-ai-bubble {
            max-width: 84% !important;
        }
    }


    /* ==========================================================
       CLEAN PROFESSIONAL V3
       Removes redundant status decoration and visual noise.
       ========================================================== */

    #sariSellerAiPanel {
        border-color: #e2e6eb !important;
        border-radius: 18px !important;
        background: #ffffff !important;
        box-shadow:
            0 22px 55px rgba(16, 24, 40, .14),
            0 3px 12px rgba(16, 24, 40, .04) !important;
    }

    /* Header */
    #sariSellerAiPanel .sari-seller-ai-header {
        min-height: 64px !important;
        padding: 9px 12px !important;
        border-bottom-color: #edf0f3 !important;
    }

    #sariSellerAiPanel .sari-seller-ai-identity {
        gap: 9px !important;
    }

    #sariSellerAiPanel .sari-seller-ai-avatar {
        width: 42px !important;
        height: 42px !important;
        flex: 0 0 42px !important;
        overflow: visible !important;
        border: 0 !important;
        background: transparent !important;
    }

    #sariSellerAiPanel .sari-seller-ai-avatar img {
        width: 42px !important;
        height: 42px !important;
        max-width: 42px !important;
        object-fit: contain !important;
        filter: none !important;
    }

    /* Remove any old dot generated by previous CSS/markup */
    #sariSellerAiPanel .sari-seller-ai-avatar > span {
        display: none !important;
    }

    #sariSellerAiPanel .sari-seller-ai-identity-copy > strong {
        display: block !important;
        overflow: hidden !important;
        color: #1f2937 !important;
        font-size: 10.7px !important;
        font-weight: 750 !important;
        line-height: 1.25 !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    #sariSellerAiPanel .sari-seller-ai-name-row,
    #sariSellerAiPanel .sari-seller-ai-beta {
        display: none !important;
    }

    #sariSellerAiPanel .sari-seller-ai-identity-copy small {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        margin-top: 4px !important;
        color: #8993a1 !important;
        font-size: 7.1px !important;
        font-weight: 500 !important;
        line-height: 1.2 !important;
    }

    #sariSellerAiPanel .sari-seller-ai-status-dot {
        width: 6px !important;
        height: 6px !important;
        flex: 0 0 6px !important;
        border-radius: 50% !important;
        background: #35a568 !important;
    }

    #sariSellerAiPanel .sari-seller-ai-header-actions {
        gap: 2px !important;
    }

    #sariSellerAiPanel .sari-seller-ai-icon-button {
        width: 30px !important;
        height: 30px !important;
        border-radius: 8px !important;
        color: #667085 !important;
    }

    #sariSellerAiPanel .sari-seller-ai-icon-button:hover,
    #sariSellerAiPanel .sari-seller-ai-icon-button:focus-visible {
        border-color: #e6e9ed !important;
        background: #f7f8fa !important;
        color: #344054 !important;
    }

    /* Context bar */
    #sariSellerAiPanel .sari-seller-ai-context-bar {
        grid-template-columns: 28px minmax(0, 1fr) !important;
        min-height: 46px !important;
        padding: 7px 12px !important;
        background: #fbfcfd !important;
        border-bottom-color: #edf0f3 !important;
    }

    #sariSellerAiPanel .sari-seller-ai-context-icon {
        width: 28px !important;
        height: 28px !important;
        border-color: #e4e7ec !important;
        border-radius: 8px !important;
        background: #fff !important;
        color: #7c8592 !important;
    }

    #sariSellerAiPanel .sari-seller-ai-context-copy small {
        color: #a0a7b2 !important;
        font-size: 6.2px !important;
        font-weight: 700 !important;
        letter-spacing: .07em !important;
    }

    #sariSellerAiPanel .sari-seller-ai-context-copy strong {
        margin-top: 1px !important;
        color: #475467 !important;
        font-size: 8.2px !important;
        font-weight: 700 !important;
    }

    #sariSellerAiPanel .sari-seller-ai-live-pill {
        display: none !important;
    }

    /* Conversation */
    #sariSellerAiMessages {
        background: #f8fafb !important;
        padding: 12px 11px 14px !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message {
        margin-top: 9px !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message-label {
        margin: 0 0 4px 35px !important;
        color: #9aa3af !important;
        font-size: 6.2px !important;
        font-weight: 750 !important;
        letter-spacing: .07em !important;
    }

    #sariSellerAiMessages .sari-seller-ai-mini-avatar {
        width: 26px !important;
        height: 26px !important;
        flex-basis: 26px !important;
    }

    #sariSellerAiMessages .sari-seller-ai-mini-avatar img {
        width: 26px !important;
        height: 26px !important;
        max-width: 26px !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--assistant {
        grid-template-columns: 26px minmax(0, 1fr) !important;
        gap: 7px !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--assistant > .sari-seller-ai-bubble {
        max-width: 100% !important;
        border-color: #e5e8ec !important;
        border-radius: 12px 12px 12px 5px !important;
        background: #fff !important;
        padding: 8px 10px !important;
        color: #475467 !important;
        font-size: 8.7px !important;
        line-height: 1.55 !important;
        box-shadow: none !important;
    }

    #sariSellerAiMessages .sari-seller-ai-message--user > .sari-seller-ai-bubble {
        max-width: min(76%, 260px) !important;
        border-radius: 12px 12px 5px 12px !important;
        background: #d59617 !important;
        padding: 8px 10px !important;
        color: #fff !important;
        font-size: 8.7px !important;
        line-height: 1.5 !important;
        box-shadow: none !important;
    }

    /* Quick prompts */
    #sariSellerAiSuggestions {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 5px !important;
        padding: 7px 10px 3px !important;
        background: #fff !important;
    }

    #sariSellerAiSuggestions button {
        min-height: 28px !important;
        border-color: #e4e7ec !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 0 8px !important;
        color: #667085 !important;
        font-size: 7px !important;
        font-weight: 650 !important;
    }

    #sariSellerAiSuggestions button:hover,
    #sariSellerAiSuggestions button:focus-visible {
        border-color: #dfc68e !important;
        background: #fffaf0 !important;
        color: #9b6505 !important;
    }

    /* Composer */
    #sariSellerAiPanel .sari-seller-ai-composer-wrap {
        background: #fff !important;
        border-top: 1px solid #edf0f3 !important;
    }

    #sariSellerAiForm {
        padding: 8px 10px 4px !important;
        gap: 7px !important;
    }

    #sariSellerAiInput {
        min-height: 40px !important;
        max-height: 92px !important;
        border-color: #dde2e7 !important;
        border-radius: 11px !important;
        padding: 10px 11px !important;
        background: #fff !important;
        color: #344054 !important;
        font-size: 8.9px !important;
        box-shadow: none !important;
    }

    #sariSellerAiInput:focus {
        border-color: #d59617 !important;
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .07) !important;
    }

    #sariSellerAiSend {
        flex-basis: 40px !important;
        width: 40px !important;
        min-width: 40px !important;
        max-width: 40px !important;
        height: 40px !important;
        border-radius: 10px !important;
        box-shadow: none !important;
    }

    #sariSellerAiPanel .sari-seller-ai-composer-meta {
        padding: 0 11px 6px !important;
        color: #b0b7c3 !important;
        font-size: 5.9px !important;
    }

    #sariSellerAiPanel .sari-seller-ai-footer {
        padding: 7px 10px !important;
        border-top-color: #edf0f3 !important;
        background: #fcfcfd !important;
        color: #9aa3af !important;
        font-size: 6.2px !important;
    }

    @media (max-width: 640px) {
        #sariSellerAiPanel .sari-seller-ai-header {
            min-height: 62px !important;
        }

        #sariSellerAiMessages .sari-seller-ai-message--user > .sari-seller-ai-bubble {
            max-width: 82% !important;
        }
    }

</style>

<script data-navigate-once>
(function () {
    if (window.__SARI_SELLER_AI_ASSISTANT_V2__) return;
    window.__SARI_SELLER_AI_ASSISTANT_V2__ = true;

    const root = document.getElementById('sariSellerAi');
    if (!root) return;

    const launcher = document.getElementById('sariSellerAiLauncher');
    const panel = document.getElementById('sariSellerAiPanel');
    const closeButton = document.getElementById('sariSellerAiClose');
    const clearButton = document.getElementById('sariSellerAiClear');
    const messagesEl = document.getElementById('sariSellerAiMessages');
    const welcomeEl = document.getElementById('sariSellerAiWelcome');
    const welcomeActionsEl = document.getElementById('sariSellerAiWelcomeActions');
    const suggestionsEl = document.getElementById('sariSellerAiSuggestions');
    const pageLabelEl = document.getElementById('sariSellerAiPageLabel');
    const form = document.getElementById('sariSellerAiForm');
    const input = document.getElementById('sariSellerAiInput');
    const sendButton = document.getElementById('sariSellerAiSend');
    const errorEl = document.getElementById('sariSellerAiError');

    const endpoint = root.dataset.endpoint || '';
    const sellerId = root.dataset.sellerId || 'seller';
    const storageKey = `sari:seller-ai:v2:${sellerId}:history`;
    const openKey = `sari:seller-ai:v2:${sellerId}:open`;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
        || form?.querySelector('input[name="_token"]')?.value
        || '';

    const arrowIcon = `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M5 12h14"></path>
            <path d="m14 7 5 5-5 5"></path>
        </svg>`;

    const pageDefinitions = [
        {
            key: 'add-product',
            test: path => path.includes('/seller/products/create'),
            title: 'Add Product',
            prompts: [
                'Explain this page.',
                'What do I need before publishing a product?',
                'How should I set price and stock?',
                'What makes a good product listing?',
            ],
        },
        {
            key: 'archived',
            test: path => path.includes('/seller/products/archived'),
            title: 'Archived Products',
            prompts: [
                'Explain this page.',
                'What are archived products?',
                'How should I manage old listings?',
                'Summarize my catalog status.',
            ],
        },
        {
            key: 'products',
            test: path => path.includes('/seller/products'),
            title: 'Product Management',
            prompts: [
                'What is my best-selling product?',
                'Summarize my catalog.',
                'How many products are under review?',
                'What should I improve in my listings?',
            ],
        },
        {
            key: 'inventory',
            test: path => path.includes('/seller/inventory'),
            title: 'Inventory',
            prompts: [
                'What should I restock?',
                'How many products are low stock?',
                'Do I have out-of-stock products?',
                'Explain this page.',
            ],
        },
        {
            key: 'orders',
            test: path => path.includes('/seller/orders'),
            title: 'Order Management',
            prompts: [
                'How many orders need my attention?',
                'Summarize my order status.',
                'What should I process next?',
                'Explain this page.',
            ],
        },
        {
            key: 'shipping',
            test: path => path.includes('/seller/shipping'),
            title: 'Shipping',
            prompts: [
                'Summarize my fulfillment status.',
                'How many orders are still in progress?',
                'Explain this page.',
                'What should I check before pickup?',
            ],
        },
        {
            key: 'returns',
            test: path => path.includes('/seller/returns'),
            title: 'Returns & Refunds',
            prompts: [
                'Explain this page.',
                'How does the Seller return flow work?',
                'What happens after an item is returned?',
                'How are refunds recorded?',
            ],
        },
        {
            key: 'vouchers',
            test: path => path.includes('/seller/vouchers'),
            title: 'Promotions & Vouchers',
            prompts: [
                'How many vouchers are active?',
                'Explain this page.',
                'How should I use a voucher?',
                'What should I consider for a discount campaign?',
            ],
        },
        {
            key: 'finance',
            test: path => path.includes('/seller/finance'),
            title: 'Finance & Earnings',
            prompts: [
                'Summarize my earnings.',
                'How much is pending settlement?',
                'Explain this page.',
                'What does net revenue mean here?',
            ],
        },
        {
            key: 'reviews',
            test: path => path.includes('/seller/reviews'),
            title: 'Reviews & Ratings',
            prompts: [
                'What is my current rating?',
                'Summarize my reviews.',
                'Explain this page.',
                'How should I reply to Buyer reviews?',
            ],
        },
        {
            key: 'reports',
            test: path => path.includes('/seller/reports'),
            title: 'Generate Report',
            prompts: [
                'Summarize my store performance.',
                'What is my best-selling product?',
                'Explain this page.',
                'What should I look for in my reports?',
            ],
        },
        {
            key: 'messaging',
            test: path => path.includes('/seller/messages'),
            title: 'Chat / Messaging',
            prompts: [
                'What is the difference between you and Admin Support?',
                'Explain this page.',
                'When should I contact SARI Admin?',
                'What can you help me with?',
            ],
        },
        {
            key: 'dashboard',
            test: path => path.includes('/seller/dashboard') || path === '/seller',
            title: 'Dashboard Overview',
            prompts: [
                'Summarize my store performance.',
                'What needs my attention today?',
                'What is my best-selling product?',
                'What should I restock?',
            ],
        },
    ];

    const defaultPage = {
        key: 'workspace',
        title: 'Seller workspace',
        prompts: [
            'Summarize my store performance.',
            'What needs my attention today?',
            'What is my best-selling product?',
            'Explain this page.',
        ],
    };

    let history = readHistory();
    let busy = false;
    let activeRequest = null;
    let currentPage = resolvePageContext();

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function readHistory() {
        try {
            const parsed = JSON.parse(
                window.localStorage.getItem(storageKey) || '[]'
            );

            if (!Array.isArray(parsed)) return [];

            return parsed
                .filter(item =>
                    item
                    && ['user', 'assistant'].includes(item.role)
                    && typeof item.content === 'string'
                )
                .slice(-20);
        } catch (_) {
            return [];
        }
    }

    function saveHistory() {
        try {
            window.localStorage.setItem(
                storageKey,
                JSON.stringify(history.slice(-20))
            );
        } catch (_) {}
    }

    function resolvePageContext() {
        const path = window.location.pathname || '/';
        const definition = pageDefinitions.find(item => item.test(path))
            || defaultPage;

        return {
            key: definition.key,
            title: definition.title,
            path,
            prompts: [...definition.prompts],
        };
    }

    function updatePageContext() {
        currentPage = resolvePageContext();

        if (pageLabelEl) {
            pageLabelEl.textContent = currentPage.title;
        }

        renderSuggestions();
        renderWelcomeActions();
    }

    function setError(message = '') {
        if (!errorEl) return;
        errorEl.textContent = message;
    }

    function formatAssistantContent(value) {
        let html = escapeHtml(value ?? '');

        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/(^|\s)\*(?!\*)([^*\n]+)\*(?=\s|$)/g, '$1<em>$2</em>');

        const blocks = html
            .split(/\n{2,}/)
            .map(part => part.trim())
            .filter(Boolean);

        if (!blocks.length) {
            return '';
        }

        return blocks.map(part => `<p>${part.replace(/\n/g, '<br>')}</p>`).join('');
    }

    function messageHtml(message) {
        if (message.role === 'user') {
            return `
                <div class="sari-seller-ai-message sari-seller-ai-message--user">
                    <div class="sari-seller-ai-bubble">${escapeHtml(message.content)}</div>
                </div>`;
        }

        return `
            <div>
                <span class="sari-seller-ai-message-label">SARI AI</span>

                <div class="sari-seller-ai-message sari-seller-ai-message--assistant">
                    <span class="sari-seller-ai-mini-avatar" aria-hidden="true">
                        <img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="">
                    </span>

                    <div class="sari-seller-ai-bubble">${formatAssistantContent(message.content)}</div>
                </div>
            </div>`;
    }

    function render() {
        if (!messagesEl) return;

        if (!history.length) {
            if (welcomeEl) welcomeEl.hidden = false;

            messagesEl.innerHTML = '';
            if (welcomeEl) messagesEl.appendChild(welcomeEl);
            renderWelcomeActions();
            return;
        }

        messagesEl.innerHTML = history.map(messageHtml).join('');

        requestAnimationFrame(() => {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        });
    }

    function suggestionButton(prompt, welcome = false) {
        if (welcome) {
            return `
                <button type="button" data-sari-ai-suggestion="${escapeHtml(prompt)}">
                    <span>${escapeHtml(prompt)}</span>
                    ${arrowIcon}
                </button>`;
        }

        return `
            <button type="button" data-sari-ai-suggestion="${escapeHtml(prompt)}">
                ${escapeHtml(prompt)}
            </button>`;
    }

    function renderSuggestions() {
        if (!suggestionsEl) return;

        if (!history.length) {
            suggestionsEl.innerHTML = '';
            return;
        }

        suggestionsEl.innerHTML = currentPage.prompts
            .slice(0, 2)
            .map(prompt => suggestionButton(prompt))
            .join('');
    }

    function renderWelcomeActions() {
        if (!welcomeActionsEl || history.length) return;

        welcomeActionsEl.innerHTML = currentPage.prompts
            .slice(0, 4)
            .map(prompt => suggestionButton(prompt, true))
            .join('');
    }

    function showTyping() {
        if (!messagesEl || document.getElementById('sariSellerAiTyping')) {
            return;
        }

        if (welcomeEl) welcomeEl.hidden = true;

        messagesEl.insertAdjacentHTML('beforeend', `
            <div id="sariSellerAiTyping" class="sari-seller-ai-typing-wrap">
                <span class="sari-seller-ai-mini-avatar" aria-hidden="true">
                    <img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="">
                </span>

                <div class="sari-seller-ai-typing-content">
                    <span class="sari-seller-ai-typing-label">Thinking with your Seller data</span>

                    <div class="sari-seller-ai-typing" aria-label="SARI Seller AI is thinking">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        `);

        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function hideTyping() {
        document.getElementById('sariSellerAiTyping')?.remove();
    }

    function setOpen(open) {
        root.classList.toggle('is-open', open);
        panel?.setAttribute('aria-hidden', open ? 'false' : 'true');
        launcher?.setAttribute('aria-expanded', open ? 'true' : 'false');

        try {
            window.localStorage.setItem(openKey, open ? '1' : '0');
        } catch (_) {}

        if (open) {
            updatePageContext();
            render();
            window.setTimeout(() => input?.focus(), 80);
        }
    }

    function resizeInput() {
        if (!input) return;

        input.style.height = 'auto';
        input.style.height = `${Math.min(
            104,
            Math.max(42, input.scrollHeight)
        )}px`;
    }

    async function ask(question) {
        const content = String(question || '').trim();

        if (!content || busy || !endpoint) return;

        busy = true;
        setError('');
        updatePageContext();

        const previousHistory = history.slice(-10);

        history.push({
            role: 'user',
            content,
        });

        history = history.slice(-20);
        saveHistory();
        render();
        renderSuggestions();

        if (input) {
            input.value = '';
            resizeInput();
        }

        if (sendButton) sendButton.disabled = true;
        showTyping();

        activeRequest?.abort();
        activeRequest = new AbortController();

        const timeout = window.setTimeout(
            () => activeRequest?.abort(),
            16000
        );

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                cache: 'no-store',
                signal: activeRequest.signal,
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message: content,
                    history: previousHistory,
                    page_context: {
                        key: currentPage.key,
                        title: currentPage.title,
                        path: currentPage.path,
                    },
                }),
            });

            const raw = await response.text();
            let data = {};

            try {
                data = raw ? JSON.parse(raw) : {};
            } catch (_) {}

            if (!response.ok) {
                const validationMessage =
                    Object.values(data.errors || {})?.[0]?.[0];
                const serverMessage = String(data.message || '').trim();
                const isGenericServerError =
                    /^server error$/i.test(serverMessage)
                    || /^internal server error$/i.test(serverMessage);

                throw new Error(
                    validationMessage
                    || (
                        isGenericServerError
                            ? 'SARI AI hit a temporary service issue. Please try again.'
                            : (
                                serverMessage
                                || 'SARI AI could not respond.'
                            )
                    )
                );
            }

            const reply = String(data.reply || '').trim();

            if (!reply) {
                throw new Error('SARI AI returned an empty response.');
            }

            history.push({
                role: 'assistant',
                content: reply,
            });

            history = history.slice(-20);
            saveHistory();
            render();
            renderSuggestions();
        } catch (error) {
            if (error?.name === 'AbortError') {
                setError(
                    'SARI AI is taking longer than expected. Please try again.'
                );
            } else {
                setError(
                    error?.message
                    || 'SARI AI is temporarily unavailable. Your Seller workspace is still available.'
                );
            }
        } finally {
            window.clearTimeout(timeout);
            activeRequest = null;
            hideTyping();
            busy = false;

            if (sendButton) sendButton.disabled = false;
            input?.focus();
        }
    }

    launcher?.addEventListener('click', () => setOpen(true));
    closeButton?.addEventListener('click', () => setOpen(false));

    clearButton?.addEventListener('click', () => {
        if (busy) return;

        history = [];
        saveHistory();
        setError('');
        updatePageContext();
        render();
        input?.focus();
    });

    form?.addEventListener('submit', event => {
        event.preventDefault();
        ask(input?.value || '');
    });

    input?.addEventListener('input', resizeInput);

    input?.addEventListener('keydown', event => {
        if (
            event.key === 'Enter'
            && !event.shiftKey
            && !event.isComposing
        ) {
            event.preventDefault();
            form?.requestSubmit();
        }
    });

    function handleSuggestionClick(event) {
        const button = event.target.closest('[data-sari-ai-suggestion]');
        if (!button) return;

        ask(button.dataset.sariAiSuggestion || '');
    }

    suggestionsEl?.addEventListener('click', handleSuggestionClick);
    welcomeActionsEl?.addEventListener('click', handleSuggestionClick);

    document.addEventListener('keydown', event => {
        if (
            event.key === 'Escape'
            && root.classList.contains('is-open')
        ) {
            setOpen(false);
            launcher?.focus();
        }
    });

    document.addEventListener('livewire:navigating', () => {
        if (activeRequest) {
            activeRequest.abort();
            activeRequest = null;
        }

        busy = false;
        hideTyping();

        if (sendButton) sendButton.disabled = false;
    });

    document.addEventListener('livewire:navigated', () => {
        updatePageContext();

        if (root.classList.contains('is-open')) {
            render();
        }
    });

    updatePageContext();
    render();
    resizeInput();

    try {
        if (window.localStorage.getItem(openKey) === '1') {
            setOpen(true);
        }
    } catch (_) {}
})();
</script>
