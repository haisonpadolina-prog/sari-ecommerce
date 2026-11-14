<div id="sariCriticalAlertOverlay" class="sari-critical-alert-overlay" hidden aria-live="assertive" aria-modal="true" role="alertdialog">
    <div class="sari-critical-alert-backdrop"></div>
    <section class="sari-critical-alert-card">
        <div class="sari-critical-alert-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 2.8 19h18.4L12 3Z"></path><path d="M12 9v4"></path><path d="M12 16.5h.01"></path></svg>
        </div>
        <div class="sari-critical-alert-copy">
            <p class="sari-critical-alert-kicker">Critical SARI Alert</p>
            <h2 id="sariCriticalAlertTitle">Immediate review required</h2>
            <p id="sariCriticalAlertMessage">A critical security or operations condition was detected.</p>
            <p id="sariCriticalAlertCount" class="sari-critical-alert-count"></p>
        </div>
        <div class="sari-critical-alert-actions">
            <a id="sariCriticalAlertReview" href="{{ route('admin.security') }}">Review</a>
            <button id="sariCriticalAlertAcknowledge" type="button">Acknowledge</button>
            <button id="sariCriticalAlertSound" type="button" title="Enable or test security alert sound">Sound</button>
        </div>
    </section>
</div>

<style>
    .sari-critical-alert-overlay[hidden]{display:none!important}.sari-critical-alert-overlay{position:fixed;inset:0;z-index:20000;display:grid;place-items:start center;padding:88px 16px 16px;pointer-events:none}.sari-critical-alert-backdrop{position:absolute;inset:0;background:rgba(112,16,16,.13);box-shadow:inset 0 0 0 4px rgba(190,45,45,.68);animation:sariSecurityPulse 1.25s ease-in-out 2;pointer-events:none}.sari-critical-alert-card{position:relative;display:grid;grid-template-columns:auto minmax(0,1fr) auto;gap:13px;align-items:center;width:min(780px,calc(100vw - 32px));border:1px solid #d84f4f;border-radius:12px;background:#fff8f8;padding:14px 15px;box-shadow:0 24px 70px rgba(92,20,20,.25);pointer-events:auto}.sari-critical-alert-icon{display:grid;width:42px;height:42px;place-items:center;border-radius:10px;background:#c53d3d;color:#fff}.sari-critical-alert-icon svg{width:21px;height:21px}.sari-critical-alert-kicker{margin:0;color:#ad3434;font-size:7px;font-weight:900;letter-spacing:.13em;text-transform:uppercase}.sari-critical-alert-copy h2{margin:4px 0 0;color:#742828;font-size:13px;font-weight:800}.sari-critical-alert-copy>p:not(.sari-critical-alert-kicker){margin:4px 0 0;color:#8a5353;font-size:8px;line-height:1.55}.sari-critical-alert-count{font-weight:750!important}.sari-critical-alert-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:6px}.sari-critical-alert-actions a,.sari-critical-alert-actions button{display:inline-flex;min-height:32px;align-items:center;justify-content:center;border-radius:7px;padding:0 10px;font:800 7px 'Poppins',sans-serif;text-decoration:none;cursor:pointer}.sari-critical-alert-actions a{border:1px solid #c94343;background:#c94343;color:#fff}.sari-critical-alert-actions button{border:1px solid #e4b7b7;background:#fff;color:#954343}.sari-critical-alert-actions button:last-child{border-color:#d8dde2;color:#65707b}@keyframes sariSecurityPulse{0%,100%{opacity:.72}50%{opacity:1}}@media(max-width:720px){.sari-critical-alert-overlay{padding-top:76px}.sari-critical-alert-card{grid-template-columns:auto 1fr}.sari-critical-alert-actions{grid-column:1/-1;justify-content:stretch}.sari-critical-alert-actions>*{flex:1}}
    @media(prefers-reduced-motion:reduce){.sari-critical-alert-backdrop{animation:none}}
</style>

<script>
(() => {
    if (window.__sariSecurityAlertWatchStarted) return;
    window.__sariSecurityAlertWatchStarted = true;

    const feedUrl = @json(route('admin.security.alert-feed'));
    const acknowledgeUrl = @json(route('admin.security.alerts.acknowledge'));
    const csrf = @json(csrf_token());
    let currentAlert = null;
    let lastSoundFingerprint = null;
    let audioContext = null;

    const overlay = () => document.getElementById('sariCriticalAlertOverlay');
    const title = () => document.getElementById('sariCriticalAlertTitle');
    const message = () => document.getElementById('sariCriticalAlertMessage');
    const count = () => document.getElementById('sariCriticalAlertCount');
    const review = () => document.getElementById('sariCriticalAlertReview');

    function ensureAudioContext() {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return null;
        if (!audioContext) audioContext = new AudioCtx();
        return audioContext;
    }

    async function playAlarm(force = false) {
        try {
            const ctx = ensureAudioContext();
            if (!ctx) return false;
            if (ctx.state === 'suspended') await ctx.resume();
            const now = ctx.currentTime;
            [0, .18, .36].forEach((offset, index) => {
                const oscillator = ctx.createOscillator();
                const gain = ctx.createGain();
                oscillator.type = 'square';
                oscillator.frequency.setValueAtTime(index === 1 ? 880 : 660, now + offset);
                gain.gain.setValueAtTime(0.0001, now + offset);
                gain.gain.exponentialRampToValueAtTime(.055, now + offset + .015);
                gain.gain.exponentialRampToValueAtTime(.0001, now + offset + .13);
                oscillator.connect(gain); gain.connect(ctx.destination);
                oscillator.start(now + offset); oscillator.stop(now + offset + .14);
            });
            return true;
        } catch (_) {
            return false;
        }
    }

    function render(data) {
        const alerts = Array.isArray(data?.alerts) ? data.alerts : [];
        const box = overlay();
        if (!box) return;
        if (!alerts.length) {
            currentAlert = null;
            box.hidden = true;
            return;
        }

        currentAlert = alerts[0];
        title().textContent = currentAlert.title || 'Immediate review required';
        message().textContent = currentAlert.message || 'A critical condition was detected.';
        count().textContent = alerts.length > 1 ? `${alerts.length} critical alerts are currently unacknowledged.` : '1 critical alert is currently unacknowledged.';
        review().href = currentAlert.url || @json(route('admin.security'));
        box.hidden = false;

        if (currentAlert.fingerprint && currentAlert.fingerprint !== lastSoundFingerprint) {
            lastSoundFingerprint = currentAlert.fingerprint;
            playAlarm();
        }
    }

    async function poll() {
        try {
            const response = await fetch(feedUrl, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin', cache:'no-store'});
            if (response.status === 401) return;
            if (!response.ok) return;
            render(await response.json());
        } catch (_) {}
    }

    async function acknowledge() {
        if (!currentAlert?.fingerprint) return;
        const button = document.getElementById('sariCriticalAlertAcknowledge');
        if (button) button.disabled = true;
        try {
            const response = await fetch(acknowledgeUrl, {
                method:'POST', credentials:'same-origin',
                headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},
                body:JSON.stringify({fingerprint:currentAlert.fingerprint})
            });
            if (response.ok) await poll();
        } finally {
            if (button) button.disabled = false;
        }
    }

    document.addEventListener('click', (event) => {
        if (event.target?.id === 'sariCriticalAlertAcknowledge') acknowledge();
        if (event.target?.id === 'sariCriticalAlertSound') playAlarm(true);
    });

    // Browser audio policies require a user gesture before sound can always play.
    document.addEventListener('pointerdown', () => { ensureAudioContext()?.resume?.().catch(() => {}); }, {once:true, passive:true});
    document.addEventListener('keydown', () => { ensureAudioContext()?.resume?.().catch(() => {}); }, {once:true});

    poll();
    window.__sariSecurityAlertInterval = window.setInterval(poll, 15000);
    document.addEventListener('livewire:navigated', poll);
})();
</script>
