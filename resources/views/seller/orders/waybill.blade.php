<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Waybill {{ $order->order_number }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --black: #211d18;
            --text: #3c3630;
            --muted: #8c8379;
            --border: #ddd5ca;
            --light-border: #ebe5dd;
            --surface: #fbfaf7;
            --gold: #d39418;
            --gold-dark: #9c6b14;
            --gold-soft: #fff8e9;
            --green: #4f7d63;
            --green-soft: #f1f8f4;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #f5f3ef;
            color: var(--black);
            font-family: "Poppins", Arial, sans-serif;
            font-weight: 400;
        }

        .waybill-toolbar {
            width: 100%;
            max-width: 900px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
        }

        .print-button {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            border: 0;
            border-radius: 10px;

            padding: 0 18px;

            background: var(--gold);
            color: #fff;

            font-family: inherit;
            font-size: 11px;
            font-weight: 500;

            cursor: pointer;
            transition:
                background .18s ease,
                transform .18s ease;
        }

        .print-button:hover {
            background: #c18410;
            transform: translateY(-1px);
        }

        .waybill {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;

            overflow: hidden;

            border: 1px solid #d8d0c5;
            border-radius: 4px;

            background: white;

            box-shadow: 0 20px 55px rgba(38, 29, 19, .08);
        }

        .waybill-inner {
            padding: 34px 38px;
        }

        /* =========================================================
           HEADER
        ========================================================== */

        .waybill-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;

            padding-bottom: 28px;

            border-bottom: 2px solid var(--gold);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .brand-main-logo {
            width: 74px;
            height: 74px;

            object-fit: contain;

            border-radius: 16px;
        }

       .brand-wordmark {
    max-width: 210px;
    height: 58px;

    object-fit: contain;
    object-position: left center;

   
    filter: brightness(0) saturate(100%);
    opacity: 1;
}

        .waybill-heading {
            text-align: right;
        }

        .waybill-heading h1 {
            margin: 0;

            font-size: 22px;
            line-height: 1.2;

            font-weight: 500;
            letter-spacing: .12em;

            text-transform: uppercase;
        }

        .waybill-heading p {
            margin: 7px 0 0;

            font-size: 9px;
            font-weight: 400;

            color: var(--muted);

            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .order-number-card {
            margin-top: 18px;

            min-width: 285px;

            border: 1px solid var(--border);
            border-radius: 12px;

            padding: 14px 16px;

            text-align: left;
        }

        .label {
            margin: 0;

            color: var(--muted);

            font-size: 8px;
            font-weight: 500;

            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .order-number {
            margin: 6px 0 0;

            color: var(--black);

            font-size: 17px;
            line-height: 1.3;

            font-weight: 600;
        }

        .order-date {
            margin: 6px 0 0;

            color: var(--muted);

            font-size: 8px;
            font-weight: 400;
        }

        /* =========================================================
           SHIPPING
        ========================================================== */

        .shipping-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;

            border-bottom: 1px solid var(--border);
        }

        .shipping-section {
            padding: 28px 26px 28px 0;
        }

        .shipping-section + .shipping-section {
            padding-left: 30px;

            border-left: 1px solid var(--light-border);
        }

        .section-heading {
            display: flex;
            align-items: center;
            gap: 10px;

            margin-bottom: 16px;
        }

        .section-icon {
            width: 31px;
            height: 31px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #ead9b5;
            border-radius: 9px;

            background: var(--gold-soft);
            color: var(--gold-dark);
        }

        .section-icon svg {
            width: 15px;
            height: 15px;
        }

        .section-heading h2 {
            margin: 0;

            font-size: 12px;
            font-weight: 600;

            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .person-name {
            margin: 0;

            font-size: 11px;
            font-weight: 600;
        }

        .person-detail {
            margin: 7px 0 0;

            color: #625a52;

            font-size: 9px;
            line-height: 1.7;

            font-weight: 400;
        }

        /* =========================================================
           ITEMS
        ========================================================== */

        .items-section {
            padding: 26px 0;
        }

        .items-table {
            width: 100%;

            margin-top: 14px;

            border-collapse: separate;
            border-spacing: 0;

            overflow: hidden;

            border: 1px solid var(--light-border);
            border-radius: 11px;
        }

        .items-table thead {
            background: #26221e;
            color: white;
        }

        .items-table th {
            padding: 11px 12px;

            text-align: left;

            font-size: 7.5px;
            font-weight: 500;

            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 12px;

            border-bottom: 1px solid var(--light-border);

            color: #4b443d;

            font-size: 8.5px;
            font-weight: 400;

            vertical-align: top;
        }

        .items-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .items-table th:nth-child(4),
        .items-table th:nth-child(5),
        .items-table td:nth-child(4),
        .items-table td:nth-child(5) {
            text-align: right;
        }

        .item-name {
            color: var(--black);
            font-weight: 500;
        }

        .item-variant {
            display: block;

            margin-top: 3px;

            color: var(--muted);

            font-size: 7.5px;
        }

        /* =========================================================
           PAYMENT + TOTAL
        ========================================================== */

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;

            gap: 18px;

            padding-top: 26px;

            border-top: 1px solid var(--border);
        }

        .summary-card {
            border: 1px solid var(--light-border);
            border-radius: 13px;

            padding: 17px;

            background: #fff;
        }

        .payment-method {
            margin-top: 15px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;
        }

        .payment-method span:first-child {
            color: #71685f;

            font-size: 8.5px;
        }

        .cod-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;

            border: 1px solid #ead19a;
            border-radius: 999px;

            padding: 6px 10px;

            background: var(--gold-soft);
            color: var(--gold-dark);

            font-size: 8px;
            font-weight: 500;
        }

        .payment-note {
            margin: 12px 0 0;

            color: var(--muted);

            font-size: 7.5px;
            line-height: 1.6;
        }

        .amount-line {
            display: flex;
            justify-content: space-between;
            gap: 20px;

            margin-bottom: 9px;

            color: #746b62;

            font-size: 8.5px;
        }

        .amount-line strong {
            color: #4b443d;
            font-weight: 500;
        }

        .total-line {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            gap: 20px;

            margin-top: 15px;
            padding-top: 14px;

            border-top: 1px solid #dec38b;
        }

        .total-label {
            font-size: 10px;
            font-weight: 600;

            text-transform: uppercase;
        }

        .total-amount {
            color: var(--black);

            font-size: 24px;
            line-height: 1;

            font-weight: 600;
        }

        .collect-box {
            margin-top: 13px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            border: 1px solid #d2e4d8;
            border-radius: 10px;

            padding: 10px 12px;

            background: var(--green-soft);
            color: var(--green);
        }

        .collect-box span {
            font-size: 8px;
            font-weight: 500;
        }

        .collect-box strong {
            font-size: 11px;
            font-weight: 600;
        }

        /* =========================================================
           FOOTER
        ========================================================== */

        .waybill-footer {
            display: flex;
            justify-content: space-between;
            gap: 25px;

            margin-top: 30px;
            padding-top: 20px;

            border-top: 1px solid var(--border);
        }

        .footer-note {
            max-width: 480px;

            color: var(--muted);

            font-size: 7.5px;
            line-height: 1.7;
        }

        .footer-brand {
            text-align: right;

            color: var(--gold-dark);

            font-size: 8px;
            font-weight: 500;

            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .bottom-bar {
            height: 8px;

            background: #28231e;

            border-top: 2px solid var(--gold);
        }

        /* =========================================================
           PRINT
        ========================================================== */

        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            body {
                padding: 0;

                background: white;

                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .waybill {
                max-width: none;

                border-color: #aaa;
                box-shadow: none;
            }

            .waybill-inner {
                padding: 24px;
            }
        }

        @media (max-width: 700px) {
            body {
                padding: 15px;
            }

            .waybill-inner {
                padding: 22px;
            }

            .waybill-header {
                flex-direction: column;
            }

            .waybill-heading {
                width: 100%;
                text-align: left;
            }

            .order-number-card {
                min-width: 0;
            }

            .shipping-grid,
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .shipping-section + .shipping-section {
                padding-left: 0;

                border-left: 0;
                border-top: 1px solid var(--light-border);
            }

            .brand-main-logo {
                width: 58px;
                height: 58px;
            }

            .brand-wordmark {
                max-width: 160px;
                height: 48px;
            }
        }
    </style>
</head>

<body>

    {{-- ============================================================
        PRINT BUTTON
    ============================================================= --}}
    <div class="waybill-toolbar no-print">
        <button
            type="button"
            onclick="window.print()"
            class="print-button"
        >
            <svg
                width="15"
                height="15"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M6 9V3h12v6"></path>
                <path d="M6 18H4V9h16v9h-2"></path>
                <path d="M7 14h10v7H7z"></path>
            </svg>

            Print Waybill
        </button>
    </div>


    {{-- ============================================================
        WAYBILL
    ============================================================= --}}
    <main class="waybill">

        <div class="waybill-inner">

            {{-- ====================================================
                HEADER
            ===================================================== --}}
            <header class="waybill-header">

                <div class="brand">

                    {{-- MAIN IMAGE LOGO --}}
                    <img
                        src="{{ asset('images/sari-main-logo.png') }}"
                        alt="SARI Main Logo"
                        class="brand-main-logo"
                    >

                    {{-- SARI WORD LOGO --}}
                    <img
                        src="{{ asset('images/sari-logo.png') }}"
                        alt="SARI"
                        class="brand-wordmark"
                    >

                </div>


                <div class="waybill-heading">

                    <h1>Delivery Waybill</h1>

                    <p>SARI Marketplace</p>

                    <div class="order-number-card">

                        <p class="label">
                            Order Number
                        </p>

                        <p class="order-number">
                            {{ $order->order_number }}
                        </p>

                        <p class="order-date">
                            Order Date:
                            {{ $order->created_at?->format('M d, Y · h:i A') }}
                        </p>

                    </div>

                </div>

            </header>


            {{-- ====================================================
                SHIPPING INFORMATION
            ===================================================== --}}
            <section class="shipping-grid">

                {{-- SELLER --}}
                <div class="shipping-section">

                    <div class="section-heading">

                        <span class="section-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="8" r="3"></circle>
                                <path d="M5 20a7 7 0 0 1 14 0"></path>
                            </svg>
                        </span>

                        <h2>Pickup / Seller</h2>

                    </div>


                    <p class="person-name">
                        {{ $order->pickup_name }}
                    </p>

                    <p class="person-detail">
                        {{ $order->pickup_address }}
                    </p>

                </div>


                {{-- BUYER --}}
                <div class="shipping-section">

                    <div class="section-heading">

                        <span class="section-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M12 21s7-5.3 7-12a7 7 0 1 0-14 0c0 6.7 7 12 7 12Z"></path>
                                <circle cx="12" cy="9" r="2"></circle>
                            </svg>
                        </span>

                        <h2>Deliver To</h2>

                    </div>


                    <p class="person-name">
                        {{ $order->buyer_name }}
                    </p>

                    @if ($order->buyer_phone)
                        <p class="person-detail">
                            {{ $order->buyer_phone }}
                        </p>
                    @endif

                    <p class="person-detail">
                        {{ $order->buyer_address }}
                    </p>

                </div>

            </section>


            {{-- ====================================================
                ITEMS
            ===================================================== --}}
            <section class="items-section">

                <div class="section-heading">

                    <span class="section-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 7 12 3l8 4-8 4-8-4Z"></path>
                            <path d="m4 7 8 4v10l-8-4V7Z"></path>
                            <path d="m20 7-8 4v10l8-4V7Z"></path>
                        </svg>
                    </span>

                    <h2>Order Items</h2>

                </div>


                <table class="items-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Details</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse (($order->items ?? []) as $index => $item)

                            @php
                                $quantity = max(
                                    1,
                                    (int) ($item['qty'] ?? 1)
                                );

                                $unitPrice = (float) (
                                    $item['price'] ?? 0
                                );

                                $lineTotal = (float) (
                                    $item['line_total']
                                    ?? ($unitPrice * $quantity)
                                );
                            @endphp

                            <tr>

                                <td>
                                    {{ $index + 1 }}
                                </td>


                                <td>

                                    <span class="item-name">
                                        {{ $item['name'] ?? 'Product' }}
                                    </span>

                                    @if (!empty($item['variant_label']))

                                        <span class="item-variant">
                                            {{ $item['variant_label'] }}
                                        </span>

                                    @endif

                                </td>


                                <td>
                                    {{ $quantity }}
                                </td>


                                <td>
                                    ₱{{ number_format($unitPrice, 2) }}
                                </td>


                                <td>
                                    ₱{{ number_format($lineTotal, 2) }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5">
                                    No item details available.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </section>


            {{-- ====================================================
                PAYMENT / TOTAL
            ===================================================== --}}
            <section class="summary-grid">

                {{-- PAYMENT --}}
                <div class="summary-card">

                    <div class="section-heading">

                        <span class="section-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                                <path d="M3 10h18"></path>
                            </svg>
                        </span>

                        <h2>Payment Details</h2>

                    </div>


                    <div class="payment-method">

                        <span>Payment Method</span>

                        <span class="cod-badge">
                            Cash on Delivery
                        </span>

                    </div>


                    <div class="payment-method">

                        <span>Payment Status</span>

                        <strong>
                            {{ strtoupper($order->payment_status ?? 'PENDING') }}
                        </strong>

                    </div>


                    <p class="payment-note">
                        Payment will be collected from the buyer upon successful delivery.
                    </p>

                </div>


                {{-- TOTAL --}}
                <div class="summary-card">

                    <div class="amount-line">
                        <span>Subtotal</span>

                        <strong>
                            ₱{{ number_format((float) $order->subtotal, 2) }}
                        </strong>
                    </div>


                    <div class="amount-line">
                        <span>Delivery Fee</span>

                        <strong>
                            ₱{{ number_format((float) $order->delivery_fee, 2) }}
                        </strong>
                    </div>


                    <div class="total-line">

                        <span class="total-label">
                            Total Amount
                        </span>

                        <span class="total-amount">
                            ₱{{ number_format((float) $order->total, 2) }}
                        </span>

                    </div>


                    <div class="collect-box">

                        <span>
                            COD Amount to Collect
                        </span>

                        <strong>
                            ₱{{ number_format((float) $order->total, 2) }}
                        </strong>

                    </div>

                </div>

            </section>


            {{-- ====================================================
                FOOTER
            ===================================================== --}}
            <footer class="waybill-footer">

                <p class="footer-note">
                    Please verify the package and recipient information before completing
                    the delivery. This waybill is generated by the SARI Marketplace
                    order management system.
                </p>


                <div class="footer-brand">

                    SARI Marketplace

                </div>

            </footer>

        </div>


        <div class="bottom-bar"></div>

    </main>

</body>
</html>