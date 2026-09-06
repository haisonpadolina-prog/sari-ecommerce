@extends('layouts.courier')

@section('title', 'Delivery History')
@section('header-title', 'Delivery History')
@section('header-subtitle', 'Completed Deliveries')


@push('styles')
<style>
    .page-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }
    .page-card-hover {
        transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
    }
    .page-card-hover:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;
        box-shadow: 0 14px 32px rgba(75, 59, 30, .07);
    }
</style>
@endpush


@section('content')
<div class="mx-auto max-w-[1620px]">

    <section class="reveal mb-5 flex flex-col justify-between gap-4 border-b border-[#eee4d3] pb-5 lg:flex-row lg:items-end">
        <div>
            <h2 class="text-[15px] font-bold text-[#211d17]">Delivery History</h2>
            <p class="mt-2 text-[10px] leading-5 text-[#817769]">
                Review completed deliveries, routes, completion time, and earned courier fees.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <select id="historyFilter" class="h-10 rounded-xl border border-[#e6dccb] bg-white px-3 text-[9px] font-semibold text-[#62594d] outline-none">
                <option value="all">All deliveries</option>
                <option value="today">Today</option>
                <option value="previous">Previous days</option>
            </select>
            <button type="button" class="rounded-xl border border-[#e6dccb] bg-white px-4 py-2.5 text-[9px] font-semibold text-[#62594d]">Export</button>
        </div>
    </section>

    <section class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Completed Today','8','Successful trips'],
            ['Total Completed','184','Since joining'],
            ['Success Rate','98.4%','Completion rate'],
            ['Average Fee','₱102','Per delivery'],
        ] as [$label,$value,$sub])
            <article class="reveal page-card page-card-hover rounded-2xl p-4">
                <p class="text-[8px] text-[#887e70]">{{ $label }}</p>
                <p class="mt-2 text-[22px] font-bold tracking-[-.04em] text-[#211d17]">{{ $value }}</p>
                <p class="mt-1 text-[8px] text-[#9b9183]">{{ $sub }}</p>
            </article>
        @endforeach
    </section>

    <section class="reveal page-card overflow-hidden rounded-2xl">
        <div class="flex flex-col justify-between gap-3 border-b border-[#eee4d3] px-5 py-4 sm:flex-row sm:items-center">
            <div>
                <h3 class="text-[12px] font-bold text-[#211d17]">Completed Trips</h3>
                <p class="mt-1 text-[8px] text-[#918677]">Most recent delivery records.</p>
            </div>

            <div class="relative">
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#a09687]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>
                <input id="historySearch" type="search" placeholder="Search order, customer, route..." class="h-10 w-full rounded-xl border border-[#e5dac9] bg-white pl-9 pr-3 text-[9px] outline-none focus:border-[#d9930a] sm:w-[280px]">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px]">
                <thead class="bg-[#fbf8f1]">
                    <tr class="text-left">
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Order</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Customer</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Route</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Date</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Time</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Fee</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Status</th>
                    </tr>
                </thead>
                <tbody id="historyRows" class="divide-y divide-[#f0e8db]">
                    @foreach($history as $row)
                        <tr
                            class="history-row hover:bg-[#fffaf1]"
                            data-search="{{ strtolower($row['order'].' '.$row['customer'].' '.$row['route']) }}"
                            data-day="{{ $row['date']==='Aug 18, 2026' ? 'today' : 'previous' }}"
                        >
                            <td class="px-5 py-3.5 text-[9px] font-semibold text-[#302a23]">{{ $row['order'] }}</td>
                            <td class="px-5 py-3.5 text-[8px] text-[#746a5e]">{{ $row['customer'] }}</td>
                            <td class="px-5 py-3.5 text-[8px] text-[#746a5e]">{{ $row['route'] }}</td>
                            <td class="px-5 py-3.5 text-[8px] text-[#81776a]">{{ $row['date'] }}</td>
                            <td class="px-5 py-3.5 text-[8px] text-[#81776a]">{{ $row['time'] }}</td>
                            <td class="px-5 py-3.5 text-[9px] font-semibold text-[#302a23]">₱{{ number_format($row['fee'],2) }}</td>
                            <td class="px-5 py-3.5"><span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[7px] font-semibold text-emerald-700">{{ $row['status'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('historySearch');
    const filter = document.getElementById('historyFilter');
    const rows = [...document.querySelectorAll('.history-row')];

    const apply = () => {
        const query = (search?.value || '').trim().toLowerCase();
        const selected = filter?.value || 'all';

        rows.forEach(row => {
            const searchMatch = row.dataset.search.includes(query);
            const dayMatch = selected === 'all' || row.dataset.day === selected;
            row.classList.toggle('hidden', !(searchMatch && dayMatch));
        });
    };

    search?.addEventListener('input', apply);
    filter?.addEventListener('change', apply);
});
</script>
@endpush
