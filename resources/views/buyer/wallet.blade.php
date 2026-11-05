@extends('layouts.buyer')

@section('title', 'My Wallet — SARI')
@section('page-title', 'My Wallet')

@section('content')
@include('components.buyer.header')

<main class="mx-auto w-full max-w-[1380px] space-y-5 px-4 pb-10 pt-5 sm:px-6 lg:px-8" style="font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;">
    <header class="space-y-2">
        <p class="text-[10px] font-bold uppercase tracking-[.14em] text-[#a87316]">SARI Finance</p>
        <h1 class="text-3xl font-bold tracking-tight text-[#202938]">My Wallet & COD History</h1>
        <p class="max-w-3xl text-[12px] leading-6 text-[#738095]">This is an internal record, not a bank or reloadable e-wallet. SARI accepts <strong>Cash on Delivery only</strong> for purchases. Buyers pay the delivery Rider in cash; shopping does not use this wallet.</p>
    </header>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4" aria-label="Wallet and COD summary">
        @foreach([
            ['Available balance', $wallet->available_balance, 'Internal ledger only'],
            ['Reserved balance', $wallet->reserved_balance, 'Not spendable at checkout'],
            ['Total COD paid', $codCollected, 'Delivered and paid orders'],
            ['Lifetime wallet credits', $wallet->lifetime_credits, 'No automatic Buyer COD earnings'],
        ] as [$label, $amount, $description])
            <article class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
                <p class="text-[10px] font-semibold text-[#7e756a]">{{ $label }}</p>
                <p class="mt-3 text-2xl font-bold text-[#211d17]">₱{{ number_format((float) $amount, 2) }}</p>
                <p class="mt-2 text-[10px] text-[#8490a2]">{{ $description }}</p>
            </article>
        @endforeach
    </section>

    <section class="grid gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <h2 class="text-[14px] font-bold text-[#202938]">COD Purchase History</h2>
            <p class="mt-1 text-[11px] text-[#738095]">Orders you placed. These are cash payments, not wallet debits.</p>
            <div class="mt-4 space-y-2">
                @forelse($codOrders as $order)
                    <a href="{{ route('buyer.orders.show', $order) }}" class="flex items-center justify-between gap-4 rounded-xl border border-[#eee6da] px-4 py-3 hover:bg-[#fffaf0]">
                        <span class="min-w-0 text-[11px] text-[#202938]">
                            <strong class="block truncate">{{ $order->order_number }}</strong>
                            <span class="mt-1 block text-[#7e756a]">{{ $order->created_at?->format('M d, Y') }} · {{ ucfirst(str_replace('_', ' ', $order->status)) }} · {{ strtolower($order->payment_status) === 'paid' ? 'Paid' : 'Unpaid' }}</span>
                        </span>
                        <strong class="shrink-0 text-[12px] text-[#202938]">₱{{ number_format((float) $order->total, 2) }}</strong>
                    </a>
                @empty
                    <p class="rounded-xl bg-[#f7f9fb] p-4 text-[11px] text-[#7e756a]">No COD orders yet.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $codOrders->links() }}</div>
        </div>

        <div class="rounded-2xl border border-[#e5e0d8] bg-white p-5 shadow-sm">
            <h2 class="text-[14px] font-bold text-[#202938]">Wallet Activity</h2>
            <p class="mt-1 text-[11px] text-[#738095]">Read-only accounting entries. COD shopping does not create wallet credits or debits.</p>
            <div class="mt-4 space-y-2">
                @forelse($walletEntries as $entry)
                    <div class="flex items-center justify-between gap-4 rounded-xl border border-[#eee6da] px-4 py-3">
                        <div class="min-w-0 text-[11px]"><strong class="block text-[#202938]">{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</strong><span class="mt-1 block text-[#7e756a]">{{ $entry->occurred_at?->format('M d, Y · h:i A') }}</span></div>
                        <strong class="shrink-0 text-[12px] text-[#202938]">{{ (float) $entry->available_delta >= 0 ? '+' : '−' }}₱{{ number_format(abs((float) $entry->available_delta), 2) }}</strong>
                    </div>
                @empty
                    <p class="rounded-xl bg-[#f7f9fb] p-4 text-[11px] text-[#7e756a]">No wallet movements. This is expected for normal COD Buyers.</p>
                @endforelse
            </div>
        </div>
    </section>
</main>
@endsection
