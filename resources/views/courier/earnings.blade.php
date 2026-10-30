@extends('layouts.courier')

@section('title', 'Rider Earnings')
@section('header-title', 'Rider Earnings')
@section('header-subtitle', 'Delivery Fee Ledger')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/earnings.css') }}?v=20261029-2"
    >
@endpush

@section('content')
@php
    $maxAmount = max(1, ...array_column($daily, 'amount'));

    $hasChartEarnings = collect($daily)->contains(
        fn ($day) => (float) $day['amount'] > 0
    );

    $weeklyChangeClass = $weeklyChange === null || $weeklyChange == 0
        ? ''
        : ($weeklyChange > 0 ? 're-badge-green' : 're-badge-red');

    $summaryCards = [
        [
            'label' => 'Today',
            'key' => 'today',
            'note' => 'Recognized today',
            'class' => '',
        ],
        [
            'label' => 'This Week',
            'key' => 'week',
            'note' => 'Current week earnings',
            'class' => 're-stat-blue',
        ],
        [
            'label' => 'This Month',
            'key' => 'month',
            'note' => 'Current month earnings',
            'class' => 're-stat-green',
        ],
        [
            'label' => 'Potential',
            'key' => 'pending',
            'note' => 'Not yet recognized',
            'class' => 're-stat-neutral',
        ],
        [
            'label' => 'Available',
            'key' => 'available',
            'note' => 'Available wallet balance',
            'class' => 're-stat-green',
        ],
    ];

    $walletCards = [
        [
            'label' => 'Awaiting COD Remittance',
            'key' => 'awaiting_remittance',
            'note' => 'Earned but locked until cash remittance is verified.',
        ],
        [
            'label' => 'Reserved for Payout',
            'key' => 'reserved',
            'note' => 'Already included in a withdrawal request.',
        ],
        [
            'label' => 'Lifetime Paid Out',
            'key' => 'paid',
            'note' => 'Confirmed external payouts.',
        ],
    ];
@endphp

<div
    id="riderEarnings"
    class="courier-earnings-page courier-workspace"
>
    @if(session('success'))
        <div class="re-alert re-alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="re-alert re-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="re-heading" aria-labelledby="riderEarningsTitle">
        <div class="re-heading-copy">
            <span class="re-eyebrow">Rider Ledger</span>

            <h2 id="riderEarningsTitle" class="re-title">
                Rider <span>Earnings</span>
            </h2>

            <p class="re-description">
                Track pickup, direct-delivery, and last-mile earnings in one place.
            </p>
        </div>

        <div class="re-heading-actions">
            <a
                href="{{ route('courier.remittances.index') }}"
                class="re-button"
            >
                COD Remittance
            </a>

            <a
                href="{{ route('courier.earnings.statement') }}"
                class="re-button re-button-gold"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M12 3v12m-4-4 4 4 4-4"></path>
                    <path d="M5 16v5h14v-5"></path>
                </svg>

                Download Statement
            </a>
        </div>
    </section>

    <section class="re-summary" aria-label="Rider earnings summary">
        @foreach($summaryCards as $card)
            <div class="re-stat {{ $card['class'] }}">
                <div class="re-stat-top">
                    <p class="re-stat-label">
                        {{ $card['label'] }}
                    </p>

                    <span class="re-stat-icon" aria-hidden="true">₱</span>
                </div>

                <p class="re-stat-value">
                    ₱{{ number_format($earnings[$card['key']], 2) }}
                </p>

                <p class="re-stat-note">
                    {{ $card['note'] }}
                </p>
            </div>
        @endforeach
    </section>

    <div class="re-explainer">
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.7"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M12 11v6M12 7h.01"></path>
        </svg>

        <p>
            <strong>Potential earnings</strong> are estimates for work not yet
            recognized in the ledger. Logistics-managed shipping uses the
            configured Rider shares. Availability depends on finance recognition
            and any required COD remittance verification.
        </p>
    </div>

    <section class="re-wallet-summary" aria-label="Wallet balance breakdown">
        @foreach($walletCards as $card)
            <div class="re-wallet-card">
                <p class="re-stat-label">
                    {{ $card['label'] }}
                </p>

                <p class="re-wallet-value">
                    ₱{{ number_format($earnings[$card['key']], 2) }}
                </p>

                <p class="re-stat-note">
                    {{ $card['note'] }}
                </p>
            </div>
        @endforeach
    </section>

    <div class="re-main">
        <div class="re-main-column">
            <section class="re-panel" aria-labelledby="earningsTrendTitle">
                <div class="re-panel-head">
                    <div>
                        <h3 id="earningsTrendTitle" class="re-panel-title">
                            Earnings Trend
                        </h3>

                        <p class="re-panel-description">
                            Recognized Rider earnings for the last seven days.
                        </p>
                    </div>

                    <span class="re-badge re-badge-gold">
                        Last 7 Days
                    </span>
                </div>

                <div
                    class="re-chart-scroll"
                    tabindex="0"
                    role="region"
                    aria-label="Scrollable earnings chart for the last seven days"
                >
                    <div class="re-chart">
                        @foreach($daily as $day)
                            @php
                                $barHeight = max(
                                    0,
                                    min(
                                        180,
                                        round(((float) $day['amount'] / $maxAmount) * 180)
                                    )
                                );
                            @endphp

                            <div class="re-chart-day">
                                <p class="re-chart-amount">
                                    ₱{{ number_format($day['amount'], 2) }}
                                </p>

                                <div class="re-chart-track">
                                    <svg
                                        viewBox="0 0 34 180"
                                        aria-hidden="true"
                                        focusable="false"
                                    >
                                        <rect
                                            x="0"
                                            y="{{ 180 - $barHeight }}"
                                            width="34"
                                            height="{{ $barHeight }}"
                                            rx="4"
                                        ></rect>
                                    </svg>
                                </div>

                                <p class="re-chart-label">
                                    {{ $day['day'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if(!$hasChartEarnings)
                    <p class="re-chart-note">
                        No recognized earnings during the displayed seven days.
                    </p>
                @endif

                <div class="re-panel-footer">
                    <span>This week compared with last week</span>

                    <span class="re-badge {{ $weeklyChangeClass }}">
                        {{
                            $weeklyChange === null
                                ? 'No prior week'
                                : (
                                    ($weeklyChange >= 0 ? '+' : '')
                                    . number_format($weeklyChange, 1)
                                    . '%'
                                )
                        }}
                    </span>
                </div>
            </section>

            <section class="re-panel" aria-labelledby="recentEarningsTitle">
                <div class="re-panel-head">
                    <div>
                        <h3 id="recentEarningsTitle" class="re-panel-title">
                            Recent Earnings
                        </h3>

                        <p class="re-panel-description">
                            Recognized delivery fees and current ledger status.
                        </p>
                    </div>

                    <span class="re-badge">
                        {{ count($transactions) }}
                        {{ count($transactions) === 1 ? 'record' : 'records' }}
                    </span>
                </div>

                <div
                    class="re-table-scroll"
                    tabindex="0"
                    role="region"
                    aria-labelledby="recentEarningsTitle"
                >
                    <table class="re-table" aria-label="Recent Rider earnings">
                        <thead>
                            <tr>
                                <th scope="col">Order</th>
                                <th scope="col">Earned At</th>
                                <th scope="col">Type</th>
                                <th scope="col" class="re-money">Amount</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($transactions as $transaction)
                                @php
                                    $transactionStatusClass = match ($transaction['status']) {
                                        'Awaiting COD remittance' => 're-badge-gold',
                                        'Available in wallet', 'Paid' => 're-badge-green',
                                        'Reserved for payout' => 're-badge-blue',
                                        default => '',
                                    };
                                @endphp

                                <tr>
                                    <td class="re-order">
                                        {{ $transaction['order'] }}
                                    </td>

                                    <td>
                                        {{ $transaction['date'] }}
                                    </td>

                                    <td>
                                        {{ $transaction['type'] }}
                                    </td>

                                    <td class="re-money">
                                        +₱{{ number_format($transaction['amount'], 2) }}
                                    </td>

                                    <td>
                                        <span class="re-badge {{ $transactionStatusClass }}">
                                            {{ $transaction['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="re-table-empty">
                                        No recognized Rider earnings yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="re-panel-footer">
                    <span>Pickup, direct-delivery, and last-mile ledger entries.</span>
                </div>
            </section>
        </div>

        <aside class="re-panel" aria-labelledby="payoutSummaryTitle">
            <div class="re-panel-head">
                <div>
                    <h3 id="payoutSummaryTitle" class="re-panel-title">
                        Payout Summary
                    </h3>

                    <p class="re-panel-description">
                        Your available balance and withdrawal requests.
                    </p>
                </div>
            </div>

            <div class="re-panel-body">
                <div class="re-balance">
                    <p class="re-balance-label">Available Balance</p>

                    <p class="re-balance-value">
                        ₱{{ number_format($earnings['available'], 2) }}
                    </p>

                    <p class="re-balance-note">
                        Available in your internal Rider wallet.
                        This does not yet indicate an external payout.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('courier.earnings.payout') }}"
                    class="re-form"
                >
                    @csrf

                    <div class="re-field">
                        <label for="riderWithdrawalAmount">
                            Withdrawal amount (₱)
                        </label>

                        <input
                            id="riderWithdrawalAmount"
                            name="amount"
                            type="number"
                            min="1"
                            step="0.01"
                            max="{{ $earnings['available'] }}"
                            value="{{ old('amount') }}"
                            required
                            placeholder="Enter amount"
                        >
                    </div>

                    <div class="re-field">
                        <label for="riderPayoutMethod">
                            Payout method
                        </label>

                        <select
                            id="riderPayoutMethod"
                            name="payout_method"
                            required
                        >
                            <option
                                value="gcash"
                                @selected(old('payout_method', 'gcash') === 'gcash')
                            >
                                GCash
                            </option>

                            <option
                                value="maya"
                                @selected(old('payout_method', 'gcash') === 'maya')
                            >
                                Maya
                            </option>

                            <option
                                value="bank"
                                @selected(old('payout_method', 'gcash') === 'bank')
                            >
                                Bank Transfer
                            </option>

                            <option
                                value="manual_cash"
                                @selected(old('payout_method', 'gcash') === 'manual_cash')
                            >
                                Manual Cash Release
                            </option>
                        </select>
                    </div>

                    <div class="re-field">
                        <label for="riderPayoutAccountName">
                            Account name
                        </label>

                        <input
                            id="riderPayoutAccountName"
                            name="account_name"
                            type="text"
                            value="{{ old('account_name') }}"
                            required
                            minlength="2"
                            maxlength="120"
                            placeholder="Account name"
                        >
                    </div>

                    <div class="re-field">
                        <label for="riderPayoutAccountReference">
                            Mobile / bank account
                        </label>

                        <input
                            id="riderPayoutAccountReference"
                            name="account_reference"
                            type="text"
                            value="{{ old('account_reference') }}"
                            required
                            minlength="4"
                            maxlength="120"
                            placeholder="Mobile / bank account"
                        >
                    </div>

                    <button
                        type="submit"
                        class="re-button re-button-gold"
                        @disabled($earnings['available'] <= 0)
                    >
                        Request Withdrawal
                    </button>

                    @if($earnings['available'] <= 0)
                        <p class="re-form-note">
                            Withdrawal is unavailable while your available balance is ₱0.
                        </p>
                    @endif
                </form>

                <section class="re-requests" aria-labelledby="payoutRequestsTitle">
                    <h4 id="payoutRequestsTitle" class="re-requests-title">
                        Recent Payout Requests
                    </h4>

                    @forelse($payoutRequests as $payout)
                        <div class="re-request">
                            <span class="re-request-amount">
                                ₱{{ number_format((float) $payout->amount, 2) }}
                            </span>

                            <span class="re-badge re-request-status">
                                {{ str_replace('_', ' ', $payout->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="re-empty">
                            No payout requests yet.
                        </p>
                    @endforelse
                </section>
            </div>
        </aside>
    </div>
</div>
@endsection