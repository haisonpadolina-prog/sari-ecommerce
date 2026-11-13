@if(config('admin_ai.enabled'))
<div id="sariAdminAi" class="sari-admin-ai" data-base="{{ url('/admin/ai') }}" data-robot="{{ asset('images/sari-seller-ai-assistant.png') }}">
    <button type="button" class="sai-launch sari-admin-ai-launcher" aria-label="Open SARI Admin AI" data-i18n-aria="launch" aria-expanded="false" aria-controls="saiPanel">
        <span class="sari-admin-ai-launcher-shell"><img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" class="sari-admin-ai-launcher-image" draggable="false"></span>
        <span class="sari-admin-ai-launcher-online" aria-hidden="true"></span>
        <span class="sari-admin-ai-launcher-callout" aria-hidden="true"><strong data-i18n="ask">Ask SARI AI</strong><small data-i18n="workspace">Admin workspace assistant</small></span>
    </button>
    <section id="saiPanel" class="sai-panel sari-admin-ai-panel" role="dialog" aria-modal="false" aria-labelledby="saiTitle" hidden>
        <header class="sari-admin-ai-header">
            <div class="sari-admin-ai-identity">
                <div class="sari-admin-ai-avatar"><img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" draggable="false"></div>
                <div class="sari-admin-ai-identity-copy"><strong id="saiTitle">SARI</strong><small><span class="sari-admin-ai-status-dot" aria-hidden="true"></span><span data-i18n="workspace">Admin workspace assistant</span></small></div>
            </div>
            <div class="sari-admin-ai-header-actions">
                <button type="button" data-ai-expand class="sari-admin-ai-icon-button" data-i18n-aria="expand" data-i18n-title="expand" aria-label="Expand chat" aria-pressed="false" title="Expand chat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M8 3H3v5m13-5h5v5M3 16v5h5m13-5v5h-5"/></svg></button>
                <button type="button" data-ai-clear data-i18n-aria="clear" data-i18n-title="clear" class="sari-admin-ai-icon-button" aria-label="Start a new AI conversation" title="New conversation"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></button>
                <button type="button" data-ai-close data-i18n-aria="minimize" data-i18n-title="minimize" class="sari-admin-ai-icon-button" aria-label="Minimize SARI Admin AI" title="Minimize"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 12h12"/></svg></button>
            </div>
        </header>
        <div class="sai-toolbar">
            <button type="button" class="sai-language-trigger" data-ai-language-open aria-haspopup="dialog" aria-controls="saiLanguageDialog" aria-expanded="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c5 5 5 13 0 18-5-5-5-13 0-18Z"/></svg><span data-ai-language-label>Auto</span><span aria-hidden="true">⌄</span><span class="sai-sr" data-i18n="language">Language</span></button>
            <select data-ai-language hidden aria-label="Language"><option value="auto">Auto</option><option>Tagalog</option><option>English</option><option>Taglish</option></select>
            <button type="button" class="sai-priorities" data-ai-alerts><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M9 6h12M9 12h12M9 18h12M3 6h1M3 12h1M3 18h1"/></svg><span data-i18n="alerts">Priorities</span></button>
        </div>
        <details class="sai-tools" data-ai-tools>
            <summary data-i18n="admin_tools">Admin tools</summary>
            <div class="sai-tool-grid">
                <button type="button" data-ai-operation="action_center" data-i18n="action_center">Action Center</button>
                <button type="button" data-ai-operation="products" data-i18n="bulk_review">Bulk Product Review</button>
                <button type="button" data-ai-operation="logistics_exceptions" data-i18n="logistics_exceptions">Logistics Exceptions</button>
                <button type="button" data-ai-operation="complaints" data-i18n="complaints">Complaints</button>
                <button type="button" data-ai-operation="returns" data-i18n="returns">Returns & Refunds</button>
                <button type="button" data-ai-operation="cod_remittances" data-i18n="cod_remittances">COD Remittances</button>
                <button type="button" data-ai-operation="seller_performance" data-i18n="seller_performance">Seller Performance</button>
                <button type="button" data-ai-operation="action_history" data-i18n="action_history">AI Action History</button>
                <button type="button" data-ai-operation="permissions" data-i18n="permissions">My AI Permissions</button>
                <button type="button" data-ai-operation="admin_permissions" data-i18n="admin_permissions">Admin AI Roles</button>
            </div>
            <div class="sai-tool-dates"><label><span data-i18n="from">From</span><input type="date" data-ai-from></label><label><span data-i18n="to">To</span><input type="date" data-ai-to></label></div>
        </details>
        <p class="sai-status sai-sr" data-ai-status role="status">Ready when you are</p>
        <div class="sai-language-overlay" data-ai-language-overlay hidden>
            <section id="saiLanguageDialog" class="sai-language-dialog" role="dialog" aria-modal="true" aria-labelledby="saiLanguageTitle" aria-describedby="saiLanguageDescription" tabindex="-1">
                <header><div><span class="sai-language-eyebrow">SARI AI</span><h2 id="saiLanguageTitle" data-i18n="choose_language">Choose your language</h2></div><button type="button" class="sari-admin-ai-icon-button" data-ai-language-cancel data-i18n-aria="close_language" aria-label="Close language settings">✕</button></header>
                <p id="saiLanguageDescription" data-i18n="language_description">Choose how SARI replies and displays your conversation.</p>
                <fieldset class="sai-language-options"><legend class="sai-sr" data-i18n="language">Language</legend>
                    <label class="sai-language-option"><input type="radio" name="saiLanguageChoice" value="auto"><span><strong data-i18n="language_auto">Auto</strong><small data-i18n="language_auto_hint">Match the language of each message</small></span><span class="sai-language-check" aria-hidden="true">✓</span></label>
                    <label class="sai-language-option"><input type="radio" name="saiLanguageChoice" value="English"><span><strong data-i18n="language_english">English</strong><small data-i18n="language_english_hint">Replies and conversation in English</small></span><span class="sai-language-check" aria-hidden="true">✓</span></label>
                    <label class="sai-language-option"><input type="radio" name="saiLanguageChoice" value="Tagalog"><span><strong data-i18n="language_tagalog">Tagalog</strong><small data-i18n="language_tagalog_hint">Mga sagot at usapan sa Tagalog</small></span><span class="sai-language-check" aria-hidden="true">✓</span></label>
                    <label class="sai-language-option"><input type="radio" name="saiLanguageChoice" value="Taglish"><span><strong data-i18n="language_taglish">Taglish</strong><small data-i18n="language_taglish_hint">A natural mix of English and Tagalog</small></span><span class="sai-language-check" aria-hidden="true">✓</span></label>
                </fieldset>
                <footer><button type="button" class="sai-language-cancel" data-ai-language-cancel data-i18n="cancel">Cancel</button><button type="button" class="sai-language-save" data-ai-language-save data-i18n="save_language">Save language</button></footer>
            </section>
        </div>
        <div class="sai-feed sari-admin-ai-messages" data-ai-feed role="log" aria-label="Assistant conversation" aria-live="polite" aria-relevant="additions">
            <section class="sai-welcome sari-admin-ai-welcome">
                <div class="sari-admin-ai-welcome-visual"><img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" draggable="false"></div>
                <div class="sari-admin-ai-welcome-copy"><span class="sari-admin-ai-welcome-kicker" data-i18n="kicker">YOUR ADMIN COPILOT</span><h2 data-i18n="welcome">What can we work on?</h2><p data-i18n="intro">A clearer view of your marketplace. Ask, explore, and get things done.</p></div>
                <div class="sari-admin-ai-welcome-actions">
                    <button type="button" data-ai-query="query_priorities"><span class="sai-prompt-icon" aria-hidden="true">01</span><span data-i18n="prompt_priorities">Review today’s priorities</span><span aria-hidden="true">↗</span></button>
                    <button type="button" data-ai-query="query_products"><span class="sai-prompt-icon" aria-hidden="true">02</span><span data-i18n="prompt_products">Find low-stock products</span><span aria-hidden="true">↗</span></button>
                    <button type="button" data-ai-query="query_graph"><span class="sai-prompt-icon" aria-hidden="true">03</span><span data-i18n="prompt_graph">Explore sales performance</span><span aria-hidden="true">↗</span></button>
                    <button type="button" data-ai-query="query_hubs"><span class="sai-prompt-icon" aria-hidden="true">04</span><span data-i18n="prompt_hubs">Find logistics hubs</span><span aria-hidden="true">↗</span></button>
                </div>
            </section>
        </div>
        <div class="sari-admin-ai-composer-wrap">
            <form class="sai-compose sari-admin-ai-composer" data-ai-form>
                <label class="sai-sr" for="saiMessage" data-i18n="input_label">Message to SARI assistant</label>
                <textarea id="saiMessage" rows="1" maxlength="3000" placeholder="Ask a question or give an instruction…" data-i18n-placeholder="placeholder" required></textarea>
                <button type="submit" class="sari-admin-ai-send" data-ai-send data-i18n-aria="send" aria-label="Send message"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="m4 4 17 8-17 8 3-8-3-8ZM7 12h14"/></svg></button>
            </form>
            <p class="sai-note" data-i18n="note">You’re in control. Review every action before confirming.</p>
        </div>
    </section>
</div>
@endif
