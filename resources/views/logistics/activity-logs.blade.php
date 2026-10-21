
@extends('layouts.logistics')

@section('title', ($isHubOperator ? 'Hub Activity' : 'Activity Logs').' | SARI Logistics')
@section('page-title', $isHubOperator ? 'Hub Activity' : 'Activity Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/logistics/activity-logs.css') }}?v=20261020-1">
@endpush

@section('content')
<div class="lg-activity">
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
