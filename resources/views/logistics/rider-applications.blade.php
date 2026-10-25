@extends('layouts.logistics')

@section('title', 'Rider Applications | SARI Logistics')
@section('page-title', 'Rider Applications')

@push('styles')
<style>
    .rider-applications-page,
    .rider-applications-page * {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .ra-panel {
        border: 1px solid #e4e7eb;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(26, 31, 38, .055);
    }

    .ra-kpi {
        border: 1px solid #e1e5e9;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(26, 31, 38, .035);
    }

    .ra-focus:focus-visible {
        outline: 3px solid rgba(213, 151, 19, .18);
        outline-offset: 2px;
        border-color: #d59713 !important;
    }

    .ra-dialog {
        width: min(760px, calc(100vw - 28px));
        max-height: min(860px, calc(100dvh - 40px));
        margin: auto;
        padding: 0;
        border: 1px solid #dde2e7;
        border-radius: 20px;
        background: #fff;
        color: #1b1f24;
        box-shadow: 0 30px 80px rgba(20, 24, 30, .24);
    }

    .ra-dialog::backdrop {
        background: rgba(23, 27, 33, .44);
        backdrop-filter: blur(5px);
    }

    .ra-dialog-body {
        max-height: calc(100dvh - 42px);
        overflow-y: auto;
    }

    .ra-table-row[hidden] { display: none !important; }

    @media (prefers-reduced-motion: reduce) {
        .ra-kpi,
        .ra-focus { transition: none !important; }
    }

    /* ============================================================
       ADMIN REGISTRATIONS SCALE MATCH — FINAL VISUAL OVERRIDE
       Keeps Logistics functionality unchanged; matches the Admin
       page width, KPI height, toolbar density, and queue sizing.
       ============================================================ */
    .rider-applications-page {
        width: 100% !important;
        max-width: 1800px !important;
        padding-bottom: 24px !important;
    }

    .ra-page-header {
        min-height: 66px !important;
        margin: 0 0 12px !important;
        padding: 2px 2px 13px !important;
        border-bottom-color: #eceff2 !important;
    }

    .ra-page-header > p:first-child {
        margin: 0 0 5px !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        line-height: 1.15 !important;
        letter-spacing: .16em !important;
    }

    .ra-page-header h1 {
        margin: 0 !important;
        font-size: clamp(29px, 2.2vw, 36px) !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .ra-page-header h1 + p {
        max-width: 760px !important;
        margin: 7px 0 0 !important;
        font-size: 10px !important;
        font-weight: 400 !important;
        line-height: 1.55 !important;
    }

    .ra-summary-grid {
        gap: 12px !important;
        margin-top: 12px !important;
    }

    .ra-kpi {
        min-height: 104px !important;
        padding: 18px !important;
        border-color: #e4e7ec !important;
        border-radius: 12px !important;
        box-shadow: 0 2px 6px rgba(16,24,40,.04) !important;
    }

    .ra-kpi > div > p:first-child {
        font-size: 12px !important;
        font-weight: 500 !important;
        line-height: 20px !important;
        color: #667085 !important;
    }

    .ra-kpi > div > p:nth-child(2) {
        margin-top: 10px !important;
        font-size: 28px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        color: #101828 !important;
    }

    .ra-kpi > span {
        width: 24px !important;
        height: 24px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 0 0 24px !important;
    }

    .ra-kpi > span svg {
        width: 24px !important;
        height: 24px !important;
        stroke-width: 1.8 !important;
    }

    .ra-filter-bar {
        margin-top: 12px !important;
        grid-template-columns: minmax(320px,1fr) 160px 155px 108px 68px !important;
        gap: 9px !important;
        align-items: center !important;
    }

    .ra-filter-bar input,
    .ra-filter-bar select,
    .ra-filter-bar > div,
    .ra-filter-bar button,
    .ra-filter-bar a {
        height: 40px !important;
        border-radius: 10px !important;
        font-size: 10px !important;
        box-shadow: none !important;
    }

    .ra-filter-bar > div {
        padding-inline: 11px !important;
    }

    .ra-filter-bar input {
        padding-left: 38px !important;
        padding-right: 12px !important;
    }

    .ra-filter-bar label > span.pointer-events-none.absolute.inset-y-0 {
        width: 38px !important;
    }

    .ra-filter-bar select {
        padding-left: 30px !important;
        padding-right: 34px !important;
    }

    .ra-filter-bar button {
        padding-inline: 13px !important;
        font-size: 9.5px !important;
        font-weight: 650 !important;
        box-shadow: 0 5px 12px rgba(213,150,23,.16) !important;
    }

    .ra-filter-bar a {
        padding-inline: 13px !important;
        font-size: 9.5px !important;
        font-weight: 600 !important;
    }

    .ra-queue-card {
        margin-top: 14px !important;
        border-radius: 14px !important;
        border-color: #e5e7eb !important;
        box-shadow: 0 2px 5px rgba(15,23,42,.045), 0 10px 24px rgba(15,23,42,.075) !important;
    }

    .ra-queue-card > div:first-child {
        padding: 15px 18px !important;
        border-bottom-color: #eceff2 !important;
    }

    .ra-queue-card > div:first-child h2 {
        font-size: 14px !important;
        font-weight: 700 !important;
        letter-spacing: -.02em !important;
    }

    .ra-queue-card > div:first-child h2 + span {
        font-size: 8px !important;
    }

    .ra-queue-card > div:first-child p {
        margin-top: 3px !important;
        font-size: 9.5px !important;
        line-height: 1.5 !important;
    }

    .ra-queue-card table {
        min-width: 820px !important;
    }

    .ra-queue-card thead tr {
        background: #f9fafb !important;
    }

    .ra-queue-card th {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        letter-spacing: .075em !important;
        color: #667085 !important;
    }

    .ra-queue-card td {
        padding-top: 11px !important;
        padding-bottom: 11px !important;
        font-size: 9.5px !important;
    }

    .ra-queue-card th:first-child,
    .ra-queue-card td:first-child { padding-left: 18px !important; }
    .ra-queue-card th:last-child,
    .ra-queue-card td:last-child { padding-right: 18px !important; }

    .ra-queue-card tbody img,
    .ra-queue-card tbody td:first-child span.grid {
        width: 36px !important;
        height: 36px !important;
    }

    .ra-queue-card tbody td p {
        font-size: 9px !important;
    }

    .ra-queue-card tbody td:first-child p:first-child {
        font-size: 10px !important;
    }

    .ra-queue-card [data-open-review] {
        height: 31px !important;
        border-radius: 8px !important;
        padding-inline: 10px !important;
        font-size: 8.5px !important;
        box-shadow: none !important;
    }

    @media (max-width: 1279px) {
        .ra-filter-bar {
            grid-template-columns: minmax(260px,1fr) 150px 145px auto auto !important;
        }
    }

    @media (max-width: 1023px) {
        .ra-filter-bar {
            grid-template-columns: minmax(0,1fr) minmax(150px,.42fr) !important;
        }
        .ra-filter-bar > label:first-child { grid-column: 1 / -1 !important; }
        .ra-filter-bar button,
        .ra-filter-bar a { width: 100% !important; }
    }

    @media (max-width: 767px) {
        .ra-page-header h1 { font-size: clamp(28px,8vw,34px) !important; }
        .ra-summary-grid { gap: 9px !important; }
        .ra-kpi { min-height: 86px !important; padding: 13px !important; }
        .ra-kpi > div > p:nth-child(2) { font-size: 22px !important; }
        .ra-filter-bar { grid-template-columns: 1fr !important; }
        .ra-filter-bar > label:first-child { grid-column: auto !important; }
        .ra-filter-bar input,
        .ra-filter-bar select,
        .ra-filter-bar > div,
        .ra-filter-bar button,
        .ra-filter-bar a { height: 42px !important; font-size: 10px !important; }
    }
</style>
@endpush

@section('content')
<div class="rider-applications-page mx-auto w-full max-w-[1900px] text-[#171b20]">

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
        <p class="mt-2 max-w-[820px] text-[13px] font-normal leading-6 text-[#7a828d]">
            Verify applicant information, inspect Rider documents, then approve or reject registrations submitted to {{ $logistics->displayName() }}.
        </p>
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
        <label class="relative block">
            <span class="pointer-events-none absolute inset-y-0 left-0 grid w-12 place-items-center text-[#8b95a1]" aria-hidden="true">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"></circle><path d="m16 16 4 4"></path></svg>
            </span>
            <span class="sr-only">Search applications</span>
            <input
                id="riderApplicationSearch"
                type="search"
                placeholder="Search name, email, contact, plate, or municipality..."
                class="ra-focus h-[50px] w-full rounded-[13px] border border-[#dde2e7] bg-white pl-12 pr-4 text-[12px] font-normal text-[#28303a] placeholder:text-[#a2a9b1]"
            >
        </label>

        <div class="flex h-[50px] items-center rounded-[13px] border border-[#dde2e7] bg-white px-4 text-[12px] font-medium text-[#313843]">
            Rider Applications
        </div>

        <form id="riderStatusForm" method="GET" class="contents">
            <label class="relative block">
                <span class="sr-only">Application status</span>
                <span class="pointer-events-none absolute left-4 top-1/2 h-2 w-2 -translate-y-1/2 rounded-full
                    {{ $status === 'approved' ? 'bg-[#36a27d]' : ($status === 'rejected' ? 'bg-[#dc6262]' : 'bg-[#d79a18]') }}"></span>
                <select name="status" class="ra-focus h-[50px] w-full appearance-none rounded-[13px] border border-[#dde2e7] bg-white pl-9 pr-10 text-[12px] font-medium text-[#313843]">
                    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-4 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-[#8a929d]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m8 10 4 4 4-4"></path></svg>
            </label>

            <button type="submit" class="ra-focus inline-flex h-[50px] items-center justify-center gap-2 rounded-[13px] bg-[#dd9809] px-5 text-[12px] font-semibold text-white shadow-[0_8px_20px_rgba(221,152,9,.18)] transition hover:bg-[#c98908]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16M7 12h10M10 19h4"></path></svg>
                Apply Filter
            </button>
        </form>

        <a href="{{ route('logistics.rider-applications', [], false) }}" class="ra-focus inline-flex h-[50px] items-center justify-center rounded-[13px] border border-[#dde2e7] bg-white px-4 text-[11px] font-medium text-[#66707d] transition hover:bg-[#f7f8f9]">
            Reset
        </a>
    </section>

    {{-- APPLICATION QUEUE --}}
    <section class="ra-panel ra-queue-card mt-4 overflow-hidden">
        <div class="flex flex-col gap-2 border-b border-[#e7eaee] px-5 py-5 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-[17px] font-semibold tracking-[-.02em] text-[#171b20]">Application queue</h2>
                    <span id="riderShowingCount" class="rounded-full bg-[#f2f4f6] px-2.5 py-1 text-[9px] font-medium text-[#68727e]">Showing {{ $applications->count() }}</span>
                </div>
                <p class="mt-1 text-[11px] font-normal text-[#7f8894]">Open a registration to review the complete Rider profile and protected documents.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1120px] table-fixed text-left">
                <thead class="border-b border-[#dde2e7] bg-[#f7f8f9]">
                    <tr class="text-[9px] font-semibold uppercase tracking-[.08em] text-[#657080]">
                        <th class="w-[23%] px-5 py-3.5">Applicant</th>
                        <th class="w-[20%] px-5 py-3.5">Contact</th>
                        <th class="w-[10%] px-5 py-3.5">Role</th>
                        <th class="w-[12%] px-5 py-3.5">Status</th>
                        <th class="w-[13%] px-5 py-3.5">Documents</th>
                        <th class="w-[14%] px-5 py-3.5">Submitted</th>
                        <th class="w-[8%] px-5 py-3.5 text-right">Action</th>
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
                                    onclick="document.getElementById('rider-review-{{ $application->id }}').showModal()"
                                    aria-haspopup="dialog"
                                >
                                    Review
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 6 6 6-6 6"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <p class="text-[12px] font-medium text-[#56606c]">No {{ $status }} Rider applications</p>
                                <p class="mt-1 text-[10px] font-normal text-[#929aa4]">New Rider applications submitted to {{ $logistics->displayName() }} will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="riderSearchEmpty" class="hidden border-t border-[#edf0f2] px-5 py-12 text-center">
            <p class="text-[12px] font-medium text-[#56606c]">No matching application</p>
            <p class="mt-1 text-[10px] text-[#929aa4]">Try a different name, email, contact number, plate, or location.</p>
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
    const input = document.getElementById('riderApplicationSearch');
    const rows = Array.from(document.querySelectorAll('.ra-table-row'));
    const count = document.getElementById('riderShowingCount');
    const empty = document.getElementById('riderSearchEmpty');

    if (input && rows.length) {
        const filterRows = () => {
            const term = input.value.trim().toLowerCase();
            let visible = 0;

            rows.forEach(row => {
                const matches = !term || (row.dataset.search || '').includes(term);
                row.hidden = !matches;
                if (matches) visible += 1;
            });

            if (count) count.textContent = `Showing ${visible}`;
            if (empty) empty.classList.toggle('hidden', visible !== 0);
        };

        input.addEventListener('input', filterRows);
    }

    document.querySelectorAll('.ra-dialog').forEach(dialog => {
        dialog.addEventListener('click', event => {
            if (event.target === dialog) dialog.close();
        });
    });
})();
</script>
@endpush
