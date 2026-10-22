<div class="pmd-table-wrap">
    @if($ordersList->count())
        <table class="pmd-table">
            <thead><tr><th>Order</th><th>Buyer</th><th>Seller / Pickup</th><th>Courier</th><th>Status</th><th>Total</th><th>Updated</th></tr></thead>
            <tbody>
            @foreach($ordersList as $order)
                <tr>
                    <td><div class="pmd-name">{{ $order->order_number }}</div><div class="pmd-sub">{{ $order->payment_method ?: '—' }}</div></td>
                    <td><div class="pmd-name">{{ $order->buyer_name ?: 'Buyer' }}</div><div class="pmd-sub">{{ $order->buyer_email ?: '—' }}</div></td>
                    <td><div>{{ $order->pickup_name ?: 'Seller' }}</div><div class="pmd-sub">{{ \Illuminate\Support\Str::limit((string)$order->pickup_address, 48) }}</div></td>
                    <td>{{ $order->courier_name ?: 'Unassigned' }}</td>
                    <td><span class="pmd-pill">{{ $order->statusLabel() }}</span></td>
                    <td>₱{{ number_format((float)$order->total, 2) }}</td>
                    <td>{{ $order->updated_at?->format('M d · h:i A') ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="pmd-empty">No order records in this section.</div>
    @endif
</div>
