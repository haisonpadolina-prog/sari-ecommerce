/* SARI Buyer My Orders: map-only route preview.
 * Saved S/O/D/B locations come from the existing buyer-authorized tracking API.
 * OSRM-compatible road geometry is a planned path, NOT rider GPS or actual travel history.
 * Checked milestones are derived only from saved logistics events.
 */
(() => {
    'use strict';
    if (window.__sariBuyerRealDeliveryStarted) return;
    window.__sariBuyerRealDeliveryStarted = true;

    const STYLE = {
        version: 8,
        name: 'SARI Order Route',
        sources: {'osm-streets': {type:'raster', tiles:['https://tile.openstreetmap.org/{z}/{x}/{y}.png'], tileSize:256, attribution:'© OpenStreetMap contributors'}},
        layers: [{id:'osm-background',type:'raster',source:'osm-streets'}]
    };
    const INITIAL_CENTER = [121.774, 12.8797];
    const UPDATE_MS = 20000;
    const ROUTE_LAYERS = ['sari-next-leg','sari-complete-route','sari-full-route','sari-route-halo'];
    const DEFAULT_ROUTE_INFO = 'Map shows saved seller, origin hub, destination hub, and buyer checkpoints. Road lines show planned directions, not the rider’s live GPS location.';
    const roadCache = new Map();
    let map = null;
    let markers = [];
    let selectedId = null;
    let fetchUrl = '';
    let dataController = null;
    let roadController = null;
    let timer = null;
    let selectionGeneration = 0;
    let renderGeneration = 0;
    let lastSignature = '';
    let routeInfo = DEFAULT_ROUTE_INFO;

    const get = id => document.getElementById(id);
    const status = message => { const node = get('sariDeliveryMapStatus'); if(node) node.textContent = message; };
    const stopPoint = stop => [Number(stop.lng), Number(stop.lat)];
    const validPoint = p => Array.isArray(p) && p.length === 2
        && Number.isFinite(Number(p[0])) && Number.isFinite(Number(p[1]))
        && Number(p[0]) >= 116 && Number(p[0]) <= 127.5
        && Number(p[1]) >= 4 && Number(p[1]) <= 22.5;
    const coordinates = route => (Array.isArray(route?.stops) ? route.stops : [])
        .filter(stop => stop && stop.lat != null && stop.lng != null && validPoint(stopPoint(stop)))
        .sort((a,b) => Number(a.sequence)-Number(b.sequence));
    const formatDate = value => {
        const d = value ? new Date(value) : null;
        return d && !Number.isNaN(d.getTime())
            ? d.toLocaleString('en-PH',{month:'short',day:'numeric',minute:'2-digit',hour:'numeric'})
            : 'not yet recorded';
    };
    const stopLabel = stop => stop.type==='seller' ? 'S' : stop.type==='buyer' ? 'B'
        : stop.key==='origin_facility' ? 'O' : stop.key==='destination_facility' ? 'D'
        : String(stop.sequence || '•');

    function setInfo(message) {
        routeInfo = message || DEFAULT_ROUTE_INFO;
        const node = get('sariDeliveryMapNotice');
        if (node && !node.hidden) node.textContent = routeInfo;
    }
    function showInfo(show) {
        const node = get('sariDeliveryMapNotice');
        const btn = get('sariDeliveryMapInfo');
        if (!node || !btn) return;
        node.hidden = !show;
        node.textContent = routeInfo;
        btn.setAttribute('aria-expanded', String(show));
    }
    function clearRoute() {
        markers.forEach(marker => marker.remove());
        markers = [];
        if (!map || !map.isStyleLoaded()) return;
        [...ROUTE_LAYERS].reverse().forEach(id => {
            if (map.getLayer(id)) map.removeLayer(id);
            if (map.getSource(id)) map.removeSource(id);
        });
    }
    function ensureMap() {
        if (map) return map;
        if (!get('sariRealDeliveryMap') || !window.maplibregl) {
            setInfo('The map library is unavailable. Refresh this page to retry.');
            status('Map library unavailable');
            return null;
        }
        map = new maplibregl.Map({container:get('sariRealDeliveryMap'),style:STYLE,
            center:INITIAL_CENTER,zoom:5.3,attributionControl:true,dragRotate:false,pitchWithRotate:false});
        map.addControl(new maplibregl.NavigationControl({showCompass:false}),'top-right');
        map.on('error', event => {
            const message = String(event?.error?.message || '').toLowerCase();
            if (/style|tile|source|network|fetch/.test(message)) {
                setInfo('Map background unavailable. Check your connection to OpenStreetMap. Saved tracking data has not changed.');
                status('Map tiles unavailable');
            }
        });
        return map;
    }
    function addLine(id, segments, color, width, dasharray=null, opacity=1) {
        const paths=segments.filter(s=>Array.isArray(s) && s.length>=2 && s.every(validPoint));
        if (!map || !paths.length || !map.isStyleLoaded()) return;
        map.addSource(id,{type:'geojson',data:{type:'Feature',properties:{},geometry:{type:'MultiLineString',coordinates:paths}}});
        map.addLayer({id,type:'line',source:id,layout:{'line-cap':'round','line-join':'round'},
            paint:{'line-color':color,'line-width':width,'line-opacity':opacity,...(dasharray?{'line-dasharray':dasharray}:{})}});
    }
    function drawLines(stops, legs, approximate) {
        const validLegs = legs.filter((leg,i) => Number(stops[i+1]?.sequence) === Number(stops[i]?.sequence)+1 && leg?.length>=2);
        addLine('sari-route-halo',validLegs,'#ffffff',10,approximate?[2,1.4]:null,.96);
        // A gray road line under the gold milestones, like the Order Details map.
        addLine('sari-full-route',validLegs,approximate?'#8995a3':'#65758a',5.5,approximate?[1.3,1.5]:null,.9);
        const completed = legs.filter((leg,i)=>leg?.length>=2 && stops[i+1]?.reached === true);
        addLine('sari-complete-route',completed,'#c88912',6,approximate?[1.3,1.5]:null);
        const next = legs.findIndex((leg,i) => leg?.length>=2 && !stops[i+1]?.reached
            && (i===0 || stops[i]?.reached));
        if (next>=0) addLine('sari-next-leg',[legs[next]],'#c88912',5,[1.15,1.35]);
    }
    function drawMarkers(route, stops) {
        stops.forEach(stop => {
            const el=document.createElement('span');
            el.className='sari-real-delivery-pin'
                +(stop.reached?' is-reached':' is-future')
                +(stop.type==='buyer'?' is-buyer':'')
                +(Number(stop.sequence)===Number(route.current_sequence)?' is-current':'');
            el.textContent=stopLabel(stop);
            el.setAttribute('aria-label',`${stopLabel(stop)}: ${String(stop.name || 'Delivery stop')}`);
            el.setAttribute('title',String(stop.name || 'Delivery stop'));
            const popupContent=document.createElement('div');
            popupContent.style.cssText='font-family:Poppins,system-ui,sans-serif;font-size:11px;line-height:1.5;color:#334155';
            const title=document.createElement('strong');
            title.textContent=String(stop.name || 'Route stop');
            popupContent.appendChild(title);
            if (stop.address) {
                const address=document.createElement('p');
                address.style.cssText='margin:5px 0;color:#64748b';
                address.textContent=String(stop.address);
                popupContent.appendChild(address);
            }
            const milestone=document.createElement('small');
            milestone.style.cssText='display:block;margin-top:5px;color:#996711';
            milestone.textContent=stop.reached ? `Arrival confirmed: ${formatDate(stop.reached_at)}` : 'Arrival not yet confirmed';
            popupContent.appendChild(milestone);
            const popup=new maplibregl.Popup({offset:16,maxWidth:'250px'}).setDOMContent(popupContent);
            markers.push(new maplibregl.Marker({element:el}).setLngLat(stopPoint(stop)).setPopup(popup).addTo(map));
        });
    }
    function fit(stops, legs=[]) {
        const points=legs.flat().filter(validPoint);
        const mapped=points.length ? points : stops.map(stopPoint);
        if (mapped.length>1) {
            const bounds=new maplibregl.LngLatBounds();
            mapped.forEach(p=>bounds.extend(p));
            map.fitBounds(bounds,{padding:{top:48,right:42,bottom:46,left:42},maxZoom:13,duration:0});
        } else if (mapped.length===1) map.jumpTo({center:mapped[0],zoom:12});
        else map.jumpTo({center:INITIAL_CENTER,zoom:5.3});
    }
    function joinedStepGeometry(steps) {
        const coords=[];
        for (const step of steps) {
            if (!Array.isArray(step?.geometry?.coordinates)) continue;
            for (const raw of step.geometry.coordinates) {
                if (!validPoint(raw)) return [];
                const point=[Number(raw[0]),Number(raw[1])];
                const last=coords[coords.length-1];
                if (!last || last[0]!==point[0] || last[1]!==point[1]) coords.push(point);
                if (coords.length>12000) return [];
            }
        }
        return coords;
    }
    async function getRoadLegs(stops, signal) {
        const panel=get('sariBuyerRealDelivery');
        const base=String(panel?.dataset.roadRouter || 'https://router.project-osrm.org').trim().replace(/\/+$/,'');
        if (!/^https:\/\//i.test(base)) throw new Error('Road routing service must use HTTPS.');
        const key=base+'|'+stops.map(stop=>stopPoint(stop).map(n=>n.toFixed(6)).join(',')).join(';');
        if (roadCache.has(key)) return roadCache.get(key);
        const url=`${base}/route/v1/driving/${stops.map(stop=>stopPoint(stop).join(',')).join(';')}?alternatives=false&steps=true&geometries=geojson&overview=full`;
        const timeout=new AbortController();
        const timeoutId=setTimeout(()=>timeout.abort(),12000);
        const propagate=()=>timeout.abort();
        signal?.addEventListener('abort',propagate,{once:true});
        try {
            const response=await fetch(url,{signal:timeout.signal,mode:'cors',credentials:'omit'});
            if (!response.ok) throw new Error(`Routing HTTP ${response.status}`);
            const data=await response.json();
            const route=data?.routes?.[0];
            if (data?.code!=='Ok' || !Array.isArray(route?.legs) || route.legs.length!==stops.length-1)
                throw new Error('No complete road itinerary returned');
            if (Array.isArray(data.waypoints) && data.waypoints.some(p=>Number(p?.distance || 0)>2500))
                throw new Error('A saved stop is too far from drivable roads');
            const legs=route.legs.map((leg,i)=>{
                const coords=joinedStepGeometry(leg.steps || []);
                if (coords.length>=2) return coords;
                const a=stopPoint(stops[i]), b=stopPoint(stops[i+1]);
                if (Math.abs(a[0]-b[0])<.000001 && Math.abs(a[1]-b[1])<.000001) return [a,b];
                return [];
            });
            if (legs.some(l=>l.length<2)) throw new Error('Road route geometry incomplete');
            roadCache.set(key,legs);
            return legs;
        } finally {
            clearTimeout(timeoutId);
            signal?.removeEventListener('abort',propagate);
        }
    }
    function approximateLegs(stops) {
        return stops.slice(0,-1).map((stop,i)=>
            Number(stops[i+1].sequence)===Number(stop.sequence)+1
                ? [stopPoint(stop),stopPoint(stops[i+1])] : []);
    }
    function draw(route) {
        const m=ensureMap();
        if (!m) return;
        const stops=coordinates(route);
        const generation=++renderGeneration;
        roadController?.abort();
        roadController=null;
        const render=async()=>{
            if (generation!==renderGeneration || !m.isStyleLoaded()) return;
            clearRoute();
            drawMarkers(route,stops);
            fit(stops);
            if (stops.length<2) {
                setInfo(stops.length?'One stop saved. Additional checkpoint coordinates are pending.':'Delivery checkpoint coordinates are not saved for this order yet.');
                status('Waiting for delivery checkpoint coordinates');
                return;
            }
            // Only request one connected road route if seller, both hubs, buyer are all present.
            const ready=route.ready===true && stops.length===4
                && stops.every((stop,i)=>Number(stop.sequence)===i+1);
            let legs=[];
            let approximate=false;
            if (ready) {
                const request=new AbortController();
                roadController=request;
                status('Finding road directions for saved delivery checkpoints');
                try {
                    legs=await getRoadLegs(stops,request.signal);
                    if (generation!==renderGeneration || request.signal.aborted) return;
                    setInfo(DEFAULT_ROUTE_INFO);
                    status('Road route ready. Arrival progress follows confirmed Logistics events.');
                } catch (_) {
                    if (generation!==renderGeneration || request.signal.aborted) return;
                    approximate=true;
                    legs=approximateLegs(stops);
                    setInfo('The road router could not provide directions. Dashed straight lines are approximate connections between saved checkpoints, not roads or live rider GPS.');
                    status('Approximate connections between saved stops');
                } finally {
                    if (roadController===request) roadController=null;
                }
            } else {
                approximate=true;
                legs=approximateLegs(stops);
                setInfo('More coordinates are being saved by Logistics. Dashed lines show approximate links between adjacent saved checkpoints only.');
                status('Partial saved delivery checkpoints');
            }
            if (generation!==renderGeneration) return;
            drawLines(stops,legs,approximate);
            fit(stops,legs);
        };
        if (m.isStyleLoaded()) render(); else m.once('load',render);
        requestAnimationFrame(()=>m.resize());
    }
    async function update() {
        const panel=get('sariBuyerRealDelivery');
        if (!panel || panel.hidden || !fetchUrl || document.hidden) return;
        dataController?.abort();
        const request=new AbortController();
        dataController=request;
        const generation=selectionGeneration;
        try {
            const response=await fetch(fetchUrl,{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},
                credentials:'same-origin',cache:'no-store',signal:request.signal});
            if (!response.ok) throw new Error(response.status===403 ? 'Tracking unavailable for this buyer.' : `Tracking temporarily unavailable (${response.status}).`);
            const data=await response.json();
            if (generation!==selectionGeneration || request.signal.aborted) return;
            const route=data.route || {};
            const signature=JSON.stringify({route,orderId:data.order_id});
            if (signature!==lastSignature) { lastSignature=signature; draw(route); }
        } catch (error) {
            if (request.signal.aborted || generation!==selectionGeneration) return;
            setInfo(error.message || 'Could not check delivery updates.');
            status('Tracking update unavailable');
        }
    }
    function select(card) {
        const panel=get('sariBuyerRealDelivery');
        if (!panel || !card) return;
        const step=Number(card.dataset.currentStep || 0);
        const visible=step>=2 && step<4 && card.dataset.orderStatus!=='cancelled';
        panel.hidden=!visible;
        const id=String(card.dataset.orderId || '');
        if (!visible) {
            ++selectionGeneration;
            ++renderGeneration;
            dataController?.abort(); roadController?.abort();
            selectedId=null;fetchUrl='';lastSignature='';clearRoute();showInfo(false);
            return;
        }
        if (selectedId===id && fetchUrl===card.dataset.trackingStateUrl) {
            if (map) requestAnimationFrame(()=>map.resize());
            return;
        }
        ++selectionGeneration;
        ++renderGeneration;
        dataController?.abort();roadController?.abort();
        selectedId=id;
        fetchUrl=card.dataset.trackingStateUrl || '';
        lastSignature='';
        clearRoute();
        setInfo(DEFAULT_ROUTE_INFO);
        showInfo(false);
        status('Loading selected delivery route');
        ensureMap();
        if (map) requestAnimationFrame(()=>map.resize());
        update();
    }
    function boot() {
        if (!get('sariBuyerRealDelivery')) return;
        const card=document.querySelector('.sari-order-card[data-tracking-selected="true"]') || document.querySelector('.sari-order-card');
        if (card) select(card);
        if (!timer) timer=setInterval(update,UPDATE_MS);
    }
    document.addEventListener('click',event=>{
        const btn=get('sariDeliveryMapInfo');
        if (btn && (event.target===btn || btn.contains(event.target))) {
            showInfo(btn.getAttribute('aria-expanded')!=='true');
        } else if (get('sariDeliveryMapNotice') && !get('sariDeliveryMapNotice').contains(event.target)) {
            showInfo(false);
        }
    });
    document.addEventListener('keydown',event=>{if(event.key==='Escape') showInfo(false);});
    window.addEventListener('sari:buyer-order-selected',event=>{
        const id=String(event.detail?.orderId || '');
        const card=Array.from(document.querySelectorAll('.sari-order-card')).find(item=>item.dataset.orderId===id);
        if (card) select(card);
    });
    if (document.readyState==='loading') document.addEventListener('DOMContentLoaded',boot,{once:true}); else boot();
    document.addEventListener('visibilitychange',()=>{if (!document.hidden) update();});
    window.addEventListener('pagehide',()=>{
        ++renderGeneration;
        dataController?.abort();roadController?.abort();
        if (timer) {clearInterval(timer);timer=null;}
    });
    window.addEventListener('pageshow',()=>{if(!timer) boot();});
})();
