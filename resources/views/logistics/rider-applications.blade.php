
@extends('layouts.logistics')

@section('title', 'Rider Applications | SARI Logistics')
@section('page-title', 'Rider Applications')

@push('styles')
<style>
/* SARI Rider Applications — Seller design language, page-scoped. */

#lgRiderApplications {
    --ra-amber: #d9950b;
    --ra-ink: #26211c;
    --ra-muted: #8a8177;
    --ra-border: #e8e2d9;
    --ra-shadow: 0 6px 18px rgba(33, 24, 14, .035);

    max-width: 1620px;
    color: var(--ra-ink);
    font-family: Poppins, ui-sans-serif, system-ui, sans-serif;
}

#lgRiderApplications [hidden] {
    display: none !important;
}

/* PAGE HEADING */

#lgRiderApplications .ra-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    padding: 5px 2px 17px;
    border-bottom: 1px solid #e4e1dc;
}

#lgRiderApplications .ra-eyebrow {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: #aa7315;
}

#lgRiderApplications .ra-page-title {
    margin-top: 5px;
    color: #24201b;
    font-size: clamp(23px, 2.15vw, 30px);
    font-weight: 750;
    letter-spacing: -.045em;
    line-height: 1.2;
}

#lgRiderApplications .ra-page-title span {
    color: var(--ra-amber);
}

#lgRiderApplications .ra-intro {
    margin-top: 6px;
    max-width: 710px;
    color: var(--ra-muted);
    font-size: 10px;
    line-height: 1.7;
}

#lgRiderApplications .ra-viewing {
    min-width: 136px;
    padding: 10px 14px;
    background: #fff;
    border: 1px solid var(--ra-border);
    border-radius: 11px;
    box-shadow: var(--ra-shadow);
}

#lgRiderApplications .ra-viewing small {
    display: block;
    font-size: 8px;
    color: #8c8277;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
}

#lgRiderApplications .ra-viewing strong {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 5px;
    font-size: 11px;
    font-weight: 650;
    text-transform: capitalize;
}

#lgRiderApplications .ra-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #d9950b;
}

#lgRiderApplications .ra-dot.approved {
    background: #4c9d6b;
}

#lgRiderApplications .ra-dot.rejected {
    background: #c66c65;
}

/* KPI CARDS */

#lgRiderApplications .ra-kpis {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

#lgRiderApplications .ra-kpi {
    display: flex;
    min-width: 0;
    min-height: 96px;
    justify-content: space-between;
    gap: 9px;
    padding: 14px;
    border: 1px solid var(--ra-border);
    border-radius: 16px;
    background: #fff;
    box-shadow: var(--ra-shadow);

    transition:
        transform .18s cubic-bezier(.22, 1, .36, 1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgRiderApplications .ra-kpi.is-selected {
    border-color: #dcbf82;
    box-shadow: 0 0 0 2px rgba(217, 149, 11, .08);
}

#lgRiderApplications .ra-kpi-label {
    font-size: 10px;
    color: #756d63;
    font-weight: 600;
}

#lgRiderApplications .ra-kpi-value {
    margin-top: 8px;
    font-size: 25px;
    font-weight: 750;
    letter-spacing: -.045em;
    line-height: 1.15;
}

#lgRiderApplications .ra-kpi-note {
    margin-top: 7px;
    color: #8b8277;
    font-size: 9px;
    line-height: 1.4;
}

#lgRiderApplications .ra-kpi-icon {
    display: grid;
    place-items: center;
    flex: 0 0 40px;
    width: 40px;
    height: 40px;
    border-radius: 11px;
    background: #f3f7fd;
    color: #5c82ac;
}

#lgRiderApplications .ra-kpi-icon svg {
    width: 18px;
    height: 18px;
}

#lgRiderApplications .ra-kpi-icon.pending {
    background: #fff8ed;
    color: #c18a22;
}

#lgRiderApplications .ra-kpi-icon.approved {
    background: #f0f8f3;
    color: #528866;
}

#lgRiderApplications .ra-kpi-icon.rejected {
    background: #fff2f2;
    color: #bf6c68;
}

/* MAIN PANEL */

#lgRiderApplications .ra-panel {
    overflow: hidden;
    background: #fff;
    border: 1px solid var(--ra-border);
    border-radius: 17px;
    box-shadow: var(--ra-shadow);
}

#lgRiderApplications .ra-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    padding: 15px 17px;
    border-bottom: 1px solid #eee8df;
}

#lgRiderApplications .ra-panel-title {
    margin-top: 4px;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.035em;
}

#lgRiderApplications .ra-panel-desc {
    margin-top: 4px;
    font-size: 9px;
    color: #948a80;
    line-height: 1.5;
}

/* SEARCH AND FILTER */

#lgRiderApplications .ra-tools {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

#lgRiderApplications .ra-search-wrap {
    position: relative;
}

#lgRiderApplications .ra-search-icon {
    position: absolute;
    left: 11px;
    top: 50%;
    width: 15px;
    height: 15px;
    transform: translateY(-50%);
    color: #a3988c;
    pointer-events: none;
}

#lgRiderApplications .ra-search,
#lgRiderApplications .ra-filter {
    height: 39px;
    border: 1px solid #dfe2e6;
    border-radius: 10px;
    background: white;
    outline: 0;
    font: inherit;
    font-size: 10px;
    color: #50483e;

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}

#lgRiderApplications .ra-search {
    width: 230px;
    padding: 0 12px 0 34px;
}

#lgRiderApplications .ra-filter {
    min-width: 118px;
    padding: 0 12px;
}

#lgRiderApplications .ra-search:focus,
#lgRiderApplications .ra-filter:focus,
#lgRiderApplications .ra-note:focus {
    border-color: var(--ra-amber);
    box-shadow: 0 0 0 3px rgba(217, 149, 11, .09);
}

#lgRiderApplications .ra-apply {
    height: 39px;
    padding: 0 17px;
    color: white;
    font-size: 10px;
    font-weight: 650;
    background: #d9950b;
    border-radius: 10px;

    transition:
        background .15s ease,
        box-shadow .15s ease;
}

#lgRiderApplications .ra-apply:hover {
    background: #c88708;
    box-shadow: 0 5px 12px rgba(194, 132, 10, .14);
}

/* APPLICANT CARDS */

#lgRiderApplications .ra-card-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-items: start;
    gap: 12px;
    padding: 14px;
}

#lgRiderApplications .ra-card {
    min-width: 0;
    overflow: hidden;
    border: 1px solid #e9e3db;
    border-radius: 13px;
    background: white;

    transition:
        transform .18s cubic-bezier(.22, 1, .36, 1),
        border-color .18s ease,
        box-shadow .18s ease;
}

#lgRiderApplications .ra-card-head {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    padding: 15px;
    border-bottom: 1px solid #f0ebe5;
}

#lgRiderApplications .ra-person {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 11px;
}

#lgRiderApplications .ra-avatar {
    display: grid;
    place-items: center;
    flex: 0 0 43px;
    width: 43px;
    height: 43px;
    border: 1px solid #eee3ce;
    border-radius: 11px;
    background: #fff6e5;
    color: #a97312;
    font-size: 12px;
    font-weight: 700;
    object-fit: cover;
}

#lgRiderApplications .ra-name {
    font-size: 12px;
    color: #322c25;
    font-weight: 700;
    line-height: 1.4;
}

#lgRiderApplications .ra-contact {
    margin-top: 3px;
    color: #847b70;
    font-size: 9px;
    line-height: 1.4;
    overflow-wrap: anywhere;
}

#lgRiderApplications .ra-date {
    margin-top: 6px;
    color: #a09589;
    font-size: 8px;
    white-space: nowrap;
}

/* STATUS BADGES */

#lgRiderApplications .ra-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
    padding: 5px 9px;
    border: 1px solid #eadfc9;
    border-radius: 999px;
    color: #a36d16;
    background: #fff8ea;
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .025em;
}

#lgRiderApplications .ra-badge.approved {
    border-color: #cfe1d5;
    background: #f1faf4;
    color: #3b7f54;
}

#lgRiderApplications .ra-badge.rejected {
    border-color: #e8cccc;
    background: #fff3f3;
    color: #a55a5a;
}

#lgRiderApplications .ra-card-content {
    padding: 14px 15px 15px;
}

/* SERVICE AREA AND VEHICLE */

#lgRiderApplications .ra-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 9px;
}

#lgRiderApplications .ra-detail {
    min-width: 0;
    padding: 12px;
    border: 1px solid #eee8e0;
    border-radius: 10px;
    background: #fbfbfc;
}

#lgRiderApplications .ra-mini-label {
    display: block;
    color: #93897d;
    font-size: 8px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
}

#lgRiderApplications .ra-detail strong {
    display: block;
    margin-top: 6px;
    color: #453d34;
    font-size: 10px;
    font-weight: 650;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

#lgRiderApplications .ra-detail p {
    margin-top: 3px;
    color: #8d8378;
    font-size: 9px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

/* ADDRESS AND REVIEW NOTES */

#lgRiderApplications .ra-address,
#lgRiderApplications .ra-review-note {
    margin-top: 9px;
    padding: 12px;
    border: 1px solid #eee8e0;
    border-radius: 10px;
    background: #fbfbfc;
}

#lgRiderApplications .ra-address p,
#lgRiderApplications .ra-review-note p {
    margin-top: 5px;
    color: #61594f;
    font-size: 9px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

/* DOCUMENT BUTTONS */

#lgRiderApplications .ra-documents {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 7px;
    margin-top: 13px;
}

#lgRiderApplications .ra-document {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-height: 32px;
    padding: 0 10px;
    border: 1px solid #dfded9;
    border-radius: 8px;
    background: white;
    font-size: 9px;
    font-weight: 600;
    color: #60574e;

    transition:
        background .15s ease,
        color .15s ease,
        border-color .15s ease;
}

#lgRiderApplications .ra-document svg {
    width: 13px;
    height: 13px;
}

#lgRiderApplications a.ra-document:hover {
    background: #fff9ef;
    color: #92600e;
    border-color: #dbc49b;
}

#lgRiderApplications .ra-document.is-missing {
    border-style: dashed;
    color: #a79c91;
}

/* APPROVE / REJECT ACTIONS */

#lgRiderApplications .ra-review-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 9px;
    margin-top: 14px;
    padding-top: 13px;
    border-top: 1px solid #eee9e2;
}

#lgRiderApplications .ra-decision {
    display: flex;
    flex-direction: column;
    min-width: 0;
    padding: 12px;
    border: 1px solid #dce7df;
    border-radius: 11px;
    background: #f8fbf9;
}

#lgRiderApplications .ra-decision.reject {
    border-color: #eddcdb;
    background: #fffafa;
}

#lgRiderApplications .ra-decision-title {
    display: flex;
    gap: 7px;
    align-items: center;
    color: #467758;
    font-size: 10px;
    font-weight: 700;
}

#lgRiderApplications .ra-decision.reject .ra-decision-title {
    color: #a65c58;
}

#lgRiderApplications .ra-decision small {
    display: block;
    margin-top: 4px;
    font-size: 8px;
    color: #8b968e;
    line-height: 1.4;
}

#lgRiderApplications .ra-decision.reject small {
    color: #a18b8a;
}

#lgRiderApplications .ra-note {
    width: 100%;
    height: 38px;
    margin-top: 12px;
    padding: 0 10px;
    border: 1px solid #dedfdb;
    border-radius: 8px;
    background: white;
    outline: 0;
    color: #453c34;
    font: inherit;
    font-size: 9px;
}

#lgRiderApplications .ra-decision-button {
    display: block;
    width: 100%;
    min-height: 37px;
    margin-top: 8px;
    padding: 6px 8px;
    border-radius: 8px;
    color: #fff;
    background: #548365;
    font-size: 9px;
    font-weight: 700;
    transition: background .15s ease;
}

#lgRiderApplications .ra-decision-button:hover {
    background: #426c52;
}

#lgRiderApplications .ra-decision.reject .ra-decision-button {
    background: #ad635d;
}

#lgRiderApplications .ra-decision.reject .ra-decision-button:hover {
    background: #914e49;
}

#lgRiderApplications .ra-completed {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 13px;
    padding-top: 12px;
    border-top: 1px solid #eee9e2;
}

#lgRiderApplications .ra-completed p {
    color: #8e8377;
    font-size: 9px;
}

/* EMPTY STATES */

#lgRiderApplications .ra-empty {
    grid-column: 1 / -1;
    padding: 42px 17px;
    text-align: center;
    border: 1px dashed #e2d9cc;
    border-radius: 12px;
    background: #fff;
}

#lgRiderApplications .ra-empty-icon {
    display: grid;
    place-items: center;
    width: 44px;
    height: 44px;
    margin: 0 auto;
    border-radius: 12px;
    background: #fff7e8;
    color: #c28a27;
}

#lgRiderApplications .ra-empty strong {
    display: block;
    margin-top: 12px;
    font-size: 11px;
    color: #51483e;
}

#lgRiderApplications .ra-empty p {
    margin-top: 5px;
    font-size: 9px;
    color: #94897c;
    line-height: 1.5;
}

#lgRiderApplications .ra-footer {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    padding: 11px 16px;
    border-top: 1px solid #eee8df;
    color: #95897e;
    font-size: 9px;
}

/* HOVER ANIMATIONS */

@media (hover: hover) and (pointer: fine) {
    #lgRiderApplications .ra-kpi:hover,
    #lgRiderApplications .ra-card:hover {
        transform: translateY(-2px);
        border-color: #d8c9b2;
        box-shadow: 0 13px 28px rgba(43, 33, 21, .06);
    }
}

/* RESPONSIVE */

@media (max-width: 1199px) {
    #lgRiderApplications .ra-card-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 850px) {
    #lgRiderApplications .ra-kpis {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    #lgRiderApplications .ra-panel-head {
        align-items: stretch;
    }

    #lgRiderApplications .ra-tools {
        width: 100%;
    }

    #lgRiderApplications .ra-search-wrap {
        flex: 1 1 100%;
    }

    #lgRiderApplications .ra-search {
        width: 100%;
    }

    #lgRiderApplications .ra-heading {
        align-items: flex-start;
    }

    #lgRiderApplications .ra-detail-grid,
    #lgRiderApplications .ra-review-actions {
        grid-template-columns: 1fr;
    }

    #lgRiderApplications .ra-card-grid {
        padding: 10px;
    }
}

@media (prefers-reduced-motion: reduce) {
    #lgRiderApplications *,
    #lgRiderApplications *::before,
    #lgRiderApplications *::after {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }

    #lgRiderApplications .ra-kpi:hover,
    #lgRiderApplications .ra-card:hover {
        transform: none !important;
    }
}
</style>
@endpush

@section('content')

<div id="lgRiderApplications" class="mx-auto w-full space-y-4">

    {{-- FLASH MESSAGES --}}

    @if(session('success'))
        <div
            class="rounded-xl border border-[#bfe0cd] bg-[#f1faf5] px-4 py-3 text-[10px] text-[#327a53]"
            role="status"
        >
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div
            class="rounded-xl border border-[#efc6c6] bg-[#fff4f4] px-4 py-3 text-[10px] text-[#b53e3e]"
            role="alert"
        >
            <strong class="mb-1 block">
                Please review the following:
            </strong>

            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SELLER-STYLE PAGE HEADING --}}

    <header class="ra-heading">

        <div class="min-w-0">

            <p class="ra-eyebrow">
                Riders &amp; Network
            </p>

            <h2 class="ra-page-title">
                Rider <span>Applications</span>
            </h2>

            <p class="ra-intro">
                Review applicants who selected

                <strong class="font-semibold text-[#51483d]">
                    {{ $logistics->displayName() }}
                </strong>

                as their Logistics provider. Verify their service area,
                vehicle and documents before making a decision.
            </p>

        </div>

        <div
            class="ra-viewing"
            aria-label="Current application filter"
        >
            <small>Currently viewing</small>

            <strong>
                <span
                    class="ra-dot {{ $status }}"
                    aria-hidden="true"
                ></span>

                {{ $status }} applications
            </strong>
        </div>

    </header>

    {{-- APPLICATION STATISTICS --}}

    <section
        class="ra-kpis"
        aria-label="Rider application overview"
    >

        {{-- TOTAL APPLICATIONS --}}

        <article class="ra-kpi">

            <div class="min-w-0">

                <p class="ra-kpi-label">
                    Total Applications
                </p>

                <p class="ra-kpi-value">
                    {{ number_format($stats['total']) }}
                </p>

                <p class="ra-kpi-note">
                    All Rider submissions
                </p>

            </div>

            <span class="ra-kpi-icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="8" cy="8" r="3"/>
                    <circle cx="17" cy="9" r="2.5"/>
                    <path d="M2.5 20c.5-4 2.5-6 5.5-6M13 15c3.2 0 5.2 1.7 5.7 5"/>
                </svg>
            </span>

        </article>

        {{-- PENDING --}}

        <a
            href="{{ route('logistics.rider-applications', ['status' => 'pending']) }}"
            class="ra-kpi {{ $status === 'pending' ? 'is-selected' : '' }}"
            @if($status === 'pending') aria-current="page" @endif
        >

            <div class="min-w-0">

                <p class="ra-kpi-label">
                    Pending Review
                </p>

                <p class="ra-kpi-value">
                    {{ number_format($stats['pending']) }}
                </p>

                <p class="ra-kpi-note">
                    Waiting for decision
                </p>

            </div>

            <span class="ra-kpi-icon pending" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </span>

        </a>

        {{-- APPROVED --}}

        <a
            href="{{ route('logistics.rider-applications', ['status' => 'approved']) }}"
            class="ra-kpi {{ $status === 'approved' ? 'is-selected' : '' }}"
            @if($status === 'approved') aria-current="page" @endif
        >

            <div class="min-w-0">

                <p class="ra-kpi-label">
                    Approved
                </p>

                <p class="ra-kpi-value">
                    {{ number_format($stats['approved']) }}
                </p>

                <p class="ra-kpi-note">
                    Approved Rider applications
                </p>

            </div>

            <span class="ra-kpi-icon approved" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 2.5 2.5L16 9"/>
                </svg>
            </span>

        </a>

        {{-- REJECTED --}}

        <a
            href="{{ route('logistics.rider-applications', ['status' => 'rejected']) }}"
            class="ra-kpi {{ $status === 'rejected' ? 'is-selected' : '' }}"
            @if($status === 'rejected') aria-current="page" @endif
        >

            <div class="min-w-0">

                <p class="ra-kpi-label">
                    Rejected
                </p>

                <p class="ra-kpi-value">
                    {{ number_format($stats['rejected']) }}
                </p>

                <p class="ra-kpi-note">
                    Applications declined
                </p>

            </div>

            <span class="ra-kpi-icon rejected" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m9 9 6 6m0-6-6 6"/>
                </svg>
            </span>

        </a>

    </section>

    {{-- APPLICATION QUEUE --}}

    <section
        class="ra-panel"
        aria-labelledby="raQueueHeading"
    >

        <header class="ra-panel-head">

            <div>

                <p class="ra-eyebrow">
                    Application Queue
                </p>

                <h3
                    id="raQueueHeading"
                    class="ra-panel-title capitalize"
                >
                    {{ $status }} Rider applications
                </h3>

                <p class="ra-panel-desc">
                    {{ $applications->count() }}

                    {{
                        $applications->count() === 1
                            ? 'application'
                            : 'applications'
                    }}

                    in this view
                </p>

            </div>

            {{-- SEARCH AND STATUS FILTER --}}

            <div class="ra-tools">

                <label class="ra-search-wrap">

                    <span class="sr-only">
                        Search applicants
                    </span>

                    <svg
                        class="ra-search-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>

                    <input
                        id="riderApplicationSearch"
                        class="ra-search"
                        type="search"
                        placeholder="Search applicant, area or plate..."
                        autocomplete="off"
                    >

                </label>

                <form method="GET" class="ra-tools">

                    <label
                        class="sr-only"
                        for="raStatusFilter"
                    >
                        Application status
                    </label>

                    <select
                        id="raStatusFilter"
                        name="status"
                        class="ra-filter"
                    >

                        <option
                            value="pending"
                            @selected($status === 'pending')
                        >
                            Pending
                        </option>

                        <option
                            value="approved"
                            @selected($status === 'approved')
                        >
                            Approved
                        </option>

                        <option
                            value="rejected"
                            @selected($status === 'rejected')
                        >
                            Rejected
                        </option>

                    </select>

                    <button type="submit" class="ra-apply">
                        Apply Filter
                    </button>

                </form>

            </div>

        </header>

        {{-- APPLICANT GRID --}}

        <div id="riderApplicationGrid" class="ra-card-grid">

            @forelse($applications as $application)

                @php
                    $searchText = strtolower(implode(' ', [
                        $application->fullName(),
                        $application->email,
                        $application->contact_no,
                        $application->vehicle_type,
                        $application->plate_number,
                        $application->province_name,
                        $application->municipality_name,
                        $application->barangay_name,
                        $application->street_address,
                    ]));

                    $initials = strtoupper(
                        mb_substr(
                            $application->first_name ?: '',
                            0,
                            1
                        )
                        .
                        mb_substr(
                            $application->last_name ?: '',
                            0,
                            1
                        )
                    );

                    $badgeTone = in_array(
                        $application->status,
                        ['approved', 'rejected'],
                        true
                    )
                        ? $application->status
                        : '';
                @endphp

                <article
                    class="ra-card"
                    data-rider-application
                    data-search="{{ $searchText }}"
                >

                    {{-- APPLICANT HEADER --}}

                    <header class="ra-card-head">

                        <div class="ra-person">

                            @if($application->profile_image_path)

                                <img
                                    class="ra-avatar"
                                    src="{{ asset('storage/' . $application->profile_image_path) }}"
                                    alt="Profile photo of {{ $application->fullName() }}"
                                >

                            @else

                                <span
                                    class="ra-avatar"
                                    aria-hidden="true"
                                >
                                    {{ $initials ?: 'R' }}
                                </span>

                            @endif

                            <div class="min-w-0">

                                <h4 class="ra-name">
                                    {{ $application->fullName() }}
                                </h4>

                                <p class="ra-contact">
                                    {{ $application->email }}
                                </p>

                                <p class="ra-contact">
                                    {{ $application->contact_no ?: 'No contact number' }}
                                </p>

                            </div>

                        </div>

                        <div class="flex flex-col items-start gap-1 sm:items-end">

                            <span class="ra-badge {{ $badgeTone }}">
                                {{ $application->status }}
                            </span>

                            <span class="ra-date">
                                {{ $application->created_at?->format('M d, Y · h:i A') }}
                            </span>

                        </div>

                    </header>

                    {{-- APPLICANT DETAILS --}}

                    <div class="ra-card-content">

                        <div class="ra-detail-grid">

                            {{-- SERVICE AREA --}}

                            <div class="ra-detail">

                                <span class="ra-mini-label">
                                    Service Area
                                </span>

                                <strong>
                                    {{ $application->barangay_name ?: '—' }}
                                </strong>

                                <p>
                                    {{ $application->municipality_name ?: '—' }}

                                    @if($application->province_name)
                                        · {{ $application->province_name }}
                                    @endif
                                </p>

                            </div>

                            {{-- VEHICLE --}}

                            <div class="ra-detail">

                                <span class="ra-mini-label">
                                    Vehicle
                                </span>

                                <strong>
                                    {{ $application->vehicle_type ?: '—' }}
                                </strong>

                                <p>
                                    Plate:
                                    {{ $application->plate_number ?: 'Not provided' }}
                                </p>

                            </div>

                        </div>

                        {{-- RESIDENTIAL ADDRESS --}}

                        <div class="ra-address">

                            <span class="ra-mini-label">
                                Residential Address
                            </span>

                            <p>
                                {{ $application->street_address ?: 'No street address' }}

                                @if($application->barangay_name)
                                    , {{ $application->barangay_name }}
                                @endif

                                @if($application->municipality_name)
                                    , {{ $application->municipality_name }}
                                @endif

                                @if($application->province_name)
                                    , {{ $application->province_name }}
                                @endif
                            </p>

                        </div>

                        {{-- DOCUMENTS --}}

                        <div class="ra-documents">

                            <span class="ra-mini-label mr-1">
                                Documents
                            </span>

                            @if($application->id_path)

                                <a
                                    class="ra-document"
                                    href="{{ route('logistics.rider-applications.document', [$application, 'id']) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        aria-hidden="true"
                                    >
                                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                                        <circle cx="9" cy="11" r="2"/>
                                        <path d="M15 10h3M15 14h3"/>
                                    </svg>

                                    ID / License
                                </a>

                            @else

                                <span class="ra-document is-missing">
                                    No ID
                                </span>

                            @endif

                            @if($application->orcr_path)

                                <a
                                    class="ra-document"
                                    href="{{ route('logistics.rider-applications.document', [$application, 'orcr']) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        aria-hidden="true"
                                    >
                                        <path d="M6 2h9l4 4v16H6zM14 2v5h5M9 12h6M9 16h6"/>
                                    </svg>

                                    OR / CR
                                </a>

                            @else

                                <span class="ra-document is-missing">
                                    No OR / CR
                                </span>

                            @endif

                        </div>

                        {{-- EXISTING REVIEW NOTE --}}

                        @if($application->admin_note)

                            <div class="ra-review-note">

                                <span class="ra-mini-label">
                                    Review Note
                                </span>

                                <p>
                                    {{ $application->admin_note }}
                                </p>

                            </div>

                        @endif

                        {{-- REVIEW ACTIONS --}}

                        @if($application->status === 'pending')

                            <div class="ra-review-actions">

                                {{-- APPROVE --}}

                                <form
                                    method="POST"
                                    action="{{ route('logistics.rider-applications.approve', $application) }}"
                                    class="ra-decision"
                                >

                                    @csrf

                                    <div class="ra-decision-title">

                                        <svg
                                            width="17"
                                            height="17"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            aria-hidden="true"
                                        >
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="m8 12 2.5 2.5L16 9"/>
                                        </svg>

                                        Approve Rider
                                    </div>

                                    <small>
                                        Creates an active Rider account
                                    </small>

                                    <label
                                        class="sr-only"
                                        for="raApproveNote{{ $application->id }}"
                                    >
                                        Optional approval note
                                    </label>

                                    <input
                                        id="raApproveNote{{ $application->id }}"
                                        class="ra-note"
                                        type="text"
                                        name="review_note"
                                        maxlength="1500"
                                        placeholder="Optional approval note..."
                                    >

                                    <button
                                        type="submit"
                                        class="ra-decision-button"
                                    >
                                        Approve Application
                                    </button>

                                </form>

                                {{-- REJECT --}}

                                <form
                                    method="POST"
                                    action="{{ route('logistics.rider-applications.reject', $application) }}"
                                    class="ra-decision reject"
                                >

                                    @csrf

                                    <div class="ra-decision-title">

                                        <svg
                                            width="17"
                                            height="17"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            aria-hidden="true"
                                        >
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="m9 9 6 6m0-6-6 6"/>
                                        </svg>

                                        Reject Rider
                                    </div>

                                    <small>
                                        Rejection reason is required
                                    </small>

                                    <label
                                        class="sr-only"
                                        for="raRejectNote{{ $application->id }}"
                                    >
                                        Reason for rejection
                                    </label>

                                    <input
                                        id="raRejectNote{{ $application->id }}"
                                        class="ra-note"
                                        type="text"
                                        name="review_note"
                                        required
                                        minlength="5"
                                        maxlength="1500"
                                        placeholder="Reason for rejection..."
                                    >

                                    <button
                                        type="submit"
                                        class="ra-decision-button"
                                    >
                                        Reject Application
                                    </button>

                                </form>

                            </div>

                        @else

                            {{-- ALREADY REVIEWED --}}

                            <div class="ra-completed">

                                <div>

                                    <span class="ra-mini-label">
                                        Review Completed
                                    </span>

                                    <p>
                                        {{
                                            $application->reviewed_at?->format('M d, Y · h:i A')
                                            ?: 'Decision recorded'
                                        }}
                                    </p>

                                </div>

                                <span class="ra-badge {{ $badgeTone }}">
                                    {{ $application->status }}
                                </span>

                            </div>

                        @endif

                    </div>

                </article>

            @empty

                {{-- EMPTY QUEUE --}}

                <div class="ra-empty">

                    <span
                        class="ra-empty-icon"
                        aria-hidden="true"
                    >
                        <svg
                            width="21"
                            height="21"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="9" cy="8" r="3"/>
                            <path d="M3 20c.6-4 2.8-6 6-6M17 8h4M19 6v4"/>
                        </svg>
                    </span>

                    <strong>
                        No {{ $status }} Rider applications
                    </strong>

                    <p>
                        Applications submitted to
                        {{ $logistics->displayName() }}
                        will appear here.
                    </p>

                </div>

            @endforelse

            {{-- SEARCH EMPTY STATE --}}

            <div
                id="riderApplicationSearchEmpty"
                class="ra-empty"
                hidden
            >

                <strong>
                    No matching applicants
                </strong>

                <p>
                    Try another name, location, vehicle, or plate number.
                </p>

            </div>

        </div>

        {{-- FOOTER --}}

        <footer class="ra-footer">

            <span id="raVisibleCount">
                {{ $applications->count() }}

                {{
                    $applications->count() === 1
                        ? 'application'
                        : 'applications'
                }}

                shown
            </span>

            <span>
                Current filter: {{ ucfirst($status) }}
            </span>

        </footer>

    </section>

</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', () => {

    const root = document.getElementById('lgRiderApplications');

    if (!root) {
        return;
    }

    const search = root.querySelector(
        '#riderApplicationSearch'
    );

    const cards = [
        ...root.querySelectorAll('[data-rider-application]')
    ];

    const empty = root.querySelector(
        '#riderApplicationSearchEmpty'
    );

    const visibleCount = root.querySelector(
        '#raVisibleCount'
    );

    const normalize = value =>
        String(value || '').toLocaleLowerCase().trim();

    function filterApplications() {

        const keyword = normalize(search?.value);

        let shown = 0;

        cards.forEach(card => {

            const visible =
                !keyword ||
                normalize(card.dataset.search).includes(keyword);

            card.hidden = !visible;

            if (visible) {
                shown++;
            }

        });

        if (empty) {
            empty.hidden = !cards.length || shown !== 0;
        }

        if (visibleCount) {

            visibleCount.textContent =
                `${shown} of ${cards.length} ` +
                `${cards.length === 1 ? 'application' : 'applications'} shown`;

        }

    }

    search?.addEventListener(
        'input',
        filterApplications
    );

    filterApplications();

});
</script>

@endpush
