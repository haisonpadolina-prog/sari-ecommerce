{{-- Manager-only COD custody warning, works on dashboard + remittances. --}}
@if(session('is_logistics') && !session('is_logistics_hub_operator'))
    @php
        $sariCodAlerts = app(\App\Services\Finance\CodCustodyMonitor::class)
            ->logistics((int) session('logistics_account_id', 0));
        $sariCodOverdueTotal = $sariCodAlerts->sum('overdue_cents');
    @endphp
    <section id="sariCodOverdueBanner" data-cod-status-url="{{ route('logistics.cod-compliance.status') }}" role="status" aria-live="polite" class="mb-4 rounded-2xl border px-4 py-3 shadow-sm {{ $sariCodOverdueTotal > 0 ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-white' }}">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <strong data-cod-banner-title class="text-[13px] {{ $sariCodOverdueTotal > 0 ? 'text-red-800' : 'text-slate-800' }}">
                    {{ $sariCodOverdueTotal > 0 ? '⚠ Overdue Rider COD cash requires review' : 'COD cash monitoring' }}
                </strong>
                <p data-cod-banner-description class="mt-1 text-[11px] {{ $sariCodOverdueTotal > 0 ? 'text-red-700' : 'text-slate-500' }}">
                    @if($sariCodOverdueTotal > 0)
                        {{ $sariCodAlerts->filter(fn ($a) => $a['overdue_cents'] > 0)->count() }} Rider(s) · ₱{{ number_format($sariCodOverdueTotal / 100, 2) }} overdue.
                        Review physical cash receipt before taking action. Submitted batches are not verified cash.
                    @else
                        No overdue Rider COD detected at the time this page loaded.
                    @endif
                </p>
            </div>
            <a data-cod-banner-link href="{{ route('logistics.cod-compliance.index') }}" class="inline-flex min-h-[38px] items-center rounded-xl px-4 py-2 text-[11px] font-semibold text-white {{ $sariCodOverdueTotal > 0 ? 'bg-red-700 hover:bg-red-800' : 'bg-[#C88912] hover:bg-[#A87918]' }}">Open COD monitoring →</a>
        </div>
    </section>
    <script>
    (() => {
        const box = document.getElementById('sariCodOverdueBanner');
        if (!box || box.dataset.polling === '1') return;
        box.dataset.polling = '1';
        const title = box.querySelector('[data-cod-banner-title]');
        const description = box.querySelector('[data-cod-banner-description]');
        const link = box.querySelector('[data-cod-banner-link]');
        const redraw = data => {
            const cents = Number(data.overdue_cents) || 0;
            const count = Number(data.riders_count) || 0;
            const overdue = cents > 0;
            box.classList.toggle('border-red-300', overdue);
            box.classList.toggle('bg-red-50', overdue);
            box.classList.toggle('border-slate-200', !overdue);
            box.classList.toggle('bg-white', !overdue);
            title.classList.toggle('text-red-800', overdue);
            title.classList.toggle('text-slate-800', !overdue);
            description.classList.toggle('text-red-700', overdue);
            description.classList.toggle('text-slate-500', !overdue);
            link.classList.toggle('bg-red-700', overdue);
            link.classList.toggle('bg-[#C88912]', !overdue);
            title.textContent = overdue ? '⚠ Overdue Rider COD cash requires review' : 'COD cash monitoring';
            const money = (cents / 100).toLocaleString('en-PH', {minimumFractionDigits:2,maximumFractionDigits:2});
            description.textContent = overdue
                ? `${count} Rider(s) · ₱${money} overdue. Check physical cash verification before taking action.`
                : 'No overdue Rider COD detected as of the latest automatic check.';
        };
        const refresh = async () => {
            if (document.hidden || !box.isConnected) return;
            try {
                const response = await fetch(box.dataset.codStatusUrl, {headers:{'Accept':'application/json'},credentials:'same-origin',cache:'no-store'});
                if (response.ok) redraw(await response.json());
            } catch (e) { /* Preserve the last known state if connection fails. */ }
        };
        const interval = setInterval(refresh, 60000);
        window.addEventListener('pagehide', () => clearInterval(interval), {once:true});
    })();
    </script>
@endif
