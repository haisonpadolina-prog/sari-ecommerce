@php
    $routeStops = collect($trackingRoute['stops'] ?? [])->sortBy('sequence')->values();
    $confirmedStops = $routeStops->filter(fn (array $stop) => (bool) ($stop['reached'] ?? false));
    $reachedStops = $confirmedStops->count();
    $lastConfirmedStop = $confirmedStops->last();
    $sellerStop = $routeStops->firstWhere('type', 'seller');
    $buyerStop = $routeStops->firstWhere('type', 'buyer');
    $riderName = trim((string) ($order->courier_name ?? ''));
    $pickupName = trim((string) ($sellerStop['name'] ?? $order->seller?->store_name ?? 'Seller pickup'));
@endphp
<section class="sari-seller-route-card" aria-labelledby="sariSellerRouteHeading">
    <header class="sari-seller-route-header">
        <div>
            <p class="sari-seller-route-eyebrow">DELIVERY ROUTE</p>
            <h2 id="sariSellerRouteHeading">Track your parcel</h2>
            <p>Follow the same saved Logistics route and confirmed checkpoints shown in Buyer tracking.</p>
        </div>
        <span class="sari-seller-route-count">{{ $reachedStops }} of {{ $routeStops->count() }} stops confirmed</span>
    </header>

    <div class="sari-seller-route-grid">
        <div class="sari-seller-route-map-side">
            <div class="sari-seller-route-map-wrap" data-seller-delivery-route="{{ json_encode($trackingRoute, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}">
                <div id="sariSellerDeliveryMap" class="sari-seller-route-map" data-seller-maplibre-map role="region" aria-label="Interactive map showing seller pickup, logistics facilities and buyer destination"></div>
                <span class="sari-seller-map-loading" data-seller-map-loading>Loading route map…</span>
                <div class="sari-seller-route-notice" data-seller-route-notice hidden>
                    <strong>Route update</strong>
                    <small>Checking saved coordinates.</small>
                </div>
            </div>
            <div class="sari-seller-route-legend">
                <span><i class="sari-route-legend-line"></i> Road route</span>
                <span><i class="sari-route-legend-line is-confirmed"></i> Confirmed</span>
                <span><i class="sari-route-legend-line is-next"></i> Next leg</span>
                <small data-seller-road-status>Preparing map…</small>
            </div>
        </div>

        <aside class="sari-seller-route-side" aria-label="Saved shipment route and assigned rider">
            <div class="sari-seller-route-party">
                <span class="sari-seller-route-label">SELLER PICKUP</span>
                <strong>{{ $pickupName }}</strong>
                <small>{{ $sellerStop['address'] ?? $order->pickup_address ?? 'Pickup address pending' }}</small>
            </div>
            <div class="sari-seller-route-party">
                <span class="sari-seller-route-label">ASSIGNED RIDER</span>
                <strong>{{ $riderName !== '' ? $riderName : 'Waiting for rider assignment' }}</strong>
                <small>{{ $riderName !== '' ? 'Assigned to this shipment' : 'Logistics will assign a rider' }}</small>
            </div>
            <div class="sari-seller-route-party">
                <span class="sari-seller-route-label">BUYER DESTINATION</span>
                <strong>{{ $buyerStop['name'] ?? $order->buyer_name ?? 'Buyer' }}</strong>
                <small>{{ $buyerStop['address'] ?? $order->buyer_address ?? 'Destination pending' }}</small>
            </div>
            <div class="sari-seller-last-checkpoint" aria-live="polite">
                <span class="sari-seller-route-label">LAST CONFIRMED PARCEL CHECKPOINT</span>
                <strong>{{ $lastConfirmedStop['name'] ?? 'No confirmed scans yet' }}</strong>
                <small>{{ !empty($lastConfirmedStop['reached_at']) ? \Illuminate\Support\Carbon::parse($lastConfirmedStop['reached_at'])->timezone('Asia/Manila')->format('M d, Y · h:i A') : 'Waiting for Logistics to confirm the next location' }}</small>
            </div>
            <div class="sari-seller-route-stops-head">
                <strong>Route checkpoints</strong>
                <small>Confirmed Logistics events</small>
            </div>
            <ol class="sari-seller-route-stops">
                @forelse ($routeStops as $stop)
                    @php
                        $confirmed = (bool) ($stop['reached'] ?? false);
                        $isCurrent = !$confirmed && (int) ($stop['sequence'] ?? 0) === (int) ($trackingRoute['current_sequence'] ?? 0);
                        $locationName = match ($stop['key'] ?? '') {
                            'seller' => 'Seller pickup',
                            'origin_facility' => 'Origin sorting hub',
                            'destination_facility' => 'Destination sorting hub',
                            'buyer' => 'Buyer destination',
                            default => 'Delivery stop',
                        };
                    @endphp
                    <li class="sari-seller-route-stop {{ $confirmed ? 'is-confirmed' : ($isCurrent ? 'is-current' : '') }}">
                        <span class="sari-seller-route-stop-dot" aria-hidden="true">{{ (int) ($stop['sequence'] ?? $loop->iteration) }}</span>
                        <div>
                            <strong>{{ $locationName }}</strong>
                            <span>{{ $stop['name'] ?? 'Location pending' }}</span>
                        </div>
                        <small>{{ $confirmed ? 'Reached' : ($isCurrent ? 'Next' : 'Pending') }}</small>
                    </li>
                @empty
                    <li class="sari-seller-route-stop-empty">Logistics has not saved route stops yet.</li>
                @endforelse
            </ol>
            <p class="sari-seller-route-disclaimer">The map shows planned roads and saved milestones, not the rider's live GPS position. Confirmed stops update when Logistics records progress.</p>
        </aside>
    </div>
</section>
