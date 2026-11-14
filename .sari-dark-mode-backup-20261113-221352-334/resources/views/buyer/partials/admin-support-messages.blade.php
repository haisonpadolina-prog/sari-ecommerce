{{-- Buyer-only admin support and complaint conversations. Uses the existing /messaging/api endpoints. --}}
<style>
.sari-admin-support{--gold:#c88912;--line:#e5e9ef;--ink:#263242;--muted:#697789;max-width:1320px;margin:0 auto;padding:22px;font-family:Poppins,ui-sans-serif,system-ui,sans-serif;color:var(--ink)}
.sari-admin-support *{box-sizing:border-box}
.sari-admin-support a{text-decoration:none}
.sari-as-header{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:18px;flex-wrap:wrap}
.sari-as-kicker{color:#a97516;font-size:11px;letter-spacing:.11em;text-transform:uppercase;font-weight:750}
.sari-as-title{font-size:23px;font-weight:750;letter-spacing:-.025em;margin:4px 0;color:#27313f}
.sari-as-subtitle{font-size:12.5px;color:var(--muted);margin:0}
.sari-as-back{display:inline-flex;align-items:center;min-height:40px;padding:0 16px;border:1px solid var(--line);border-radius:11px;background:#fff;color:#4b5563;font-size:12px;font-weight:700}
.sari-as-shell{display:grid;grid-template-columns:minmax(235px,310px) minmax(0,1fr);min-height:min(650px,calc(100dvh - 230px));border:1px solid var(--line);border-radius:18px;overflow:hidden;background:#fff;box-shadow:0 9px 27px rgba(20,32,46,.045)}
.sari-as-side{padding:20px;border-right:1px solid var(--line);background:#fafbfc}
.sari-as-nav{display:grid;gap:9px}
.sari-as-tab{display:flex;align-items:center;gap:10px;padding:14px 13px;border:1px solid transparent;border-radius:12px;color:#566172;background:transparent;font:650 12.5px Poppins,sans-serif;text-align:left;cursor:pointer;text-decoration:none}
.sari-as-tab.is-active,.sari-as-tab:hover{border-color:#ebd8b2;background:#fffaf0;color:#93630d}
.sari-as-side h3{font-size:12px;color:#64748b;margin:25px 0 10px}
.sari-as-history{display:grid;gap:7px;max-height:230px;overflow-y:auto}
.sari-as-history button{border:1px solid var(--line);background:#fff;border-radius:11px;padding:11px;text-align:left;cursor:pointer;font:600 11.5px/1.5 Poppins,sans-serif;color:#475569}
.sari-as-history button.is-selected{border-color:#d6ac61;background:#fffbf2}
.sari-as-history small{display:block;font-size:10px;color:#8491a0;margin-top:3px}
.sari-as-chat{display:flex;flex-direction:column;min-height:440px;min-width:0;background:#f7f8fa}
.sari-as-chat-head{padding:17px 19px;border-bottom:1px solid var(--line);background:#fff}
.sari-as-chat-head strong{font-size:15px;color:#27313f}
.sari-as-chat-head p{font-size:11px;color:var(--muted);margin:4px 0 0}
.sari-as-messages{flex:1;min-height:190px;max-height:470px;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:13px}
.sari-as-bubble{max-width:84%;padding:12px 15px;border:1px solid var(--line);border-radius:15px 15px 15px 5px;background:#fff;color:#344054;font-size:12.5px;line-height:1.7;white-space:pre-wrap;overflow-wrap:anywhere;align-self:flex-start}
.sari-as-bubble.is-mine{align-self:flex-end;border-radius:15px 15px 5px 15px;border-color:#ead9b8;background:#fffbf3}
.sari-as-bubble small{display:block;color:#8a94a1;margin-top:7px;font-size:10px}
.sari-as-hint{align-self:center;margin:auto;color:#748195;line-height:1.7;text-align:center;font-size:12.5px}
.sari-as-message-form{display:flex;gap:10px;padding:13px 16px;border-top:1px solid var(--line);background:#fff}
.sari-as-message-form textarea{flex:1;min-width:0;min-height:46px;max-height:150px;resize:vertical;padding:12px 14px;border:1px solid #d9e0e7;border-radius:12px;background:#fff;font:12px/1.55 Poppins,sans-serif;color:#334155}
.sari-as-button{padding:11px 18px;min-height:43px;border:0;border-radius:11px;background:#c88912;color:#fff;font:700 12px Poppins,sans-serif;cursor:pointer}
.sari-as-button:disabled{opacity:.6;cursor:wait}
.sari-as-report-form{display:grid;gap:12px;padding:17px;background:#fff;border-bottom:1px solid var(--line)}
.sari-as-report-form label{display:grid;gap:6px;color:#546173;font-size:12px;font-weight:700}
.sari-as-report-form input,.sari-as-report-form textarea{width:100%;padding:12px;border:1px solid #d9e0e7;border-radius:11px;background:#fff;font:12.5px/1.6 Poppins,sans-serif;color:#334155}
.sari-as-report-form textarea{min-height:85px;resize:vertical}
.sari-as-report-form p{margin:0;color:#64748b;font-size:11px}
.sari-as-alert{margin:9px 0 0;padding:11px 13px;border:1px solid #edd4aa;border-radius:11px;background:#fffaf0;color:#86580f;font-size:12px}
.sari-as-alert.is-error{border-color:#e5b6b6;background:#fff6f5;color:#a63737}
.sari-as-alert[hidden],.sari-as-report-form[hidden],.sari-as-message-form[hidden]{display:none!important}
@media(max-width:790px){.sari-admin-support{padding:12px}.sari-as-shell{grid-template-columns:1fr}.sari-as-side{border-right:0;border-bottom:1px solid var(--line);padding:12px}.sari-as-nav{display:flex}.sari-as-tab{flex:1;justify-content:center;padding:10px}.sari-as-side h3,.sari-as-history{display:none}.sari-as-messages{min-height:260px;max-height:52vh}.sari-as-header{align-items:flex-start}.sari-as-title{font-size:20px}}
@media(prefers-reduced-motion:reduce){.sari-admin-support *{scroll-behavior:auto!important}}
</style>
<div id="sariBuyerAdminSupport" class="sari-admin-support" data-mode="{{ request()->query('channel') === 'report' ? 'report' : 'support' }}"
    data-conversation="{{ request()->query('conversation', '') }}"
    data-open-support="{{ route('messaging.api.support') }}"
    data-open-report-template="{{ route('messaging.api.report-support',['complaint'=>'__COMPLAINT__']) }}"
    data-list="{{ route('messaging.api.connections') }}"
    data-details-template="{{ route('messaging.api.show',['conversation'=>'__CONVERSATION__']) }}"
    data-send-template="{{ route('messaging.api.send',['conversation'=>'__CONVERSATION__']) }}"
    data-new-report="{{ session('submitted_complaint_id') ? (int)session('submitted_complaint_id') : '' }}">
    <header class="sari-as-header"><div><span class="sari-as-kicker">SARI Marketplace</span><h1 class="sari-as-title">Messages · Admin Support</h1><p class="sari-as-subtitle">Private conversations with the SARI Administration team</p></div><a class="sari-as-back" href="{{ route('buyer.messages') }}">← Store messages</a></header>
    <div class="sari-as-shell">
        <aside class="sari-as-side" aria-label="Support conversations"><nav class="sari-as-nav">
            <a href="{{ route('buyer.messages',['channel'=>'support']) }}" class="sari-as-tab {{ request()->query('channel') === 'support' ? 'is-active' : '' }}">Chat with Support</a>
            <a href="{{ route('buyer.messages',['channel'=>'report']) }}" class="sari-as-tab {{ request()->query('channel') === 'report' ? 'is-active' : '' }}">Report an Issue</a>
        </nav><h3>My submitted reports</h3><div id="sariAsReportHistory" class="sari-as-history"><p class="sari-as-subtitle">Loading your reports…</p></div></aside>
        <section class="sari-as-chat" aria-label="SARI Admin conversation">
            <div class="sari-as-chat-head"><strong id="sariAsThreadTitle">{{ request()->query('channel') === 'report' ? 'Report an Issue' : 'SARI Admin Support' }}</strong><p id="sariAsThreadSubtitle">Messages are visible to your account and authorized administrators.</p></div>
            @if(request()->query('channel') === 'report' && !request()->filled('conversation'))
                <form id="sariAsComplaintForm" class="sari-as-report-form" action="{{ route('platform.complaints.store') }}" method="POST">
                    @csrf
                    <label>Issue subject<input type="text" name="subject" maxlength="150" required value="{{ old('subject') }}" placeholder="Briefly describe your concern"></label>
                    <label>What happened?<textarea name="description" minlength="10" maxlength="3000" required placeholder="Describe the issue, order number, and what assistance you need.">{{ old('description') }}</textarea></label>
                    <p>Submitting creates a case in Admin Complaints and opens a linked conversation in Admin Messages.</p>
                    <button type="submit" class="sari-as-button">Submit issue to SARI Admin</button>
                </form>
            @endif
            <div id="sariAsFeedback" class="sari-as-alert" role="status" hidden></div>
            @if($errors->any())<div class="sari-as-alert is-error" role="alert">{{ $errors->first() }}</div>@endif
            @if(session('success'))<div class="sari-as-alert" role="status">{{ session('success') }}</div>@endif
            <div id="sariAsMessages" class="sari-as-messages" aria-live="polite"><p class="sari-as-hint">Connecting to SARI Admin…</p></div>
            <form id="sariAsReplyForm" class="sari-as-message-form" hidden>
                <label for="sariAsReplyText" class="sr-only">Message to SARI Admin</label>
                <textarea id="sariAsReplyText" maxlength="3000" placeholder="Write a message to SARI Admin…" required></textarea>
                <button class="sari-as-button" type="submit">Send</button>
            </form>
        </section>
    </div>
</div>
