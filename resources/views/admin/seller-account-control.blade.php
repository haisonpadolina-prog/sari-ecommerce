@extends('layouts.admin')

@section('title', 'Seller Account Control — SARI Admin')
@section('page-title', 'Seller Account Control')

@section('content')

<style>
    .seller-account-control-page {
        --sac-xs: clamp(0.74rem, 0.70rem + 0.08vw, 0.82rem);
        --sac-sm: clamp(0.80rem, 0.75rem + 0.10vw, 0.90rem);
        --sac-md: clamp(0.88rem, 0.82rem + 0.14vw, 0.98rem);
        --sac-lg: clamp(1rem, 0.93rem + 0.18vw, 1.14rem);
        --sac-title: clamp(1.45rem, 1.28rem + 0.38vw, 1.85rem);
    }

    .seller-account-control-page .account-control-surface {
        box-shadow: 0 10px 28px rgba(45, 37, 28, .035);
    }

    .seller-account-control-page .account-control-summary-card {
        min-height: 104px;
        box-shadow: 0 7px 18px rgba(45, 37, 28, .028);
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease;
    }

    .seller-account-control-page .account-control-summary-card:hover {
        transform: translateY(-1px);
        border-color: #ddd3c7;
        box-shadow: 0 12px 28px rgba(45, 37, 28, .045);
    }

    .seller-account-control-page .account-control-workspace {
        box-shadow: 0 12px 30px rgba(45, 37, 28, .035);
    }

    .seller-account-control-page .account-control-row {
        box-shadow: 0 7px 18px rgba(45, 37, 28, .028);
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            background-color .18s ease;
    }

    .seller-account-control-page .account-control-row:hover {
        border-color: #ddd4c7;
        background: #fffdfa;
        box-shadow: 0 11px 24px rgba(45, 37, 28, .042);
    }

    .seller-account-control-page .account-control-input {
        color: #332e28 !important;
        -webkit-text-fill-color: #332e28 !important;
        background: #fff !important;
        font-size: var(--sac-sm) !important;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .seller-account-control-page .account-control-input::placeholder {
        color: #aaa196 !important;
        -webkit-text-fill-color: #aaa196 !important;
    }

    .seller-account-control-page .account-control-input:focus {
        outline: none;
        border-color: #c99128 !important;
        box-shadow: 0 0 0 4px rgba(201, 145, 40, .08);
    }

    .seller-account-control-page .account-control-action {
        min-height: 40px;
        font-size: var(--sac-sm) !important;
    }

    [data-seller-control-modal][hidden] {
        display: none !important;
    }

    @media (min-width: 1280px) {
        .seller-account-control-page .account-control-row-grid {
            display: grid;
            grid-template-columns:
                minmax(260px, 1.6fr)
                130px
                120px
                140px
                165px
                92px;
            align-items: center;
            column-gap: 1rem;
        }
    }

    @media (max-width: 639px) {
        .seller-account-control-page {
            --sac-xs: .76rem;
            --sac-sm: .82rem;
            --sac-md: .90rem;
            --sac-lg: 1rem;
        }

        .seller-account-control-page .account-control-summary-card {
            min-height: 100px;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | LIGHTER SELLER-COMPLIANCE TYPOGRAPHY
    |--------------------------------------------------------------------------
    | Match the visual weight from the Seller Compliance reference:
    | less black, less bold, softer brown/gray hierarchy.
    */
    .seller-account-control-page {
        color: #4b433b;
    }

    .seller-account-control-page .sac-page-title {
        color: #2f2923 !important;
        font-weight: 650 !important;
        letter-spacing: -0.025em !important;
    }

    .seller-account-control-page .sac-page-subtitle {
        color: #8a8075 !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .sac-summary-label {
        color: #8b7568 !important;
        font-weight: 500 !important;
    }

    .seller-account-control-page .sac-summary-value {
        color: #332c25 !important;
        font-weight: 650 !important;
        letter-spacing: -0.025em !important;
    }

    .seller-account-control-page .sac-workspace-title {
        color: #3a332c !important;
        font-weight: 650 !important;
    }

    .seller-account-control-page .sac-workspace-copy,
    .seller-account-control-page .sac-result-copy {
        color: #948a7f !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .sac-column-label {
        color: #8f7d6d !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-seller-name {
        color: #3a332d !important;
        font-weight: 650 !important;
    }

    .seller-account-control-page .sac-muted {
        color: #9a9085 !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .sac-badge {
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-data-value {
        color: #514940 !important;
        font-weight: 500 !important;
    }

    .seller-account-control-page .sac-action-button {
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-modal-title {
        color: #3a332c !important;
        font-weight: 650 !important;
    }

    .seller-account-control-page .sac-modal-section-title {
        color: #514940 !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .sac-modal-value {
        color: #514940 !important;
        font-weight: 600 !important;
    }

    .seller-account-control-page .account-control-surface {
        box-shadow: 0 8px 22px rgba(45, 37, 28, .025);
    }

    .seller-account-control-page .account-control-summary-card {
        box-shadow: 0 6px 16px rgba(45, 37, 28, .022);
    }

    .seller-account-control-page .account-control-summary-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 9px 20px rgba(45, 37, 28, .032);
    }

    .seller-account-control-page .account-control-workspace {
        box-shadow: 0 9px 24px rgba(45, 37, 28, .025);
    }

    .seller-account-control-page .account-control-row {
        box-shadow: none;
    }

    .seller-account-control-page .account-control-row:hover {
        background: #fffdfa;
        box-shadow: 0 7px 18px rgba(45, 37, 28, .028);
    }

    /*
    |--------------------------------------------------------------------------
    | COMPACT SELLER ACCOUNT WORKSPACE
    |--------------------------------------------------------------------------
    | Keep the summary cards readable, but make the actual Seller Accounts
    | workspace closer to the lighter/compact Seller Compliance reference.
    */
    .seller-account-control-page .account-control-workspace {
        --sac-list-xs: clamp(.67rem, .64rem + .05vw, .72rem);
        --sac-list-sm: clamp(.72rem, .68rem + .07vw, .78rem);
        --sac-list-md: clamp(.78rem, .73rem + .08vw, .84rem);
        --sac-list-title: clamp(.84rem, .79rem + .10vw, .92rem);
    }

    .seller-account-control-page .account-control-workspace .sac-workspace-title {
        font-size: var(--sac-list-title) !important;
        font-weight: 600 !important;
        color: #453d35 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-workspace-copy,
    .seller-account-control-page .account-control-workspace .sac-result-copy {
        font-size: var(--sac-list-xs) !important;
        line-height: 1.45 !important;
        color: #988e83 !important;
    }

    .seller-account-control-page .account-control-workspace .account-control-input {
        min-height: 40px !important;
        height: 40px !important;
        font-size: var(--sac-list-sm) !important;
        font-weight: 400 !important;
    }

    .seller-account-control-page .account-control-workspace select.account-control-input {
        font-weight: 500 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-column-label {
        font-size: var(--sac-list-xs) !important;
        font-weight: 600 !important;
        color: #917f70 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-seller-name {
        font-size: var(--sac-list-md) !important;
        font-weight: 600 !important;
        color: #443b33 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-muted {
        font-size: var(--sac-list-xs) !important;
        color: #9e9489 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-badge {
        font-size: var(--sac-list-xs) !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
    }

    .seller-account-control-page .account-control-workspace .sac-data-value {
        font-size: var(--sac-list-sm) !important;
        font-weight: 500 !important;
        color: #595047 !important;
    }

    .seller-account-control-page .account-control-workspace [class*="xl:hidden"] {
        font-size: var(--sac-list-xs) !important;
    }

    .seller-account-control-page .account-control-workspace #sellerAccountClearFilters,
    .seller-account-control-page .account-control-workspace #sellerAccountEmptyClear {
        font-size: var(--sac-list-xs) !important;
        font-weight: 600 !important;
    }

    @media (min-width: 1280px) {
        .seller-account-control-page .account-control-workspace .account-control-row-grid {
            grid-template-columns:
                minmax(260px, 1.65fr)
                120px
                115px
                130px
                150px
                90px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-account-control-page .account-control-summary-card,
        .seller-account-control-page .account-control-row,
        .seller-account-control-page .account-control-input {
            transition: none !important;
            transform: none !important;
        }
    }
</style>

<div class="seller-account-control-page mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        FLASH MESSAGES
    ========================================================== --}}
    @if (session('success'))
        <div class="mb-5 flex items-start gap-3 rounded-[18px] border border-[#cfe2d5] bg-[#f5faf6] px-4 py-4 shadow-[0_8px_20px_rgba(36,32,26,.03)] sm:px-5">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#dce9e1] bg-white text-[#56816a]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 12 3 3 7-7"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="text-[var(--sac-sm)] font-semibold text-[#5f836b]">Action completed</p>
                <p class="mt-1 text-[var(--sac-xs)] leading-5 text-[#607969]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-3 rounded-[18px] border border-[#ead0d0] bg-[#fff7f7] px-4 py-4 shadow-[0_8px_20px_rgba(36,32,26,.03)] sm:px-5">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#efdddd] bg-white text-[#a65f5f]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="M12 8v5"></path>
                    <path d="M12 16.5h.01"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="text-[var(--sac-sm)] font-semibold text-[#a36a6a]">Action required</p>
                <p class="mt-1 text-[var(--sac-xs)] leading-5 text-[#8d6262]">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif


    {{-- =========================================================
        TOP ACTION
    ========================================================== --}}
    <div class="mb-4 flex justify-end">
        <a
            href="{{ route('admin.seller-compliance') }}"
            wire:navigate
            class="inline-flex h-9 items-center justify-center gap-1.5 rounded-[10px] border border-[#d9930a] bg-[#d9930a] px-3 text-[clamp(.72rem,.69rem+.06vw,.78rem)] font-medium text-white shadow-[0_4px_10px_rgba(217,147,10,.10)] transition duration-200 hover:border-[#c98505] hover:bg-[#c98505]"
        >
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
            Seller Compliance
        </a>
    </div>

    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}
    @php
        $summaryCards = [
            ['label' => 'Total Sellers', 'value' => $stats['total'], 'tone' => 'border-[#dfe7ec] bg-[#f4f7f9] text-[#657f94]', 'icon' => 'users'],
            ['label' => 'Active', 'value' => $stats['active'], 'tone' => 'border-[#d7e7dd] bg-[#f3f8f5] text-[#56816a]', 'icon' => 'active'],
            ['label' => 'Suspended', 'value' => $stats['suspended'], 'tone' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]', 'icon' => 'suspended'],
            ['label' => 'Banned', 'value' => $stats['banned'], 'tone' => 'border-[#ecdada] bg-[#fff3f3] text-[#a65d5d]', 'icon' => 'banned'],
            ['label' => 'Deactivated', 'value' => $stats['deactivated'], 'tone' => 'border-[#e4e0dc] bg-[#f6f4f2] text-[#746d64]', 'icon' => 'deactivated'],
        ];
    @endphp

    <section class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
        @foreach($summaryCards as $card)
            <article class="account-control-summary-card rounded-[17px] border border-[#ebe4da] bg-white p-3.5 sm:p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="sac-summary-label text-[var(--sac-sm)]">{{ $card['label'] }}</p>
                        <p class="sac-summary-value mt-2 text-[clamp(1.40rem,1.27rem+.30vw,1.72rem)]">{{ $card['value'] }}</p>
                    </div>

                    <div class="grid h-[34px] w-[34px] shrink-0 place-items-center rounded-[11px] border {{ $card['tone'] }}">
                        @if($card['icon'] === 'users')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            </svg>
                        @elseif($card['icon'] === 'active')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8.5 12 2.2 2.2 4.8-5"></path>
                            </svg>
                        @elseif($card['icon'] === 'suspended')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M10 8v8"></path>
                                <path d="M14 8v8"></path>
                            </svg>
                        @elseif($card['icon'] === 'banned')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 8 8 8"></path>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7h16"></path>
                                <path d="M6 7v12h12V7"></path>
                                <path d="M9 11h6"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    {{-- =========================================================
        SELLER ACCOUNT WORKSPACE
    ========================================================== --}}
    <section class="account-control-workspace mt-4 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">

        {{-- TOOLBAR --}}
        <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h3 class="sac-workspace-title text-[var(--sac-lg)] tracking-[-.015em]">Seller Accounts</h3>
                    <p class="sac-workspace-copy mt-1 text-[var(--sac-xs)] leading-5">
                        Review seller account status and open controls only when action is required.
                    </p>
                </div>

                <div class="grid w-full grid-cols-1 gap-2.5 sm:grid-cols-[minmax(260px,1fr)_180px] xl:w-[560px]">
                    <div class="relative">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b9287]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input
                            id="sellerAccountSearch"
                            type="search"
                            placeholder="Search seller name or email..."
                            class="account-control-input h-10 w-full rounded-xl border border-[#e4ddd3] pl-10 pr-3"
                        >
                    </div>

                    <div class="relative">
                        <select
                            id="sellerAccountStatusFilter"
                            class="account-control-input h-10 w-full appearance-none rounded-xl border border-[#e4ddd3] pl-3 pr-9 font-medium"
                        >
                            <option value="">All statuses</option>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="banned">Banned</option>
                            <option value="deactivated">Deactivated</option>
                        </select>

                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#91887d]" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                <p id="sellerAccountResultCount" class="sac-result-copy text-[var(--sac-xs)]">
                    Showing {{ $sellers->count() }} seller{{ $sellers->count() === 1 ? '' : 's' }}
                </p>

                <button
                    id="sellerAccountClearFilters"
                    type="button"
                    class="hidden rounded-lg px-3 py-1.5 text-[var(--sac-xs)] font-bold text-[#9a6817] transition hover:bg-[#fff7e9]"
                >
                    Clear filters
                </button>
            </div>
        </div>


        {{-- DESKTOP COLUMN LABELS --}}
        <div class="hidden border-b border-[#eee8df] bg-[#fcfbf9] px-5 py-2.5 xl:grid xl:grid-cols-[minmax(270px,1.55fr)_125px_125px_145px_165px_110px] xl:gap-4 sm:px-6">
            <p class="sac-column-label text-[var(--sac-xs)]">Seller</p>
            <p class="sac-column-label text-[var(--sac-xs)]">Status</p>
            <p class="sac-column-label text-[var(--sac-xs)]">Warnings</p>
            <p class="sac-column-label text-[var(--sac-xs)]">Account</p>
            <p class="sac-column-label text-[var(--sac-xs)]">Suspension</p>
            <p class="sac-column-label text-right text-[var(--sac-xs)]">Action</p>
        </div>


        {{-- SELLER LIST --}}
        <div id="sellerAccountList" class="space-y-3 p-4 sm:p-5">
            @forelse($sellers as $seller)
                @php
                    $status = $seller->account_status ?: 'active';

                    $until = $seller->suspended_until
                        ? \Illuminate\Support\Carbon::parse($seller->suspended_until)
                        : null;

                    $isSuspended =
                        $status === 'active'
                        && (
                            ((int) ($seller->warning_count ?? 0)) >= 3
                            || ($until && now()->lt($until))
                        );

                    $displayStatus = match (true) {
                        $status === 'banned' => 'BANNED',
                        $status === 'deactivated' => 'DEACTIVATED',
                        $isSuspended => 'SUSPENDED',
                        default => 'ACTIVE',
                    };

                    $filterStatus = strtolower($displayStatus);

                    $tone = match($displayStatus) {
                        'ACTIVE' => 'border-[#d4e5da] bg-[#f3f8f5] text-[#56816a]',
                        'SUSPENDED' => 'border-[#ecd9d1] bg-[#fff6f2] text-[#a86858]',
                        'BANNED' => 'border-[#e9cece] bg-[#fff3f3] text-[#a65353]',
                        default => 'border-[#dfdcd7] bg-[#f5f4f2] text-[#716a63]',
                    };

                    $warningCount = (int) ($seller->warning_count ?? 0);

                    $warningTone = match (true) {
                        $warningCount >= 3 => 'border-[#ecd2d2] bg-[#fff4f4] text-[#a65353]',
                        $warningCount >= 2 => 'border-[#ead9c8] bg-[#fff7ef] text-[#a86b38]',
                        $warningCount >= 1 => 'border-[#eee0c5] bg-[#fff9ef] text-[#a8731f]',
                        default => 'border-[#dfe5e2] bg-[#f6f8f7] text-[#687a70]',
                    };

                    $sellerName = $seller->store_name ?: 'SARI Seller';
                    $sellerInitials = strtoupper(substr(trim($sellerName), 0, 2));
                    $searchText = strtolower(trim($sellerName . ' ' . $seller->email));
                    $modalId = 'sellerControlModal-' . $seller->id;
                @endphp

                <article
                    data-seller-account-row
                    data-seller-account-search="{{ $searchText }}"
                    data-seller-account-status="{{ $filterStatus }}"
                    class="account-control-row rounded-[18px] border border-[#ebe4da] bg-white"
                >
                    <div class="account-control-row-grid">

                        {{-- SELLER --}}
                        <div class="flex min-w-0 items-center gap-3 border-b border-[#f0ebe4] px-4 py-3.5 xl:border-b-0">
                            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#3a342d] text-[var(--sac-xs)] font-bold text-white">
                                {{ $sellerInitials }}
                            </div>

                            <div class="min-w-0">
                                <p class="sac-seller-name truncate text-[var(--sac-md)]">{{ $sellerName }}</p>
                                <p class="sac-muted mt-1 truncate text-[var(--sac-xs)]">{{ $seller->email }}</p>
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Status</span>
                            <span class="sac-badge rounded-full border px-2.5 py-1.5 text-[var(--sac-xs)] {{ $tone }}">{{ $displayStatus }}</span>
                        </div>

                        {{-- WARNINGS --}}
                        <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Warnings</span>
                            <span class="sac-badge rounded-full border px-2.5 py-1.5 text-[var(--sac-xs)] {{ $warningTone }}">
                                {{ $warningCount }} / 3
                            </span>
                        </div>

                        {{-- ACCOUNT --}}
                        <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Account</span>
                            <span class="sac-data-value text-[var(--sac-sm)]">{{ ucfirst($status) }}</span>
                        </div>

                        {{-- SUSPENSION --}}
                        <div class="flex items-center justify-between gap-3 border-b border-[#f0ebe4] px-4 py-3 xl:border-b-0 xl:px-0">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Suspension</span>

                            <div class="text-right xl:text-left">
                                <p class="sac-data-value text-[var(--sac-sm)]">
                                    {{ $until ? $until->format('M d, Y') : '—' }}
                                </p>

                                @if($until)
                                    <p class="sac-muted mt-0.5 text-[var(--sac-xs)]">
                                        {{ $until->isFuture() ? $until->diffForHumans() : 'Expired' }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- ACTION --}}
                        <div class="flex items-center justify-between px-4 py-4 xl:justify-end xl:px-0 xl:pr-4">
                            <span class="xl:hidden text-[var(--sac-xs)] font-medium text-[#958c80]">Action</span>

                            <button
                                type="button"
                                data-seller-control-open="{{ $modalId }}"
                                title="Manage seller"
                                aria-label="Manage seller"
                                class="grid h-10 w-10 place-items-center rounded-xl border border-[#e6dbc8] bg-[#fffaf4] text-[#a17228] transition hover:border-[#d8c39a] hover:bg-[#fff7ea]"
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                </article>


                {{-- =====================================================
                    SELLER MANAGEMENT MODAL
                ====================================================== --}}
                <div
                    id="{{ $modalId }}"
                    data-seller-control-modal
                    hidden
                    class="fixed inset-0 z-[190] items-center justify-center bg-black/40 p-4 backdrop-blur-[3px]"
                >
                    <div class="flex max-h-[90vh] w-full max-w-[760px] flex-col overflow-hidden rounded-[24px] border border-[#e8dfd3] bg-white shadow-[0_30px_90px_rgba(37,29,19,.22)]">

                        {{-- MODAL HEADER --}}
                        <div class="flex shrink-0 items-start justify-between gap-4 border-b border-[#eee8df] px-5 py-5 sm:px-6">
                            <div class="flex min-w-0 items-start gap-3.5">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#3a342d] text-[var(--sac-xs)] font-bold text-white">
                                    {{ $sellerInitials }}
                                </div>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="sac-modal-title text-[var(--sac-lg)]">{{ $sellerName }}</h3>
                                        <span class="sac-badge rounded-full border px-2.5 py-1 text-[var(--sac-xs)] {{ $tone }}">{{ $displayStatus }}</span>
                                    </div>

                                    <p class="sac-muted mt-1 text-[var(--sac-xs)]">{{ $seller->email }}</p>
                                </div>
                            </div>

                            <button
                                type="button"
                                data-seller-control-close
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e6dfd5] bg-white text-[#756d63] transition hover:bg-[#fffaf2]"
                                aria-label="Close seller account controls"
                            >
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="m7 7 10 10"></path>
                                    <path d="m17 7-10 10"></path>
                                </svg>
                            </button>
                        </div>


                        {{-- MODAL BODY --}}
                        <div class="min-h-0 flex-1 overflow-y-auto bg-[#fcfbf9] p-5 sm:p-6">

                            {{-- SNAPSHOT --}}
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                <div class="rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                    <p class="sac-muted text-[var(--sac-xs)]">Warnings</p>
                                    <p class="sac-modal-value mt-1.5 text-[var(--sac-md)] {{ $warningCount >= 2 ? 'text-[#a86b67]' : 'text-[#a27635]' }}">
                                        {{ $warningCount }} / 3
                                    </p>
                                </div>

                                <div class="rounded-[14px] border border-[#e8e2d9] bg-white p-3.5">
                                    <p class="sac-muted text-[var(--sac-xs)]">Account</p>
                                    <p class="sac-modal-value mt-1.5 text-[var(--sac-md)]">{{ ucfirst($status) }}</p>
                                </div>

                                <div class="col-span-2 rounded-[14px] border border-[#e8e2d9] bg-white p-3.5 sm:col-span-1">
                                    <p class="sac-muted text-[var(--sac-xs)]">Suspended Until</p>
                                    <p class="sac-modal-value mt-1.5 text-[var(--sac-md)]">
                                        {{ $until ? $until->format('M d, Y') : '—' }}
                                    </p>
                                </div>
                            </div>


                            {{-- STATUS REASON --}}
                            @if($status === 'banned' && $seller->ban_reason)
                                <div class="mt-4 rounded-[14px] border border-[#ead3d3] bg-[#fff7f7] p-4">
                                    <p class="text-[var(--sac-xs)] font-bold text-[#9d5f5f]">Ban reason</p>
                                    <p class="mt-1.5 text-[var(--sac-sm)] leading-5 text-[#8f6262]">{{ $seller->ban_reason }}</p>
                                </div>
                            @elseif($status === 'deactivated' && $seller->deactivation_reason)
                                <div class="mt-4 rounded-[14px] border border-[#e1ddd8] bg-white p-4">
                                    <p class="text-[var(--sac-xs)] font-bold text-[#746e67]">Deactivation reason</p>
                                    <p class="mt-1.5 text-[var(--sac-sm)] leading-5 text-[#746e67]">{{ $seller->deactivation_reason }}</p>
                                </div>
                            @elseif($isSuspended && $seller->suspension_reason)
                                <div class="mt-4 rounded-[14px] border border-[#ead9d4] bg-[#fff9f7] p-4">
                                    <p class="text-[var(--sac-xs)] font-bold text-[#8d665e]">Suspension reason</p>
                                    <p class="mt-1.5 text-[var(--sac-sm)] leading-5 text-[#876a64]">{{ $seller->suspension_reason }}</p>
                                </div>
                            @endif


                            {{-- AVAILABLE ACTIONS --}}
                            <section class="mt-4 rounded-[16px] border border-[#e8e2d9] bg-white p-4">
                                <div>
                                    <p class="sac-modal-section-title text-[var(--sac-md)]">Available Actions</p>
                                    <p class="sac-muted mt-1 text-[var(--sac-xs)] leading-5">
                                        Only actions valid for the seller's current account state are shown.
                                    </p>
                                </div>

                                <div class="mt-4 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                                    @if($status === 'active')

                                        @if($isSuspended)
                                            <form method="POST" action="{{ route('admin.compliance.sellers.unsuspend', $seller) }}">
                                                @csrf
                                                <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#cfe1d5] bg-[#f2f8f4] px-3 text-[var(--sac-sm)] font-semibold text-[#5f836b] transition hover:bg-[#eaf5ed]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                                                        <path d="M3 3v6h6"></path>
                                                    </svg>
                                                    Lift Suspension
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.compliance.sellers.suspend30', $seller) }}">
                                                @csrf
                                                <input type="hidden" name="reason" value="Manual 30-day suspension issued from Seller Account Control.">

                                                <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#ead8d2] bg-[#fff7f3] px-3 text-[var(--sac-sm)] font-semibold text-[#a96f62] transition hover:bg-[#fff1eb]">
                                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                        <circle cx="12" cy="12" r="8"></circle>
                                                        <path d="M10 9v6"></path>
                                                        <path d="M14 9v6"></path>
                                                    </svg>
                                                    Suspend 30 Days
                                                </button>
                                            </form>
                                        @endif

                                        <button
                                            type="button"
                                            data-account-action="ban"
                                            data-seller="{{ $sellerName }}"
                                            data-url="{{ route('admin.seller-accounts.ban', $seller) }}"
                                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-[#e6caca] bg-[#fff5f5] px-3 text-[var(--sac-sm)] font-semibold text-[#a86464] transition hover:bg-[#ffeded]"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <circle cx="12" cy="12" r="8"></circle>
                                                <path d="m8.5 8.5 7 7"></path>
                                            </svg>
                                            Ban Seller
                                        </button>

                                        <button
                                            type="button"
                                            data-account-action="deactivate"
                                            data-seller="{{ $sellerName }}"
                                            data-url="{{ route('admin.seller-accounts.deactivate', $seller) }}"
                                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-[#e0d8d0] bg-[#f7f5f2] px-3 text-[var(--sac-sm)] font-semibold text-[#71675d] transition hover:bg-[#f0ede8]"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <path d="M4 7h16"></path>
                                                <path d="M9 7V4h6v3"></path>
                                                <path d="M7 7l1 13h8l1-13"></path>
                                            </svg>
                                            Deactivate Account
                                        </button>

                                    @elseif($status === 'banned')

                                        <form method="POST" action="{{ route('admin.seller-accounts.unban', $seller) }}">
                                            @csrf
                                            <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#cfe1d5] bg-[#f2f8f4] px-3 text-[var(--sac-sm)] font-semibold text-[#5f836b] transition hover:bg-[#eaf5ed]">
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                                                    <path d="M3 3v6h6"></path>
                                                </svg>
                                                Unban Seller
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            data-account-action="deactivate"
                                            data-seller="{{ $sellerName }}"
                                            data-url="{{ route('admin.seller-accounts.deactivate', $seller) }}"
                                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-[#e0d8d0] bg-[#f7f5f2] px-3 text-[var(--sac-sm)] font-semibold text-[#71675d] transition hover:bg-[#f0ede8]"
                                        >
                                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                <path d="M4 7h16"></path>
                                                <path d="M9 7V4h6v3"></path>
                                                <path d="M7 7l1 13h8l1-13"></path>
                                            </svg>
                                            Deactivate Account
                                        </button>

                                    @else

                                        <form method="POST" action="{{ route('admin.seller-accounts.restore', $seller) }}" class="sm:col-span-2">
                                            @csrf
                                            <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-[#cfe1d5] bg-[#f2f8f4] px-3 text-[var(--sac-sm)] font-semibold text-[#5f836b] transition hover:bg-[#eaf5ed]">
                                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                                                    <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                                                    <path d="M3 3v6h6"></path>
                                                </svg>
                                                Restore Account
                                            </button>
                                        </form>

                                    @endif
                                </div>
                            </section>


                            {{-- PRESERVATION NOTE --}}
                            <div class="mt-4 flex items-start gap-3 rounded-[14px] border border-[#e5ddd1] bg-[#fffaf3] p-4">
                                <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="8"></circle>
                                    <path d="M12 8v4"></path>
                                    <path d="M12 16h.01"></path>
                                </svg>

                                <p class="text-[var(--sac-xs)] leading-5 text-[#88775f]">
                                    Deactivation blocks seller access but preserves compliance evidence, moderation history, and account records.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center">
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl border border-[#ebe4da] bg-white text-[#91887d]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="8" r="3"></circle>
                            <path d="M5 20a7 7 0 0 1 14 0"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[var(--sac-md)] font-bold text-[#514a42]">No seller accounts found</p>
                    <p class="mt-1 text-[var(--sac-xs)] text-[#91887d]">Seller accounts will appear here when available.</p>
                </div>
            @endforelse
        </div>


        {{-- FILTER EMPTY --}}
        <div id="sellerAccountFilterEmpty" class="hidden p-5">
            <div class="rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center">
                <p class="text-[var(--sac-md)] font-bold text-[#514a42]">No matching sellers</p>
                <p class="mt-1 text-[var(--sac-xs)] text-[#91887d]">Try another seller name or account status.</p>

                <button
                    id="sellerAccountEmptyClear"
                    type="button"
                    class="mt-4 rounded-xl border border-[#e2d8c8] bg-white px-4 py-2.5 text-[var(--sac-sm)] font-bold text-[#9a6817] transition hover:bg-[#fffaf2]"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    </section>
</div>


{{-- =============================================================
    BAN / DEACTIVATION CONFIRMATION MODAL
============================================================== --}}
<div
    id="sellerAccountActionModal"
    class="fixed inset-0 z-[220] hidden items-center justify-center bg-black/45 p-4 backdrop-blur-[3px]"
>
    <div class="w-full max-w-[520px] rounded-[24px] border border-[#ead8d1] bg-white p-5 shadow-[0_35px_100px_rgba(38,30,18,.24)] sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-[var(--sac-xs)] font-bold uppercase tracking-[.10em] text-[#a8731f]">Account Action</p>
                <h3 id="sellerAccountActionTitle" class="sac-modal-title mt-1.5 text-[var(--sac-lg)]">Confirm Action</h3>
                <p id="sellerAccountActionDescription" class="mt-1.5 text-[var(--sac-sm)] leading-5 text-[#81786c]"></p>
            </div>

            <button
                type="button"
                id="sellerAccountActionClose"
                class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#e4ddd3] bg-white text-[#756d63] transition hover:bg-[#fffaf2]"
                aria-label="Close"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m7 7 10 10"></path>
                    <path d="m17 7-10 10"></path>
                </svg>
            </button>
        </div>

        <form id="sellerAccountActionForm" method="POST" action="" class="mt-5">
            @csrf

            <label class="text-[var(--sac-sm)] font-semibold text-[#514a41]">Reason *</label>

            <textarea
                name="reason"
                rows="4"
                required
                class="account-control-input mt-2 w-full resize-none rounded-xl border border-[#e4ddd3] px-4 py-3"
                placeholder="Enter the administrator reason..."
            ></textarea>

            <div id="sellerDeleteConfirmWrap" class="mt-4 hidden">
                <label class="text-[var(--sac-sm)] font-semibold text-[#8d5151]">Type DELETE to confirm *</label>

                <input
                    id="sellerDeleteConfirmInput"
                    name="confirmation"
                    type="text"
                    class="account-control-input mt-2 h-11 w-full rounded-xl border border-[#e4caca] bg-[#fffafa] px-4"
                    placeholder="DELETE"
                >
            </div>

            <div class="mt-5 flex justify-end gap-2 border-t border-[#eee8df] pt-4">
                <button
                    type="button"
                    id="sellerAccountActionCancel"
                    class="h-10 rounded-xl border border-[#e0d7ca] bg-white px-5 text-[var(--sac-sm)] font-semibold text-[#62594e]"
                >
                    Cancel
                </button>

                <button
                    id="sellerAccountActionSubmit"
                    type="submit"
                    class="h-10 rounded-xl bg-[#a95656] px-5 text-[var(--sac-sm)] font-semibold text-white transition hover:bg-[#984a4a]"
                >
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

@endsection


@push('scripts')
<script>
(function () {
    /*
    |--------------------------------------------------------------------------
    | SEARCH + STATUS FILTER
    |--------------------------------------------------------------------------
    */
    const searchInput = document.getElementById('sellerAccountSearch');
    const statusFilter = document.getElementById('sellerAccountStatusFilter');
    const sellerRows = Array.from(document.querySelectorAll('[data-seller-account-row]'));
    const sellerList = document.getElementById('sellerAccountList');
    const filterEmpty = document.getElementById('sellerAccountFilterEmpty');
    const resultCount = document.getElementById('sellerAccountResultCount');
    const clearFiltersButton = document.getElementById('sellerAccountClearFilters');
    const emptyClearButton = document.getElementById('sellerAccountEmptyClear');

    function applySellerFilters() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        const status = (statusFilter?.value || '').trim().toLowerCase();

        let visible = 0;

        sellerRows.forEach(function (row) {
            const searchable = (row.dataset.sellerAccountSearch || '').toLowerCase();
            const rowStatus = (row.dataset.sellerAccountStatus || '').toLowerCase();

            const matchesQuery = query === '' || searchable.includes(query);
            const matchesStatus = status === '' || rowStatus === status;
            const matches = matchesQuery && matchesStatus;

            row.classList.toggle('hidden', !matches);

            if (matches) {
                visible++;
            }
        });

        if (resultCount) {
            resultCount.textContent =
                'Showing ' + visible +
                ' of ' + sellerRows.length +
                ' seller' + (visible === 1 ? '' : 's');
        }

        const hasFilters = query !== '' || status !== '';

        clearFiltersButton?.classList.toggle('hidden', !hasFilters);

        if (sellerRows.length > 0) {
            sellerList?.classList.toggle('hidden', visible === 0);
            filterEmpty?.classList.toggle('hidden', visible !== 0);
        }
    }

    function clearSellerFilters() {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        applySellerFilters();
        searchInput?.focus();
    }

    searchInput?.addEventListener('input', applySellerFilters);
    statusFilter?.addEventListener('change', applySellerFilters);
    clearFiltersButton?.addEventListener('click', clearSellerFilters);
    emptyClearButton?.addEventListener('click', clearSellerFilters);

    applySellerFilters();


    /*
    |--------------------------------------------------------------------------
    | SELLER MANAGEMENT MODALS
    |--------------------------------------------------------------------------
    */
    const sellerControlModals = Array.from(
        document.querySelectorAll('[data-seller-control-modal]')
    );

    function closeSellerControlModals() {
        sellerControlModals.forEach(function (modal) {
            modal.hidden = true;
            modal.classList.remove('flex');
        });

        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-seller-control-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            const modal = document.getElementById(this.dataset.sellerControlOpen);

            if (!modal) return;

            closeSellerControlModals();

            modal.hidden = false;
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });

    document.querySelectorAll('[data-seller-control-close]').forEach(function (button) {
        button.addEventListener('click', closeSellerControlModals);
    });

    sellerControlModals.forEach(function (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeSellerControlModals();
            }
        });
    });


    /*
    |--------------------------------------------------------------------------
    | BAN / DEACTIVATE CONFIRMATION MODAL
    |--------------------------------------------------------------------------
    */
    const actionModal = document.getElementById('sellerAccountActionModal');
    const actionForm = document.getElementById('sellerAccountActionForm');
    const actionTitle = document.getElementById('sellerAccountActionTitle');
    const actionDescription = document.getElementById('sellerAccountActionDescription');
    const confirmWrap = document.getElementById('sellerDeleteConfirmWrap');
    const confirmInput = document.getElementById('sellerDeleteConfirmInput');

    function closeActionModal() {
        actionModal?.classList.add('hidden');
        actionModal?.classList.remove('flex');
        actionForm?.reset();
        confirmWrap?.classList.add('hidden');

        if (confirmInput) {
            confirmInput.required = false;
        }

        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-account-action]').forEach(function (button) {
        button.addEventListener('click', function () {
            const action = this.dataset.accountAction;
            const seller = this.dataset.seller;

            actionForm.action = this.dataset.url;

            const deactivating = action === 'deactivate';

            actionTitle.textContent =
                deactivating
                    ? 'Deactivate Seller Account?'
                    : 'Ban Seller Account?';

            actionDescription.textContent =
                deactivating
                    ? `${seller} will be blocked from seller access while compliance and moderation history remain preserved.`
                    : `${seller} will be blocked from seller login until an administrator removes the ban.`;

            confirmWrap.classList.toggle('hidden', !deactivating);

            if (confirmInput) {
                confirmInput.required = deactivating;
            }

            closeSellerControlModals();

            actionModal.classList.remove('hidden');
            actionModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });

    document.getElementById('sellerAccountActionCancel')?.addEventListener('click', closeActionModal);
    document.getElementById('sellerAccountActionClose')?.addEventListener('click', closeActionModal);

    actionModal?.addEventListener('click', function (event) {
        if (event.target === actionModal) {
            closeActionModal();
        }
    });


    /*
    |--------------------------------------------------------------------------
    | ESCAPE
    |--------------------------------------------------------------------------
    */
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        if (
            actionModal &&
            !actionModal.classList.contains('hidden')
        ) {
            closeActionModal();
            return;
        }

        closeSellerControlModals();
    });
})();
</script>
@endpush
