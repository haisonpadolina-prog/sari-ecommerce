import 'maplibre-gl/dist/maplibre-gl.css';

import {
    LngLatBounds,
    Map,
    Marker,
    NavigationControl,
    Popup,
    setWorkerUrl,
} from 'maplibre-gl';

import workerUrl from 'maplibre-gl/dist/maplibre-gl-worker.mjs?worker&url';

setWorkerUrl(workerUrl);

const MAP_STYLE = 'https://tiles.openfreemap.org/styles/liberty';
const PHILIPPINES_CENTER = [121.7740, 12.8797];

let animationFrame = null;
let animationTimer = null;

const parseRoute = (wrapper) => {
    try {
        return JSON.parse(wrapper?.dataset?.sariDeliveryRoute || '{}');
    } catch (_) {
        return {};
    }
};

const validStops = (route) => {
    return Array.isArray(route?.stops)
        ? route.stops.filter((stop) => {
            const lat = Number(stop?.lat);
            const lng = Number(stop?.lng);

            return Number.isFinite(lat)
                && Number.isFinite(lng)
                && lat >= -90
                && lat <= 90
                && lng >= -180
                && lng <= 180;
        })
        : [];
};

const stopLabel = (stop) => {
    if (stop.type === 'seller') return 'S';
    if (stop.type === 'buyer') return 'B';

    return String(Math.max(1, Number(stop.sequence || 1) - 1));
};

const markerElement = (stop, route) => {
    const el = document.createElement('div');
    el.className = 'sari-buyer-map-marker';
    el.textContent = stopLabel(stop);

    if (stop.type === 'buyer') {
        el.classList.add('is-buyer');
    }

    if (
        !stop.reached
        && Number(stop.sequence) > Number(route?.current_sequence || 1)
    ) {
        el.classList.add('is-future');
    }

    if (Number(stop.sequence) === Number(route?.current_sequence || 0)) {
        el.classList.add('is-current');
    }

    return el;
};

const lineFeature = (coordinates) => ({
    type: 'Feature',
    properties: {},
    geometry: {
        type: 'LineString',
        coordinates,
    },
});

const addLine = (
    map,
    id,
    coordinates,
    {
        color,
        width,
        opacity = 1,
        dasharray = null,
    }
) => {
    if (!Array.isArray(coordinates) || coordinates.length < 2) {
        return false;
    }

    map.addSource(id, {
        type: 'geojson',
        data: lineFeature(coordinates),
    });

    const paint = {
        'line-color': color,
        'line-width': width,
        'line-opacity': opacity,
    };

    if (dasharray) {
        paint['line-dasharray'] = dasharray;
    }

    map.addLayer({
        id,
        type: 'line',
        source: id,
        layout: {
            'line-cap': 'round',
            'line-join': 'round',
        },
        paint,
    });

    return true;
};

const startCurrentLegAnimation = (map, layerId) => {
    if (!map.getLayer(layerId)) {
        return;
    }

    const patterns = [
        [0.5, 2.1],
        [0.8, 1.8],
        [1.1, 1.5],
        [1.4, 1.2],
        [1.1, 1.5],
        [0.8, 1.8],
    ];

    let index = 0;

    if (animationTimer) {
        window.clearInterval(animationTimer);
    }

    animationTimer = window.setInterval(() => {
        if (!map.getLayer(layerId)) {
            return;
        }

        index = (index + 1) % patterns.length;
        map.setPaintProperty(
            layerId,
            'line-dasharray',
            patterns[index]
        );
    }, 150);
};

const fitRoute = (map, stops) => {
    if (stops.length === 0) {
        map.setCenter(PHILIPPINES_CENTER);
        map.setZoom(5.2);
        return;
    }

    if (stops.length === 1) {
        map.setCenter([
            Number(stops[0].lng),
            Number(stops[0].lat),
        ]);
        map.setZoom(13);
        return;
    }

    const bounds = new LngLatBounds();

    stops.forEach((stop) => {
        bounds.extend([
            Number(stop.lng),
            Number(stop.lat),
        ]);
    });

    map.fitBounds(bounds, {
        padding: {
            top: 90,
            right: 70,
            bottom: 90,
            left: 70,
        },
        maxZoom: 14,
        duration: 0,
    });
};

const buildRouteLayers = (map, route, stops) => {
    const coordinates = stops.map((stop) => [
        Number(stop.lng),
        Number(stop.lat),
    ]);

    if (coordinates.length < 2) {
        return;
    }

    // Entire planned route.
    addLine(
        map,
        'sari-route-full',
        coordinates,
        {
            color: '#d5d0c9',
            width: 5,
            opacity: 0.92,
        }
    );

    const lastReached = Math.max(
        0,
        Math.min(5, Number(route?.last_reached_sequence || 0))
    );

    // Only coordinates whose sequence is already completed.
    const completedStops = stops.filter(
        (stop) => Number(stop.sequence) <= lastReached
    );

    const completedCoordinates = completedStops.map((stop) => [
        Number(stop.lng),
        Number(stop.lat),
    ]);

    addLine(
        map,
        'sari-route-completed',
        completedCoordinates,
        {
            color: '#c98913',
            width: 5.5,
            opacity: 1,
        }
    );

    const currentStop = stops.find(
        (stop) => Number(stop.sequence) === lastReached
    );

    const nextStop = stops.find(
        (stop) => Number(stop.sequence) === lastReached + 1
    );

    // If nothing has been reached yet, animate Seller -> first hub.
    const currentLegStart = currentStop || stops.find(
        (stop) => Number(stop.sequence) === 1
    );

    const currentLegEnd = nextStop || (
        lastReached === 0
            ? stops.find((stop) => Number(stop.sequence) === 2)
            : null
    );

    if (currentLegStart && currentLegEnd) {
        const currentLegAdded = addLine(
            map,
            'sari-route-current',
            [
                [
                    Number(currentLegStart.lng),
                    Number(currentLegStart.lat),
                ],
                [
                    Number(currentLegEnd.lng),
                    Number(currentLegEnd.lat),
                ],
            ],
            {
                color: '#d89513',
                width: 5.5,
                opacity: 1,
                dasharray: [0.8, 1.8],
            }
        );

        if (currentLegAdded) {
            startCurrentLegAnimation(
                map,
                'sari-route-current'
            );
        }
    }
};

const addMarkers = (map, route, stops) => {
    stops.forEach((stop) => {
        const popup = new Popup({
            offset: 18,
            closeButton: false,
            maxWidth: '250px',
        }).setHTML(`
            <div style="font-family:Poppins,system-ui,sans-serif;padding:2px 1px;">
                <strong style="display:block;font-size:11px;color:#3c3631;">
                    ${String(stop.name || 'Route stop')}
                </strong>
                ${
                    stop.address
                        ? `<span style="display:block;margin-top:3px;font-size:9px;line-height:1.4;color:#81776d;">
                            ${String(stop.address)}
                           </span>`
                        : ''
                }
            </div>
        `);

        new Marker({
            element: markerElement(stop, route),
            anchor: 'center',
        })
            .setLngLat([
                Number(stop.lng),
                Number(stop.lat),
            ])
            .setPopup(popup)
            .addTo(map);
    });
};

const updateNotice = (wrapper, title, copy) => {
    const notice = wrapper?.querySelector(
        '[data-sari-route-notice]'
    );

    if (!notice) return;

    const titleNode = notice.querySelector('strong');
    const copyNode = notice.querySelector('small');

    if (titleNode && title) {
        titleNode.textContent = title;
    }

    if (copyNode && copy) {
        copyNode.textContent = copy;
    }
};

const boot = () => {
    const wrapper = document.querySelector(
        '[data-sari-delivery-route]'
    );

    const mapNode = document.querySelector(
        '[data-sari-maplibre-map]'
    );

    if (!wrapper || !mapNode || mapNode.dataset.ready === '1') {
        return;
    }

    mapNode.dataset.ready = '1';

    const route = parseRoute(wrapper);
    const stops = validStops(route);

    const map = new Map({
        container: mapNode,
        style: MAP_STYLE,
        center: PHILIPPINES_CENTER,
        zoom: 5.2,
        attributionControl: true,
        interactive: true,
        dragRotate: false,
        pitchWithRotate: false,
    });

    map.addControl(
        new NavigationControl({
            showCompass: false,
            visualizePitch: false,
        }),
        'top-right'
    );

    map.on('load', () => {
        fitRoute(map, stops);
        addMarkers(map, route, stops);
        buildRouteLayers(map, route, stops);

        if (stops.length >= 2) {
            const notice = wrapper.querySelector(
                '[data-sari-route-notice]'
            );

            if (notice && route?.ready) {
                notice.remove();
            }
        }
    });

    map.on('error', (event) => {
        const message = String(
            event?.error?.message || ''
        ).toLowerCase();

        // Surface a useful UI message only for actual style/tile failures.
        if (
            message.includes('style')
            || message.includes('source')
            || message.includes('tile')
            || message.includes('network')
            || message.includes('fetch')
        ) {
            updateNotice(
                wrapper,
                'Map background could not load',
                'The tracking data is safe. Refresh the page or verify that tiles.openfreemap.org is allowed by your server/browser network policy.'
            );
        }
    });

    // Laravel/Livewire layouts can settle after map creation.
    window.setTimeout(() => {
        map.resize();
        fitRoute(map, stops);
    }, 180);
};

const cleanup = () => {
    if (animationTimer) {
        window.clearInterval(animationTimer);
        animationTimer = null;
    }

    if (animationFrame) {
        window.cancelAnimationFrame(animationFrame);
        animationFrame = null;
    }
};

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        boot,
        { once: true }
    );
} else {
    boot();
}

document.addEventListener(
    'livewire:navigated',
    () => {
        cleanup();
        boot();
    }
);
