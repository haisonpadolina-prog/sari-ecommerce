@extends('layouts.admin')
@section('title', 'Security Center — SARI Admin')
@section('page-title', 'Security Center')
@section('content')
<div class="sari-security mx-auto w-full max-w-[1500px] pb-8">
    <style>
        .sari-security{--gold:#D59617;--line:#DFE3E8;--ink:#202124;--muted:#6E7985;font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;color:var(--ink)}
        .sec-head{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;padding-bottom:15px;border-bottom:1px solid var(--line)}
        .sec-eye{margin:0;color:#717B87;font-size:8px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}
        .sec-title{margin:5px 0 0;font-size:clamp(29px,2.2vw,37px);font-weight:700;line-height:1.06;letter-spacing:-.045em}.sec-title span{color:var(--gold)}
        .sec-copy{max-width:830px;margin:7px 0 0;color:var(--muted);font-size:9.5px;line-height:1.6}
        .sec-live{display:inline-flex;min-height:34px;align-items:center;gap:7px;border:1px solid #d8e6dc;border-radius:999px;background:#f5faf6;padding:0 11px;color:#4f765d;font-size:7.5px;font-weight:800;white-space:nowrap}.sec-live i{width:6px;height:6px;border-radius:999px;background:#55a16b;box-shadow:0 0 0 4px rgba(85,161,107,.1)}
        .sec-flash{margin-top:12px;border:1px solid #d7e7dc;border-radius:8px;background:#f5faf6;padding:9px 11px;color:#51715d;font-size:8.5px;font-weight:650}
        .sec-metrics{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:9px;margin-top:14px}.sec-metric{border:1px solid var(--line);border-radius:8px;background:#fff;padding:12px 13px;box-shadow:0 1px 3px rgba(15,23,42,.025)}.sec-metric span{display:block;color:#77828e;font-size:6.7px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}.sec-metric strong{display:block;margin-top:7px;font-size:19px;line-height:1;font-weight:750;color:#2a323b}.sec-metric.alert strong{color:#b64242}
        .sec-grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(360px,.85fr);gap:14px;margin-top:14px;align-items:start}.sec-stack{display:grid;gap:14px}
        .sec-panel{overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 3px 7px rgba(15,23,42,.035),0 14px 32px rgba(15,23,42,.055)}
        .sec-panel-head{display:flex;min-height:58px;align-items:center;justify-content:space-between;gap:12px;padding:11px 14px;border-bottom:1px solid #eceff2}.sec-panel-head h2{margin:0;font-size:11px;font-weight:750;color:#303844}.sec-panel-head p{margin:3px 0 0;font-size:7.8px;line-height:1.5;color:#7d8792}.sec-pill{display:inline-flex;min-height:23px;align-items:center;border:1px solid #e0e4e8;border-radius:999px;background:#f8f9fa;padding:0 8px;color:#68737e;font-size:6.6px;font-weight:800;white-space:nowrap}
        .sec-risk-list{display:grid;gap:8px;padding:12px}.sec-risk{display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:10px;align-items:start;border:1px solid #eceff2;border-radius:8px;padding:10px;background:#fcfcfd}.sec-risk.critical{border-color:#efc1c1;background:#fff7f7}.sec-risk.high{border-color:#ecd3c1;background:#fff9f5}.sec-risk.medium{border-color:#eadfbe;background:#fffcf3}.sec-score{display:grid;width:42px;height:42px;place-items:center;border-radius:50%;background:#f1f3f5;color:#58636e;font-size:9px;font-weight:800}.critical .sec-score{background:#ffe7e7;color:#a83f3f}.high .sec-score{background:#fff0e6;color:#995a32}.medium .sec-score{background:#fff5d9;color:#8a6a23}.sec-risk strong{display:block;font-size:8.5px;color:#343e48}.sec-risk span{display:block;margin-top:3px;font-size:7px;line-height:1.5;color:#7a8590}.sec-risk-meta{margin-top:5px!important;color:#9a8870!important}.sec-risk-link{display:inline-flex;height:29px;align-items:center;border:1px solid #d7dde3;border-radius:7px;background:#fff;padding:0 9px;color:#56616c;font-size:6.8px;font-weight:800;text-decoration:none;white-space:nowrap}.sec-sev{margin-bottom:5px;border-radius:999px;padding:4px 7px;font-size:5.8px;font-weight:800;text-align:center;text-transform:uppercase}.sec-sev.critical{background:#ffe7e7;color:#a73d3d}.sec-sev.high{background:#fff0e5;color:#9c5a31}.sec-sev.medium{background:#fff5d9;color:#8a6822}
        .sec-table-wrap{overflow:auto}.sec-table{width:100%;min-width:760px;border-collapse:collapse}.sec-table th{padding:9px 11px;border-bottom:1px solid #eceff2;background:#fafbfc;color:#717b87;font-size:6.5px;font-weight:800;letter-spacing:.06em;text-align:left;text-transform:uppercase;white-space:nowrap}.sec-table td{padding:10px 11px;border-bottom:1px solid #f0f2f4;color:#46505d;font-size:7.5px;line-height:1.45;vertical-align:middle}.sec-table tr:last-child td{border-bottom:0}.sec-name{font-size:8.2px;font-weight:750;color:#303943}.sec-sub{margin-top:2px;font-size:6.5px;color:#7d8792}.sec-btn{display:inline-flex;min-height:30px;align-items:center;border:1px solid #e1c4c4;border-radius:7px;background:#fff5f5;padding:0 9px;color:#a14d4d;font-size:6.7px;font-weight:800;cursor:pointer}.sec-current{color:#4e7b5e;font-size:6.8px;font-weight:800}
        .sec-audit{display:grid}.sec-event{display:grid;grid-template-columns:120px minmax(0,1fr);gap:12px;padding:11px 13px;border-bottom:1px solid #f0f2f4}.sec-event:last-child{border-bottom:0}.sec-time{font-size:6.8px;line-height:1.5;color:#84909b}.sec-event strong{display:block;font-size:8px;color:#37414b}.sec-event p{margin:3px 0 0;font-size:6.9px;line-height:1.5;color:#7a8590}.sec-diff{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-top:7px}.sec-diff div{border:1px solid #e6eaee;border-radius:7px;background:#fafbfc;padding:7px}.sec-diff b{display:block;margin-bottom:4px;font-size:5.8px;letter-spacing:.05em;text-transform:uppercase;color:#7b8691}.sec-diff pre{margin:0;white-space:pre-wrap;word-break:break-word;font:600 6px/1.45 'Poppins',sans-serif;color:#53606b}.sec-empty{padding:24px 14px;text-align:center;color:#7d8792;font-size:7.5px}
        @media(max-width:1180px){.sec-metrics{grid-template-columns:repeat(3,minmax(0,1fr))}.sec-grid{grid-template-columns:1fr}}@media(max-width:640px){.sec-head{align-items:stretch;flex-direction:column}.sec-metrics{grid-template-columns:1fr 1fr}.sec-event{grid-template-columns:1fr}.sec-diff{grid-template-columns:1fr}}
    
/* Match the compact monitoring screens. */
.sari-security {--line:#E4E7EC;--ink:#101828;--muted:#667085;}
.sari-security .sec-head {align-items:center;}
.sari-security .sec-metrics {gap:12px;}
.sari-security .sec-metric {position:relative;min-width:0;min-height:86px;padding:15px 16px;border-radius:12px;box-shadow:0 2px 6px rgba(16,24,40,.04);}
.sari-security .sec-metric > span:first-child {padding-right:34px;color:#667085;font-size:10px;font-weight:600;line-height:1.35;text-transform:none;letter-spacing:normal;}
.sari-security .sec-metric strong {margin-top:5px;font-size:24px;line-height:1.15;letter-spacing:-.035em;}
.sari-security .sec-metric > .sec-metric-icon {position:absolute;top:14px;right:16px;display:inline-flex;width:25px;height:25px;padding:0;border:0;background:transparent;border-radius:0;box-shadow:none;pointer-events:none;}
.sari-security .sec-metric-icon svg {display:block;width:25px;height:25px;}
.sari-security .sec-grid,.sari-security .sec-stack {min-width:0;}
.sari-security .sec-panel {border-radius:12px;box-shadow:0 2px 6px rgba(16,24,40,.04);}
.sari-security .sec-panel-head {padding:14px 16px;}
.sari-security .sec-panel-head h2 {font-size:12px;font-weight:600;color:#252A32;}
.sari-security .sec-panel-head p {font-size:9px;line-height:1.6;color:#667085;}
.sari-security .sec-copy {font-size:10px;}
.sari-security .sec-live,.sari-security .sec-pill {font-size:9px;font-weight:600;}
.sari-security .sec-risk {background:#fff;border-color:#E4E7EC;padding:12px;border-radius:10px;}
.sari-security .sec-risk:hover {background:#F3F4F6;}
.sari-security .sec-risk strong {font-size:11px;font-weight:600;}
.sari-security .sec-risk span {font-size:9px;line-height:1.6;}
.sari-security .sec-risk-meta {color:#667085!important;}
.sari-security .sec-sev {font-size:8px;}
.sari-security .sec-risk-link {justify-content:center;min-height:34px;height:auto;padding:8px 12px;border:1px solid var(--gold);border-radius:8px;background:var(--gold);color:#fff;font-size:10px;font-weight:600;}
.sari-security .sec-risk-link:hover {background:#B97805;border-color:#B97805;color:#fff;}
.sari-security .sec-table th {padding:11px 14px;font-size:9px;font-weight:600;background:#F8FAFC;}
.sari-security .sec-table td {padding:12px 14px;font-size:10px;}
.sari-security .sec-table tbody tr:hover td {background:#F3F4F6;}
.sari-security .sec-name {font-size:10px;font-weight:600;}
.sari-security .sec-sub {font-size:9px;}
.sari-security .sec-btn {min-height:34px;justify-content:center;padding:8px 12px;border:1px solid #DC2626;border-radius:8px;background:#DC2626;color:#fff;font-size:10px;font-weight:600;white-space:nowrap;}
.sari-security .sec-btn:hover {background:#B91C1C;border-color:#B91C1C;}
.sari-security .sec-current {font-size:9px;}
.sari-security .sec-event {padding:14px 16px;grid-template-columns:110px minmax(0,1fr);}
.sari-security .sec-time {font-size:9px;line-height:1.6;}
.sari-security .sec-event strong {font-size:10px;font-weight:600;}
.sari-security .sec-event p {font-size:9px;line-height:1.6;}
.sari-security .sec-diff b {font-size:8px;}
.sari-security .sec-diff pre {font-size:9px;line-height:1.6;}
.sari-security .sec-empty {font-size:10px;line-height:1.6;}
.sari-security .sec-panel > div[style] {font-size:10px!important;color:#667085!important;line-height:1.7!important;padding:16px!important;}
.sari-security :is(.sec-risk-link,.sec-btn):focus-visible {outline:2px solid #475467;outline-offset:3px;}
@media(max-width:640px) {.sari-security .sec-panel-head {flex-wrap:wrap;} .sari-security .sec-event {grid-template-columns:1fr;gap:6px;} .sari-security .sec-risk {grid-template-columns:auto minmax(0,1fr);} .sari-security .sec-risk > div:last-child {grid-column:2;justify-self:start;} }

.sari-security > #active-sessions {margin-top:14px;}
.sari-security #active-sessions .sec-table {min-width:0;width:100%;}
.sari-security #active-sessions .sec-table td {overflow-wrap:anywhere;}
.sari-security #active-sessions .sec-table th {white-space:normal;}
.sari-security .sec-event[hidden] {display:none!important;}
.sari-security .sec-time {overflow-wrap:anywhere;}
.sari-security .sec-audit-toggle {display:flex;justify-content:center;align-items:center;min-height:34px;width:calc(100% - 32px);margin:12px 16px 16px;padding:8px 12px;border:1px solid #E4E7EC;border-radius:8px;background:#fff;color:#344054;font:600 10px/1.4 'Poppins',sans-serif;cursor:pointer;}
.sari-security .sec-audit-toggle:hover {background:#F3F4F6;border-color:#CBD2DB;}
.sari-security .sec-audit-toggle:focus-visible {outline:2px solid #475467;outline-offset:3px;}
@media(max-width:640px) {
    .sari-security #active-sessions .sec-table thead {display:none;}
    .sari-security #active-sessions .sec-table tbody {display:block;}
    .sari-security #active-sessions .sec-table tr {display:grid;grid-template-columns:repeat(2,minmax(0,1fr));padding:12px;border-bottom:1px solid #ECEFF2;}
    .sari-security #active-sessions .sec-table td {display:block;min-width:0;padding:8px;border:0;}
    .sari-security #active-sessions .sec-table td::before {display:block;margin-bottom:4px;color:#667085;font-size:9px;font-weight:600;}
    .sari-security #active-sessions td:nth-child(1)::before {content:'Account';}
    .sari-security #active-sessions td:nth-child(2)::before {content:'Role';}
    .sari-security #active-sessions td:nth-child(3)::before {content:'Browser';}
    .sari-security #active-sessions td:nth-child(4)::before {content:'IP';}
    .sari-security #active-sessions td:nth-child(5)::before {content:'Last seen';}
    .sari-security #active-sessions td:nth-child(6)::before {content:'Session';}
    .sari-security #active-sessions td:last-child {grid-column:1/-1;}
}
</style>
    <div class="sec-head">
        <div>
            <p class="sec-eye">Trust &amp; Safety</p>
            <h1 class="sec-title">Security <span>Center</span></h1>
            <p class="sec-copy">Central view of authentication failures, risk signals, account restrictions, active database sessions, remote logout controls, and the existing Admin audit trail. Risk flags are rule-based indicators for human review; they do not automatically ban users.</p>
        </div>
        <span class="sec-live"><i></i>15-second critical alert watch</span>
    </div>
    @if(session('success'))<div class="sec-flash">{{ session('success') }}</div>@endif
    <div class="sec-metrics">
        <div class="sec-metric alert"><span>Critical alerts</span><strong>{{ number_format((int)($metrics['critical'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 10 18H2L12 3ZM12 9v5M12 17h.01"/></svg></span></div>
        <div class="sec-metric"><span>High-risk signals</span><strong>{{ number_format((int)($metrics['high_risk'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#B7791F" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 5 8 9 8 9s8-4 8-9V6l-8-3ZM12 8v5M12 16h.01"/></svg></span></div>
        <div class="sec-metric"><span>Failed logins · 24h</span><strong>{{ number_format((int)($metrics['failed_logins_24h'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#AC7A16" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg></span></div>
        <div class="sec-metric"><span>Blocked accounts</span><strong>{{ number_format((int)($metrics['blocked'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#7C3AED" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="7" r="3"/><path d="M3 21a7 7 0 0 1 12-5M16 16l6 6M22 16l-6 6"/></svg></span></div>
        <div class="sec-metric"><span>Suspended / banned</span><strong>{{ number_format((int)($metrics['suspended'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m6 6 12 12"/></svg></span></div>
        <div class="sec-metric"><span>Active sessions</span><strong>{{ number_format((int)($metrics['active_sessions'] ?? 0)) }}</strong><span class="sec-metric-icon" style="color:#16856A" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg></span></div>
    </div>
    
            <section class="sec-panel" id="active-sessions">
                <div class="sec-panel-head"><div><h2>Active account sessions</h2><p>Read directly from Laravel's database session store. Force Logout deletes the selected server session immediately.</p></div><span class="sec-pill">{{ number_format($sessions->count()) }} sessions</span></div>
                <div class="sec-table-wrap">
                    @if($sessions->count())
                        <table class="sec-table"><thead><tr><th>Account</th><th>Role</th><th>Browser</th><th>IP</th><th>Last seen</th><th>Session</th><th></th></tr></thead><tbody>
                        @foreach($sessions as $session)
                            <tr><td><div class="sec-name">{{ $session['name'] }}</div><div class="sec-sub">ID {{ $session['user_id'] }}</div></td><td>{{ $session['role_label'] }}</td><td>{{ $session['browser'] }}</td><td>{{ $session['ip'] }}</td><td>{{ $session['last_seen_at']->diffForHumans() }}</td><td>…{{ $session['session_suffix'] }}</td><td>
                                @if($session['current'])<span class="sec-current">Current session</span>@else
                                    <form method="POST" action="{{ route('admin.security.sessions.force-logout', $session['session_handle']) }}" onsubmit="return confirm('Force logout this active session?');">@csrf<button class="sec-btn" type="submit">Force Logout</button></form>
                                @endif
                            </td></tr>
                        @endforeach
                        </tbody></table>
                    @else<div class="sec-empty">No parseable active SARI database sessions were found.</div>@endif
                </div>
            </section>
<div class="sec-grid">
        <div class="sec-stack">
            <section class="sec-panel" id="risk-signals">
                <div class="sec-panel-head"><div><h2>Smart risk &amp; fraud flags</h2><p>Failed-login bursts, cancellation patterns, repeated returns/refunds, Seller cancellation rate, and stalled order operations.</p></div><span class="sec-pill">{{ number_format($risks->count()) }} active</span></div>
                <div class="sec-risk-list">
                    @forelse($risks as $risk)
                        <article class="sec-risk {{ $risk['severity'] }}">
                            <div class="sec-score">{{ number_format((int)$risk['score']) }}</div>
                            <div><strong>{{ $risk['title'] }}</strong><span>{{ $risk['message'] }}</span><span class="sec-risk-meta">{{ $risk['category'] }} · automated indicator, Admin review required</span></div>
                            <div><div class="sec-sev {{ $risk['severity'] }}">{{ $risk['severity'] }}</div>@if($risk['url'])<a class="sec-risk-link" href="{{ $risk['url'] }}">Review</a>@endif</div>
                        </article>
                    @empty
                        <div class="sec-empty">No active risk rules are currently above their review threshold.</div>
                    @endforelse
                </div>
            </section>
            <section class="sec-panel" id="audit-timeline">
                <div class="sec-panel-head"><div><h2>Audit timeline</h2><p>Reuses the existing centralized Admin activity table. State snapshots appear when the action records before/after values.</p></div><span class="sec-pill">Latest {{ number_format($audit->count()) }}</span></div>
                <div class="sec-audit">
                    @forelse($audit as $event)
                        <article class="sec-event" @if($loop->index >= 5) data-extra-audit hidden @endif><div class="sec-time">{{ $event['created_at']?->format('M d, Y') }}<br>{{ $event['created_at']?->format('h:i:s A') }}<br>{{ $event['ip'] }}</div><div><strong>{{ $event['admin'] }} · {{ $event['action_label'] }}</strong><p>{{ $event['description'] }} · Subject: {{ $event['subject'] }}</p>
                            @if($event['before'] || $event['after'])<div class="sec-diff"><div><b>Before</b><pre>{{ json_encode($event['before'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></div><div><b>After</b><pre>{{ json_encode($event['after'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre></div></div>@endif
                        </div></article>
                    @empty<div class="sec-empty">No administrator actions have been recorded yet.</div>@endforelse
                </div>
@if($audit->count() > 5)
    <button type="button" id="securityAuditToggle" class="sec-audit-toggle" aria-expanded="false" aria-controls="audit-timeline">See more ({{ $audit->count() - 5 }})</button>
@endif

            </section>
        </div>
        <aside class="sec-stack">
            <section class="sec-panel" id="failed-logins">
                <div class="sec-panel-head"><div><h2>Failed sign-in watch</h2><p>Unsuccessful password sign-in submissions recorded by the security middleware.</p></div><span class="sec-pill">Latest {{ number_format($failedLogins->count()) }}</span></div>
                <div class="sec-table-wrap">
                    @if($failedLogins->count())
                        <table class="sec-table" style="min-width:620px"><thead><tr><th>Identity</th><th>IP</th><th>Browser</th><th>Time</th></tr></thead><tbody>
                        @foreach($failedLogins as $login)<tr><td><div class="sec-name">{{ $login['name'] }}</div><div class="sec-sub">{{ $login['email_masked'] }}</div></td><td>{{ $login['ip'] }}</td><td>{{ $login['browser'] }}</td><td>{{ $login['created_at']?->format('M d · h:i A') }}</td></tr>@endforeach
                        </tbody></table>
                    @else<div class="sec-empty">No failed sign-in attempts have been recorded.</div>@endif
                </div>
            </section>
            <section class="sec-panel">
                <div class="sec-panel-head"><div><h2>Alert behavior</h2><p>Critical alerts are checked throughout the Admin UI, not only on this page.</p></div></div>
                <div style="padding:14px;color:#6f7a85;font-size:7.6px;line-height:1.7">A new unacknowledged critical alert triggers a red on-screen warning and an audible three-tone alarm. Browsers can block audio before the first user interaction; when that happens, the visual alert remains visible and sound starts after the Admin interacts with the page. Acknowledgement suppresses the same fingerprint for 12 hours while keeping it visible in this Security Center.</div>
            </section>
        </aside>
    </div>
</div>

<script>
(() => {
    const button = document.getElementById('securityAuditToggle');
    const timeline = document.getElementById('audit-timeline');
    if (!button || !timeline) return;
    const extra = Array.from(timeline.querySelectorAll('[data-extra-audit]'));
    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') !== 'true';
        extra.forEach(entry => { entry.hidden = !expanded; });
        button.setAttribute('aria-expanded', String(expanded));
        button.textContent = expanded ? 'Show less' : 'See more (' + extra.length + ')';
        if (!expanded) button.focus({ preventScroll: true });
    });
})();
</script>

@endsection
