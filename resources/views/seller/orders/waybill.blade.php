<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waybill {{ $order->order_number }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    @php
        $items = collect($order->items ?? []);
        $itemCount = (int) $items->sum(fn ($item) => max(1, (int) ($item['qty'] ?? 1)));
        $shortBuyerName = trim((string) ($order->buyer_name ?? 'Buyer'));
        $serviceCode = 'COD';
        $trackingNumber = preg_replace('/\s+/', '', (string) $order->order_number);
        $safePickupAddress = trim((string) ($order->pickup_address ?? '-'));
        $safeBuyerAddress = trim((string) ($order->buyer_address ?? '-'));
        $safeBuyerPhone = trim((string) ($order->buyer_phone ?? ''));
        $safePickupName = trim((string) ($order->pickup_name ?? 'Seller'));
        $safePickupPhone = trim((string) ($order->pickup_phone ?? ''));
        $codAmount = (float) ($order->total ?? 0);
        $subtotal = (float) ($order->subtotal ?? 0);
        $deliveryFee = (float) ($order->delivery_fee ?? 0);
    @endphp

    <style>
        * { box-sizing: border-box; }

        :root {
            --ink: #151515;
            --muted: #5f6368;
            --line: #1e1e1e;
            --soft-line: #d8d8d8;
            --panel: #ffffff;
            --canvas: #f3f4f6;
            --accent: #111827;
            --accent-soft: #f3f4f6;
            --success: #0f9d58;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: var(--canvas);
            color: var(--ink);
            font-family: "Inter", Arial, sans-serif;
        }

        body {
            padding: 20px;
        }

        .no-print { display: block; }

        .waybill-toolbar {
            width: 100%;
            max-width: 104mm;
            margin: 0 auto 14px;
            display: flex;
            justify-content: flex-end;
        }

        .print-button {
            border: 0;
            background: #111827;
            color: #fff;
            border-radius: 10px;
            height: 40px;
            padding: 0 18px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 10px 24px rgba(17,24,39,.18);
        }

        .waybill {
            width: 100mm;
            min-height: 150mm;
            margin: 0 auto;
            background: var(--panel);
            border: 1px solid var(--line);
            box-shadow: 0 16px 42px rgba(0,0,0,.08);
            overflow: hidden;
        }

        .label-shell {
            display: grid;
            grid-template-rows: auto auto auto auto auto 1fr auto;
            min-height: 150mm;
        }

        .blk {
            border-bottom: 1px solid var(--line);
        }

        .pad-sm { padding: 6px 7px; }
        .pad-md { padding: 8px 9px; }
        .pad-lg { padding: 10px 11px; }

        .topbar {
            display: grid;
            grid-template-columns: 1fr auto auto;
            align-items: stretch;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 24mm;
        }

        .brand-logo-box {
            width: 18mm;
            height: 18mm;
            border: 1px solid var(--soft-line);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
            flex: 0 0 auto;
        }

        .brand-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-copy {
            min-width: 0;
        }

        .brand-title {
            margin: 0;
            font-size: 16px;
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .brand-subtitle {
            margin: 4px 0 0;
            font-size: 8.5px;
            color: var(--muted);
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .service-pill,
        .zone-pill {
            min-width: 18mm;
            border-left: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px 6px;
            text-align: center;
        }

        .service-pill strong,
        .zone-pill strong {
            font-size: 17px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: .04em;
        }

        .service-pill span,
        .zone-pill span {
            display: block;
            font-size: 8px;
            color: var(--muted);
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .barcode-section {
            text-align: center;
            padding: 7px 8px 5px;
        }

        .barcode-label {
            margin: 0 0 4px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        #sariTrackingBarcode {
            width: 100%;
            max-width: 86mm;
            height: 20mm;
        }

        .tracking-human {
            margin-top: 4px;
            font-size: 14px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: .22em;
            text-transform: uppercase;
            word-break: break-word;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 26mm;
            min-height: 24mm;
        }

        .hero-left {
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
        }

        .ship-to-title {
            margin: 0;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .ship-to-name {
            margin: 0;
            font-size: 15px;
            line-height: 1.1;
            font-weight: 800;
        }

        .ship-to-address {
            margin: 0;
            font-size: 9px;
            line-height: 1.35;
            font-weight: 500;
        }

        .dest-code {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        .dest-code .big {
            font-size: 26px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: .06em;
        }

        .dest-code .small {
            margin-top: 4px;
            font-size: 8px;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
        }

        .party-grid {
            display: grid;
            grid-template-columns: 1fr 30mm;
        }

        .party-cells {
            border-right: 1px solid var(--line);
        }

        .party-box + .party-box {
            border-top: 1px solid var(--line);
        }

        .box-heading {
            margin: 0 0 4px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .box-name {
            margin: 0 0 2px;
            font-size: 11px;
            line-height: 1.2;
            font-weight: 800;
        }

        .box-meta {
            margin: 0;
            font-size: 8.2px;
            line-height: 1.35;
        }

        .qr-side {
            display: grid;
            grid-template-rows: auto 1fr auto;
            min-height: 100%;
        }

        .qr-side-top {
            text-align: center;
            border-bottom: 1px solid var(--line);
        }

        .qr-side-top .value {
            font-size: 22px;
            line-height: 1;
            font-weight: 800;
        }

        .qr-side-top .label {
            margin-top: 4px;
            font-size: 8px;
            color: var(--muted);
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .qr-box-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
        }

        #sariParcelQr {
            width: 24mm;
            height: 24mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #sariParcelQr img,
        #sariParcelQr canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .qr-side-bottom {
            border-top: 1px solid var(--line);
            text-align: center;
        }

        .qr-side-bottom .label {
            font-size: 7.2px;
            line-height: 1.3;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .spec-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .spec-left {
            border-right: 1px solid var(--line);
        }

        .spec-table {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 4px 7px;
            align-items: start;
        }

        .spec-label,
        .spec-value {
            font-size: 9px;
            line-height: 1.3;
        }

        .spec-label {
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .spec-value {
            font-weight: 600;
        }

        .cod-card {
            display: flex;
            height: 100%;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: #fafafa;
        }

        .cod-card .label {
            font-size: 8px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .cod-card .amount {
            margin-top: 5px;
            font-size: 16px;
            line-height: 1.15;
            font-weight: 800;
        }

        .items-block {
            display: grid;
            grid-template-columns: 1fr 26mm;
            border-bottom: 1px solid var(--line);
        }

        .items-main {
            border-right: 1px solid var(--line);
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-table thead th {
            font-size: 7.5px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            text-align: left;
            padding: 0 0 5px;
            font-weight: 800;
            border-bottom: 1px solid var(--soft-line);
        }

        .mini-table tbody td {
            padding: 5px 0 0;
            font-size: 8.5px;
            vertical-align: top;
            line-height: 1.3;
        }

        .mini-table tbody tr:first-child td {
            padding-top: 6px;
        }

        .item-main-name {
            display: block;
            font-weight: 700;
        }

        .item-main-variant {
            display: block;
            color: var(--muted);
            font-size: 7.6px;
            margin-top: 2px;
        }

        .items-side {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            gap: 5px;
        }

        .items-side .big-number {
            font-size: 24px;
            line-height: 1;
            font-weight: 800;
        }

        .items-side .helper {
            font-size: 7.5px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 18mm 18mm 18mm;
            min-height: 14mm;
        }

        .remarks-cell {
            border-right: 1px solid var(--line);
        }

        .remarks-title {
            margin: 0 0 4px;
            font-size: 8px;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .remarks-copy {
            margin: 0;
            font-size: 7.8px;
            line-height: 1.3;
            color: var(--ink);
        }

        .step-cell {
            border-left: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            line-height: 1;
            font-weight: 800;
        }

        .status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 6px 9px;
            background: #111827;
            color: white;
        }

        .status-left,
        .status-right {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .status-kicker {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 700;
            color: rgba(255,255,255,.72);
        }

        .status-value {
            font-size: 9px;
            font-weight: 700;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 22px;
            padding: 0 9px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .qr-status-inline {
            margin: 0;
            font-size: 7px;
            line-height: 1.25;
            color: var(--muted);
            text-align: center;
        }

        @media print {
            @page {
                size: 100mm 150mm;
                margin: 0;
            }

            html, body {
                width: 100mm;
                height: 150mm;
                background: #fff;
            }

            body { padding: 0; }
            .no-print { display: none !important; }
            .waybill {
                box-shadow: none;
                margin: 0;
                width: 100mm;
                min-height: 150mm;
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="waybill-toolbar no-print">
        <button type="button" onclick="window.print()" class="print-button">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M6 9V3h12v6"></path>
                <path d="M6 18H4V9h16v9h-2"></path>
                <path d="M7 14h10v7H7z"></path>
            </svg>
            Print Waybill
        </button>
    </div>

    <main class="waybill">
        <div class="label-shell">
            <section class="topbar blk">
                <div class="brand-wrap pad-md">
                    <div class="brand-logo-box">
                        <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI logo">
                    </div>
                    <div class="brand-copy">
                        <p class="brand-title">SARI</p>
                        <p class="brand-subtitle">Marketplace delivery label</p>
                    </div>
                </div>
                <div class="service-pill">
                    <div>
                        <strong>{{ $serviceCode }}</strong>
                        <span>Payment</span>
                    </div>
                </div>
                <div class="zone-pill">
                    <div>
                        <strong>EZ</strong>
                        <span>Lane</span>
                    </div>
                </div>
            </section>

            <section class="barcode-section blk">
                <p class="barcode-label">Tracking Number</p>
                <svg id="sariTrackingBarcode"></svg>
                <div class="tracking-human">{{ $trackingNumber }}</div>
            </section>

            <section class="hero-grid blk">
                <div class="hero-left pad-lg">
                    <p class="ship-to-title">Ship To</p>
                    <p class="ship-to-name">{{ $shortBuyerName }}</p>
                    @if ($safeBuyerPhone)
                        <p class="ship-to-address">{{ $safeBuyerPhone }}</p>
                    @endif
                    <p class="ship-to-address">{{ $safeBuyerAddress }}</p>
                </div>
                <div class="dest-code pad-sm">
                    <div class="big">{{ str_pad((string) $itemCount, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="small">Items</div>
                </div>
            </section>

            <section class="party-grid blk">
                <div class="party-cells">
                    <div class="party-box pad-md">
                        <p class="box-heading">Sender</p>
                        <p class="box-name">{{ $safePickupName }}</p>
                        @if ($safePickupPhone)
                            <p class="box-meta">{{ $safePickupPhone }}</p>
                        @endif
                        <p class="box-meta">{{ $safePickupAddress }}</p>
                    </div>
                    <div class="party-box pad-md">
                        <p class="box-heading">Receiver</p>
                        <p class="box-name">{{ $shortBuyerName }}</p>
                        @if ($safeBuyerPhone)
                            <p class="box-meta">{{ $safeBuyerPhone }}</p>
                        @endif
                        <p class="box-meta">{{ $safeBuyerAddress }}</p>
                    </div>
                </div>

                <div class="qr-side">
                    <div class="qr-side-top pad-sm">
                        <div class="value">{{ str_pad((string) $order->id, 3, '0', STR_PAD_LEFT) }}</div>
                        <div class="label">Route Code</div>
                    </div>

                    <div class="qr-box-wrap">
                        <div id="sariParcelQr" data-payload="{{ $parcelQrPayload }}" aria-label="Parcel handoff QR code"></div>
                    </div>

                    <div class="qr-side-bottom pad-sm">
                        <div class="label">Secure parcel handoff QR</div>
                        <p id="sariParcelQrStatus" class="qr-status-inline">Preparing secure QR…</p>
                    </div>
                </div>
            </section>

            <section class="spec-grid blk">
                <div class="spec-left pad-md">
                    <div class="spec-table">
                        <div class="spec-label">Order</div>
                        <div class="spec-value">{{ $order->order_number }}</div>

                        <div class="spec-label">Date</div>
                        <div class="spec-value">{{ $order->created_at?->format('M d, Y · h:i A') }}</div>

                        <div class="spec-label">Subtotal</div>
                        <div class="spec-value">₱{{ number_format($subtotal, 2) }}</div>

                        <div class="spec-label">Shipping</div>
                        <div class="spec-value">₱{{ number_format($deliveryFee, 2) }}</div>

                        <div class="spec-label">Payment</div>
                        <div class="spec-value">{{ strtoupper((string) ($order->payment_status ?? 'PENDING')) }}</div>
                    </div>
                </div>
                <div class="cod-card pad-md">
                    <div class="label">COD Collect</div>
                    <div class="amount">₱{{ number_format($codAmount, 2) }}</div>
                </div>
            </section>

            <section class="items-block">
                <div class="items-main pad-md">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th style="width: 56%;">Item</th>
                                <th style="width: 12%;">Qty</th>
                                <th style="width: 15%;">Price</th>
                                <th style="width: 17%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($items as $item)
                            @php
                                $quantity = max(1, (int) ($item['qty'] ?? 1));
                                $unitPrice = (float) ($item['price'] ?? 0);
                                $lineTotal = (float) ($item['line_total'] ?? ($unitPrice * $quantity));
                            @endphp
                            <tr>
                                <td>
                                    <span class="item-main-name">{{ $item['name'] ?? 'Product' }}</span>
                                    @if (!empty($item['variant_label']))
                                        <span class="item-main-variant">{{ $item['variant_label'] }}</span>
                                    @endif
                                </td>
                                <td>{{ $quantity }}</td>
                                <td>₱{{ number_format($unitPrice, 0) }}</td>
                                <td>₱{{ number_format($lineTotal, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No item details available.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="items-side pad-sm">
                    <div class="big-number">{{ str_pad((string) $items->count(), 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="helper">SKU lines</div>
                </div>
            </section>

            <section class="bottom-grid blk">
                <div class="remarks-cell pad-md">
                    <p class="remarks-title">Instructions</p>
                    <p class="remarks-copy">
                        Keep this label visible. Scan the QR at authorized SARI handoff checkpoints only.
                        Verify package and recipient details before delivery.
                    </p>
                </div>
                <div class="step-cell">1</div>
                <div class="step-cell">2</div>
                <div class="step-cell">3</div>
            </section>

            <section class="status-bar">
                <div class="status-left">
                    <span class="status-kicker">Current Status</span>
                    <span class="status-value">{{ strtoupper(str_replace('_', ' ', (string) ($order->status ?? 'ready_for_pickup'))) }}</span>
                </div>
                <div class="status-chip">SARI Marketplace</div>
                <div class="status-right" style="text-align:right;">
                    <span class="status-kicker">Label Type</span>
                    <span class="status-value">4 × 6 Waybill</span>
                </div>
            </section>
        </div>
    </main>

    <script>
    (() => {
        const qrBox = document.getElementById('sariParcelQr');
        const qrStatus = document.getElementById('sariParcelQrStatus');
        const barcodeNode = document.getElementById('sariTrackingBarcode');
        const trackingValue = @json($trackingNumber);

        const renderQr = () => {
            if (!qrBox || !qrStatus) return;
            if (typeof QRCode === 'undefined') {
                qrStatus.textContent = 'QR unavailable. Reload before printing.';
                return;
            }

            qrBox.innerHTML = '';

            new QRCode(qrBox, {
                text: qrBox.dataset.payload,
                width: 112,
                height: 112,
                colorDark: '#111111',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });

            qrStatus.textContent = 'Secure parcel QR ready for scanning';
        };

        const renderBarcode = () => {
            if (!barcodeNode || typeof JsBarcode === 'undefined') {
                return;
            }

            JsBarcode(barcodeNode, String(trackingValue || ''), {
                format: 'CODE128',
                lineColor: '#111111',
                width: 1.65,
                height: 56,
                displayValue: false,
                margin: 0,
                background: '#ffffff',
            });
        };

        const run = () => {
            renderQr();
            renderBarcode();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', run, { once: true });
        } else {
            run();
        }
    })();
    </script>
</body>
</html>
