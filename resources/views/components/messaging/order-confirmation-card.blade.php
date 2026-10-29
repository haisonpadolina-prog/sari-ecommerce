@php
    $forBuyer = (bool) ($forBuyer ?? false);
    $products = collect($order->items ?? [])->values();
    $confirmed = $order->delivery_address_confirmed_at !== null;
@endphp
<div class="sari-order-card" data-order-card="{{ $order->id }}">
    <div class="sari-order-card-head">
        <span class="sari-order-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5z"/><path d="m3 7.5 9 4.5 9-4.5M12 12v9"/></svg>
        </span>
        <div class="sari-order-headline">
            <strong>Order confirmation</strong>
            <small>{{ $order->order_number }}</small>
        </div>
        <span class="sari-order-status {{ $confirmed ? 'is-confirmed' : '' }}">{{ $confirmed ? 'Confirmed' : 'Needs confirmation' }}</span>
    </div>
    <div class="sari-order-products">
        <span class="sari-order-products-label">Your items</span>
        @foreach ($products->take(3) as $item)
            <div class="sari-order-product">
                @if (!empty($item['product_id']))
                    <img src="{{ route($forBuyer ? 'buyer.product.image' : 'seller.products.image', ['product' => (int) $item['product_id']]) }}" alt="" loading="lazy">
                @endif
                <span>{{ (int) ($item['qty'] ?? 1) }} × {{ $item['name'] ?? 'Product' }}</span>
            </div>
        @endforeach
        @if ($products->count() > 3)<small>+{{ $products->count() - 3 }} more products</small>@endif
        <strong class="sari-order-total">₱{{ number_format((float) $order->total, 2) }} total</strong>
    </div>
    <dl class="sari-order-details">
        <div><dt>Phone number</dt><dd>{{ $order->buyer_phone ?: 'Not provided' }}</dd></div>
        <div><dt>Delivery address</dt><dd>{{ $order->buyer_address }}</dd></div>
    </dl>
    @if ($confirmed)
        <div class="sari-order-confirmed"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m4 12 5 5L20 6"/></svg>Delivery details confirmed</div>
    @elseif ($forBuyer && !in_array($order->status, ['cancelled', 'delivered'], true))
        <form method="POST" action="{{ route('buyer.messages.confirm-address', $order) }}" data-confirm-address-form>
            @csrf
            <button class="sari-order-confirm-button" type="submit">Confirm delivery details</button>
        </form>
        <p class="sari-order-hint">Wrong address or number? Send the seller a message below instead of confirming.</p>
    @else
        <p class="sari-order-hint">{{ in_array($order->status, ['cancelled', 'delivered'], true) ? 'Confirmation is no longer available for this order.' : 'Waiting for buyer confirmation.' }}</p>
    @endif
</div>
