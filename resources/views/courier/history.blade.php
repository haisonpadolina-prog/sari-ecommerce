@extends('layouts.courier')

@section('title', 'Delivery History')
@section('header-title', 'Delivery History')
@section('header-subtitle', 'Completed Deliveries')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/history.css') }}?v=20261030-1"
    >
@endpush

@section('content')
@php
    $recordCount = count($history);
    $recordedFees = collect($history)->sum('fee');
@endphp

<div
    id="riderHistory"
    class="courier-history-page courier-workspace"
>
    <header class="rh-page-header">
        <div>
            <p class="rh-eyebrow">Rider Workspace</p>

            <h2 class="rh-page-title">
                Delivery <span>History</span>
            </h2>

            <p class="rh-page-description">
                Review your completed trips, routes, and recorded delivery fees.
            </p>
        </div>

        <a
            href="{{ route('courier.history.export') }}"
            class="rh-button rh-button-primary"
        >
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M12 3v12M7 10l5 5 5-5"/>
                <path d="M5 16v4h14v-4"/>
            </svg>
            Export History
        </a>
    </header>

    <section class="rh-summary" aria-label="Recent delivery summary">
        <article class="rh-stat">
            <div class="rh-stat-top">
                <p class="rh-stat-label">Completed Today</p>

                <span class="rh-stat-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 11h18M9 16l2 2 4-4"/>
                    </svg>
                </span>
            </div>

            <strong class="rh-stat-value">
                {{ $historyStats['today'] }}
            </strong>

            <p class="rh-stat-note">Within the recent records</p>
        </article>

        <article class="rh-stat">
            <div class="rh-stat-top">
                <p class="rh-stat-label">Recent Completed</p>

                <span class="rh-stat-icon rh-stat-icon-blue">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                        <path d="M3 8v9l9 5 9-5V8M12 13v9"/>
                    </svg>
                </span>
            </div>

            <strong class="rh-stat-value">
                {{ $historyStats['total'] }}
            </strong>

            <p class="rh-stat-note">Up to 100 latest records</p>
        </article>

        <article class="rh-stat">
            <div class="rh-stat-top">
                <p class="rh-stat-label">Recorded Fees</p>

                <span
                    class="rh-stat-icon rh-stat-icon-green"
                    aria-hidden="true"
                >
                    ₱
                </span>
            </div>

            <strong class="rh-stat-value">
                ₱{{ number_format($recordedFees, 2) }}
            </strong>

            <p class="rh-stat-note">Combined fees in this recent list</p>
        </article>

        <article class="rh-stat">
            <div class="rh-stat-top">
                <p class="rh-stat-label">Average Fee</p>

                <span class="rh-stat-icon rh-stat-icon-neutral">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M4 20h16M7 16v-4M12 16V5M17 16V9"/>
                    </svg>
                </span>
            </div>

            <strong class="rh-stat-value">
                ₱{{ number_format($historyStats['average_fee'], 2) }}
            </strong>

            <p class="rh-stat-note">Per delivery in the recent list</p>
        </article>
    </section>

    <section
        class="rh-filter-panel"
        aria-label="Delivery history filters"
    >
        <div class="rh-filter-grid">
            <div class="rh-search">
                <label for="historySearch" class="rh-field-label">
                    Search Records
                </label>

                <div class="rh-search-control">
                    <svg
                        class="rh-search-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        aria-hidden="true"
                    >
                        <circle cx="10.5" cy="10.5" r="6.5"/>
                        <path d="m16 16 4.5 4.5"/>
                    </svg>

                    <input
                        id="historySearch"
                        type="search"
                        class="rh-control rh-search-input"
                        placeholder="Search order, customer, or route"
                        aria-controls="historyRows"
                    >
                </div>
            </div>

            <div class="rh-filter">
                <label for="historyFilter" class="rh-field-label">
                    Delivery Period
                </label>

                <select
                    id="historyFilter"
                    class="rh-control"
                    aria-controls="historyRows"
                >
                    <option value="all">All deliveries</option>
                    <option value="today">Today</option>
                    <option value="previous">Previous days</option>
                </select>
            </div>

            <button
                id="historyReset"
                type="button"
                class="rh-button rh-reset"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M3 10a9 9 0 1 1 2.6 8.4"/>
                    <path d="M3 4v6h6"/>
                </svg>
                Reset
            </button>
        </div>

        <p class="rh-filter-note">
            Filters apply to the recent records below. Summary cards remain
            based on the full recent list; CSV export includes all delivery
            history records.
        </p>

        <noscript>
            <p class="rh-filter-note">
                Enable JavaScript to use search and filters.
                All available recent records are displayed below.
            </p>
        </noscript>
    </section>

    <section class="rh-panel" aria-labelledby="historyPanelTitle">
        <div class="rh-panel-header">
            <div>
                <h3 id="historyPanelTitle" class="rh-panel-title">
                    Completed Trips
                </h3>

                <p class="rh-panel-description">
                    Recent direct and last-mile deliveries.
                </p>
            </div>

            <span class="rh-count-badge">
                {{ $recordCount }}
                {{ $recordCount === 1 ? 'record' : 'records' }}
            </span>
        </div>

        <div
            class="rh-table-scroll"
            tabindex="0"
            role="region"
            aria-label="Scrollable completed delivery records"
        >
            <table class="rh-table">
                <caption class="rh-sr-only">
                    Recent completed deliveries, including customer, route,
                    recorded date and time, fee, and status.
                </caption>

                <thead>
                    <tr>
                        <th scope="col">Order</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Route</th>
                        <th scope="col">Date &amp; Time</th>
                        <th scope="col" class="rh-money">Delivery Fee</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>

                <tbody id="historyRows">
                    @foreach($history as $row)
                        <tr
                            class="history-row"
                            data-search="{{ strtolower($row['order'].' '.$row['customer'].' '.$row['route']) }}"
                            data-day="{{ $row['is_today'] ? 'today' : 'previous' }}"
                        >
                            <td>
                                <span class="rh-order">
                                    {{ $row['order'] }}
                                </span>
                            </td>

                            <td>{{ $row['customer'] }}</td>

                            <td class="rh-route">
                                {{ $row['route'] }}
                            </td>

                            <td class="rh-date">
                                {{ $row['date'] }}

                                <span class="rh-time">
                                    {{ $row['time'] }}
                                </span>
                            </td>

                            <td class="rh-money">
                                <span class="rh-fee">
                                    ₱{{ number_format($row['fee'], 2) }}
                                </span>
                            </td>

                            <td>
                                <span class="rh-status">
                                    <svg
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="m3.5 8 3 3 6-6"/>
                                    </svg>

                                    {{ $row['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div
            id="historyEmpty"
            class="rh-empty"
            @if($recordCount > 0) hidden @endif
        >
            <div class="rh-empty-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="5" y="4" width="14" height="17" rx="2"/>
                    <path d="M9 4V2h6v2M9 10h6M9 14h6M9 18h3"/>
                </svg>
            </div>

            <strong id="historyEmptyTitle" class="rh-empty-title">
                No completed deliveries yet
            </strong>

            <p id="historyEmptyDescription" class="rh-empty-description">
                Your completed direct and last-mile trips will appear here.
            </p>
        </div>

        <footer class="rh-panel-footer">
            <p
                id="historyResultsCount"
                class="rh-results-count"
                role="status"
                aria-live="polite"
                aria-atomic="true"
            >
                Showing {{ $recordCount }} of {{ $recordCount }} records
            </p>

            <p>
                Recorded fees may differ from your available wallet balance.
            </p>
        </footer>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('riderHistory');

    if (!root) return;

    const search = root.querySelector('#historySearch');
    const filter = root.querySelector('#historyFilter');
    const reset = root.querySelector('#historyReset');
    const rows = Array.from(root.querySelectorAll('.history-row'));

    const empty = root.querySelector('#historyEmpty');
    const emptyTitle = root.querySelector('#historyEmptyTitle');
    const emptyDescription = root.querySelector('#historyEmptyDescription');
    const resultsCount = root.querySelector('#historyResultsCount');

    const apply = () => {
        const query = (search?.value || '').trim().toLowerCase();
        const selected = filter?.value || 'all';

        let visibleRows = 0;

        rows.forEach((row) => {
            const searchMatch = (row.dataset.search || '').includes(query);
            const dayMatch =
                selected === 'all' || row.dataset.day === selected;

            const visible = searchMatch && dayMatch;

            row.hidden = !visible;

            if (visible) {
                visibleRows += 1;
            }
        });

        if (resultsCount) {
            resultsCount.textContent =
                `Showing ${visibleRows} of ${rows.length} ` +
                `${rows.length === 1 ? 'record' : 'records'}`;
        }

        if (empty) {
            empty.hidden = visibleRows > 0;
        }

        if (emptyTitle) {
            emptyTitle.textContent = rows.length
                ? 'No matching deliveries'
                : 'No completed deliveries yet';
        }

        if (emptyDescription) {
            emptyDescription.textContent = rows.length
                ? 'Try another search or reset the filters to see all recent records.'
                : 'Your completed direct and last-mile trips will appear here.';
        }
    };

    search?.addEventListener('input', apply);
    filter?.addEventListener('change', apply);

    reset?.addEventListener('click', () => {
        if (search) search.value = '';
        if (filter) filter.value = 'all';

        apply();
        search?.focus();
    });

    window.addEventListener('pageshow', apply);

    apply();
});
</script>
@endpush