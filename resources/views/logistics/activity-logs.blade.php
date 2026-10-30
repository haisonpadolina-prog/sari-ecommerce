
@extends('layouts.logistics')

@section('title', ($isHubOperator ? 'Hub Activity' : 'Activity Logs').' | SARI Logistics')
@section('page-title', $isHubOperator ? 'Hub Activity' : 'Activity Logs')

@push('styles')
<style>
/* =========================================================
   MERGED FROM public/css/logistics/activity-logs.css
   Kept page-scoped/shared exactly as the original stylesheet.
========================================================= */

/* =====================================================
   SARI LOGISTICS — ACTIVITY LOGS
   Main Logistics & Hub Operator
   ===================================================== */

.lg-activity {
    --la-primary: #d69823;
    --la-primary-dark: #ba8017;

    --la-text: #332c25;
    --la-muted: #827a70;

    --la-border: #ece5db;
    --la-background: #ffffff;

    width: 100%;
    max-width: 1480px;

    margin: 0 auto;
    padding: 3px 0 40px;

    font-family: "Poppins", sans-serif;
    font-size: 11px;

    color: var(--la-text);
}

.lg-activity *,
.lg-activity *::before,
.lg-activity *::after {
    box-sizing: border-box;
}

/* =====================================================
   PAGE HEADER
   ===================================================== */

.lg-activity .la-head {
    display: flex;
    align-items: center;
    justify-content: space-between;

    flex-wrap: wrap;
    gap: 20px;

    margin-bottom: 20px;
}

.lg-activity .la-eyebrow {
    display: block;

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.15em;

    color: #b77a12;
}

.lg-activity .la-head h1 {
    margin: 6px 0;

    font-size: 23px;
    font-weight: 700;

    line-height: 1.35;
    letter-spacing: -0.04em;

    color: #2b271f;
}

.lg-activity .la-head p {
    max-width: 700px;

    margin: 0;

    font-size: 11px;
    line-height: 1.7;

    color: var(--la-muted);
}

.lg-activity .la-head p strong {
    font-weight: 600;
    color: #594638;
}

/* READ-ONLY INDICATOR */

.lg-activity .la-read-only {
    display: inline-flex;
    align-items: center;

    gap: 8px;
    padding: 9px 12px;

    border: 1px solid #e6e1d8;
    border-radius: 9px;

    background: #ffffff;

    font-size: 10px;
    font-weight: 600;

    color: #6d665c;
    white-space: nowrap;
}

.lg-activity .la-status-dot {
    display: inline-block;
    flex-shrink: 0;

    width: 7px;
    height: 7px;

    border-radius: 50%;
    background: #579774;
}

/* =====================================================
   STATISTICS CARDS
   ===================================================== */

.lg-activity .la-stat-grid {
    display: grid;

    grid-template-columns: repeat(3, minmax(0, 1fr));

    gap: 13px;
    margin-bottom: 18px;
}

.lg-activity .la-stat {
    display: flex;
    flex-direction: column;

    gap: 4px;

    min-width: 0;
    padding: 18px 20px;

    border: 1px solid #eee7dd;
    border-radius: 12px;

    background: #ffffff;

    box-shadow:
        0 3px 13px rgba(68, 53, 26, 0.025);
}

.lg-activity .la-stat > span {
    font-size: 10px;
    font-weight: 600;

    color: #827a70;
}

.lg-activity .la-stat > strong {
    font-size: 24px;
    font-weight: 700;

    line-height: 1.4;
    letter-spacing: -0.04em;

    color: #3b332c;
}

.lg-activity .la-stat > small {
    font-size: 9px;
    color: #a29b91;
}

/* =====================================================
   MAIN ACTIVITY PANEL
   ===================================================== */

.lg-activity .la-panel {
    overflow: hidden;

    background: #ffffff;

    border: 1px solid var(--la-border);
    border-radius: 12px;

    box-shadow:
        0 5px 20px rgba(68, 53, 26, 0.025);
}

/* PANEL HEADING */

.lg-activity .la-panel-heading {
    padding: 20px 21px 13px;
}

.lg-activity .la-panel-heading h2 {
    margin: 0 0 4px;

    font-size: 14px;
    font-weight: 700;

    color: #312b23;
}

.lg-activity .la-panel-heading p {
    margin: 0;

    font-size: 10px;
    line-height: 1.6;

    color: #968e83;
}

/* =====================================================
   FILTERS
   ===================================================== */

.lg-activity .la-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;

    gap: 9px;

    padding: 0 21px 18px;

    border-bottom: 1px solid #eee9e2;
}

.lg-activity .la-field {
    display: flex;
    flex: 1;
    flex-direction: column;

    gap: 6px;

    min-width: 128px;
}

.lg-activity .la-field > span {
    font-size: 9px;
    font-weight: 600;

    color: #746b5f;
}

/* INPUT AND SELECT */

.lg-activity .la-field input,
.lg-activity .la-field select {
    width: 100%;
    min-height: 37px;

    padding: 0 11px;

    border: 1px solid #e6e0d7;
    border-radius: 7px;

    background: #ffffff;

    font-family: inherit;
    font-size: 10px;

    color: #494139;

    outline: none;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.lg-activity .la-field input::placeholder {
    color: #aaa197;
}

.lg-activity .la-field input:focus,
.lg-activity .la-field select:focus {
    border-color: #cd982e;

    box-shadow:
        0 0 0 3px rgba(205, 152, 46, 0.10);
}

/* SEARCH FIELD */

.lg-activity .la-search {
    flex: 1.6;
    min-width: 155px;
}

/* DATE FIELDS */

.lg-activity .la-date {
    min-width: 136px;
}

/* FILTER BUTTONS */

.lg-activity .la-apply,
.lg-activity .la-reset {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    height: 37px;

    padding: 0 14px;

    border-radius: 7px;

    font-family: inherit;
    font-size: 10px;
    font-weight: 600;

    text-decoration: none;
    white-space: nowrap;

    transition:
        background 0.2s ease,
        border-color 0.2s ease;
}

.lg-activity .la-apply {
    background: var(--la-primary);

    border: 1px solid var(--la-primary);

    color: #ffffff;

    cursor: pointer;
}

.lg-activity .la-apply:hover {
    background: var(--la-primary-dark);
    border-color: var(--la-primary-dark);
}

.lg-activity .la-reset {
    background: #ffffff;

    border: 1px solid #e6e0d7;

    color: #665c51;
}

.lg-activity .la-reset:hover {
    background: #f9f7f4;
}

.lg-activity .la-apply:focus-visible,
.lg-activity .la-reset:focus-visible {
    outline: 2px solid #cd982e;
    outline-offset: 3px;
}

/* =====================================================
   ACTIVITY TABLE
   ===================================================== */

.lg-activity .la-table-scroll {
    width: 100%;
    overflow-x: auto;
}

.lg-activity .la-table {
    width: 100%;
    min-width: 750px;

    border-collapse: collapse;
    border-spacing: 0;

    text-align: left;
}

.lg-activity .la-table th {
    padding: 13px 19px;

    background: #fdfbf8;

    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.035em;

    color: #857b6f;

    white-space: nowrap;
}

.lg-activity .la-table td {
    padding: 16px 19px;

    border-top: 1px solid #f1ede8;

    vertical-align: middle;

    color: #534c42;
}

.lg-activity .la-table tbody tr {
    transition: background 0.15s ease;
}

.lg-activity .la-table tbody tr:hover {
    background: #fffdf9;
}

/* DATE AND TIME */

.lg-activity .la-time {
    display: block;

    font-size: 10px;
    font-weight: 600;

    color: #433a30;

    white-space: nowrap;
}

.lg-activity .la-table td small {
    display: block;

    margin-top: 5px;

    font-size: 9px;

    color: #a1988e;
}

/* MODULE BADGE */

.lg-activity .la-module {
    display: inline-block;

    margin-bottom: 7px;
    padding: 4px 7px;

    background: #fff4df;

    border: 1px solid #f4e7cb;
    border-radius: 5px;

    font-size: 8px;
    font-weight: 700;

    color: #a56c0a;
}

/* ACTIVITY DESCRIPTION */

.lg-activity .la-description {
    display: block;

    font-size: 10px;
    font-weight: 600;

    line-height: 1.6;

    color: #433a30;

    overflow-wrap: anywhere;
}

.lg-activity .la-action {
    text-transform: capitalize;
}

/* PERFORMED BY */

.lg-activity .la-actor {
    display: block;

    font-size: 10px;
    font-weight: 600;

    color: #433a30;
}

/* HUB / SCOPE */

.lg-activity .la-location {
    display: block;

    font-size: 10px;
    font-weight: 600;

    color: #433a30;
}

/* REFERENCE */

.lg-activity .la-reference {
    display: inline-block;

    padding: 5px 7px;

    border: 1px solid #ede4d4;
    border-radius: 6px;

    background: #faf7f1;

    font-size: 9px;
    font-weight: 600;

    color: #6b5c43;

    overflow-wrap: anywhere;
}

.lg-activity .la-empty-reference {
    color: #c6bdb3;
}

/* =====================================================
   EMPTY STATE
   ===================================================== */

.lg-activity .la-empty {
    padding: 52px 15px !important;

    text-align: center;
}

.lg-activity .la-empty-icon {
    display: block;

    margin-bottom: 6px;

    font-size: 32px;

    color: #cb9b45;
}

.lg-activity .la-empty strong {
    display: block;

    font-size: 12px;

    color: #52493e;
}

.lg-activity .la-empty p {
    margin: 6px 0 0;

    font-size: 10px;

    color: #999084;
}

/* =====================================================
   PAGINATION
   ===================================================== */

.lg-activity .la-pagination {
    padding: 16px 20px;

    border-top: 1px solid #f1ede8;
}

/* =====================================================
   TABLE FOOTER
   ===================================================== */

.lg-activity .la-footer {
    padding: 13px 20px;

    border-top: 1px solid #f1ede8;

    font-size: 9px;
    line-height: 1.7;

    color: #968c7e;
}

/* =====================================================
   RESPONSIVE — TABLET
   ===================================================== */

@media (max-width: 1024px) {

    .lg-activity .la-head {
        align-items: flex-start;
    }

    .lg-activity .la-filters {
        gap: 10px;
    }

    .lg-activity .la-field {
        flex: 1 1 30%;
    }

    .lg-activity .la-search {
        flex: 1 1 100%;
    }

    .lg-activity .la-apply,
    .lg-activity .la-reset {
        flex: 1;
    }
}

/* =====================================================
   RESPONSIVE — MOBILE
   ===================================================== */

@media (max-width: 760px) {

    .lg-activity {
        padding-bottom: 25px;
    }

    .lg-activity .la-head {
        gap: 12px;
    }

    .lg-activity .la-head h1 {
        font-size: 20px;
    }

    .lg-activity .la-read-only {
        font-size: 9px;
    }

    /* STAT CARDS */

    .lg-activity .la-stat-grid {
        grid-template-columns: 1fr;
        gap: 9px;
    }

    .lg-activity .la-stat {
        padding: 13px 16px;
    }

    .lg-activity .la-stat > strong {
        font-size: 20px;
    }

    /* PANEL */

    .lg-activity .la-panel-heading {
        padding: 16px 14px 13px;
    }

    /* FILTERS */

    .lg-activity .la-filters {
        padding: 0 14px 16px;
    }

    .lg-activity .la-field {
        flex: 1 1 calc(50% - 8px);
        min-width: 0;
    }

    .lg-activity .la-search {
        flex: 1 1 100%;
        min-width: 100%;
    }

    .lg-activity .la-apply,
    .lg-activity .la-reset {
        flex: 1;
    }

    /* TABLE */

    .lg-activity .la-table {
        min-width: 700px;
    }

    .lg-activity .la-table th,
    .lg-activity .la-table td {
        padding-left: 13px;
        padding-right: 13px;
    }

    /* FOOTER */

    .lg-activity .la-pagination,
    .lg-activity .la-footer {
        padding-left: 14px;
        padding-right: 14px;
    }
}

/* =====================================================
   RESPONSIVE — SMALL MOBILE
   ===================================================== */

@media (max-width: 420px) {

    .lg-activity .la-field {
        flex-basis: 100%;
    }

    .lg-activity .la-head h1 {
        font-size: 19px;
    }

    .lg-activity .la-apply,
    .lg-activity .la-reset {
        min-width: calc(50% - 5px);
    }

    .lg-activity .la-table-scroll {
        -webkit-overflow-scrolling: touch;
    }
}

/* =====================================================
   ACCESSIBILITY
   ===================================================== */

@media (prefers-reduced-motion: reduce) {

    .lg-activity *,
    .lg-activity *::before,
    .lg-activity *::after {
        transition: none !important;
        animation: none !important;
    }
}
</style>
@endpush

@section('content')
<div class="sari-logistics-ui lg-activity">
    <div class="la-head">
        <div>
            <span class="la-eyebrow">
                LOGISTICS / {{ $isHubOperator ? 'HUB INSIGHTS' : 'MANAGEMENT' }}
            </span>

            <h1>{{ $isHubOperator ? 'Hub Activity' : 'Activity Logs' }}</h1>

            <p>
                @if ($isHubOperator)
                    Read-only history of completed actions recorded for
                    <strong>{{ $hub->name }}</strong>.
                @else
                    Centralized, read-only history of completed actions
                    across your Logistics network.
                @endif
            </p>
        </div>

        <span class="la-read-only">
            <span class="la-status-dot"></span>
            Read-only audit history
        </span>
    </div>

    {{-- STATISTICS --}}
    <div class="la-stat-grid">

        <div class="la-stat">
            <span>All-time activities</span>
            <strong>{{ number_format($totalCount) }}</strong>
            <small>
                {{ $isHubOperator ? 'Assigned hub' : 'Entire provider network' }}
            </small>
        </div>

        <div class="la-stat">
            <span>Recorded today</span>
            <strong>{{ number_format($todayCount) }}</strong>
            <small>{{ now('Asia/Manila')->format('F j, Y') }}</small>
        </div>

        <div class="la-stat">
            <span>Current view</span>
            <strong>{{ number_format($logs->total()) }}</strong>
            <small>Matching the active filters</small>
        </div>

    </div>

    {{-- ACTIVITY HISTORY --}}
    <section class="la-panel">

        <div class="la-panel-heading">
            <div>
                <h2>Activity history</h2>
                <p>
                    Search or filter successful actions.
                    Page visits and failed submissions are not tracked.
                </p>
            </div>
        </div>

        {{-- FILTERS --}}
        <form
            action="{{ route('logistics.activity-logs') }}"
            method="GET"
            class="la-filters"
        >

            <label class="la-field la-search">
                <span>Search</span>
                <input
                    type="text"
                    name="q"
                    maxlength="80"
                    value="{{ $keyword }}"
                    placeholder="Operator, activity, or hub"
                >
            </label>

            <label class="la-field">
                <span>Module</span>

                <select name="module">
                    <option value="">All modules</option>

                    @foreach ($modules as $module)
                        <option
                            value="{{ $module }}"
                            @selected(($filters['module'] ?? '') === $module)
                        >
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </label>

            {{-- ONLY MAIN LOGISTICS CAN FILTER HUBS --}}
            @unless ($isHubOperator)
                <label class="la-field">
                    <span>Facility</span>

                    <select name="facility">
                        <option value="">All hubs</option>

                        @foreach ($facilities as $facility)
                            <option
                                value="{{ $facility->id }}"
                                @selected(
                                    (string) ($filters['facility'] ?? '') ===
                                    (string) $facility->id
                                )
                            >
                                {{ $facility->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endunless

            <label class="la-field la-date">
                <span>From</span>
                <input
                    type="date"
                    name="from"
                    value="{{ $filters['from'] ?? '' }}"
                >
            </label>

            <label class="la-field la-date">
                <span>To</span>
                <input
                    type="date"
                    name="to"
                    value="{{ $filters['to'] ?? '' }}"
                >
            </label>

            <button class="la-apply" type="submit">
                Apply filters
            </button>

            <a
                class="la-reset"
                href="{{ route('logistics.activity-logs') }}"
            >
                Reset
            </a>

        </form>

        {{-- ACTIVITY TABLE --}}
        <div class="la-table-scroll">
            <table class="la-table">

                <thead>
                    <tr>
                        <th>Date &amp; Time</th>
                        <th>Activity</th>
                        <th>Performed by</th>

                        @unless ($isHubOperator)
                            <th>Hub / Scope</th>
                        @endunless

                        <th>Reference</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($logs as $log)
                        <tr>

                            {{-- DATE --}}
                            <td>
                                <span class="la-time">
                                    {{ $log->created_at?->copy()->timezone('Asia/Manila')->format('M d, Y') }}
                                </span>

                                <small>
                                    {{ $log->created_at?->copy()->timezone('Asia/Manila')->format('h:i A') }}
                                </small>
                            </td>

                            {{-- ACTIVITY --}}
                            <td>
                                <span class="la-module">
                                    {{ $log->module }}
                                </span>

                                <span class="la-description">
                                    {{ $log->description }}
                                </span>

                                <small class="la-action">
                                    {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                </small>
                            </td>

                            {{-- ACTOR --}}
                            <td>
                                <span class="la-actor">
                                    {{ $log->actor_name }}
                                </span>

                                <small>
                                    {{ $log->actor_type === 'hub_operator'
                                        ? 'Hub Operator'
                                        : 'Main Logistics' }}
                                </small>
                            </td>

                            {{-- HUB --}}
                            @unless ($isHubOperator)
                                <td>
                                    <span class="la-location">
                                        {{ $log->facility_name ?: 'Network-wide' }}
                                    </span>
                                </td>
                            @endunless

                            {{-- REFERENCE --}}
                            <td>
                                @if ($log->subject_type && $log->subject_id)
                                    <span class="la-reference">
                                        {{ ucfirst($log->subject_type) }}
                                        #{{ $log->subject_id }}
                                    </span>
                                @else
                                    <span class="la-empty-reference">
                                        —
                                    </span>
                                @endif
                            </td>

                        </tr>

                    @empty

                        {{-- EMPTY STATE --}}
                        <tr>
                            <td
                                colspan="{{ $isHubOperator ? 4 : 5 }}"
                                class="la-empty"
                            >
                                <span class="la-empty-icon">⌕</span>

                                <strong>No activity found</strong>

                                <p>
                                    Completed Logistics actions will appear here.
                                    Try clearing the filters if needed.
                                </p>
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($logs->hasPages())
            <div class="la-pagination">
                {{ $logs->links() }}
            </div>
        @endif

        {{-- FOOTER --}}
        <footer class="la-footer">
            Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}
            of {{ number_format($logs->total()) }} matching activities.

            Historical entries cannot be edited or deleted from this page.
        </footer>

    </section>

</div>
@endsection
