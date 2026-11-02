{{-- Pre-rendered per order so View Order opens without an API request. --}}
@php
    $progress = $progressValue($order->status);
    $workflowSteps = [
        ['Order placed', 10],
        ['Preparing', 25],
        ['Ready for pickup', 40],
        ['In transit', 80],
        ['Delivered', 100],
    ];
    $nextActionLabel = match ($order->status) {
        'new' => 'Prepare this order',
        'preparing' => 'Mark ready for pickup',
        'ready_for_pickup' => 'Waiting for courier',
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'Courier pickup in progress',
        'in_transit', 'arrived_buyer' => 'Delivery in progress',
        'delivered' => 'Order completed',
        'cancelled' => 'Order cancelled',
        default => $order->statusLabel(),
    };
@endphp
<template id="sellerOrderTemplate-{{ $order->id }}">
    <div class="sari-order-dialog" role="dialog" aria-modal="true" aria-labelledby="sariOrderDialogTitle-{{ $order->id }}" aria-describedby="sariOrderDialogDescription-{{ $order->id }}">
        <header class="sari-order-dialog__head">
            <div class="sari-order-dialog__head-copy">
                <p class="sari-order-dialog__eyebrow">ORDER MANAGEMENT</p>
                <h2 id="sariOrderDialogTitle-{{ $order->id }}" class="sari-order-dialog__title">Order details</h2>
                <p id="sariOrderDialogDescription-{{ $order->id }}" class="sari-order-dialog__intro">Review the buyer, products, payment, and fulfillment status.</p>
            </div>
            <button class="sari-order-dialog__close" type="button" data-close-order-modal aria-label="Close order details">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </header>

        <div class="sari-order-dialog__scroll">
            <section class="sari-order-dialog__section" aria-labelledby="sari-order-primary-{{ $order->id }}">
                <div class="sari-order-dialog__section-head">
                    <h3 id="sari-order-primary-{{ $order->id }}">Order information</h3>
                    <span class="sari-order-dialog__status" data-order-status="{{ $order->status }}">{{ $order->statusLabel() }}</span>
                </div>
                <div class="sari-order-dialog__fields sari-order-dialog__fields--two">
                    <div class="sari-order-dialog__field">
                        <span class="sari-order-dialog__label">Order number</span>
                        <div class="sari-order-dialog__readout sari-order-dialog__readout--strong">{{ $order->order_number }}</div>
                    </div>
                    <div class="sari-order-dialog__field">
                        <span class="sari-order-dialog__label">Order date</span>
                        <div class="sari-order-dialog__readout">{{ $order->created_at?->format('M d, Y · h:i A') ?: '—' }}</div>
                    </div>
                </div>
            </section>

            <section class="sari-order-dialog__section" aria-labelledby="sari-order-buyer-{{ $order->id }}">
                <h3 id="sari-order-buyer-{{ $order->id }}">Buyer & delivery</h3>
                <div class="sari-order-dialog__fields sari-order-dialog__fields--two">
                    <div class="sari-order-dialog__field">
                        <span class="sari-order-dialog__label">Buyer</span>
                        <div class="sari-order-dialog__readout">{{ $order->buyer_name ?: '—' }}</div>
                    </div>
                    <div class="sari-order-dialog__field">
                        <span class="sari-order-dialog__label">Email</span>
                        <div class="sari-order-dialog__readout sari-order-dialog__readout--wrap">{{ $order->buyer_email ?: 'Buyer account' }}</div>
                    </div>
                    <div class="sari-order-dialog__field sari-order-dialog__field--full">
                        <span class="sari-order-dialog__label">Shipping address</span>
                        <div class="sari-order-dialog__readout sari-order-dialog__readout--multiline">{{ $order->buyer_address ?: 'No shipping address provided' }}</div>
                    </div>
                    <div class="sari-order-dialog__field">
                        <span class="sari-order-dialog__label">Courier</span>
                        <div class="sari-order-dialog__readout">{{ $order->courier_name ?: 'Waiting for courier' }}</div>
                    </div>
                    <div class="sari-order-dialog__field">
                        <span class="sari-order-dialog__label">Payment method</span>
                        <div class="sari-order-dialog__readout">{{ $order->payment_method ?: '—' }}</div>
                    </div>
                </div>
            </section>

            <section class="sari-order-dialog__section sari-order-dialog__map-section" aria-labelledby="sari-order-map-{{ $order->id }}">
                <div class="sari-order-dialog__section-head">
                    <div>
                        <h3 id="sari-order-map-{{ $order->id }}">Delivery location</h3>
                        <p class="sari-order-dialog__section-caption">Map searched from the buyer's shipping address; not live rider tracking.</p>
                    </div>
                    @if (trim((string) ($order->buyer_address ?? '')) !== '')
                        <a class="sari-order-dialog__map-link" href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode(trim((string) $order->buyer_address)) }}" target="_blank" rel="noopener noreferrer">Open map <span aria-hidden="true">↗</span></a>
                    @endif
                </div>
                @if (trim((string) ($order->buyer_address ?? '')) !== '')
                    <div class="sari-order-dialog__map-frame">
                        <iframe title="Map of delivery address for order {{ $order->order_number }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" data-order-map-src="https://maps.google.com/maps?q={{ rawurlencode(trim((string) $order->buyer_address)) }}&amp;output=embed"></iframe>
                    </div>
                @else
                    <div class="sari-order-dialog__map-unavailable">No shipping address is available for this order.</div>
                @endif
            </section>

            <section class="sari-order-dialog__section" aria-labelledby="sari-order-items-{{ $order->id }}">
                <div class="sari-order-dialog__section-head">
                    <h3 id="sari-order-items-{{ $order->id }}">Ordered products</h3>
                    <span class="sari-order-dialog__subtle">{{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}</span>
                </div>
                <div class="sari-order-dialog__items">
                    @forelse ($items as $item)
                        @php
                            $itemProductId = (int) ($item['product_id'] ?? 0);
                            $itemImage = $itemProductId > 0 ? route('seller.products.image', $itemProductId) : null;
                            $itemQty = max(1, (int) ($item['qty'] ?? 1));
                            $itemPrice = (float) ($item['price'] ?? 0);
                            $itemLineTotal = (float) ($item['line_total'] ?? ($itemPrice * $itemQty));
                        @endphp
                        <div class="sari-order-dialog__item">
                            <div class="sari-order-dialog__item-image">
                                @if ($itemImage)
                                    <img src="{{ $itemImage }}" alt="{{ $item['name'] ?? 'Ordered product' }}" loading="lazy" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                                @endif
                                <div class="{{ $itemImage ? 'hidden' : '' }} sari-order-dialog__item-placeholder" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="m5 17 5-5 4 4 2-2 3 3"/></svg>
                                </div>
                            </div>
                            <div class="sari-order-dialog__item-copy">
                                <strong>{{ $item['name'] ?? 'Product' }}</strong>
                                @if (!empty($item['variant_label']))
                                    <span class="sari-order-dialog__item-variant">{{ $item['variant_label'] }}</span>
                                @endif
                                <span class="sari-order-dialog__item-qty">Quantity: <b>{{ $itemQty }}</b></span>
                            </div>
                            <div class="sari-order-dialog__item-amount">
                                <strong class="sari-order-dialog__item-price">₱{{ number_format($itemLineTotal, 2) }}</strong>
                                <span>₱{{ number_format($itemPrice, 2) }} each</span>
                            </div>
                        </div>
                    @empty
                        <div class="sari-order-dialog__items-empty">No item details available.</div>
                    @endforelse
                </div>
            </section>

            <div class="sari-order-dialog__lower">
                <section class="sari-order-dialog__section" aria-labelledby="sari-order-payment-{{ $order->id }}">
                    <h3 id="sari-order-payment-{{ $order->id }}">Payment summary</h3>
                    <p class="sari-order-dialog__section-caption">Order charges</p>
                    <dl class="sari-order-dialog__money">
                        <div><dt>Subtotal</dt><dd>₱{{ number_format((float) $order->subtotal, 2) }}</dd></div>
                        <div><dt>Delivery fee</dt><dd>₱{{ number_format((float) $order->delivery_fee, 2) }}</dd></div>
                        <div class="sari-order-dialog__money-total"><dt>Order total</dt><dd>₱{{ number_format((float) $order->total, 2) }}</dd></div>
                    </dl>
                </section>
                <section class="sari-order-dialog__section" aria-labelledby="sari-order-progress-{{ $order->id }}">
                    <div class="sari-order-dialog__section-head">
                        <h3 id="sari-order-progress-{{ $order->id }}">Fulfillment progress</h3>
                        <strong class="sari-order-dialog__percent">{{ $progress }}%</strong>
                    </div>
                    <div class="sari-order-dialog__progress" role="progressbar" aria-label="Order fulfillment progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress }}"><span style="width: {{ $progress }}%"></span></div>
                    <p class="sari-order-dialog__section-caption sari-order-dialog__progress-caption">Current stage: {{ $order->statusLabel() }}</p>
                    <ol class="sari-order-dialog__steps">
                        @foreach ($workflowSteps as [$step, $stepProgress])
                            <li class="{{ $progress >= $stepProgress ? 'is-complete' : '' }}">
                                <span class="sari-order-dialog__step-dot" aria-hidden="true">@if ($progress >= $stepProgress)✓@endif</span>
                                <span>{{ $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                </section>
            </div>
        </div>

        <footer class="sari-order-dialog__footer">
            <div class="sari-order-dialog__next">
                <span class="sari-order-dialog__next-label">NEXT ACTION</span>
                <strong>{{ $nextActionLabel }}</strong>
            </div>
            <div class="sari-order-dialog__footer-actions">
                @if ($order->buyer_account_id || $order->buyer_social_account_id)
                    <a href="{{ route('seller.messages', ['buyer' => $order->buyer_account_id ? 'account-' . $order->buyer_account_id : 'social-' . $order->buyer_social_account_id]) }}" data-message-buyer class="sari-order-dialog__button sari-order-dialog__button--light">Message buyer</a>
                @endif
                <a href="{{ route('seller.orders.waybill', $order) }}" target="_blank" rel="noopener noreferrer" class="sari-order-dialog__button sari-order-dialog__button--light">Print waybill</a>
                @if ($order->status === 'new')
                    <form method="POST" action="{{ route('seller.orders.prepare', $order) }}">@csrf
                        <button type="submit" class="sari-order-dialog__button sari-order-dialog__button--gold">Prepare order</button>
                    </form>
                @elseif ($order->status === 'preparing')
                    <form method="POST" action="{{ route('seller.orders.ready-pickup', $order) }}">@csrf
                        <button type="submit" class="sari-order-dialog__button sari-order-dialog__button--gold" data-ready-for-pickup>Ready for Pickup</button>
                    </form>
                @else
                    <span class="sari-order-dialog__current-status">{{ $order->statusLabel() }}</span>
                @endif
            </div>
        </footer>
    </div>
</template>
