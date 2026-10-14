@extends('layouts.logistics')

@section('title', 'Sorting Facilities | SARI Logistics')
@section('page-title', 'Sorting Facilities')

@push('styles')
<link
    rel="stylesheet"
    href="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.css"
>
<style>
    #sariFacilityMap {
        min-height: 330px;
        border-radius: 18px;
        overflow: hidden;
        background: #eef1ed;
    }

    #sariFacilityMap .maplibregl-ctrl-group {
        overflow: hidden;
        border: 1px solid #e7ddcf;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(64, 50, 34, .08);
    }

    #sariFacilityMap .maplibregl-ctrl-attrib {
        font-size: 7px;
    }

    .sari-facility-pin {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 3px solid #fff;
        border-radius: 999px;
        background: #c98913;
        color: #fff;
        font-size: 9px;
        font-weight: 800;
        box-shadow: 0 7px 18px rgba(67, 49, 22, .24);
    }
</style>
@endpush

@section('content')
<section class="space-y-5">
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[10px] font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-[10px] text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-[24px] border border-[#eee4d3] bg-white p-5 shadow-[0_12px_36px_rgba(76,58,34,.05)] sm:p-6">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-[720px]">
                <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#b97805]">
                    Logistics network
                </p>
                <h2 class="mt-2 text-[24px] font-bold tracking-[-.035em] text-[#28221b]">
                    Manage your real sorting facility locations
                </h2>
                <p class="mt-2 text-[10px] leading-5 text-[#82786c]">
                    Orders assigned to {{ $logistics->displayName() }} use only the active facilities registered here.
                    SARI automatically chooses the facility nearest the Seller as the origin hub and the nearest distinct
                    facility to the Buyer as the destination hub.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                <div class="rounded-2xl border border-[#eee4d3] bg-[#fffdf8] px-4 py-3">
                    <p class="text-[7px] font-bold uppercase tracking-[.09em] text-[#9a9083]">Total</p>
                    <p class="mt-1 text-[20px] font-bold text-[#2d271f]">{{ $stats['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-[#eee4d3] bg-[#fffdf8] px-4 py-3">
                    <p class="text-[7px] font-bold uppercase tracking-[.09em] text-[#9a9083]">Active</p>
                    <p class="mt-1 text-[20px] font-bold text-[#2d271f]">{{ $stats['active'] }}</p>
                </div>
                <div class="rounded-2xl border border-[#eee4d3] bg-[#fffdf8] px-4 py-3">
                    <p class="text-[7px] font-bold uppercase tracking-[.09em] text-[#9a9083]">Used</p>
                    <p class="mt-1 text-[20px] font-bold text-[#2d271f]">{{ $stats['used'] }}</p>
                </div>
                <div class="rounded-2xl border {{ $stats['routing_ready'] ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} px-4 py-3">
                    <p class="text-[7px] font-bold uppercase tracking-[.09em] {{ $stats['routing_ready'] ? 'text-emerald-600' : 'text-amber-600' }}">
                        Routing
                    </p>
                    <p class="mt-1 text-[10px] font-bold {{ $stats['routing_ready'] ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ $stats['routing_ready'] ? 'READY' : 'NEEDS 2 ACTIVE HUBS' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @unless ($stats['routing_ready'])
        <div class="rounded-[18px] border border-amber-200 bg-amber-50 px-4 py-3">
            <p class="text-[10px] font-bold text-amber-800">Add at least two active facilities before SARI assigns new orders to this Logistics network.</p>
            <p class="mt-1 text-[8px] leading-4 text-amber-700">
                One facility becomes the Seller-area origin hub and a different facility becomes the Buyer-area destination hub.
            </p>
        </div>
    @endunless

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_440px]">
        <section class="rounded-[22px] border border-[#eee4d3] bg-white">
            <div class="flex items-center justify-between gap-3 border-b border-[#eee4d3] px-5 py-4">
                <div>
                    <h3 class="text-[13px] font-bold text-[#332c24]">Your facility network</h3>
                    <p class="mt-1 text-[8px] text-[#92887b]">Only your own facilities are visible and eligible for your orders.</p>
                </div>
                <span class="rounded-full bg-[#fff6e5] px-3 py-1.5 text-[7px] font-bold text-[#9d6d18]">
                    {{ $facilities->count() }} HUB{{ $facilities->count() === 1 ? '' : 'S' }}
                </span>
            </div>

            <div class="divide-y divide-[#f0e9df]">
                @forelse ($facilities as $facility)
                    <article class="p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-[12px] font-bold text-[#312a22]">{{ $facility->name }}</h4>
                                    <span class="rounded-full px-2.5 py-1 text-[7px] font-bold {{ $facility->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-[#f2f0ed] text-[#8c847b]' }}">
                                        {{ $facility->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                    </span>
                                </div>

                                <p class="mt-2 max-w-[720px] text-[9px] leading-4 text-[#746b61]">
                                    {{ $facility->address }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[7.5px] text-[#958b80]">
                                    <span>{{ $facility->municipality_name }}, {{ $facility->province_name }}</span>
                                    <span>{{ number_format((float) $facility->latitude, 5) }}, {{ number_format((float) $facility->longitude, 5) }}</span>
                                    <span>{{ $facility->route_stops_count }} route use{{ $facility->route_stops_count === 1 ? '' : 's' }}</span>
                                    <span class="font-semibold text-[#aa7413]">{{ $facility->code }}</span>
                                </div>
                            </div>

                            <div class="flex shrink-0 flex-wrap gap-2">
                                <a
                                    href="{{ route('logistics.facilities.edit', $facility) }}"
                                    class="inline-flex h-9 items-center rounded-xl border border-[#e4d9ca] bg-white px-3.5 text-[8px] font-bold text-[#63594f] transition hover:border-[#d4b97e] hover:bg-[#fffaf0] hover:text-[#9b680d]"
                                >
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('logistics.facilities.toggle', $facility) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="h-9 rounded-xl border px-3.5 text-[8px] font-bold transition {{ $facility->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                    >
                                        {{ $facility->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                @if ((int) $facility->route_stops_count === 0)
                                    <form
                                        method="POST"
                                        action="{{ route('logistics.facilities.destroy', $facility) }}"
                                        onsubmit="return confirm('Delete this unused sorting facility?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="h-9 rounded-xl border border-rose-200 bg-rose-50 px-3.5 text-[8px] font-bold text-rose-700 transition hover:bg-rose-100"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="px-5 py-12 text-center">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#fff7e8] text-[#b97805]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 21h18"></path>
                                <path d="M5 21V7l7-4 7 4v14"></path>
                                <path d="M9 10h2"></path>
                                <path d="M13 10h2"></path>
                                <path d="M9 14h2"></path>
                                <path d="M13 14h2"></path>
                            </svg>
                        </div>
                        <p class="mt-3 text-[10px] font-bold text-[#4b433a]">No sorting facilities yet</p>
                        <p class="mt-1 text-[8px] text-[#948a7e]">Add your real hub locations using the form.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="self-start rounded-[22px] border border-[#eee4d3] bg-white p-5 shadow-[0_12px_36px_rgba(76,58,34,.04)] xl:sticky xl:top-[108px]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#b97805]">
                        {{ $editing ? 'Edit facility' : 'New facility' }}
                    </p>
                    <h3 class="mt-1 text-[17px] font-bold text-[#302920]">
                        {{ $editing ? $editing->name : 'Register sorting location' }}
                    </h3>
                </div>

                @if ($editing)
                    <a href="{{ route('logistics.facilities.index') }}" class="text-[8px] font-semibold text-[#9a8f82] hover:text-[#a96e05]">
                        Cancel edit
                    </a>
                @endif
            </div>

            <form
                method="POST"
                action="{{ $editing ? route('logistics.facilities.update', $editing) : route('logistics.facilities.store') }}"
                class="mt-5 space-y-4"
                id="sariFacilityForm"
            >
                @csrf
                @if ($editing)
                    @method('PATCH')
                @endif

                <label class="block">
                    <span class="text-[8px] font-semibold text-[#5f574e]">Facility name</span>
                    <input
                        name="name"
                        value="{{ old('name', $editing?->name) }}"
                        maxlength="140"
                        required
                        placeholder="e.g. SARI Bacoor Sorting Hub"
                        class="mt-2 h-11 w-full rounded-xl border border-[#e5ddd1] bg-white px-3 text-[9px] outline-none transition focus:border-[#d3ad63] focus:ring-2 focus:ring-[#f5e7cb]"
                    >
                </label>

                <label class="block">
                    <span class="text-[8px] font-semibold text-[#5f574e]">Full address / landmark</span>
                    <textarea
                        name="address"
                        rows="3"
                        maxlength="1200"
                        required
                        placeholder="Building, street, landmark, barangay..."
                        class="mt-2 w-full resize-none rounded-xl border border-[#e5ddd1] bg-white px-3 py-3 text-[9px] leading-4 outline-none transition focus:border-[#d3ad63] focus:ring-2 focus:ring-[#f5e7cb]"
                    >{{ old('address', $editing?->address) }}</textarea>
                </label>

                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-[8px] font-semibold text-[#5f574e]">Province / Region</span>
                        <input
                            name="province_name"
                            value="{{ old('province_name', $editing?->province_name) }}"
                            required
                            placeholder="Cavite"
                            class="mt-2 h-11 w-full rounded-xl border border-[#e5ddd1] px-3 text-[9px] outline-none focus:border-[#d3ad63] focus:ring-2 focus:ring-[#f5e7cb]"
                        >
                    </label>

                    <label class="block">
                        <span class="text-[8px] font-semibold text-[#5f574e]">City / Municipality</span>
                        <input
                            name="municipality_name"
                            value="{{ old('municipality_name', $editing?->municipality_name) }}"
                            required
                            placeholder="Bacoor"
                            class="mt-2 h-11 w-full rounded-xl border border-[#e5ddd1] px-3 text-[9px] outline-none focus:border-[#d3ad63] focus:ring-2 focus:ring-[#f5e7cb]"
                        >
                    </label>
                </div>

                <label class="block">
                    <span class="text-[8px] font-semibold text-[#5f574e]">Barangay <span class="font-normal text-[#aaa198]">(optional)</span></span>
                    <input
                        name="barangay_name"
                        value="{{ old('barangay_name', $editing?->barangay_name) }}"
                        placeholder="Habay"
                        class="mt-2 h-11 w-full rounded-xl border border-[#e5ddd1] px-3 text-[9px] outline-none focus:border-[#d3ad63] focus:ring-2 focus:ring-[#f5e7cb]"
                    >
                </label>

                <div>
                    <div class="mb-2 flex items-end justify-between gap-3">
                        <div>
                            <p class="text-[8px] font-semibold text-[#5f574e]">Exact map location</p>
                            <p class="mt-0.5 text-[7px] text-[#9a9084]">Click the map exactly where the facility is located.</p>
                        </div>
                        <span id="sariFacilityMapStatus" class="text-[7px] font-semibold text-[#a96e05]">
                            {{ old('latitude', $editing?->latitude) && old('longitude', $editing?->longitude) ? 'PIN SET' : 'SELECT A PIN' }}
                        </span>
                    </div>

                    <div id="sariFacilityMap"></div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="text-[7px] font-semibold text-[#83796d]">Latitude</span>
                        <input
                            id="sariFacilityLatitude"
                            name="latitude"
                            type="number"
                            step="0.0000001"
                            min="4"
                            max="22.5"
                            value="{{ old('latitude', $editing?->latitude) }}"
                            readonly
                            required
                            class="mt-1 h-10 w-full rounded-xl border border-[#e7dfd4] bg-[#faf8f4] px-3 text-[8px] text-[#71685e]"
                        >
                    </label>

                    <label class="block">
                        <span class="text-[7px] font-semibold text-[#83796d]">Longitude</span>
                        <input
                            id="sariFacilityLongitude"
                            name="longitude"
                            type="number"
                            step="0.0000001"
                            min="116"
                            max="127.5"
                            value="{{ old('longitude', $editing?->longitude) }}"
                            readonly
                            required
                            class="mt-1 h-10 w-full rounded-xl border border-[#e7dfd4] bg-[#faf8f4] px-3 text-[8px] text-[#71685e]"
                        >
                    </label>
                </div>

                <label class="flex items-start gap-3 rounded-xl border border-[#eee4d3] bg-[#fffdf8] p-3">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $editing?->is_active ?? true) ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 rounded border-[#d9cdbb] text-[#c98913] focus:ring-[#d8b770]"
                    >
                    <span>
                        <strong class="block text-[8px] text-[#51493f]">Active for new routes</strong>
                        <small class="mt-0.5 block text-[7px] leading-4 text-[#93897e]">
                            Inactive facilities stay in historical order snapshots but are not selected for new orders.
                        </small>
                    </span>
                </label>

                <button
                    type="submit"
                    class="h-11 w-full rounded-xl bg-[#c98913] px-5 text-[9px] font-bold text-white shadow-[0_10px_24px_rgba(201,137,19,.18)] transition hover:bg-[#b77b0e] focus:outline-none focus:ring-2 focus:ring-[#e2c07b]"
                >
                    {{ $editing ? 'Save Facility Changes' : 'Add Sorting Facility' }}
                </button>
            </form>

            <div class="mt-4 rounded-xl border border-[#eee4d3] bg-[#faf9f6] p-3">
                <p class="text-[7.5px] font-bold text-[#696056]">Account address is not a sorting facility</p>
                <p class="mt-1 text-[7px] leading-4 text-[#948a7e]">
                    {{ $logistics->street_address }}, {{ $logistics->municipality_name }}, {{ $logistics->province_name }}
                </p>
                <p class="mt-1 text-[7px] leading-4 text-[#a1988e]">
                    Register only actual warehouses/hubs above.
                </p>
            </div>
        </aside>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.js"></script>
<script>
(() => {
    const mapNode = document.getElementById('sariFacilityMap');
    const latInput = document.getElementById('sariFacilityLatitude');
    const lngInput = document.getElementById('sariFacilityLongitude');
    const statusNode = document.getElementById('sariFacilityMapStatus');

    if (!mapNode || !latInput || !lngInput || !window.maplibregl) {
        return;
    }

    const PH_BOUNDS = [
        [116.0, 4.0],
        [127.5, 22.5],
    ];

    const initialLat = Number(latInput.value);
    const initialLng = Number(lngInput.value);
    const hasInitial = Number.isFinite(initialLat)
        && Number.isFinite(initialLng)
        && initialLat >= 4
        && initialLat <= 22.5
        && initialLng >= 116
        && initialLng <= 127.5;

    const style = {
        version: 8,
        sources: {
            'osm-streets': {
                type: 'raster',
                tiles: [
                    'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
                ],
                tileSize: 256,
                minzoom: 0,
                maxzoom: 19,
                attribution: '© OpenStreetMap contributors',
            },
        },
        layers: [
            {
                id: 'osm',
                type: 'raster',
                source: 'osm-streets',
            },
        ],
    };

    const map = new maplibregl.Map({
        container: mapNode,
        style,
        center: hasInitial
            ? [initialLng, initialLat]
            : [121.0, 12.4],
        zoom: hasInitial ? 13 : 5.3,
        maxBounds: PH_BOUNDS,
        dragRotate: false,
        pitchWithRotate: false,
        attributionControl: true,
    });

    map.addControl(
        new maplibregl.NavigationControl({
            showCompass: false,
        }),
        'top-right'
    );

    let marker = null;

    const setPin = (lng, lat) => {
        if (
            lat < 4
            || lat > 22.5
            || lng < 116
            || lng > 127.5
        ) {
            return;
        }

        latInput.value = Number(lat).toFixed(7);
        lngInput.value = Number(lng).toFixed(7);
        statusNode.textContent = 'PIN SET';

        if (!marker) {
            const element = document.createElement('div');
            element.className = 'sari-facility-pin';
            element.textContent = 'H';

            marker = new maplibregl.Marker({
                element,
                anchor: 'center',
            })
                .setLngLat([lng, lat])
                .addTo(map);
        } else {
            marker.setLngLat([lng, lat]);
        }
    };

    if (hasInitial) {
        setPin(initialLng, initialLat);
    }

    map.on('click', (event) => {
        setPin(
            event.lngLat.lng,
            event.lngLat.lat
        );
    });

    window.setTimeout(() => map.resize(), 120);
})();
</script>
@endpush
