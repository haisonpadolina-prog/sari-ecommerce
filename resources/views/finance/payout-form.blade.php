@php
    $payoutTitle = $payoutTitle ?? 'Withdraw Earnings';
    $payoutHelp = $payoutHelp ?? 'A payout request reserves your wallet balance. Admin marks it paid only after the real external transfer or cash release is confirmed.';
@endphp

<div class="rounded-2xl border border-[#e7dfd2] bg-white p-5 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-[13px] font-bold text-[#211d17]">{{ $payoutTitle }}</h3>
            <p class="mt-1 max-w-2xl text-[9px] leading-5 text-[#817769]">{{ $payoutHelp }}</p>
        </div>
        <div class="rounded-xl bg-[#f7f4ee] px-4 py-3 text-right">
            <p class="text-[7px] font-semibold uppercase tracking-[.12em] text-[#8b8175]">Available</p>
            <p class="mt-1 text-[18px] font-bold text-[#211d17]">₱{{ number_format((float) $wallet->available_balance, 2) }}</p>
            @if((float) $wallet->reserved_balance > 0)
                <p class="mt-1 text-[8px] text-[#8b6940]">₱{{ number_format((float) $wallet->reserved_balance, 2) }} reserved</p>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ $payoutAction }}" class="mt-5 grid gap-3 lg:grid-cols-2">
        @csrf

        <label class="block">
            <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Amount (PHP)</span>
            <input name="amount" type="number" min="1" step="0.01" max="{{ (float) $wallet->available_balance }}" required
                value="{{ old('amount') }}"
                class="mt-2 w-full rounded-xl border border-[#ddd4c6] bg-white px-3 py-3 text-[11px] outline-none focus:border-[#d9930a]">
        </label>

        <label class="block">
            <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Payout Method</span>
            <select name="payout_method" required class="mt-2 w-full rounded-xl border border-[#ddd4c6] bg-white px-3 py-3 text-[11px] outline-none focus:border-[#d9930a]">
                <option value="gcash">GCash</option>
                <option value="maya">Maya</option>
                <option value="bank">Bank Transfer</option>
                <option value="manual_cash">Manual Cash Release</option>
            </select>
        </label>

        <label class="block">
            <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Account Name</span>
            <input name="account_name" type="text" minlength="2" maxlength="120" required value="{{ old('account_name') }}"
                class="mt-2 w-full rounded-xl border border-[#ddd4c6] bg-white px-3 py-3 text-[11px] outline-none focus:border-[#d9930a]">
        </label>

        <label class="block">
            <span class="text-[8px] font-semibold uppercase tracking-[.08em] text-[#73695d]">Mobile / Bank Account</span>
            <input name="account_reference" type="text" minlength="4" maxlength="120" required value="{{ old('account_reference') }}"
                placeholder="09XXXXXXXXX or account number"
                class="mt-2 w-full rounded-xl border border-[#ddd4c6] bg-white px-3 py-3 text-[11px] outline-none focus:border-[#d9930a]">
        </label>

        <div class="lg:col-span-2">
            <button type="submit" @disabled((float) $wallet->available_balance <= 0)
                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#d9930a] px-5 py-3 text-[9px] font-semibold text-white transition hover:bg-[#bd7e07] disabled:cursor-not-allowed disabled:opacity-45">
                Request Withdrawal
            </button>
        </div>
    </form>
</div>
