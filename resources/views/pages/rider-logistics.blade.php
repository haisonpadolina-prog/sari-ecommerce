@extends('layouts.app')

@section('title', 'Choose Logistics — SARI Rider')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<style>
    .rider-provider-card {
        border: 1px solid #e7e0d7;
        background: #fff;
        box-shadow:
            10px 12px 28px rgba(52, 40, 25, .065),
            -4px -4px 14px rgba(255, 255, 255, .9);
        transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease;
    }

    .rider-provider-card:hover {
        transform: translateY(-2px);
        border-color: #d7bf91;
        box-shadow:
            13px 16px 32px rgba(52, 40, 25, .085),
            -5px -5px 16px rgba(255, 255, 255, .94);
    }
</style>

<div class="min-h-screen bg-[#f7f4ee] font-['Poppins',sans-serif] text-[#211d17]">
    <div class="fixed inset-0 pointer-events-none">
        <img src="{{ asset('images/login-bg.jpg') }}" alt="" class="h-full w-full object-cover object-center opacity-[.10]">
        <div class="absolute inset-0 bg-[#f8f5ef]/90"></div>
    </div>

    <main class="relative z-10 mx-auto w-full max-w-[1180px] px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-[11px] font-semibold text-[#8e7755] hover:text-[#a96f06]">
                    <span>←</span> Back to account registration
                </a>

                <p class="mt-7 text-[10px] font-bold uppercase tracking-[.18em] text-[#b97805]">SARI Rider Network</p>
                <h1 class="mt-2 text-[30px] font-bold tracking-[-.04em] text-[#201c17] sm:text-[38px]">Choose your Logistics team</h1>
                <p class="mt-3 max-w-[650px] text-[12px] leading-6 text-[#7d7469] sm:text-[13px]">
                    Rider applications are reviewed by Logistics. Choose the active Logistics provider you want to work with, then submit your Rider requirements directly to their team.
                </p>
            </div>

            <a href="{{ route('login') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-[#dfd7cc] bg-white px-5 text-[11px] font-semibold text-[#625a50] shadow-[0_5px_15px_rgba(52,40,25,.04)]">
                Already registered? Sign in
            </a>
        </div>

        <section class="mt-8 rounded-[22px] border border-[#e7e0d7] bg-white/95 p-5 shadow-[0_14px_34px_rgba(52,40,25,.06)] sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-[16px] font-bold tracking-[-.02em] text-[#2b261f]">Active Logistics providers</h2>
                    <p class="mt-1 text-[10px] text-[#91887e]">Only approved and active Logistics accounts can receive Rider applications.</p>
                </div>

                <form method="GET" action="{{ route('rider.logistics.index') }}" class="flex w-full max-w-[430px] gap-2">
                    <div class="relative flex-1">
                        <svg viewBox="0 0 24 24" class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9a9186]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="6"></circle><path d="m16 16 4 4"></path>
                        </svg>
                        <input
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Search name, province, or city"
                            class="h-11 w-full rounded-xl border border-[#e3ddd4] bg-white pl-10 pr-4 text-[11px] text-[#2f2a24] outline-none transition focus:border-[#d48f08] focus:ring-4 focus:ring-[#d48f08]/10"
                        >
                    </div>
                    <button class="h-11 rounded-xl bg-[#d48f08] px-5 text-[10px] font-bold text-white hover:bg-[#bd7d05]">Search</button>
                </form>
            </div>
        </section>

        <section class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($logistics as $provider)
                @php
                    $providerName = $provider->displayName();
                    $location = collect([$provider->municipality_name, $provider->province_name])->filter()->implode(', ');
                @endphp

                <article class="rider-provider-card rounded-[20px] p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-[14px] bg-[#fff5df] text-[#b67814]">
                            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 19V7l8-4 8 4v12"></path>
                                <path d="M8 19v-5h8v5M8 9h.01M12 9h.01M16 9h.01"></path>
                            </svg>
                        </span>

                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#edf7f2] px-3 py-1.5 text-[8px] font-bold text-[#438461]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#438461]"></span>
                            Active
                        </span>
                    </div>

                    <h3 class="mt-5 text-[17px] font-bold tracking-[-.025em] text-[#29241e]">{{ $providerName }}</h3>
                    <p class="mt-1 text-[10px] text-[#8e857b]">{{ $location ?: 'SARI Logistics network' }}</p>

                    <div class="mt-5 grid grid-cols-2 gap-3 border-y border-[#f0ebe4] py-4">
                        <div>
                            <p class="text-[8px] uppercase tracking-[.1em] text-[#9b9288]">Approved Riders</p>
                            <p class="mt-1 text-[15px] font-bold text-[#2b261f]">{{ number_format((int) ($provider->approved_riders_count ?? 0)) }}</p>
                        </div>
                        <div>
                            <p class="text-[8px] uppercase tracking-[.1em] text-[#9b9288]">Coverage</p>
                            <p class="mt-1 truncate text-[10px] font-semibold text-[#62594f]">{{ $provider->province_name ?: 'Philippines' }}</p>
                        </div>
                    </div>

                    <a
                        href="{{ route('rider.logistics.apply', $provider) }}"
                        class="mt-5 inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#211f18] px-4 text-[10px] font-bold text-white transition hover:bg-[#343026]"
                    >
                        Apply to this Logistics
                        <span>→</span>
                    </a>
                </article>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-[22px] border border-dashed border-[#ddd3c4] bg-white/80 px-6 py-14 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#fff5df] text-[#b67814]">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V7l8-4 8 4v12"></path><path d="M8 19v-5h8v5"></path></svg>
                    </div>
                    <h3 class="mt-4 text-[15px] font-bold text-[#322c25]">No active Logistics provider found</h3>
                    <p class="mx-auto mt-2 max-w-[520px] text-[10px] leading-5 text-[#91887d]">
                        A Logistics account must first be approved and active before Riders can submit applications to it.
                    </p>
                    @if ($search !== '')
                        <a href="{{ route('rider.logistics.index') }}" class="mt-4 inline-flex text-[10px] font-bold text-[#a96f06]">Clear search</a>
                    @endif
                </div>
            @endforelse
        </section>

        <p class="mt-8 text-center text-[8px] text-[#9b9288]">© {{ date('Y') }} SARI. Rider applications are reviewed by the selected Logistics provider.</p>
    </main>
</div>
@endsection
