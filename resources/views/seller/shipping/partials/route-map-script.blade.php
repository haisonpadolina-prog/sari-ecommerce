<link
        rel="stylesheet"
        href="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.css"
    >

    <script data-navigate-once defer src="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.js"
        onload="document.dispatchEvent(new Event('sari:seller-maplibre-ready'))"
        onerror="window.__sariSellerMaplibreFailed = true; document.dispatchEvent(new Event('sari:seller-maplibre-ready'))"></script>

    <script>
    (() => {
        'use strict';

        // OSRM-compatible road router. For production use an operator-controlled service.
        const ROUTER_URL = @json(config('services.sari_road_router.url', 'https://router.project-osrm.org'));
        const PHILIPPINES_CENTER = [121.774, 12.8797];
        const MAP_STYLE = {
            version: 8,
            name: 'SARI Street Map',
            sources: {
                'osm-streets': {
                    type: 'raster',
                    tiles: ['https://tile.openstreetmap.org/{z}/{x}/{y}.png'],
                    tileSize: 256,
                    attribution: '© OpenStreetMap contributors',
                },
            },
            layers: [{ id: 'sari-osm-background', type: 'raster', source: 'osm-streets' }],
        };

        if (window.__sariSellerShippingRoadMap) {
            window.__sariSellerShippingRoadMap.restart();
            return;
        }

        let activeMap = null;
        let mapResizeObserver = null;
        let pendingRoute = null;
        let renderGeneration = 0;
        const legCache = new Map();

        const point = (stop) => [Number(stop.lng), Number(stop.lat)];
        const validPoint = (coordinates) => Array.isArray(coordinates)
            && coordinates.length === 2
            && Number.isFinite(Number(coordinates[0]))
            && Number.isFinite(Number(coordinates[1]))
            && Number(coordinates[0]) >= 116 && Number(coordinates[0]) <= 127.5
            && Number(coordinates[1]) >= 4 && Number(coordinates[1]) <= 22.5;

        function validStops(route) {
            return (Array.isArray(route?.stops) ? route.stops : [])
                .filter((stop) => stop && stop.lng != null && stop.lat != null && validPoint(point(stop)))
                .sort((a, b) => Number(a.sequence) - Number(b.sequence));
        }

        function setStatus(text) {
            const node = document.querySelector('[data-seller-road-status]');
            if (node) node.textContent = text;
        }

        function setNotice(wrapper, title, copy) {
            const notice = wrapper?.querySelector('[data-seller-route-notice]');
            if (!notice) return;
            if (!title) {
                notice.hidden = true;
                return;
            }
            notice.querySelector('strong').textContent = title;
            notice.querySelector('small').textContent = copy || '';
            notice.hidden = false;
        }

        function stopLetter(stop) {
            if (stop.type === 'seller') return 'S';
            if (stop.type === 'buyer') return 'B';
            if (stop.key === 'origin_facility') return 'O';
            if (stop.key === 'destination_facility') return 'D';
            return String(stop.sequence || '•');
        }

        function drawStopMarkers(map, route, stops) {
            stops.forEach((stop) => {
                const badge = document.createElement('div');
                badge.className = 'sari-seller-map-marker';
                badge.textContent = stopLetter(stop);
                if (stop.type === 'buyer') badge.classList.add('is-buyer');
                if (!stop.reached && Number(stop.sequence) > Number(route.current_sequence || 1)) {
                    badge.classList.add('is-future');
                }
                if (Number(stop.sequence) === Number(route.current_sequence || 0)) {
                    badge.classList.add('is-current');
                }

                // Use textContent, never HTML interpolation of seller/address content.
                const popupBody = document.createElement('div');
                popupBody.style.fontFamily = 'Poppins, sans-serif';
                const name = document.createElement('strong');
                name.style.cssText = 'display:block;font-size:12px;color:#1f2937';
                name.textContent = String(stop.name || 'Delivery stop');
                popupBody.appendChild(name);
                if (stop.address) {
                    const address = document.createElement('p');
                    address.style.cssText = 'margin:6px 0 0;font-size:10px;line-height:1.5;color:#64748b';
                    address.textContent = String(stop.address);
                    popupBody.appendChild(address);
                }
                const reached = document.createElement('small');
                reached.style.cssText = 'display:block;margin-top:6px;color:#9a6811;font-size:10px';
                reached.textContent = stop.reached ? 'Reached (confirmed)' : 'Not yet confirmed reached';
                popupBody.appendChild(reached);
                const popup = new maplibregl.Popup({offset:18, maxWidth:'250px'}).setDOMContent(popupBody);
                new maplibregl.Marker({element:badge, anchor:'center'})
                    .setLngLat(point(stop)).setPopup(popup).addTo(map);
            });
        }

        function addLine(map, name, coordinates, {color, width = 5, dashed = false, opacity = 1}) {
            if (coordinates.length < 2) return;
            map.addSource(name, {
                type: 'geojson',
                data: {type:'Feature',properties:{},geometry:{type:'LineString',coordinates}},
            });
            const paint = {'line-color':color,'line-width':width,'line-opacity':opacity};
            if (dashed) paint['line-dasharray'] = [1.1, 1.5];
            map.addLayer({
                id: name, type: 'line', source: name,
                layout: {'line-cap':'round','line-join':'round'}, paint,
            });
        }

        function showLegs(map, stops, legs, approximate) {
            legs.forEach((leg, index) => {
                const coords = leg.filter(validPoint);
                if (coords.length < 2) return;
                // Lighter outline keeps route legible over busy OSM streets.
                addLine(map, `sari-seller-leg-${index}-outline`, coords, {
                    color: '#ffffff', width: 10, dashed: approximate, opacity: .92,
                });
                addLine(map, `sari-seller-leg-${index}-path`, coords, {
                    color: approximate ? '#728096' : '#64748b',
                    width: approximate ? 4 : 5.5,
                    dashed: approximate, opacity: approximate ? .77 : .95,
                });
                if (stops[index + 1]?.reached) {
                    // Gold only means the destination stop was reached in persisted logistics data.
                    addLine(map, `sari-seller-leg-${index}-completed`, coords, {
                        color: '#c88912', width: 6, dashed: approximate,
                    });
                } else if (
                    stops[index] && stops[index + 1]
                    && Number(stops[index + 1].sequence) === Number(stops[index].sequence) + 1
                    && (index === 0 || stops.slice(0, index + 1).every(s => s.reached))
                ) {
                    // Planned NEXT leg. Does not indicate the rider's live GPS position.
                    addLine(map, `sari-seller-leg-${index}-next`, coords, {
                        color: '#d89a19', width: 4, dashed: true, opacity: .95,
                    });
                }
            });
        }

        function fitRoute(map, stops, legs) {
            const all = legs.flat().filter(validPoint);
            const points = all.length ? all : stops.map(point);
            if (!points.length) {
                map.jumpTo({center:PHILIPPINES_CENTER,zoom:5.2});
                return;
            }
            if (points.length === 1) {
                map.jumpTo({center:points[0],zoom:13});
                return;
            }
            const bounds = new maplibregl.LngLatBounds();
            points.forEach(p => bounds.extend(p));
            map.fitBounds(bounds, {
                padding:{top:46,right:48,bottom:46,left:48}, maxZoom:14, duration:350,
            });
        }

        function concatStepGeometries(steps) {
            const leg = [];
            for (const step of steps) {
                const coords = step?.geometry?.coordinates;
                if (!Array.isArray(coords)) continue;
                for (const rawPoint of coords) {
                    if (!validPoint(rawPoint)) return [];
                    const p = [Number(rawPoint[0]), Number(rawPoint[1])];
                    if (!leg.length || leg[leg.length - 1][0] !== p[0] || leg[leg.length - 1][1] !== p[1]) {
                        leg.push(p);
                    }
                    if (leg.length > 12000) return [];
                }
            }
            return leg;
        }

        async function requestRoadLegs(stops, signal) {
            const baseUrl = String(ROUTER_URL || '').trim().replace(/\/+$/, '');
            if (!/^https:\/\//i.test(baseUrl)) throw new Error('Road router must use HTTPS.');
            const key = stops.map(stop => point(stop).map(n => n.toFixed(6)).join(',')).join(';');
            if (legCache.has(key)) return legCache.get(key);
            const url = `${baseUrl}/route/v1/driving/${key}?alternatives=false&steps=true&geometries=geojson&overview=full`;
            const timeout = new AbortController();
            const timeoutId = window.setTimeout(() => timeout.abort(), 12000);
            const propagateAbort = () => timeout.abort();
            signal?.addEventListener('abort', propagateAbort, {once:true});
            try {
                const response = await fetch(url, {signal:timeout.signal,mode:'cors',credentials:'omit'});
                if (!response.ok) throw new Error(`Road router HTTP ${response.status}`);
                const result = await response.json();
                const itinerary = result?.routes?.[0];
                if (result?.code !== 'Ok' || !Array.isArray(itinerary?.legs)
                    || itinerary.legs.length !== stops.length - 1) {
                    throw new Error('No connected driving route returned.');
                }
                if (Array.isArray(result.waypoints)
                    && result.waypoints.some(waypoint => Number(waypoint?.distance || 0) > 2500)) {
                    throw new Error('One or more saved locations are too far from routable roads.');
                }
                const legs = itinerary.legs.map((leg, i) => {
                    const coords = concatStepGeometries(leg.steps || []);
                    const p1 = point(stops[i]);
                    const p2 = point(stops[i + 1]);
                    if (coords.length < 2 && Math.abs(p1[0] - p2[0]) < .000001
                        && Math.abs(p1[1] - p2[1]) < .000001) {
                        return [p1, p2]; // Both facility stops can be the same physical hub.
                    }
                    return coords;
                });
                if (legs.some(leg => leg.length < 2)) throw new Error('Road route geometry incomplete.');
                legCache.set(key, legs);
                return legs;
            } finally {
                window.clearTimeout(timeoutId);
                signal?.removeEventListener('abort', propagateAbort);
            }
        }

        // Only connect neighboring sequence numbers; never jump over an unresolved hub.
        function approximateLegs(stops) {
            const legs = [];
            for (let i = 0; i < stops.length - 1; i++) {
                const adjacent = Number(stops[i + 1].sequence) === Number(stops[i].sequence) + 1;
                legs.push(adjacent ? [point(stops[i]),point(stops[i + 1])] : []);
            }
            return legs;
        }

        function destroy() {
            renderGeneration++;
            pendingRoute?.abort();
            pendingRoute = null;
            mapResizeObserver?.disconnect();
            mapResizeObserver = null;
            if (activeMap) {
                try { activeMap.remove(); } catch (_) {}
                activeMap = null;
            }
            document.querySelectorAll('[data-seller-maplibre-map]').forEach(el => delete el.dataset.ready);
        }

        function boot() {
            const wrapper = document.querySelector('[data-seller-delivery-route]');
            const mapNode = document.querySelector('[data-seller-maplibre-map]');
            if (!wrapper || !mapNode || mapNode.dataset.ready === '1') return;
            if (!window.maplibregl) {
                // External MapLibre can finish loading after a Livewire page swap.
                // The sari:seller-maplibre-ready event calls boot again when it is ready.
                if (window.__sariSellerMaplibreFailed) {
                    setNotice(wrapper,'Map library unavailable','Unable to load the street map. Try again with a network connection.');
                    wrapper.querySelector('[data-seller-map-loading]')?.classList.add('is-hidden');
                    setStatus('Map could not load');
                } else {
                    setStatus('Preparing interactive map…');
                }
                return;
            }
            const generation = ++renderGeneration;
            mapNode.dataset.ready = '1';
            let route;
            try { route = JSON.parse(wrapper.dataset.sellerDeliveryRoute || '{}'); }
            catch (_) { route = {}; }
            const stops = validStops(route);
            const ready = route.ready === true && stops.length === 4
                && stops.every((stop, index) => Number(stop.sequence) === index + 1);

            // Road geometry and map tiles are independent network requests.
            // Start routing now instead of waiting for the map's initial tiles.
            // Catch immediately so routing failures never become unhandled promises.
            const abortController = new AbortController();
            pendingRoute = abortController;
            const roadLegsResult = ready
                ? requestRoadLegs(stops, abortController.signal)
                    .then(legs => ({legs, error: null}), error => ({legs: [], error}))
                : null;

            const map = new maplibregl.Map({
                container:mapNode, style:MAP_STYLE,center:PHILIPPINES_CENTER,zoom:5.2,
                attributionControl:true,dragRotate:false,pitchWithRotate:false,
            });
            activeMap = map;
            if (typeof ResizeObserver !== 'undefined') {
                mapResizeObserver = new ResizeObserver(() => {
                    if (generation !== renderGeneration || !mapNode.isConnected) return;
                    window.requestAnimationFrame(() => {
                        if (generation === renderGeneration) map.resize();
                    });
                });
                mapResizeObserver.observe(mapNode);
            }
            map.addControl(new maplibregl.NavigationControl({showCompass:false}), 'top-right');
            // Style loading is sufficient for adding our sources and layers.
            // Waiting for all OSM tiles can unnecessarily delay route rendering.
            map.once('style.load', async () => {
                if (generation !== renderGeneration) return;
                wrapper.querySelector('[data-seller-map-loading]')?.classList.add('is-hidden');
                drawStopMarkers(map,route,stops);
                fitRoute(map,stops,[]); // Show the actual saved area immediately while road geometry loads.
                let legs = [];
                let approximate = false;
                if (ready) {
                    setStatus('Finding roads between saved logistics stops…');
                    const result = await roadLegsResult;
                    if (generation !== renderGeneration || abortController.signal.aborted) return;
                    if (!result.error) {
                        legs = result.legs;
                        setNotice(wrapper,'','');
                        setStatus('Road route · Confirmed Logistics milestones, not rider GPS');
                    } else {
                        approximate = true;
                        legs = approximateLegs(stops);
                        setNotice(wrapper,'Road route temporarily unavailable',
                            'Dashed links connect saved stops only; they are NOT the actual road route. Please try again later.');
                        setStatus('Approximate stop connections · No verified road path');
                    }
                } else if (stops.length) {
                    approximate = true;
                    legs = approximateLegs(stops);
                    setNotice(wrapper,'Waiting for full route coordinates',
                        `${stops.length} saved stop${stops.length !== 1 ? 's' : ''} available. The road route needs confirmed seller, both hubs, and buyer coordinates.`);
                    setStatus('Partial saved stops · Road route pending');
                } else {
                    setNotice(wrapper,'Route not yet available',
                        'SARI Logistics has not saved route coordinates for this order.');
                    setStatus('Waiting for saved route coordinates');
                }
                if (generation !== renderGeneration) return;
                showLegs(map,stops,legs,approximate);
                fitRoute(map,stops,legs);
                map.triggerRepaint();
            });
            map.on('error', event => {
                if (generation !== renderGeneration) return;
                const message = String(event?.error?.message || '').toLowerCase();
                if (/style|tile|source|network|fetch/.test(message)) {
                    wrapper.querySelector('[data-seller-map-loading]')?.classList.add('is-hidden');
                    setNotice(wrapper,'Map tiles unavailable',
                        'The route data has not been changed. Check access to OpenStreetMap tile servers.');
                }
            });
            window.setTimeout(() => { if (generation === renderGeneration) map.resize(); }, 200);
        }

        const controller = {restart: () => { destroy(); boot(); }};
        window.__sariSellerShippingRoadMap = controller;
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', boot, {once:true});
        } else {
            boot();
        }
        document.addEventListener('sari:seller-maplibre-ready', boot);
        document.addEventListener('livewire:navigating', destroy);
        document.addEventListener('livewire:navigated', () => {
            // Wait for the replacement map container to have a measured size.
            window.requestAnimationFrame(boot);
        });
    })();

    </script>
