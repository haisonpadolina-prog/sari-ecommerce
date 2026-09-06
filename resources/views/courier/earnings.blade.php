@extends('layouts.courier')

@section('title', 'Profit Dashboard')
@section('header-title', 'Profit Dashboard')
@section('header-subtitle', 'Courier Earnings')


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
            <h2 class="text-[15px] font-bold text-[#211d17]">Courier Earnings</h2>
            <p class="mt-2 text-[10px] leading-5 text-[#817769]">
                Track credited delivery fees, pending earnings, and weekly performance.
            </p>
        </div>
        <button type="button" class="rounded-xl border border-[#e6dccb] bg-white px-4 py-2.5 text-[9px] font-semibold text-[#62594d]">
            Download Statement
        </button>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['Today','₱'.number_format($earnings['today'],2),'#fff2d8','#b77900'],
            ['This Week','₱'.number_format($earnings['week'],2),'#eaf2f7','#3C6E91'],
            ['This Month','₱'.number_format($earnings['month'],2),'#eaf3ed','#4F7D63'],
            ['Pending','₱'.number_format($earnings['pending'],2),'#f0ecf6','#715A86'],
            ['Available','₱'.number_format($earnings['available'],2),'#ecf5ee','#437356'],
        ] as [$label,$value,$bg,$color])
            <article class="reveal page-card page-card-hover rounded-2xl p-4">
                <div class="mb-4 h-2 w-2 rounded-full" style="background:{{ $color }}"></div>
                <p class="text-[8px] text-[#887e70]">{{ $label }}</p>
                <p class="mt-2 text-[20px] font-bold tracking-[-.035em] text-[#211d17]">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.4fr)_minmax(320px,.6fr)]">
        <div class="reveal page-card rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-[12px] font-bold text-[#211d17]">Weekly Performance</h3>
                    <p class="mt-1 text-[8px] text-[#918677]">Delivery earnings for the current week.</p>
                </div>
                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[7px] font-semibold text-emerald-700">+12.5%</span>
            </div>

            @php
                $maxAmount = max(array_column($daily, 'amount'));
            @endphp

            <div class="mt-8 grid h-[220px] grid-cols-7 items-end gap-2 sm:gap-4">
                @foreach($daily as $day)
                    @php $height = max(20, round(($day['amount'] / $maxAmount) * 180)); @endphp
                    <div class="flex h-full flex-col justify-end">
                        <div class="mb-2 text-center text-[7px] font-semibold text-[#645b4f]">₱{{ $day['amount'] }}</div>
                        <div class="mx-auto w-full max-w-[42px] rounded-t-xl bg-[#3C6E91]/90 transition hover:bg-[#d9930a]" style="height:{{ $height }}px"></div>
                        <p class="mt-2 text-center text-[8px] font-semibold text-[#857b6d]">{{ $day['day'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <aside class="reveal page-card rounded-2xl p-5">
            <h3 class="text-[12px] font-bold text-[#211d17]">Payout Summary</h3>
            <p class="mt-1 text-[8px] text-[#918677]">Current courier balance</p>

            <div class="mt-5 rounded-2xl bg-[#3C6E91] p-5 text-white">
                <p class="text-[8px] uppercase tracking-[.12em] text-white/60">Available Balance</p>
                <p class="mt-2 text-[25px] font-bold">₱{{ number_format($earnings['available'],2) }}</p>
                <p class="mt-2 text-[8px] text-white/70">Next scheduled payout: Friday</p>
            </div>

            <button type="button" class="mt-4 w-full rounded-xl bg-[#d9930a] px-4 py-3 text-[9px] font-semibold text-white transition hover:-translate-y-0.5">
                Request Payout
            </button>

            <p class="mt-3 text-center text-[7px] leading-4 text-[#9a9081]">
                Demo action only. Payout processing can be connected to the database later.
            </p>
        </aside>
    </section>

    <section class="reveal page-card mt-5 overflow-hidden rounded-2xl">
        <div class="border-b border-[#eee4d3] px-5 py-4">
            <h3 class="text-[12px] font-bold text-[#211d17]">Recent Earnings</h3>
            <p class="mt-1 text-[8px] text-[#918677]">Latest delivery fee credits.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px]">
                <thead class="bg-[#fbf8f1]">
                    <tr class="text-left">
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Order</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Date</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Type</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Amount</th>
                        <th class="px-5 py-3 text-[7px] uppercase tracking-[.11em] text-[#9c9182]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0e8db]">
                    @foreach($transactions as $transaction)
                        <tr class="hover:bg-[#fffaf1]">
                            <td class="px-5 py-3.5 text-[9px] font-semibold text-[#302a23]">{{ $transaction['order'] }}</td>
                            <td class="px-5 py-3.5 text-[8px] text-[#81776a]">{{ $transaction['date'] }}</td>
                            <td class="px-5 py-3.5 text-[8px] text-[#81776a]">{{ $transaction['type'] }}</td>
                            <td class="px-5 py-3.5 text-[9px] font-semibold text-[#302a23]">+₱{{ number_format($transaction['amount'],2) }}</td>
                            <td class="px-5 py-3.5"><span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[7px] font-semibold text-emerald-700">{{ $transaction['status'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
