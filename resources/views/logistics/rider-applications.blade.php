@extends('layouts.logistics')

@section('title', 'Rider Applications | SARI Logistics')
@section('page-title', 'Rider Applications')

@push('styles')
<style>
    /* Seller Orders visual scale, scoped to the Rider Applications workspace. */
    .rider-applications-page {
        --ra-gold: #C88912;
        --ra-gold-hover: #B47B0E;
        --ra-ink: var(--sari-dark-e23e64dd1e, #2F3742);
        --ra-muted: var(--sari-dark-a63aa16d7c, #8793A1);
        --ra-border: var(--sari-dark-d8c2716225, #E5E7EB);
        --ra-surface: var(--sari-dark-950baec030, #FFFFFF);
        --ra-soft: var(--sari-dark-4d21eb669b, #F8FAFC);
        width: 100%; max-width: 1520px !important; margin: 0 auto; padding-bottom: 28px;
        color: var(--ra-ink);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    }
    .rider-applications-page *, .ra-dialog { font-family: inherit; }
    .rider-applications-page [hidden] { display: none !important; }
    .rider-applications-page .ra-page-header {
        min-height: 0; margin: 0 0 20px; padding: 0 0 14px;
        border-bottom: 1px solid var(--ra-border);
    }
    .ra-page-header > p:first-child {
        margin: 0; font-size: 10.5px !important; font-weight: 800;
        line-height: 1.4; letter-spacing: .14em; color: var(--sari-dark-685eb4fc40, #A87316);
    }
    .ra-page-header h1 {
        margin: 6px 0 0; font-size: clamp(27px, 2.3vw, 31px) !important;
        font-weight: 760; line-height: 1.08; letter-spacing: -.04em; color: var(--ra-ink);
    }
    .ra-page-header h1 span { color: var(--sari-dark-19b301f69a, #C88912); }
    .rider-applications-page .ra-summary-grid {
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin: 0;
    }
    .ra-kpi, .ra-panel {
        border: 1px solid var(--ra-border); border-radius: 18px; background: var(--ra-surface);
        box-shadow: 0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)),
                    0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055));
    }
    .rider-applications-page .ra-kpi {
        display: flex; align-items: flex-start; justify-content: space-between;
        gap: 12px; min-height: 110px; padding: 14px 16px;
    }
    .ra-kpi > div > p:first-child {
        margin: 0; font-size: 13px !important; font-weight: 650; line-height: 1.4;
        color: var(--sari-dark-a63aa16d7c, #637081);
    }
    .ra-kpi > div > p:nth-child(2) {
        margin: 7px 0 0; font-size: clamp(25px, 1.9vw, 30px) !important;
        font-weight: 650; line-height: 1.12; letter-spacing: -.02em;
        font-variant-numeric: tabular-nums; color: var(--ra-ink);
    }
    .ra-kpi > span, .ra-kpi > span svg { width: 27px; height: 27px; flex: 0 0 27px; }
    .ra-kpi > span svg { stroke-width: 1.8; }
    .rider-applications-page .ra-filter-bar {
        display: grid; grid-template-columns: minmax(0,1fr) minmax(185px,210px) auto auto;
        align-items: center; gap: 12px; position: relative; z-index: 15;
        margin: 20px 0 0; padding: 0; border: 0; background: transparent; box-shadow: none;
    }
    .ra-filter-bar > *, .ra-filter-bar form > * { min-width: 0; }
    .ra-filter-bar input, .ra-status-trigger, .ra-filter-apply, .ra-filter-reset {
        width: 100%; height: 48px !important; min-height: 48px; border-radius: 14px !important;
        font-family: inherit; box-shadow: none; transform: none !important; translate: none !important;
    }
    .ra-filter-bar input, .ra-status-trigger, .ra-filter-reset {
        border: 1px solid var(--sari-dark-d8c2716225, #DCE2E8);
        background: var(--ra-surface); color: var(--ra-ink);
        font-size: 11.5px !important; font-weight: 600;
    }
    .ra-filter-bar input { padding: 0 14px 0 46px; }
    .ra-filter-bar input::placeholder { color: var(--ra-muted); font-size: 11.5px; font-weight: 400; }
    .ra-search-icon { position: absolute; top: 0; bottom: 0; left: 16px; display: grid; place-items: center; width: 21px; pointer-events: none; color: var(--ra-muted); }
    .ra-search-icon svg { width: 21px; height: 21px; }
    .ra-filter-form { display: contents; }
    .ra-status-dropdown { position: relative; z-index: 50; }
    .ra-status-dropdown[data-enhanced] .ra-native-status {
        position: absolute !important; width: 1px !important; height: 1px !important;
        margin: 0 !important; padding: 0 !important; clip-path: inset(50%);
        overflow: hidden; opacity: 0; pointer-events: none;
    }
    .ra-status-trigger { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 0 16px; text-align: left; cursor: pointer; }
    .ra-status-trigger svg { width: 17px; height: 17px; flex-shrink: 0; transition: transform .15s ease; }
    .ra-status-trigger[aria-expanded="true"] svg { transform: rotate(180deg); }
    .ra-status-menu {
        position: absolute; top: calc(100% + 8px); left: 0; right: 0; z-index: 100;
        width: 100%; margin: 0; padding: 8px; border: 1px solid var(--ra-border);
        border-radius: 14px; background: var(--ra-surface);
        box-shadow: 0 16px 32px rgba(15,23,42,.12), 0 2px 6px rgba(15,23,42,.05);
    }
    .ra-status-option {
        display: flex; align-items: center; width: 100%; min-height: 38px;
        padding: 0 12px; border: 0; border-radius: 10px; background: transparent;
        color: var(--ra-ink); font-size: 11.5px; font-weight: 600; text-align: left; cursor: pointer;
    }
    .ra-status-option:hover, .ra-status-option:focus-visible, .ra-status-option[aria-selected="true"] { background: var(--ra-soft); color: var(--sari-dark-af18afcaa4, #A56D0A); }
    .ra-status-option[aria-selected="true"] { font-weight: 700; }
    .ra-filter-apply, .ra-filter-reset { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 18px; font-size: 12px !important; font-weight: 700; white-space: nowrap; cursor: pointer; text-decoration: none; }
    .ra-filter-apply { border: 1px solid var(--ra-gold); background: var(--ra-gold); color: #FFF; }
    .ra-filter-apply:hover { background: var(--ra-gold-hover); border-color: var(--ra-gold-hover); }
    .ra-filter-apply svg { width: 15px; height: 15px; }
    .ra-filter-reset { min-width: 86px; }
    .ra-filter-reset:hover, .ra-filter-bar input:hover, .ra-status-trigger:hover { background: var(--ra-soft); border-color: var(--sari-dark-d8c2716225, #CFD6DE); }
    .rider-applications-page .ra-focus:focus-visible,
    .rider-applications-page .ra-status-trigger[aria-expanded="true"] {
        outline: 2px solid var(--ra-gold); outline-offset: 2px; border-color: var(--ra-gold);
    }
    .ra-filter-bar input:focus { outline: 2px solid var(--ra-gold); outline-offset: 2px; }
    .rider-applications-page .ra-queue-card { margin-top: 20px; overflow: hidden; }
    .ra-table-titlebar { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 21px 23px; border-bottom: 1px solid var(--ra-border); }
    .ra-table-titlebar h2 { margin: 0; color: var(--ra-ink); font-size: 18px; font-weight: 780; }
    .ra-table-titlebar p { margin: 4px 0 0; color: var(--ra-muted); font-size: 11.5px; line-height: 1.5; }
    .ra-table-counter { flex-shrink: 0; color: var(--ra-muted); font-size: 12px; font-weight: 600; }
    .ra-table-scroll { overflow-x: auto; width: 100%; scrollbar-width: thin; }
    .ra-table-scroll:focus-visible { outline: 2px solid var(--ra-gold); outline-offset: -2px; }
    .rider-applications-page .ra-queue-card table { min-width: 1060px; }
    .ra-queue-card thead { background: var(--ra-soft); }
    .ra-queue-card th { height: 48px; padding: 13px 16px; font-size: 11px; font-weight: 750; line-height: 1.4; letter-spacing: .075em; text-transform: uppercase; color: var(--ra-muted); border-bottom: 1px solid var(--ra-border); }
    .ra-queue-card tbody { background: var(--ra-surface); }
    .ra-queue-card td { padding: 17px 16px; font-size: 11.5px; border-bottom: 1px solid var(--ra-border); }
    .ra-table-row { background: var(--ra-surface); color: var(--ra-ink); transition: background-color .18s ease; }
    .ra-table-row:hover { background: var(--ra-soft); }
    .ra-queue-card td p { margin: 0; color: var(--ra-ink); font-size: 11.5px; line-height: 1.5; }
    .ra-queue-card td p + p { margin-top: 3px; color: var(--ra-muted); font-size: 11px; }
    .ra-queue-card td:first-child p:first-child { font-size: 12.5px; font-weight: 750; text-transform: none; letter-spacing: normal; }
    .ra-queue-card td:nth-child(2) p:first-child, .ra-queue-card td:nth-child(6) p:first-child { font-weight: 650; }
    .ra-queue-card tbody td:first-child img, .ra-queue-card tbody td:first-child span.grid { width: 44px; height: 44px; flex: 0 0 44px; border-radius: 9px; }
    .ra-queue-card td:nth-child(3) > span, .ra-queue-card td:nth-child(4) > span { min-height: 27px; padding: 5px 10px; font-size: 10.5px; font-weight: 700; line-height: 1.3; letter-spacing: normal; }
    .ra-queue-card [data-open-review] { display: inline-grid; place-items: center; width: 32px; height: 32px; min-width: 32px; padding: 0; border: 1px solid var(--ra-border); border-radius: 10px; background: var(--ra-surface); color: var(--ra-muted); box-shadow: none; cursor: pointer; }
    .ra-queue-card [data-open-review] svg { width: 17px; height: 17px; }
    .ra-queue-card [data-open-review]:hover { border-color: var(--ra-gold); color: var(--ra-gold); background: var(--ra-soft); }
    .ra-table-footer { display: flex; min-height: 64px; align-items: center; justify-content: space-between; gap: 16px; padding: 13px 20px; border-top: 1px solid var(--ra-border); background: var(--ra-surface); }
    .ra-result-count { margin: 0; font-size: 11px; font-weight: 550; color: var(--ra-muted); }
    .ra-pagination, .ra-page-numbers { display: flex; align-items: center; gap: 6px; }
    .ra-pagination button { display: grid; place-items: center; width: 34px; min-width: 34px; height: 34px; padding: 0; border: 1px solid var(--ra-border); border-radius: 10px; background: var(--ra-surface); color: var(--ra-muted); font: 700 11.5px/1 'Poppins',sans-serif; cursor: pointer; }
    .ra-pagination svg { width: 15px; height: 15px; }
    .ra-pagination button[aria-current="page"] { background: var(--ra-gold); border-color: var(--ra-gold); color: #FFF; }
    .ra-pagination button:hover:not(:disabled) { border-color: var(--ra-gold); }
    .ra-pagination button:disabled { opacity: .42; cursor: not-allowed; }
    .ra-empty-cell, #riderSearchEmpty { padding: 72px 20px !important; text-align: center; }
    .ra-queue-card table.ra-table-empty { min-width: 0 !important; table-layout: auto; }
    .ra-queue-card table.ra-table-empty thead { display: none; }
    .ra-empty-icon { display: grid; width: 92px; height: 92px; margin: 0 auto; place-items: center; color: var(--ra-muted); }
    .ra-empty-icon svg { width: 58px; height: 58px; stroke-width: 1.6; }
    .ra-queue-card td .ra-empty-title, #riderSearchEmpty > p:first-child { margin: 10px 0 0; font-size: 22px; font-weight: 700; color: var(--ra-muted); }
    .ra-queue-card td .ra-empty-copy, #riderSearchEmpty > p:nth-child(2) { max-width: 480px; margin: 8px auto 0; font-size: 12px; line-height: 1.6; color: var(--ra-muted); }
    /* Keep the existing protected-document and approval/rejection dialogs. */
    .ra-dialog { width: min(760px, calc(100vw - 28px)); max-height: min(860px, calc(100dvh - 40px)); margin: auto; padding: 0; border: 1px solid var(--ra-border); border-radius: 20px; background: var(--ra-surface); color: var(--ra-ink); box-shadow: 0 30px 80px rgba(20,24,30,.24); }
    .ra-dialog::backdrop { background: rgba(23,27,33,.44); backdrop-filter: blur(5px); }
    .ra-dialog-body { max-height: calc(100dvh - 42px); overflow-y: auto; }
    @media (max-width: 1180px) { .rider-applications-page .ra-summary-grid { grid-template-columns: repeat(2,minmax(0,1fr)); } }
    @media (max-width: 900px) {
        .rider-applications-page .ra-filter-bar { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .ra-search-field, .ra-status-dropdown { grid-column: 1 / -1; }
    }
    @media (max-width: 767px) {
        .ra-page-header h1 { font-size: 25px !important; }
        .ra-page-header > p:first-child { font-size: 9.5px !important; }
        .ra-kpi > div > p:first-child { font-size: 12.5px !important; }
        .ra-empty-cell, #riderSearchEmpty { padding: 56px 16px !important; }
        .ra-empty-icon { width: 64px; height: 64px; }
        .ra-empty-icon svg { width: 40px; height: 40px; }
    }
    @media (max-width: 540px) {
        .rider-applications-page .ra-summary-grid { grid-template-columns: 1fr; gap: 11px; }
        .rider-applications-page .ra-kpi { min-height: 100px; }
        .ra-table-titlebar { align-items: flex-start; flex-direction: column; padding: 17px 16px; gap: 6px; }
        .ra-table-footer { flex-direction: column; align-items: flex-start; gap: 12px; }
        .ra-pagination { align-self: flex-end; }
    }
    @media (prefers-reduced-motion: reduce) { .rider-applications-page * { transition: none !important; animation: none !important; } }

    .ra-status-dropdown:not([data-enhanced]) .ra-native-status { width: 100%; height: 48px; padding: 0 16px; border: 1px solid var(--ra-border); border-radius: 14px; background: var(--ra-surface); color: var(--ra-ink); font-size: 11.5px; }
</style>
@endpush

@section('content')
<div class="sari-logistics-ui rider-applications-page mx-auto w-full max-w-[1520px] text-[#171b20]">

    @if (session('success'))
        <div class="mb-4 rounded-[14px] border border-[#cfe3d6] bg-[#f4faf6] px-4 py-3 text-[12px] font-medium text-[#4f725d]" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-[14px] border border-[#edcccc] bg-[#fff6f6] px-4 py-3 text-[12px] font-medium text-[#a04f4f]" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PAGE HEADING --}}
    <section class="ra-page-header border-b border-[#e8eaed] pb-5">
        <p class="text-[10px] font-semibold uppercase tracking-[.18em] text-[#bd7f0a]">Rider Recruitment</p>
        <h1 class="mt-1 text-[30px] font-semibold tracking-[-.035em] text-[#14181d] sm:text-[38px]">
            Review Rider <span class="text-[#d79612]">applications</span>
        </h1>
    </section>

    {{-- KPI SUMMARY --}}
    @php
        $summaryCards = [
            [
                'label' => 'Total Applications',
                'value' => $stats['total'] ?? 0,
                'tone' => 'gold',
                'icon' => 'users',
            ],
            [
                'label' => 'Pending Review',
                'value' => $stats['pending'] ?? 0,
                'tone' => 'gold',
                'icon' => 'clock',
            ],
            [
                'label' => 'Approved',
                'value' => $stats['approved'] ?? 0,
                'tone' => 'green',
                'icon' => 'check',
            ],
            [
                'label' => 'Rejected',
                'value' => $stats['rejected'] ?? 0,
                'tone' => 'red',
                'icon' => 'x',
            ],
        ];
    @endphp

    <section class="ra-summary-grid mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($summaryCards as $card)
            @php
                $iconTone = match($card['tone']) {
                    'green' => 'text-[#188d6d]',
                    'red' => 'text-[#da5555]',
                    default => 'text-[#bf7e08]',
                };
            @endphp
            <article class="ra-kpi flex min-h-[126px] items-start justify-between gap-4 px-5 py-5">
                <div>
                    <p class="text-[13px] font-medium text-[#606978]">{{ $card['label'] }}</p>
                    <p class="mt-3 text-[34px] font-semibold leading-none tracking-[-.04em] text-[#11161c]">{{ $card['value'] }}</p>
                </div>
                <span class="{{ $iconTone }}" aria-hidden="true">
                    @if($card['icon'] === 'users')
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="7" r="3"></circle><path d="M3.5 20c.5-4 2.7-6.2 5.5-6.2s5 2.2 5.5 6.2"></path><path d="M15 8.5a2.5 2.5 0 1 1 2.5 2.5M15.5 14c2.8.2 4.5 2 5 5"></path>
                        </svg>
                    @elseif($card['icon'] === 'clock')
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path>
                        </svg>
                    @elseif($card['icon'] === 'check')
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle><path d="m8.5 12.2 2.2 2.2 4.8-5"></path>
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle><path d="m9 9 6 6M15 9l-6 6"></path>
                        </svg>
                    @endif
                </span>
            </article>
        @endforeach
    </section>

    {{-- SEARCH + FILTER BAR --}}
    <section class="ra-filter-bar mt-4 grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_200px_190px_136px_86px]">
        <label class="ra-search-field relative block">
            <span class="ra-search-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"></circle><path d="m16 16 4 4"></path></svg>
            </span>
            <span class="sr-only">Search applications</span>
            <input
                id="riderApplicationSearch"
                name="search"
                form="riderStatusForm"
                value="{{ request()->query('search', '') }}"
                type="search"
                placeholder="Search name, email, contact, plate, or municipality..."
                class="ra-focus h-[50px] w-full rounded-[13px] border border-[#dde2e7] bg-white pl-12 pr-4 text-[12px] font-normal text-[#28303a] placeholder:text-[#a2a9b1]"
            >
        </label>


        <form id="riderStatusForm" method="GET" class="ra-filter-form">
            <div class="ra-status-dropdown" data-ra-status-dropdown>
                <label class="sr-only" for="riderApplicationStatus">Application status</label>
                <select id="riderApplicationStatus" name="status" class="ra-native-status ra-focus">
                    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="button" class="ra-status-trigger ra-focus" data-ra-status-trigger
                    aria-haspopup="listbox" aria-expanded="false" aria-controls="riderApplicationStatusMenu"
                    aria-label="Application status" hidden>
                    <span data-ra-status-label>{{ ucfirst($status) }}</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
                </button>
                <div id="riderApplicationStatusMenu" class="ra-status-menu" role="listbox"
                    aria-label="Application status" data-ra-status-menu hidden>
                    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                        <button type="button" role="option" class="ra-status-option ra-focus" tabindex="-1"
                            data-ra-status-option data-value="{{ $value }}" aria-selected="{{ $status === $value ? 'true' : 'false' }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="ra-filter-apply ra-focus">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h16M7 10h10M10 15h4"></path></svg>
                Apply Filter
            </button>
        </form>
        <a href="{{ route('logistics.rider-applications', [], false) }}" class="ra-filter-reset ra-focus">Reset</a>
    </section>

    {{-- APPLICATION QUEUE --}}
    <section class="ra-panel ra-queue-card mt-4 overflow-hidden">
        <div class="ra-table-titlebar">
            <div>
                <h2>Application queue</h2>
                <p>Open a registration to review the complete Rider profile and protected documents.</p>
            </div>
            <span id="riderShowingCount" class="ra-table-counter" aria-live="polite">Showing {{ $applications->count() }} of {{ $applications->count() }}</span>
        </div>

        <div class="ra-table-scroll" tabindex="0" aria-label="Rider applications table, scroll horizontally to see all columns">
            <table aria-label="Rider application records" class="w-full min-w-[1060px] table-fixed text-left {{ $applications->isEmpty() ? 'ra-table-empty' : '' }}">
                <thead class="border-b border-[#dde2e7] bg-[#f7f8f9]">
                    <tr class="text-[9px] font-semibold uppercase tracking-[.08em] text-[#657080]">
                        <th scope="col" class="w-[23%] px-5 py-3.5">Applicant</th>
                        <th scope="col" class="w-[20%] px-5 py-3.5">Contact</th>
                        <th scope="col" class="w-[10%] px-5 py-3.5">Role</th>
                        <th scope="col" class="w-[12%] px-5 py-3.5">Status</th>
                        <th scope="col" class="w-[13%] px-5 py-3.5">Documents</th>
                        <th scope="col" class="w-[14%] px-5 py-3.5">Submitted</th>
                        <th scope="col" class="w-[8%] px-5 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody id="riderApplicationsBody" class="divide-y divide-[#edf0f2] bg-white">
                    @forelse($applications as $application)
                        @php
                            $documentsComplete = collect([$application->id_path, $application->orcr_path])->filter()->count();
                            $searchText = strtolower(implode(' ', array_filter([
                                $application->fullName(),
                                $application->email,
                                $application->contact_no,
                                $application->plate_number,
                                $application->municipality_name,
                                $application->province_name,
                                $application->vehicle_type,
                            ])));
                            $initials = collect(preg_split('/\s+/', trim($application->fullName())))
                                ->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode('');
                        @endphp
                        <tr class="ra-table-row align-middle text-[11px] text-[#49525e]" data-search="{{ $searchText }}">
                            <td class="px-5 py-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    @if($application->profile_image_path)
                                        <img src="{{ asset('storage/' . $application->profile_image_path) }}" alt="{{ $application->fullName() }}" class="h-10 w-10 shrink-0 rounded-full border border-[#e2e5e8] object-cover">
                                    @else
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full border border-[#e5e1da] bg-[#fafafa] text-[10px] font-medium text-[#665d50]" aria-hidden="true">{{ $initials ?: 'R' }}</span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate text-[11px] font-semibold uppercase tracking-[.015em] text-[#1d2228]">{{ $application->fullName() }}</p>
                                        <p class="mt-1 text-[9px] font-normal text-[#8a939e]">ID #{{ $application->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="truncate text-[10px] font-medium text-[#333b45]">{{ $application->email }}</p>
                                <p class="mt-1 text-[9px] font-normal text-[#8a939e]">{{ $application->contact_no ?: 'No contact' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border border-[#e5d6b8] bg-[#fffaf1] px-2.5 py-1 text-[8px] font-semibold uppercase tracking-[.06em] text-[#9a6813]">Rider</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($application->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#cfe5da] bg-[#f3faf6] px-2.5 py-1 text-[8px] font-medium text-[#267356]"><span class="h-1.5 w-1.5 rounded-full bg-[#3ba67f]"></span>Approved</span>
                                @elseif($application->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#efd2d2] bg-[#fff6f6] px-2.5 py-1 text-[8px] font-medium text-[#a84949]"><span class="h-1.5 w-1.5 rounded-full bg-[#dd6161]"></span>Rejected</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[#ecdcb9] bg-[#fffaf0] px-2.5 py-1 text-[8px] font-medium text-[#9a6a16]"><span class="h-1.5 w-1.5 rounded-full bg-[#d69b2c]"></span>Pending</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-[9px] border {{ $documentsComplete === 2 ? 'border-[#cfe4d9] bg-[#f2f9f5] text-[#3d8061]' : 'border-[#e4e7ea] bg-[#f8f9fa] text-[#7e8792]' }}" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 3h8l4 4v14H6V3Z"></path><path d="M14 3v5h5"></path></svg>
                                    </span>
                                    <div>
                                        <p class="text-[9px] font-medium text-[#333b45]">{{ $documentsComplete }}/2 files</p>
                                        <p class="mt-0.5 text-[8px] {{ $documentsComplete === 2 ? 'text-[#438265]' : 'text-[#9a6f23]' }}">{{ $documentsComplete === 2 ? 'Complete' : 'Needs review' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-[9px] font-medium text-[#333b45]">{{ $application->created_at?->format('M d, Y') ?: '—' }}</p>
                                <p class="mt-1 text-[8px] font-normal text-[#8a939e]">{{ $application->created_at?->format('h:i A') ?: '' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button
                                    type="button"
                                    class="ra-focus inline-flex h-9 items-center justify-center gap-2 rounded-[10px] border border-[#dfe3e7] bg-white px-3 text-[9px] font-medium text-[#535d68] transition hover:border-[#d3b36f] hover:bg-[#fffaf0] hover:text-[#8f6417]"
                                    data-open-review
                                    aria-label="Review application for {{ $application->fullName() }}"
                                    onclick="document.getElementById('rider-review-{{ $application->id }}').showModal()"
                                    aria-haspopup="dialog"
                                >
                                    <span class="sr-only">Review</span>
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 6 6 6-6 6"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="ra-empty-cell text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="ra-empty-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8 4h8a2 2 0 0 1 2 2v14H6V6a2 2 0 0 1 2-2Z" />
                                            <path d="M9 9h6M9 13h6M9 17h3" />
                                        </svg>
                                    </span>
                                    <p class="ra-empty-title">No {{ $status }} Rider applications</p>
                                    <p class="ra-empty-copy">New Rider applications submitted to {{ $logistics->displayName() }} will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="riderSearchEmpty" hidden role="status">
            <p class="text-[12px] font-medium text-[#56606c]">No matching application</p>
            <p class="mt-1 text-[10px] text-[#929aa4]">Try a different name, email, contact number, plate, or location.</p>
        </div>
        <div class="ra-table-footer">
            <p id="riderResultCount" class="ra-result-count" aria-live="polite">Showing {{ $applications->count() ? 1 : 0 }}–{{ $applications->count() }} of {{ $applications->count() }} applications</p>
            <nav class="ra-pagination" aria-label="Rider application pagination" data-ra-pagination hidden>
                <button type="button" class="ra-focus" data-ra-page-prev aria-label="Previous page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                </button>
                <div class="ra-page-numbers" data-ra-page-numbers></div>
                <button type="button" class="ra-focus" data-ra-page-next aria-label="Next page" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                </button>
            </nav>
        </div>
    </section>

    {{-- REVIEW MODALS --}}
    @foreach($applications as $application)
        @php
            $documentsComplete = collect([$application->id_path, $application->orcr_path])->filter()->count();
            $initials = collect(preg_split('/\s+/', trim($application->fullName())))
                ->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode('');
        @endphp
        <dialog id="rider-review-{{ $application->id }}" class="ra-dialog" aria-labelledby="rider-review-title-{{ $application->id }}">
            <div class="ra-dialog-body">
                <header class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-[#e7eaee] bg-white px-5 py-4 sm:px-6">
                    <div class="flex min-w-0 items-center gap-3">
                        @if($application->profile_image_path)
                            <img src="{{ asset('storage/' . $application->profile_image_path) }}" alt="{{ $application->fullName() }}" class="h-11 w-11 shrink-0 rounded-full border border-[#e1e5e8] object-cover">
                        @else
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full border border-[#e2e5e8] bg-[#f7f8f9] text-[10px] font-medium text-[#646d78]">{{ $initials ?: 'R' }}</span>
                        @endif
                        <div class="min-w-0">
                            <p class="text-[9px] font-semibold uppercase tracking-[.12em] text-[#bd7f0a]">Rider Application</p>
                            <h3 id="rider-review-title-{{ $application->id }}" class="mt-1 truncate text-[18px] font-semibold tracking-[-.02em] text-[#171b20]">{{ $application->fullName() }}</h3>
                            <p class="mt-1 truncate text-[10px] text-[#818a95]">{{ $application->email }} · {{ $application->contact_no }}</p>
                        </div>
                    </div>
                    <button type="button" class="ra-focus grid h-9 w-9 shrink-0 place-items-center rounded-full border border-[#e0e4e8] text-[#747e89] hover:bg-[#f5f6f7]" onclick="this.closest('dialog').close()" aria-label="Close review">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 7 10 10M17 7 7 17"></path></svg>
                    </button>
                </header>

                <div class="space-y-5 px-5 py-5 sm:px-6">
                    <section>
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="text-[12px] font-semibold text-[#252b32]">Application details</h4>
                            @if($application->status === 'approved')
                                <span class="rounded-full bg-[#eff8f4] px-2.5 py-1 text-[8px] font-medium text-[#287659]">Approved</span>
                            @elseif($application->status === 'rejected')
                                <span class="rounded-full bg-[#fff3f3] px-2.5 py-1 text-[8px] font-medium text-[#a64c4c]">Rejected</span>
                            @else
                                <span class="rounded-full bg-[#fff8ea] px-2.5 py-1 text-[8px] font-medium text-[#966615]">Pending Review</span>
                            @endif
                        </div>

                        <dl class="mt-3 grid grid-cols-1 overflow-hidden rounded-[14px] border border-[#e6e9ec] sm:grid-cols-2">
                            @foreach([
                                ['Vehicle', $application->vehicle_type ?: '—'],
                                ['Plate Number', $application->plate_number ?: '—'],
                                ["Driver's License No.", $application->license_number ?: '—'],
                                ['License Expiry', $application->license_expiry_date?->format('M d, Y') ?? '—'],
                            ] as [$label, $value])
                                <div class="border-b border-[#edf0f2] px-4 py-3 sm:[&:nth-child(odd)]:border-r sm:[&:nth-last-child(-n+2)]:border-b-0">
                                    <dt class="text-[8px] font-medium uppercase tracking-[.08em] text-[#9098a2]">{{ $label }}</dt>
                                    <dd class="mt-1 text-[11px] font-medium text-[#333b45]">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    <section>
                        <h4 class="text-[12px] font-semibold text-[#252b32]">Address</h4>
                        <p class="mt-2 rounded-[13px] border border-[#e6e9ec] bg-[#fafbfb] px-4 py-3 text-[10px] leading-5 text-[#59636f]">
                            {{ $application->street_address }}, {{ $application->barangay_name }}, {{ $application->municipality_name }}, {{ $application->province_name }}
                        </p>
                    </section>

                    <section>
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="text-[12px] font-semibold text-[#252b32]">Protected documents</h4>
                            <span class="text-[9px] font-medium text-[#818a95]">{{ $documentsComplete }}/2 available</span>
                        </div>
                        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @if($application->id_path)
                                <a href="{{ route('logistics.rider-applications.document', [$application, 'id']) }}" target="_blank" rel="noopener noreferrer" class="ra-focus flex min-h-12 items-center justify-between rounded-[12px] border border-[#dfe4e7] bg-white px-4 text-[10px] font-medium text-[#4e5965] hover:bg-[#f8f9fa]">
                                    Driver's License
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#7f8994]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M14 5h5v5M13 11l6-6M19 13v6H5V5h6"></path></svg>
                                </a>
                            @else
                                <div class="flex min-h-12 items-center rounded-[12px] border border-dashed border-[#dfe4e7] px-4 text-[10px] text-[#9aa2ab]">Driver's License unavailable</div>
                            @endif

                            @if($application->orcr_path)
                                <a href="{{ route('logistics.rider-applications.document', [$application, 'orcr']) }}" target="_blank" rel="noopener noreferrer" class="ra-focus flex min-h-12 items-center justify-between rounded-[12px] border border-[#dfe4e7] bg-white px-4 text-[10px] font-medium text-[#4e5965] hover:bg-[#f8f9fa]">
                                    OR / CR
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#7f8994]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M14 5h5v5M13 11l6-6M19 13v6H5V5h6"></path></svg>
                                </a>
                            @else
                                <div class="flex min-h-12 items-center rounded-[12px] border border-dashed border-[#dfe4e7] px-4 text-[10px] text-[#9aa2ab]">OR / CR unavailable</div>
                            @endif
                        </div>
                    </section>

                    @if($application->admin_note)
                        <section>
                            <h4 class="text-[12px] font-semibold text-[#252b32]">Previous review note</h4>
                            <p class="mt-2 rounded-[13px] border border-[#e6e9ec] bg-[#fafbfb] px-4 py-3 text-[10px] leading-5 text-[#66717c]">{{ $application->admin_note }}</p>
                        </section>
                    @endif

                    @if($application->status === 'pending')
                        <section class="border-t border-[#e7eaee] pt-5">
                            <h4 class="text-[12px] font-semibold text-[#252b32]">Decision</h4>
                            <p class="mt-1 text-[9px] text-[#858e99]">Review documents before approving. Rejections require a reason.</p>

                            <div class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-2">
                                <form method="POST" action="{{ route('logistics.rider-applications.approve', $application) }}" class="rounded-[14px] border border-[#d8e8df] bg-[#f7fbf9] p-4">
                                    @csrf
                                    <label class="text-[9px] font-medium text-[#4e6f5d]" for="approve-note-{{ $application->id }}">Approval note <span class="font-normal text-[#8c9a91]">(optional)</span></label>
                                    <input id="approve-note-{{ $application->id }}" type="text" name="review_note" maxlength="1500" placeholder="Optional review note..." class="ra-focus mt-2 h-10 w-full rounded-[10px] border border-[#d9e5de] bg-white px-3 text-[10px] text-[#35403a] placeholder:text-[#a2aaa5]">
                                    <button type="submit" class="ra-focus mt-2 h-10 w-full rounded-[10px] bg-[#438365] text-[10px] font-semibold text-white hover:bg-[#367256]">Approve Application</button>
                                </form>

                                <form method="POST" action="{{ route('logistics.rider-applications.reject', $application) }}" class="rounded-[14px] border border-[#efdada] bg-[#fff9f9] p-4">
                                    @csrf
                                    <label class="text-[9px] font-medium text-[#8c5d5d]" for="reject-note-{{ $application->id }}">Rejection reason</label>
                                    <input id="reject-note-{{ $application->id }}" type="text" name="review_note" required minlength="5" maxlength="1500" placeholder="Required reason for rejection..." class="ra-focus mt-2 h-10 w-full rounded-[10px] border border-[#eadada] bg-white px-3 text-[10px] text-[#483939] placeholder:text-[#b4a1a1]">
                                    <button type="submit" class="ra-focus mt-2 h-10 w-full rounded-[10px] bg-[#aa5a54] text-[10px] font-semibold text-white hover:bg-[#964b46]">Reject Application</button>
                                </form>
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </dialog>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
(() => {
    const root = document.querySelector('.rider-applications-page');
    if (!root) return;

    window.__SARI_RIDER_APPLICATIONS_ABORT__?.abort();
    const controller = new AbortController();
    window.__SARI_RIDER_APPLICATIONS_ABORT__ = controller;
    const { signal } = controller;
    const input = root.querySelector('#riderApplicationSearch');
    const rows = Array.from(root.querySelectorAll('.ra-table-row'));
    const count = root.querySelector('#riderShowingCount');
    const resultCount = root.querySelector('#riderResultCount');
    const empty = root.querySelector('#riderSearchEmpty');
    const pagination = root.querySelector('[data-ra-pagination]');
    const numbers = root.querySelector('[data-ra-page-numbers]');
    const previous = root.querySelector('[data-ra-page-prev]');
    const next = root.querySelector('[data-ra-page-next]');
    const pageSize = 10;
    let currentPage = 1;
    let pageCount = 1;

    function renderApplications({ resetPage = false } = {}) {
        const term = (input?.value || '').trim().toLowerCase();
        const matches = rows.filter(row => !term || (row.dataset.search || '').includes(term));
        pageCount = Math.max(1, Math.ceil(matches.length / pageSize));
        currentPage = resetPage ? 1 : Math.min(Math.max(1, currentPage), pageCount);
        const offset = (currentPage - 1) * pageSize;
        const visibleRows = new Set(matches.slice(offset, offset + pageSize));
        rows.forEach(row => { row.hidden = !visibleRows.has(row); });

        if (count) count.textContent = `Showing ${matches.length} of ${rows.length}`;
        if (resultCount) {
            const from = matches.length ? offset + 1 : 0;
            const to = Math.min(offset + pageSize, matches.length);
            resultCount.textContent = `Showing ${from}–${to} of ${matches.length} applications`;
        }
        if (empty) empty.hidden = rows.length === 0 || matches.length > 0;
        if (pagination) pagination.hidden = pageCount <= 1;
        if (previous) previous.disabled = currentPage <= 1;
        if (next) next.disabled = currentPage >= pageCount;
        if (!numbers) return;
        numbers.replaceChildren();
        const start = Math.max(1, Math.min(currentPage - 2, pageCount - 4));
        const end = Math.min(pageCount, start + 4);
        for (let page = start; page <= end; page += 1) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'ra-focus';
            button.dataset.raPage = String(page);
            button.textContent = String(page);
            button.setAttribute('aria-label', `Page ${page}`);
            if (page === currentPage) button.setAttribute('aria-current', 'page');
            numbers.append(button);
        }
    }

    input?.addEventListener('input', () => renderApplications({ resetPage: true }), { signal });
    pagination?.addEventListener('click', event => {
        const button = event.target.closest('button');
        if (!button || button.disabled) return;
        if (button.hasAttribute('data-ra-page-prev')) currentPage -= 1;
        else if (button.hasAttribute('data-ra-page-next')) currentPage += 1;
        else if (button.dataset.raPage) currentPage = Number(button.dataset.raPage);
        else return;
        renderApplications();
        if (button.dataset.raPage) numbers?.querySelector('[aria-current="page"]')?.focus();
        else if (button.disabled) numbers?.querySelector('[aria-current="page"]')?.focus();
    }, { signal });

    const dropdown = root.querySelector('[data-ra-status-dropdown]');
    const select = root.querySelector('#riderApplicationStatus');
    const trigger = root.querySelector('[data-ra-status-trigger]');
    const label = root.querySelector('[data-ra-status-label]');
    const menu = root.querySelector('[data-ra-status-menu]');
    const options = Array.from(root.querySelectorAll('[data-ra-status-option]'));

    function syncStatus() {
        if (!select) return;
        if (label) label.textContent = select.options[select.selectedIndex]?.text || 'Pending';
        options.forEach(option => option.setAttribute('aria-selected', String(option.dataset.value === select.value)));
    }

    function closeStatus({ restoreFocus = false } = {}) {
        if (!menu || !trigger) return;
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        if (restoreFocus) trigger.focus({ preventScroll: true });
    }

    function openStatus() {
        if (!menu || !trigger) return;
        menu.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        (options.find(option => option.getAttribute('aria-selected') === 'true') || options[0])?.focus({ preventScroll: true });
    }

    if (dropdown && select && trigger && menu && options.length) {
        dropdown.dataset.enhanced = 'true';
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');
        trigger.hidden = false;
        syncStatus();
        trigger.addEventListener('click', () => menu.hidden ? openStatus() : closeStatus(), { signal });
        trigger.addEventListener('keydown', event => {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                openStatus();
            }
        }, { signal });
        menu.addEventListener('click', event => {
            const option = event.target.closest('[data-ra-status-option]');
            if (!option) return;
            select.value = option.dataset.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            closeStatus({ restoreFocus: true });
        }, { signal });
        select.addEventListener('change', syncStatus, { signal });
        dropdown.addEventListener('keydown', event => {
            if (menu.hidden) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                closeStatus({ restoreFocus: true });
            } else if (event.key === 'Tab') {
                closeStatus({ restoreFocus: true });
            } else if (['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key) && menu.contains(event.target)) {
                event.preventDefault();
                const index = options.indexOf(document.activeElement);
                const nextIndex = event.key === 'Home' ? 0 : event.key === 'End' ? options.length - 1
                    : (index + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length;
                options[nextIndex]?.focus();
            }
        }, { signal });
        document.addEventListener('click', event => {
            if (!dropdown.contains(event.target)) closeStatus();
        }, { signal });
        dropdown.addEventListener('focusout', event => {
            if (!dropdown.contains(event.relatedTarget)) closeStatus();
        }, { signal });
    }

    root.querySelectorAll('.ra-dialog').forEach(dialog => {
        dialog.addEventListener('click', event => {
            if (event.target === dialog) dialog.close();
        }, { signal });
    });

    renderApplications();
})();
</script>
@endpush
