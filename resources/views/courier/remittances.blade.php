@extends('layouts.courier')

@section('title', 'COD Cash Remittance')
@section('header-title', 'COD Cash Remittance')
@section('header-subtitle', 'Physical Hub Turnover')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/remittances.css') }}?v=20261029-2"
    >
@endpush

@section('content')
@php
    $openRecentBatches = $batches
        ->whereIn('status', ['submitted', 'discrepancy'])
        ->count();
@endphp

<div
    id="riderRemittances"
    class="courier-remittances-page courier-workspace"
>
    @if(session('success'))
        <div class="cr-alert cr-alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="cr-alert cr-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <header class="cr-page-header">
        <div>
            <p class="cr-eyebrow">Rider Workspace</p>

            <h2 class="cr-page-title">
                COD <span>Remittance</span>
            </h2>

            <p class="cr-description">
                Prepare your cash batch and track hub verification.
            </p>
        </div>

        <a
            href="{{ route('courier.earnings') }}"
            class="cr-button"
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
                <path d="M20 8V5a2 2 0 0 0-2-2H6a3 3 0 0 0 0 6h14v11H6a3 3 0 0 1-3-3V6"/>
                <path d="M20 12h-5v5h5"/>
                <path d="M16.5 14.5h.01"/>
            </svg>
            View Earnings
        </a>
    </header>

    <section class="cr-summary" aria-label="Remittance overview">
        <article class="cr-stat">
            <div class="cr-stat-top">
                <p class="cr-stat-label">Eligible COD Cash</p>
                <span class="cr-stat-icon" aria-hidden="true">₱</span>
            </div>

            <strong class="cr-stat-value">
                ₱{{ number_format($expectedTotal, 2) }}
            </strong>

            <p class="cr-stat-note">
                Available to include in a new batch
            </p>
        </article>

        <article class="cr-stat">
            <div class="cr-stat-top">
                <p class="cr-stat-label">Eligible Parcels</p>

                <span class="cr-stat-icon cr-stat-icon-blue">
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
                        <path d="M3 8v9l9 5 9-5V8"/>
                        <path d="M12 13v9M7.5 5.5l9 5"/>
                    </svg>
                </span>
            </div>

            <strong class="cr-stat-value">
                {{ $eligibleOrders->count() }}
            </strong>

            <p class="cr-stat-note">
                Delivered COD parcels ready for remittance
            </p>
        </article>

        <article class="cr-stat">
            <div class="cr-stat-top">
                <p class="cr-stat-label">Open Recent Batches</p>

                <span class="cr-stat-icon cr-stat-icon-green">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <rect x="5" y="4" width="14" height="17" rx="2"/>
                        <path d="M9 4V2h6v2M9 10h6M9 14h6M9 18h3"/>
                    </svg>
                </span>
            </div>

            <strong class="cr-stat-value">
                {{ $openRecentBatches }}
            </strong>

            <p class="cr-stat-note">
                Submitted or with a discrepancy in the recent list
            </p>
        </article>
    </section>

    <section class="cr-guidance" aria-label="Physical cash instructions">
        <div class="cr-cash-notice">
            <strong>Bring the full COD cash.</strong>
            Remittance is physical cash only. Do not deduct your Rider
            earnings; these are handled separately through your wallet.
        </div>

        <ol class="cr-steps">
            <li class="cr-step">
                <span class="cr-step-number" aria-hidden="true">01</span>
                <div>
                    <strong>Select parcels</strong>
                    <p>
                        Choose the delivered COD parcels to include in your batch.
                    </p>
                </div>
            </li>

            <li class="cr-step">
                <span class="cr-step-number" aria-hidden="true">02</span>
                <div>
                    <strong>Bring cash and QR</strong>
                    <p>
                        Present your batch QR when handing over cash at the hub.
                    </p>
                </div>
            </li>

            <li class="cr-step">
                <span class="cr-step-number" aria-hidden="true">03</span>
                <div>
                    <strong>Wait for verification</strong>
                    <p>
                        Logistics counts the cash and checks for any discrepancy.
                    </p>
                </div>
            </li>
        </ol>
    </section>

    <form
        id="remittanceBatchForm"
        method="POST"
        action="{{ route('courier.remittances.store') }}"
        class="cr-panel"
        data-has-facilities="{{ $facilities->isNotEmpty() ? 'true' : 'false' }}"
    >
        @csrf

        <div class="cr-panel-header">
            <div>
                <h3 class="cr-panel-title">Prepare Cash Batch</h3>
                <p class="cr-panel-description">
                    Select eligible parcels and choose your physical turnover hub.
                </p>
            </div>

            <span class="cr-badge cr-badge-gold">Cash only</span>
        </div>

        <div
            class="cr-table-scroll"
            role="region"
            aria-label="Eligible COD parcels"
            tabindex="0"
        >
            <table class="cr-table">
                <thead>
                    <tr>
                        <th scope="col">Select</th>
                        <th scope="col">Order</th>
                        <th scope="col">Buyer</th>
                        <th scope="col" class="cr-number">COD Collected</th>
                        <th scope="col">Delivered</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($eligibleOrders as $order)
                        <tr>
                            <td>
                                <label class="cr-check-label">
                                    <input
                                        type="checkbox"
                                        class="cr-checkbox"
                                        name="order_ids[]"
                                        value="{{ $order->id }}"
                                        data-cod-cents="{{ (int) round((float) $order->total * 100) }}"
                                        aria-label="Select order {{ $order->order_number }}"
                                        checked
                                    >
                                </label>
                            </td>

                            <td>
                                <span class="cr-order-number">
                                    {{ $order->order_number }}
                                </span>
                            </td>

                            <td>{{ $order->buyer_name ?: 'Buyer' }}</td>

                            <td class="cr-number">
                                <strong>
                                    ₱{{ number_format((float) $order->total, 2) }}
                                </strong>
                            </td>

                            <td>
                                {{ $order->delivered_at?->format('M d, Y · h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="cr-empty">
                                <strong>No eligible COD parcels</strong>
                                There is no COD cash waiting to be added to a new batch.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="cr-form-body">
            <aside class="cr-selection">
                <p class="cr-selection-label">Selected Cash to Hand Over</p>

                <div role="status" aria-live="polite" aria-atomic="true">
                    <strong
                        id="remittanceSelectedAmount"
                        class="cr-selection-amount"
                    >
                        ₱{{ number_format($expectedTotal, 2) }}
                    </strong>

                    <p
                        id="remittanceSelectedCount"
                        class="cr-selection-count"
                    >
                        {{ $eligibleOrders->count() }} parcel(s) selected
                    </p>
                </div>

                <p class="cr-selection-help">
                    Based on your selected parcels. SARI confirms the required
                    amount when preparing the batch. Rider earnings are not deducted.
                </p>

                <noscript>
                    <p class="cr-selection-help">
                        Enable JavaScript to update this preview as you change
                        selections. The batch amount is still calculated on submission.
                    </p>
                </noscript>
            </aside>

            <div class="cr-fields">
                <div class="cr-field">
                    <label
                        for="remittanceFacility"
                        class="cr-field-label"
                    >
                        Physical Turnover Hub
                    </label>

                    <select
                        id="remittanceFacility"
                        name="logistics_facility_id"
                        class="cr-control"
                        required
                    >
                        <option value="">Choose your turnover hub</option>

                        @foreach($facilities as $facility)
                            <option
                                value="{{ $facility->id }}"
                                @selected(old('logistics_facility_id') == $facility->id)
                            >
                                {{ $facility->name }}{{ $facility->municipality_name ? ' · '.$facility->municipality_name : '' }}
                            </option>
                        @endforeach
                    </select>

                    @if($facilities->isEmpty())
                        <p class="cr-field-error">
                            No active Logistics hub is configured for your provider.
                            Ask Logistics to configure one before remitting cash.
                        </p>
                    @endif
                </div>

                <div class="cr-field">
                    <label
                        for="remittanceRiderNote"
                        class="cr-field-label"
                    >
                        Rider Note
                        <span class="cr-optional">(optional)</span>
                    </label>

                    <input
                        id="remittanceRiderNote"
                        type="text"
                        name="rider_note"
                        maxlength="1000"
                        value="{{ old('rider_note') }}"
                        class="cr-control"
                        placeholder="Add a note about the physical turnover"
                    >
                </div>
            </div>
        </div>

        <div class="cr-form-footer">
            <p>
                Preparing a batch creates its QR. Cash turnover is confirmed
                separately by Logistics after the physical count.
            </p>

            <button
                id="remittancePrepareButton"
                type="submit"
                class="cr-button cr-button-primary"
                @disabled($eligibleOrders->isEmpty() || $facilities->isEmpty())
            >
                Prepare Cash Remittance
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </button>
        </div>
    </form>

    <section class="cr-panel" aria-labelledby="recentRemittancesTitle">
        <div class="cr-panel-header">
            <div>
                <h3 id="recentRemittancesTitle" class="cr-panel-title">
                    Recent Cash Batches
                </h3>

                <p class="cr-panel-description">
                    Present the QR for an open batch together with its physical cash.
                </p>
            </div>

            <span class="cr-badge">
                {{ $batches->count() }} batches
            </span>
        </div>

        @forelse($batches as $batch)
            @php
                $isOpen = in_array(
                    $batch->status,
                    ['submitted', 'discrepancy'],
                    true
                );

                $scanUrl = $isOpen
                    ? \Illuminate\Support\Facades\URL::temporarySignedRoute(
                        'logistics.remittances.show',
                        now()->addHours(24),
                        ['batch' => $batch->id],
                        false
                    )
                    : null;

                $statusClass = match ($batch->status) {
                    'verified' => 'cr-badge-green',
                    'discrepancy' => 'cr-badge-red',
                    'submitted' => 'cr-badge-gold',
                    default => '',
                };

                $hubName = $batch->hub_name_snapshot
                    ?: ($batch->facility?->name ?: 'Logistics Hub');
            @endphp

            <article class="cr-batch">
                <div class="cr-batch-main">
                    <div>
                        <div class="cr-batch-heading">
                            <h4 class="cr-batch-code">
                                {{ $batch->batch_code }}
                            </h4>
                        </div>

                        <p class="cr-batch-meta">
                            {{ $batch->items_count }} parcel(s) · Physical cash
                        </p>
                    </div>

                    <div class="cr-hub">
                        <span class="cr-detail-label">Turnover Hub</span>
                        <strong>{{ $hubName }}</strong>

                        @if($batch->hub_address_snapshot)
                            <p>{{ $batch->hub_address_snapshot }}</p>
                        @endif
                    </div>

                    <div class="cr-batch-amount">
                        <span class="cr-detail-label">Expected Cash</span>

                        <strong>
                            ₱{{ number_format((float) $batch->expected_amount, 2) }}
                        </strong>

                        <span class="cr-badge {{ $statusClass }}">
                            {{ str_replace('_', ' ', $batch->status) }}
                        </span>
                    </div>
                </div>

                @if($isOpen && $scanUrl)
                    <div class="cr-batch-footer">
                        <p>
                            @if($batch->status === 'discrepancy')
                                Resolve the cash discrepancy with Logistics before re-verification.
                            @else
                                Ready for physical turnover and cash verification.
                            @endif
                        </p>

                        <button
                            type="button"
                            class="cr-button cr-button-primary"
                            data-remittance-qr-toggle="{{ $batch->id }}"
                            aria-expanded="false"
                            aria-controls="remittanceQrPanel{{ $batch->id }}"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                                <path d="M14 14h3v3h-3zM18 18h3v3h-3zM18 14h3M14 18v3"/>
                            </svg>

                            <span data-remittance-qr-label>
                                Show Remittance QR
                            </span>
                        </button>
                    </div>

                    <div
                        id="remittanceQrPanel{{ $batch->id }}"
                        class="cr-qr-panel"
                        data-remittance-qr-panel="{{ $batch->id }}"
                        hidden
                    >
                        <div class="cr-qr-layout">
                            <div
                                id="remittanceQr{{ $batch->id }}"
                                class="cr-qr-code"
                                data-qr-url="{{ $scanUrl }}"
                                data-qr-rendered="false"
                                role="group"
                                aria-label="Remittance QR for {{ $batch->batch_code }}"
                            ></div>

                            <div>
                                <p class="cr-eyebrow">Show This QR at the Hub</p>

                                <h4 class="cr-qr-title">
                                    {{ $batch->batch_code }}
                                </h4>

                                <dl class="cr-qr-details">
                                    <div>
                                        <dt class="cr-detail-label">
                                            Physical Cash
                                        </dt>
                                        <dd>
                                            ₱{{ number_format((float) $batch->expected_amount, 2) }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="cr-detail-label">Parcels</dt>
                                        <dd>{{ $batch->items_count }}</dd>
                                    </div>
                                </dl>

                                <p class="cr-qr-hub">{{ $hubName }}</p>

                                @if($batch->hub_address_snapshot)
                                    <p class="cr-qr-help">
                                        {{ $batch->hub_address_snapshot }}
                                    </p>
                                @endif

                                <p class="cr-qr-help">
                                    Present this QR with the full physical COD cash.
                                    Logistics scans the batch and counts the money
                                    before verification.
                                </p>

                                @if($batch->status === 'discrepancy')
                                    <p class="cr-discrepancy">
                                        Previous cash count had a discrepancy of
                                        <strong>
                                            ₱{{ number_format(abs((float) $batch->difference_amount), 2) }}.
                                        </strong>
                                        Resolve the exact cash with Logistics before
                                        re-verification.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="cr-batch-footer">
                        <p>
                            This remittance batch is closed. No QR is required.
                        </p>
                    </div>
                @endif
            </article>
        @empty
            <div class="cr-empty">
                <strong>No cash batches yet</strong>
                Prepared remittance batches will appear here.
            </div>
        @endforelse
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('riderRemittances');

    if (!root) return;

    /*
     * Selection preview only.
     * No amount is submitted; the existing backend calculates the batch total.
     */
    const form = root.querySelector('#remittanceBatchForm');
    const amountOutput = root.querySelector('#remittanceSelectedAmount');
    const countOutput = root.querySelector('#remittanceSelectedCount');
    const prepareButton = root.querySelector('#remittancePrepareButton');

    const moneyFormatter = new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const updateSelection = () => {
        if (!form || !amountOutput || !countOutput || !prepareButton) return;

        const selected = Array.from(
            form.querySelectorAll('input[name="order_ids[]"]:checked')
        );

        const totalCents = selected.reduce((total, checkbox) => {
            const cents = Number(checkbox.dataset.codCents);

            return total + (Number.isFinite(cents) ? cents : 0);
        }, 0);

        amountOutput.textContent =
            `₱${moneyFormatter.format(totalCents / 100)}`;

        countOutput.textContent =
            `${selected.length} ${selected.length === 1 ? 'parcel' : 'parcels'} selected`;

        prepareButton.disabled =
            selected.length === 0 ||
            form.dataset.hasFacilities !== 'true';
    };

    if (form) {
        form.addEventListener('change', (event) => {
            if (event.target.matches('input[name="order_ids[]"]')) {
                updateSelection();
            }
        });

        form.addEventListener('reset', () => {
            window.setTimeout(updateSelection, 0);
        });

        window.addEventListener('pageshow', updateSelection);
        updateSelection();
    }

    /*
     * Existing signed remittance QR flow.
     */
    const showQrError = (node) => {
        const message = document.createElement('p');

        message.className = 'cr-qr-error';
        message.setAttribute('role', 'alert');
        message.textContent =
            'The QR could not load. Check your connection and reload this page.';

        node.replaceChildren(message);
        node.dataset.qrRendered = 'false';
    };

    const renderQr = (node) => {
        if (!node) return false;
        if (node.dataset.qrRendered === 'true') return true;

        if (typeof QRCode === 'undefined') {
            showQrError(node);
            return false;
        }

        try {
            node.replaceChildren();

            new QRCode(node, {
                text: node.dataset.qrUrl,
                width: 156,
                height: 156,
                colorDark: '#151515',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });

            const qrImage = node.querySelector('img');

            if (qrImage) {
                qrImage.alt = node.getAttribute('aria-label') || 'Remittance QR';
            }

            node.dataset.qrRendered = 'true';
            return true;
        } catch {
            showQrError(node);
            return false;
        }
    };

    root.querySelectorAll('[data-remittance-qr-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = document.getElementById(
                button.getAttribute('aria-controls')
            );

            const label = button.querySelector('[data-remittance-qr-label]');

            if (!panel || !root.contains(panel)) return;

            const opening = panel.hidden;

            panel.hidden = !opening;
            button.setAttribute('aria-expanded', String(opening));

            if (label) {
                label.textContent = opening
                    ? 'Hide Remittance QR'
                    : 'Show Remittance QR';
            }

            if (opening) {
                renderQr(panel.querySelector('[data-qr-url]'));

                panel.scrollIntoView({
                    behavior: window.matchMedia(
                        '(prefers-reduced-motion: reduce)'
                    ).matches ? 'auto' : 'smooth',
                    block: 'nearest',
                });
            }
        });
    });
});
</script>
@endpush