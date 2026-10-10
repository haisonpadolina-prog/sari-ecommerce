@extends('layouts.seller')

@section('title', 'Finance & Earnings — SARI Seller')

@section('page-title', 'Finance & Earnings')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/finance.css') }}?v=20261007-1">



@endpush

@section('content')

@php

    $summaryCards = [

        [

            'label' => 'Gross Sales',

            'value' => $stats['gross'],

            'note' => 'Eligible merchandise sales',

            'icon' => 'gross',

        ],

        [

            'label' => 'Platform Commission',

            'value' => $stats['commission'],

            'note' => 'Platform commission recorded',

            'icon' => 'commission',

        ],

        [

            'label' => 'Net Earnings',

            'value' => $stats['net'],

            'note' => 'Seller net after deductions',

            'icon' => 'net',

        ],

        [

            'label' => 'Pending Settlement',

            'value' => $stats['pending'],

            'note' => 'Not yet released in ledger',

            'icon' => 'pending',

        ],

        [

            'label' => 'Paid / Released',

            'value' => $stats['paid'],

            'note' => 'Marked released internally',

            'icon' => 'paid',

        ],

    ];

    $statusClass = fn (?string $value) => match (strtolower((string) $value)) {

        'paid', 'released', 'completed' => 'seller-finance-status--paid',

        'pending', 'eligible' => 'seller-finance-status--pending',

        default => 'seller-finance-status--default',

    };

@endphp

<div class="seller-finance-page">

    @if(session('success'))

        <div class="seller-finance-alert seller-finance-alert--success">

            <span class="seller-finance-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <circle cx="12" cy="12" r="8"></circle>

                    <path d="m8 12 2.5 2.5L16 9"></path>

                </svg>

            </span>

            <div>{{ session('success') }}</div>

        </div>

    @endif

    @if($errors->any())

        <div class="seller-finance-alert seller-finance-alert--error">

            <span class="seller-finance-alert-icon" aria-hidden="true">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">

                    <path d="M12 3 3 20h18L12 3Z"></path>

                    <path d="M12 9v5"></path>

                    <path d="M12 17h.01"></path>

                </svg>

            </span>

            <div>{{ $errors->first() }}</div>

        </div>

    @endif

    <header class="seller-finance-header">

        <p class="seller-finance-eyebrow">Business Finance</p>

        <h1 class="seller-finance-title">

            <span>Finance</span><span>& Earnings</span>

        </h1>

        <p class="seller-finance-subtitle">

            Delivered-and-paid orders only. Settlements are internal ledger records, not proof of an external bank transfer.

        </p>

    </header>

    <section class="seller-finance-summary" aria-label="Finance summary">

        @foreach($summaryCards as $card)

            <article class="seller-finance-stat">

                <div class="seller-finance-stat-inner">

                    <div class="seller-finance-stat-copy">

                        <div class="seller-finance-stat-label">{{ $card['label'] }}</div>

                        <div class="seller-finance-stat-value">

                            ₱{{ number_format((float) $card['value'], 2) }}

                        </div>

                        <div class="seller-finance-stat-note">{{ $card['note'] }}</div>

                    </div>

                    <span class="seller-finance-stat-icon seller-finance-stat-icon--{{ $card['icon'] }}" aria-hidden="true">

                        @if($card['icon'] === 'gross')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M4 18V9"></path>

                                <path d="M10 18V5"></path>

                                <path d="M16 18v-7"></path>

                                <path d="M3 19h18"></path>

                            </svg>

                        @elseif($card['icon'] === 'commission')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="8" cy="8" r="2"></circle>

                                <circle cx="16" cy="16" r="2"></circle>

                                <path d="M7 17 17 7"></path>

                            </svg>

                        @elseif($card['icon'] === 'net')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <path d="M4 12h16"></path>

                                <path d="m14 6 6 6-6 6"></path>

                                <path d="M4 7v10"></path>

                            </svg>

                        @elseif($card['icon'] === 'pending')

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="12" cy="12" r="8"></circle>

                                <path d="M12 8v4l3 2"></path>

                            </svg>

                        @else

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                                <circle cx="12" cy="12" r="8"></circle>

                                <path d="m8 12 2.5 2.5L16 9"></path>

                            </svg>

                        @endif

                    </span>

                </div>

            </article>

        @endforeach

    </section>

    <section class="seller-finance-ledger">

        <div class="seller-finance-ledger-head">

            <div class="seller-finance-ledger-title-group">

                <span class="seller-finance-ledger-icon" aria-hidden="true">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                        <path d="M5 4h14v16H5z"></path>

                        <path d="M8 8h8"></path>

                        <path d="M8 12h8"></path>

                        <path d="M8 16h5"></path>

                    </svg>

                </span>

                <div>

                    <h2 class="seller-finance-ledger-title">Settlement Ledger</h2>

                    <p class="seller-finance-ledger-subtitle">

                        Internal settlement records for eligible Seller orders.

                    </p>

                </div>

            </div>

            <span class="seller-finance-ledger-count">

                {{ number_format($settlements->total()) }} records

            </span>

        </div>

        <div class="seller-finance-table-wrap">

            <table class="seller-finance-table">

                <thead>

                    <tr>

                        <th>Order</th>

                        <th>Eligible</th>

                        <th>Gross</th>

                        <th>Commission</th>

                        <th>Withholding</th>

                        <th>Net</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($settlements as $row)

                        <tr>

                            <td class="seller-finance-order">

                                {{ $row->order?->order_number ?: 'Order unavailable' }}

                            </td>

                            <td>

                                {{ $row->eligible_at?->format('M d, Y') ?: '—' }}

                            </td>

                            <td class="seller-finance-money">

                                ₱{{ number_format((float) $row->merchandise_amount, 2) }}

                            </td>

                            <td class="seller-finance-money seller-finance-money--muted">

                                ₱{{ number_format((float) $row->platform_commission_amount, 2) }}

                            </td>

                            <td class="seller-finance-money seller-finance-money--muted">

                                ₱{{ number_format((float) $row->withholding_tax_amount, 2) }}

                            </td>

                            <td class="seller-finance-money seller-finance-money--net">

                                ₱{{ number_format((float) $row->seller_net_amount, 2) }}

                            </td>

                            <td>

                                <span class="seller-finance-status {{ $statusClass($row->status) }}">

                                    {{ ucfirst($row->status) }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="seller-finance-empty">

                                No financial settlements yet.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($settlements->hasPages())

            <div class="seller-finance-pagination">

                {{ $settlements->links() }}

            </div>

        @endif

    </section>

</div>

@endsection
