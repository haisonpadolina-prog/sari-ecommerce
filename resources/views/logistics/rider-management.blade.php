@extends('layouts.logistics')

@section('title', 'Rider Management | SARI Logistics')
@section('page-title', 'Rider Management')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .rm-page,
    .rm-page * {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .rm-page {
        --rm-gold: #D59617;
        --rm-gold-strong: #B97805;
        --rm-text: #111827;
        --rm-text-2: #344054;
        --rm-muted: #7C8592;
        --rm-line: #E5E7EB;
        --rm-line-soft: #ECEFF2;
        --rm-soft: #F9FAFB;
        --rm-green: #16856A;
        --rm-red: #C45454;
        width: 100%;
        max-width: 1800px;
        margin-inline: auto;
        padding-bottom: 24px;
        color: var(--rm-text);
    }

    .rm-page button,
    .rm-page input,
    .rm-page select {
        font: inherit;
    }

    .rm-header {
        min-height: 66px;
        margin-bottom: 12px;
        padding: 2px 2px 13px;
        border-bottom: 1px solid var(--rm-line-soft);
    }

    .rm-eyebrow {
        margin: 0 0 5px;
        color: var(--rm-gold-strong);
        font-size: 8px;
        font-weight: 700;
        line-height: 1.15;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .rm-title {
        margin: 0;
        font-size: clamp(29px, 2.2vw, 36px);
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.035em;
        color: var(--rm-text);
    }

    .rm-title-accent { color: var(--rm-gold); }

    .rm-subtitle {
        max-width: 780px;
        margin: 7px 0 0;
        color: var(--rm-muted);
        font-size: 10px;
        font-weight: 400;
        line-height: 1.55;
    }

    .rm-summary-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 12px;
    }

    @media (min-width: 640px) {
        .rm-summary-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    .rm-summary-card {
        position: relative;
        min-height: 104px;
        overflow: hidden;
        border: 1px solid var(--rm-line);
        border-radius: 12px;
        background: #fff;
        padding: 18px;
        box-shadow: 0 2px 6px rgba(16, 24, 40, .04);
    }

    .rm-summary-label {
        color: #667085;
        font-size: 12px;
        font-weight: 500;
        line-height: 20px;
    }

    .rm-summary-value {
        margin-top: 10px;
        color: #101828;
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .rm-summary-icon {
        position: absolute;
        top: 16px;
        right: 18px;
        display: inline-flex;
        width: 24px;
        height: 24px;
        align-items: center;
        justify-content: center;
        background: transparent;
    }

    .rm-summary-icon svg {
        width: 24px;
        height: 24px;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .rm-toolbar {
        display: grid;
        grid-template-columns: minmax(360px, 1fr) 155px 155px 108px;
        gap: 8px;
        align-items: center;
        margin-top: 12px;
    }

    .rm-search {
        position: relative;
        min-width: 0;
    }

    .rm-search svg {
        position: absolute;
        top: 50%;
        left: 12px;
        width: 13px;
        height: 13px;
        color: #98A2B3;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .rm-control-wrap {
        position: relative;
        min-width: 0;
    }

    .rm-control-wrap > svg {
        position: absolute;
        z-index: 2;
        top: 50%;
        right: 11px;
        width: 12px;
        height: 12px;
        color: #667085;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .rm-control {
        width: 100%;
        height: 38px;
        border: 1px solid var(--rm-line);
        border-radius: 9px;
        background: #fff;
        color: var(--rm-text-2);
        padding: 0 10px;
        font-size: 9.5px;
        font-weight: 400;
        line-height: 1;
        outline: none;
        box-shadow: none;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .rm-search .rm-control {
        padding-left: 35px;
    }

    .rm-control-wrap .rm-control {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 32px;
        cursor: pointer;
        font-weight: 500;
    }

    .rm-control::placeholder {
        color: #98A2B3;
        font-size: 9.5px;
        font-weight: 400;
    }

    .rm-control:hover {
        border-color: #D1D5DB;
        background: #fff;
    }

    .rm-control:focus {
        border-color: var(--rm-gold);
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .09);
    }

    .rm-add-button {
        display: inline-flex;
        width: 108px;
        height: 38px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1px solid var(--rm-gold);
        border-radius: 9px;
        background: var(--rm-gold);
        padding: 0 11px;
        color: #fff;
        font-size: 9px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        box-shadow: 0 4px 10px rgba(213, 150, 23, .14);
        transition: background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
    }

    .rm-add-button svg {
        width: 12px;
        height: 12px;
        flex: 0 0 12px;
    }

    .rm-add-button:hover {
        border-color: var(--rm-gold-strong);
        background: var(--rm-gold-strong);
        box-shadow: 0 5px 12px rgba(185, 120, 5, .16);
    }

    .rm-alert {
        margin-top: 12px;
        border-radius: 12px;
        padding: 11px 13px;
        font-size: 9.5px;
        line-height: 1.55;
    }

    .rm-table-card {
        margin-top: 14px;
        overflow: hidden;
        border: 1px solid var(--rm-line);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 2px 5px rgba(15, 23, 42, .04), 0 10px 24px rgba(15, 23, 42, .06);
    }

    .rm-table-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 18px;
        border-bottom: 1px solid var(--rm-line-soft);
    }

    .rm-table-title-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .rm-table-title {
        margin: 0;
        color: #101828;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -.02em;
    }

    .rm-count {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        border: 1px solid var(--rm-line);
        border-radius: 999px;
        background: var(--rm-soft);
        padding: 0 8px;
        color: #667085;
        font-size: 8px;
        font-weight: 600;
    }

    .rm-table-copy {
        margin-top: 3px;
        color: var(--rm-muted);
        font-size: 9.5px;
        line-height: 1.5;
    }

    .rm-table-wrap { overflow-x: auto; }
    .rm-table { width: 100%; min-width: 940px; border-collapse: collapse; }
    .rm-table thead tr { background: #F9FAFB; }
    .rm-table th {
        padding: 10px 14px;
        color: #667085;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .075em;
        text-transform: uppercase;
        text-align: left;
    }
    .rm-table th:first-child,
    .rm-table td:first-child { padding-left: 18px; }
    .rm-table th:last-child,
    .rm-table td:last-child { padding-right: 18px; }

    .rm-table tbody tr {
        border-top: 1px solid var(--rm-line-soft);
        background: #fff;
        transition: background-color .14s ease, box-shadow .14s ease;
    }
    .rm-table tbody tr:hover {
        background: #FFFCF5;
        box-shadow: inset 2px 0 0 var(--rm-gold);
    }
    .rm-table td {
        padding: 11px 14px;
        color: #475467;
        font-size: 9px;
        vertical-align: middle;
    }

    .rm-rider-name { color: #101828; font-size: 9.5px; font-weight: 600; }
    .rm-rider-email { margin-top: 2px; color: #98A2B3; font-size: 8px; }

    .rm-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid var(--rm-line);
        border-radius: 999px;
        padding: 4px 8px;
        font-size: 8px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
    }

    .rm-badge.is-active { border-color: #CFE1D5; background: #F2F8F4; color: #4F7C60; }
    .rm-badge.is-pending { border-color: #EADFC9; background: #FFFAF2; color: #A8731F; }
    .rm-badge.is-inactive { border-color: #E8CCCC; background: #FFF3F3; color: #A55A5A; }
    .rm-badge.is-online { border-color: #CFE1D5; background: #F2F8F4; color: #4F7C60; }
    .rm-badge.is-offline { border-color: #E5E7EB; background: #F9FAFB; color: #667085; }

    .rm-resend {
        margin-top: 6px;
        border: 0;
        background: transparent;
        padding: 0;
        color: var(--rm-gold-strong);
        font-size: 7.5px;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    /* Buyer Cart-sized, light-gray empty states (no icon tile or background). */
    .rm-empty {
        padding: 72px 20px;
        text-align: center;
    }

    /* Win over .rm-table td padding on the placeholder cell. */
    .rm-page .rm-table td.rm-empty {
        padding: 72px 24px;
    }

    .rm-page .rm-empty-icon {
        display: grid;
        width: 92px;
        height: 92px;
        place-items: center;
        margin: 0 auto;
        border: 0;
        background: transparent;
        color: #B4BBC3;
    }

    .rm-page .rm-empty-icon svg {
        width: 58px;
        height: 58px;
        stroke: currentColor;
        stroke-width: 1.45;
        stroke-linecap: round;
        stroke-linejoin: round;
        fill: none;
    }

    .rm-page .rm-empty-title {
        margin: 18px 0 0;
        color: #9CA4AD;
        font-size: 22px;
        font-weight: 750;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    .rm-page .rm-empty-copy {
        max-width: 520px;
        margin: 7px auto 0;
        color: #B0B7BF;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.55;
    }

    /* Without Riders, don't force the empty message into a 940px-wide table. */
    .rm-page .rm-table.is-empty {
        min-width: 0;
    }

    .rm-page .rm-table.is-empty thead {
        display: none;
    }

    @media (max-width: 640px) {
        .rm-empty { padding: 56px 18px; }
        .rm-page .rm-table td.rm-empty { padding: 56px 18px; }
        .rm-page .rm-empty-icon { width: 64px; height: 64px; }
        .rm-page .rm-empty-icon svg { width: 40px; height: 40px; }
        .rm-page .rm-empty-title { font-size: 20px; }
        .rm-page .rm-empty-copy { font-size: 11px; }
    }

    .rm-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(17, 24, 39, .46);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
    }
    .rm-modal-backdrop.is-open { display: flex; }

    .rm-modal {
        width: min(860px, 100%);
        max-height: min(88vh, 760px);
        overflow: hidden;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 30px 80px rgba(15, 23, 42, .20);
    }

    .rm-modal-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--rm-line-soft);
    }
    .rm-modal-kicker {
        margin: 0 0 4px;
        color: var(--rm-gold-strong);
        font-size: 7.5px;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
    }
    .rm-modal-title {
        margin: 0;
        color: #101828;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.025em;
    }
    .rm-modal-copy {
        margin-top: 5px;
        color: var(--rm-muted);
        font-size: 9px;
        line-height: 1.5;
    }
    .rm-modal-close {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border: 1px solid var(--rm-line);
        border-radius: 9px;
        background: #fff;
        color: #667085;
    }
    .rm-modal-close:hover { background: #F9FAFB; }

    .rm-modal-body {
        max-height: calc(min(88vh, 760px) - 78px);
        overflow-y: auto;
        padding: 18px 20px 20px;
    }

    .rm-form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .rm-field { display: block; min-width: 0; }
    .rm-field-label {
        display: block;
        margin-bottom: 6px;
        color: #475467;
        font-size: 8.5px;
        font-weight: 600;
    }
    .rm-form-control {
        width: 100%;
        height: 40px;
        border: 1px solid var(--rm-line);
        border-radius: 9px;
        background: #fff;
        padding: 0 11px;
        color: #344054;
        font-size: 9.5px;
        outline: none;
    }
    .rm-form-control:focus {
        border-color: var(--rm-gold);
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .10);
    }

    .rm-file-input {
        width: 100%;
        min-height: 40px;
        border: 1px dashed #D1D5DB;
        border-radius: 9px;
        background: #F9FAFB;
        padding: 8px 10px;
        color: #667085;
        font-size: 8.5px;
    }

    .rm-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--rm-line-soft);
    }
    .rm-button-secondary,
    .rm-button-primary {
        display: inline-flex;
        height: 40px;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        padding: 0 14px;
        font-size: 9.5px;
        font-weight: 600;
    }
    .rm-button-secondary {
        border: 1px solid var(--rm-line);
        background: #fff;
        color: #667085;
    }
    .rm-button-primary {
        border: 1px solid var(--rm-gold);
        background: var(--rm-gold);
        color: #fff;
    }
    .rm-button-primary:hover { border-color: var(--rm-gold-strong); background: var(--rm-gold-strong); }

    @media (max-width: 1023px) {
        .rm-toolbar { grid-template-columns: minmax(0, 1fr) 145px 145px; }
        .rm-add-button { grid-column: 1 / -1; width: 100%; }
        .rm-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 639px) {
        .rm-header { min-height: auto; }
        .rm-title { font-size: 28px; }
        .rm-subtitle { font-size: 10px; }
        .rm-toolbar { grid-template-columns: 1fr; }
        .rm-add-button { grid-column: auto; }
        .rm-form-grid { grid-template-columns: 1fr; }
        .rm-modal-backdrop { padding: 10px; }
        .rm-modal-head,
        .rm-modal-body { padding-left: 14px; padding-right: 14px; }
    }

    /* Final compact typography override for Rider toolbar */
    .rm-page .rm-control-wrap select.rm-control {
        font-size: 8.5px !important;
        font-weight: 500 !important;
        letter-spacing: 0 !important;
        line-height: 1 !important;
    }

    .rm-page .rm-control-wrap select.rm-control option {
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important;
        font-size: 8.5px !important;
        font-weight: 400 !important;
    }

    .rm-page .rm-add-button {
        font-size: 8.5px !important;
        font-weight: 500 !important;
        letter-spacing: 0 !important;
    }

    .rm-page .rm-search .rm-control,
    .rm-page .rm-search .rm-control::placeholder {
        font-size: 9px !important;
        font-weight: 400 !important;
    }

</style>
@endpush

@section('content')
<div class="rm-page sari-logistics-ui">
    <section class="rm-header">
        <p class="rm-eyebrow">Rider Operations</p>
        <h1 class="rm-title">Manage <span class="rm-title-accent">Riders</span></h1>
    </section>

    <section class="rm-summary-grid" aria-label="Rider account summary">
        <article class="rm-summary-card">
            <p class="rm-summary-label">Approved Riders</p>
            <p class="rm-summary-value">{{ $stats['total'] }}</p>
            <span class="rm-summary-icon" style="color:#AC7A16" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="9" cy="7" r="3"></circle><path d="M3 20a6 6 0 0 1 12 0"></path><path d="M17 6a3 3 0 0 1 0 6"></path><path d="M18 15a5 5 0 0 1 3 5"></path></svg>
            </span>
        </article>
        <article class="rm-summary-card">
            <p class="rm-summary-label">Active Accounts</p>
            <p class="rm-summary-value">{{ $stats['active'] }}</p>
            <span class="rm-summary-icon" style="color:#16856A" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
            </span>
        </article>
        <article class="rm-summary-card">
            <p class="rm-summary-label">Busy Now</p>
            <p class="rm-summary-value">{{ $stats['busy'] }}</p>
            <span class="rm-summary-icon" style="color:#4F82B6" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 15h2l2-5h7l2 5h3"></path><circle cx="8" cy="17" r="2"></circle><circle cx="18" cy="17" r="2"></circle><path d="M9 10V7h5"></path></svg>
            </span>
        </article>
    </section>

    @if(session('success'))
        <div class="rm-alert border border-[#CFE1D5] bg-[#F2F8F4] text-[#4F7C60]" role="status">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rm-alert border border-[#E8CCCC] bg-[#FFF3F3] text-[#A55A5A]" role="alert">{{ $errors->first() }}</div>
    @endif

    <section class="rm-toolbar" aria-label="Rider filters">
        <div class="rm-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg>
            <input id="riderSearch" type="search" autocomplete="off" class="rm-control" placeholder="Search Rider, email, contact, vehicle, or plate...">
        </div>

        <div class="rm-control-wrap">
            <select id="riderAccountFilter" class="rm-control" aria-label="Filter by account status">
                <option value="all">All Accounts</option>
                <option value="active">Active</option>
                <option value="pending_activation">Pending Activation</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
        </div>

        <div class="rm-control-wrap">
            <select id="riderAvailabilityFilter" class="rm-control" aria-label="Filter by availability">
                <option value="all">All Availability</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="busy">Busy</option>
            </select>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
        </div>

        <button id="openAddRiderModal" type="button" class="rm-add-button">
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
            Add Rider
        </button>
    </section>

    <section class="rm-table-card">
        <div class="rm-table-header">
            <div>
                <div class="rm-table-title-row">
                    <h2 class="rm-table-title">Rider directory</h2>
                    <span id="riderVisibleCount" class="rm-count">Showing {{ $riders->count() }}</span>
                </div>
                <p class="rm-table-copy">Review Rider account status, availability, assigned vehicle, and active delivery work.</p>
            </div>
        </div>

        <div class="rm-table-wrap">
            <table class="rm-table {{ $riders->isEmpty() ? 'is-empty' : '' }}">
                <thead>
                    <tr>
                        <th>Rider</th>
                        <th>Vehicle</th>
                        <th>Contact</th>
                        <th>Account</th>
                        <th>Availability</th>
                        <th>Current Work</th>
                    </tr>
                </thead>
                <tbody id="riderTableBody">
                    @forelse($riders as $rider)
                        @php
                            $activeOrder = $activeOrders->get(strtolower((string) $rider->email));
                            $accountStatus = strtolower((string) ($rider->account_status ?: 'inactive'));
                            $availability = strtolower((string) ($rider->availability_status ?: 'online'));
                            $searchText = strtolower(trim($rider->first_name.' '.$rider->last_name).' '.$rider->email.' '.$rider->contact_no.' '.$rider->vehicle_type.' '.$rider->plate_number);
                            $accountBadge = $accountStatus === 'active' ? 'is-active' : ($accountStatus === 'pending_activation' ? 'is-pending' : 'is-inactive');
                            $availabilityBadge = $availability === 'online' ? 'is-online' : 'is-offline';
                        @endphp
                        <tr
                            data-rider-row
                            data-search="{{ $searchText }}"
                            data-account="{{ $accountStatus }}"
                            data-availability="{{ $availability }}"
                        >
                            <td>
                                <p class="rm-rider-name">{{ trim($rider->first_name.' '.$rider->last_name) }}</p>
                                <p class="rm-rider-email">{{ $rider->email }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-[#344054]">{{ $rider->vehicle_type ?: '—' }}</p>
                                <p class="mt-0.5 text-[8px] text-[#98A2B3]">{{ $rider->plate_number ?: 'No plate number' }}</p>
                            </td>
                            <td>{{ $rider->contact_no ?: '—' }}</td>
                            <td>
                                <span class="rm-badge {{ $accountBadge }}">{{ str($accountStatus)->replace('_', ' ')->title() }}</span>
                                @if($accountStatus === 'pending_activation')
                                    <form method="POST" action="{{ route('logistics.rider-management.resend-activation', $rider) }}">
                                        @csrf
                                        <button type="submit" class="rm-resend">Resend activation</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <span class="rm-badge {{ $availabilityBadge }}">{{ ucfirst($availability) }}</span>
                            </td>
                            <td>
                                @if($activeOrder)
                                    <p class="font-medium text-[#344054]">{{ $activeOrder->order_number }}</p>
                                    <p class="mt-0.5 text-[8px] text-[#98A2B3]">{{ $activeOrder->statusLabel() }}</p>
                                @else
                                    <span class="text-[#667085]">{{ $availability === 'online' ? 'Available' : 'Offline' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr data-empty-row>
                            <td colspan="6" class="rm-empty">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="rm-empty-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24">
                                            <circle cx="9" cy="8" r="3"></circle>
                                            <path d="M3.5 20c.5-4.1 2.3-6.3 5.5-6.3s5 2.2 5.5 6.3"></path>
                                            <path d="M16 8.5a2.6 2.6 0 1 0 0-5.2M16.5 14c2.6.3 4.2 2.2 4.5 5"></path>
                                        </svg>
                                    </span>
                                    <p class="rm-empty-title">No Riders yet</p>
                                    <p class="rm-empty-copy">Approved Rider accounts will appear here once available.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="riderSearchEmpty" hidden class="rm-empty border-t border-[#ECEFF2]">
            <div class="flex flex-col items-center justify-center">
                <span class="rm-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <circle cx="10.5" cy="10.5" r="6.5"></circle>
                        <path d="m16 16 4.5 4.5M8 10.5h5"></path>
                    </svg>
                </span>
                <p class="rm-empty-title">No matching Riders</p>
                <p class="rm-empty-copy">Try another search or adjust your account and availability filters.</p>
            </div>
        </div>
    </section>
</div>

<div id="addRiderModal" class="rm-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="addRiderModalTitle" aria-hidden="true">
    <div class="rm-modal" role="document">
        <div class="rm-modal-head">
            <div>
                <p class="rm-modal-kicker">Rider Account</p>
                <h2 id="addRiderModalTitle" class="rm-modal-title">Add Rider</h2>
                <p class="rm-modal-copy">Creates a Pending Activation account and emails a secure 24-hour activation link.</p>
            </div>
            <button id="closeAddRiderModal" type="button" class="rm-modal-close" aria-label="Close Add Rider dialog">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 7 10 10M17 7 7 17"></path></svg>
            </button>
        </div>

        <div class="rm-modal-body">
            <form method="POST" action="{{ route('logistics.rider-management.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="rm-form-grid">
                    @foreach([
                        ['first_name','First Name','text'],
                        ['last_name','Last Name','text'],
                        ['birthday','Date of Birth','date'],
                        ['email','Email','email'],
                        ['contact_no','Mobile Number','text'],
                        ['license_number',"Driver's License No.",'text'],
                        ['license_expiry_date','License Expiry Date','date'],
                        ['plate_number','Plate Number','text'],
                    ] as [$name,$label,$type])
                        <label class="rm-field">
                            <span class="rm-field-label">{{ $label }} *</span>
                            <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name) }}" required class="rm-form-control">
                        </label>
                    @endforeach

                    <label class="rm-field">
                        <span class="rm-field-label">Vehicle Type *</span>
                        <select name="vehicle_type" required class="rm-form-control">
                            <option value="">Select vehicle</option>
                            @foreach(['Motorcycle','Car','Van'] as $vehicle)
                                <option value="{{ $vehicle }}" @selected(old('vehicle_type') === $vehicle)>{{ $vehicle }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="rm-field">
                        <span class="rm-field-label">Driver's License *</span>
                        <input type="file" name="driver_license_document" accept=".jpg,.jpeg,.png,.pdf" required class="rm-file-input">
                    </label>

                    <label class="rm-field">
                        <span class="rm-field-label">Vehicle OR/CR *</span>
                        <input type="file" name="orcr_document" accept=".jpg,.jpeg,.png,.pdf" required class="rm-file-input">
                    </label>
                </div>

                <div class="rm-modal-actions">
                    <button id="cancelAddRiderModal" type="button" class="rm-button-secondary">Cancel</button>
                    <button type="submit" class="rm-button-primary">Save Rider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    function initRiderManagementPage() {
        if (window.__SARI_RIDER_MANAGEMENT_ABORT__) {
            window.__SARI_RIDER_MANAGEMENT_ABORT__.abort();
        }

        const controller = new AbortController();
        const signal = controller.signal;
        window.__SARI_RIDER_MANAGEMENT_ABORT__ = controller;

        const search = document.getElementById('riderSearch');
        const accountFilter = document.getElementById('riderAccountFilter');
        const availabilityFilter = document.getElementById('riderAvailabilityFilter');
        const rows = Array.from(document.querySelectorAll('[data-rider-row]'));
        const count = document.getElementById('riderVisibleCount');
        const empty = document.getElementById('riderSearchEmpty');

        const modal = document.getElementById('addRiderModal');
        const openModal = document.getElementById('openAddRiderModal');
        const closeModal = document.getElementById('closeAddRiderModal');
        const cancelModal = document.getElementById('cancelAddRiderModal');
        let lastFocused = null;

        function listen(node, eventName, handler, options) {
            if (!node) return;
            node.addEventListener(eventName, handler, Object.assign({}, options || {}, { signal }));
        }

        function filterRows() {
            const query = (search?.value || '').trim().toLowerCase();
            const account = accountFilter?.value || 'all';
            const availability = availabilityFilter?.value || 'all';
            let visible = 0;

            rows.forEach(function (row) {
                const matchesSearch = !query || (row.dataset.search || '').includes(query);
                const matchesAccount = account === 'all' || row.dataset.account === account;
                const matchesAvailability = availability === 'all' || row.dataset.availability === availability;
                const matches = matchesSearch && matchesAccount && matchesAvailability;
                row.hidden = !matches;
                if (matches) visible++;
            });

            if (count) count.textContent = 'Showing ' + visible;
            if (empty) empty.hidden = visible !== 0 || rows.length === 0;
        }

        function openAddModal() {
            if (!modal) return;
            lastFocused = document.activeElement;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.documentElement.style.overflow = 'hidden';
            window.setTimeout(function () {
                modal.querySelector('input, select, button')?.focus({ preventScroll: true });
            }, 0);
        }

        function closeAddModal() {
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.documentElement.style.overflow = '';
            lastFocused?.focus?.({ preventScroll: true });
        }

        listen(search, 'input', filterRows, { passive: true });
        listen(accountFilter, 'change', filterRows);
        listen(availabilityFilter, 'change', filterRows);
        listen(openModal, 'click', openAddModal);
        listen(closeModal, 'click', closeAddModal);
        listen(cancelModal, 'click', closeAddModal);
        listen(modal, 'click', function (event) {
            if (event.target === modal) closeAddModal();
        });
        listen(document, 'keydown', function (event) {
            if (event.key === 'Escape' && modal?.classList.contains('is-open')) closeAddModal();
        });

        filterRows();

        @if($errors->any())
            openAddModal();
        @endif
    }

    window.__SARI_INIT_RIDER_MANAGEMENT__ = initRiderManagementPage;

    if (!window.__SARI_RIDER_MANAGEMENT_LIVEWIRE_LISTENER__) {
        window.__SARI_RIDER_MANAGEMENT_LIVEWIRE_LISTENER__ = true;
        document.addEventListener('livewire:navigated', function () {
            window.__SARI_INIT_RIDER_MANAGEMENT__?.();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRiderManagementPage, { once: true });
    } else {
        initRiderManagementPage();
    }
})();
</script>
@endsection
