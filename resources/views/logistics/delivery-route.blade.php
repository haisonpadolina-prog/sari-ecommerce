@extends('layouts.logistics')

@section('title', 'Delivery Route | SARI Logistics')
@section('page-title', 'Delivery Route')

@push('styles')
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css"
>
<style>
    .sari-route-map {
        height: 520px;
        overflow: hidden;
        border-radius: 16px;
        background: #f3f2ef;
    }

    .sari-route-stop-card {
        position: relative;
        border: 1px solid #e9e2d8;
        border-radius: 14px;
        background: #fff;
        padding: 14px;
    }

    .sari-route-stop-card + .sari-route-stop-card::before {
        content: '';
        position: absolute;
        left: 28px;
        top: -17px;
        width: 1px;
        height: 17px;
        background: #dca640;
    }

    .sari-route-index {
        display: grid;
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        place-items: center;
        border-radius: 999px;
        background: #c98913;
        color: #fff;
        font-size: 8px;
        font-weight: 800;
    }

    .sari-route-map-pin {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 3px solid #fff;
        border-radius: 999px;
        background: #c98913;
        color: #fff;
        font-size: 8px;
        font-weight: 800;
        box-shadow: 0 6px 16px rgba(45,35,22,.2);
    }

    .sari-route-map-pin.is-buyer {
        background: #2f2b27;
    }

    .leaflet-container img {
        max-width: none !important;
    }
</style>
@endpush

@section('content')
@php
    $routeStops = $stops->sortBy('sequence')->values();
@endphp

<div class="sari-logistics-ui sari-logistics-route mx-auto max-w-[1500px] space-y-4">
    <div class="rounded-[20px] border border-[#ebe4da] bg-white p-5">
        <a
            href="{{ route('logistics.delivery-monitoring') }}"
            class="text-[8px] font-semibold text-[#948a80] hover:text-[#a96f09]"
        >
            ← Back to Delivery Monitoring
        </a>

        <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#a96f09]">
                    Automatic facility routing
                </p>
                <h1 class="mt-1 text-[20px] font-bold tracking-[-.025em] text-[#2f2923]">
                    {{ $order->order_number }}
                </h1>
                <p class="mt-1 text-[8.5px] text-[#8d8379]">
                    Seller → nearest origin facility in your network → nearest distinct destination facility in your network → Buyer
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('logistics.delivery-routes.update', $order) }}"
            >
                @csrf
                @method('PATCH')
                <button
                    class="h-10 rounded-xl border border-[#e1c58e] bg-[#fff9ed] px-4 text-[8px] font-bold text-[#97630b]"
                >
                    Refresh nearest facilities
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[9px] font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[9px] text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-4 xl:grid-cols-[390px_minmax(0,1fr)]">
        <section class="rounded-[20px] border border-[#ebe4da] bg-[#fcfbf8] p-4">
            <p class="text-[8px] font-bold uppercase tracking-[.1em] text-[#a57115]">
                Delivery handoff
            </p>

            <div class="mt-3 space-y-4">
                @foreach ($routeStops as $stop)
                    <article class="sari-route-stop-card">
                        <div class="flex items-start gap-3">
                            <span class="sari-route-index">{{ $stop->sequence }}</span>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-[9.5px] font-bold text-[#3c3630]">
                                        {{ $stop->name }}
                                    </p>

                                    @if ($stop->stop_key === 'origin_facility')
                                        <span class="rounded-full bg-[#fff4dc] px-2 py-1 text-[6px] font-bold text-[#9d6810]">
                                            SELLER AREA
                                        </span>
                                    @elseif ($stop->stop_key === 'destination_facility')
                                        <span class="rounded-full bg-[#fff4dc] px-2 py-1 text-[6px] font-bold text-[#9d6810]">
                                            BUYER AREA
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-1 text-[7.5px] leading-4 text-[#8f857b]">
                                    {{ $stop->address_snapshot ?: 'Location is being resolved.' }}
                                </p>

                                @if ($stop->latitude !== null && $stop->longitude !== null)
                                    <p class="mt-1 text-[6.5px] text-[#aaa097]">
                                        {{ number_format((float) $stop->latitude, 5) }},
                                        {{ number_format((float) $stop->longitude, 5) }}
                                    </p>
                                @endif

                                @if ($stop->type === 'sorting_facility')
                                    <div class="mt-3">
                                        @if ($stop->reached_at)
                                            <span class="inline-flex rounded-lg bg-emerald-50 px-3 py-2 text-[7px] font-bold text-emerald-700">
                                                Reached {{ $stop->reached_at->format('M d · h:i A') }}
                                            </span>
                                        @else
                                            <form
                                                method="POST"
                                                action="{{ route('logistics.delivery-routes.stops.reached', [$order, $stop]) }}"
                                            >
                                                @csrf
                                                <button
                                                    class="rounded-lg bg-[#2f2b27] px-3 py-2 text-[7px] font-bold text-white"
                                                >
                                                    Mark facility reached
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-4 rounded-xl border border-[#eee5d8] bg-white p-3">
                <p class="text-[8px] font-bold text-[#51483f]">
                    Rider handoff model
                </p>
                <p class="mt-1 text-[7.5px] leading-4 text-[#91877d]">
                    Pickup rider: Seller → Origin Facility<br>
                    Transfer rider: Origin → Destination Facility<br>
                    Final-mile rider: Destination Facility → Buyer
                </p>
                <p class="mt-2 text-[7px] leading-4 text-[#a2978d]">
                    Current SARI courier payment/assignment remains unchanged until per-leg rider earnings are added safely.
                </p>
            </div>
        </section>

        <section class="rounded-[20px] border border-[#ebe4da] bg-white p-3">
            <div class="mb-3 flex items-center justify-between gap-3 px-1">
                <div>
                    <p class="text-[10px] font-bold text-[#37312c]">Automatic route map</p>
                    <p class="mt-0.5 text-[7.5px] text-[#958b81]">
                        Facilities are selected only from active hubs owned by {{ $order->logisticsAccount?->displayName() ?? 'your Logistics network' }}.
                    </p>
                </div>
                <span class="rounded-full bg-[#fff7e7] px-3 py-1.5 text-[7px] font-bold text-[#9b670c]">
                    2 FACILITIES
                </span>
            </div>

            <div
                id="sariRoutePlannerMap"
                class="sari-route-map"
                data-route-stops='@json(
                    $routeStops->map(fn ($stop) => [
                        "sequence" => $stop->sequence,
                        "key" => $stop->stop_key,
                        "name" => $stop->name,
                        "lat" => $stop->latitude,
                        "lng" => $stop->longitude,
                    ])->values()
                )'
            ></div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(() => {
    const mapNode = document.getElementById('sariRoutePlannerMap');
    if (!mapNode || !window.L) return;

    let stops = [];
    try {
        stops = JSON.parse(mapNode.dataset.routeStops || '[]')
            .filter((stop) => {
                const lat = Number(stop.lat);
                const lng = Number(stop.lng);

                return Number.isFinite(lat)
                    && Number.isFinite(lng)
                    && lat >= 4
                    && lat <= 22.5
                    && lng >= 116
                    && lng <= 127.5;
            });
    } catch (_) {}

    const PH_BOUNDS = L.latLngBounds(
        [4, 116],
        [22.5, 127.5]
    );

    const map = L.map(mapNode, {
        zoomControl: true,
        maxBounds: PH_BOUNDS,
        maxBoundsViscosity: 1,
        minZoom: 5,
    }).setView([12.8797, 121.7740], 6);

    L.tileLayer(
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);

    const labelFor = (stop) => {
        if (stop.key === 'seller') return 'S';
        if (stop.key === 'buyer') return 'B';
        if (stop.key === 'origin_facility') return 'O';
        return 'D';
    };

    const points = [];

    stops.forEach((stop) => {
        const lat = Number(stop.lat);
        const lng = Number(stop.lng);

        points.push([lat, lng]);

        const marker = L.marker(
            [lat, lng],
            {
                icon: L.divIcon({
                    className: '',
                    html: `<span class="sari-route-map-pin ${stop.key === 'buyer' ? 'is-buyer' : ''}">${labelFor(stop)}</span>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                }),
            }
        ).addTo(map);

        marker.bindTooltip(
            stop.name || 'Route stop',
            {
                direction: 'top',
                offset: [0, -12],
            }
        );
    });

    if (points.length >= 2) {
        L.polyline(
            points,
            {
                color: '#c98913',
                weight: 4,
                opacity: .95,
                lineCap: 'round',
                lineJoin: 'round',
            }
        ).addTo(map);

        map.fitBounds(
            L.latLngBounds(points),
            { padding: [48, 48] }
        );
    } else if (points.length === 1) {
        map.setView(points[0], 13);
    }
})();
</script>
@endpush
