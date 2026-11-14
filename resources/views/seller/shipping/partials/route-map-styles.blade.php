<style id="sariSellerShippingRouteMapCss">
.seller-shipping-detail-page .sari-seller-route-card{margin-top:14px;padding:20px 22px 18px;border:1px solid var(--sari-dark-d8c2716225, #e5e7eb);border-radius:18px;background:var(--sari-dark-950baec030, #fff);box-shadow:0 2px 5px var(--sari-dark-7593018246, rgba(15,23,42,.035)),0 12px 30px var(--sari-dark-361b571631, rgba(15,23,42,.06))}
.sari-seller-route-header{display:flex;align-items:start;justify-content:space-between;gap:16px;margin-bottom:18px}
.sari-seller-route-eyebrow{color:var(--sari-dark-685eb4fc40, #a87316);font-size:9px;font-weight:750;letter-spacing:.11em;line-height:1.3}
.sari-seller-route-header h2{margin-top:4px;color:var(--sari-dark-e23e64dd1e, #263343);font-size:19px;font-weight:750;letter-spacing:-.025em;line-height:1.2}
.sari-seller-route-header p:not(.sari-seller-route-eyebrow){margin-top:6px;color:var(--sari-dark-a63aa16d7c, #7b8795);font-size:11px;line-height:1.55}
.sari-seller-route-count{flex:none;border:1px solid var(--sari-dark-d8c2716225, #e5e9ee);background:var(--sari-dark-950baec030, #f8fafc);color:var(--sari-dark-a63aa16d7c, #526070);border-radius:999px;padding:7px 11px;font-size:10px;font-weight:650;white-space:nowrap}
.sari-seller-route-grid{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(260px,.78fr);gap:22px;align-items:start}
.sari-seller-route-map-side{min-width:0}
.sari-seller-route-map-wrap{position:relative;overflow:hidden;height:435px;border:1px solid var(--sari-dark-d8c2716225, #dfe5eb);border-radius:15px;background:var(--sari-dark-4d21eb669b, #edf2f5)}
#sariSellerDeliveryMap{height:100%;width:100%;min-height:435px}
#sariSellerDeliveryMap .maplibregl-canvas{outline:0}
#sariSellerDeliveryMap .maplibregl-ctrl-top-right{top:10px;right:10px}
#sariSellerDeliveryMap .maplibregl-ctrl-group{overflow:hidden;border:1px solid var(--sari-dark-d8c2716225, #e5e9ee);border-radius:9px;box-shadow:0 4px 14px var(--sari-dark-7c935c8886, rgba(15,23,42,.07))}
#sariSellerDeliveryMap .maplibregl-ctrl-group button{width:32px;height:32px}
#sariSellerDeliveryMap .maplibregl-ctrl-attrib{font:9px Poppins,sans-serif}
.sari-seller-map-marker{display:grid;width:34px;height:34px;place-items:center;border:3px solid var(--sari-dark-d8c2716225, #fff);border-radius:50%;background:#64748b;color:#fff;box-shadow:0 4px 15px var(--sari-dark-e267f3cebc, rgba(15,23,42,.22));font:750 11px Poppins,sans-serif;cursor:pointer}
.sari-seller-map-marker.is-buyer{background:#15803d}
.sari-seller-map-marker.is-future{background:#9ca3af;opacity:.8}
.sari-seller-map-marker.is-current{background:#c88912;box-shadow:0 0 0 5px var(--sari-dark-3793f9e558, rgba(200,137,18,.18)),0 4px 15px var(--sari-dark-e267f3cebc, rgba(15,23,42,.22))}
.sari-seller-map-loading{position:absolute;bottom:14px;right:14px;z-index:2;border:1px solid var(--sari-dark-d8c2716225, #e5e7eb);border-radius:9px;padding:9px 12px;background:var(--sari-dark-950baec030, #fff);color:var(--sari-dark-a63aa16d7c, #64748b);font-size:10px;box-shadow:0 4px 14px var(--sari-dark-c33e3d1bb4, rgba(15,23,42,.08))}
.sari-seller-map-loading.is-hidden{display:none}
.sari-seller-route-notice{position:absolute;top:14px;left:14px;right:64px;z-index:2;max-width:370px;border:1px solid var(--sari-dark-d8c2716225, #e1e6ec);border-radius:11px;padding:11px 13px;background:var(--sari-dark-d2fbbe29b8, rgba(255,255,255,.97));box-shadow:0 4px 16px var(--sari-dark-b12a5e9552, rgba(15,23,42,.09))}
.sari-seller-route-notice[hidden]{display:none}
.sari-seller-route-notice strong{display:block;color:var(--sari-dark-e23e64dd1e, #334155);font-size:11px;font-weight:700}
.sari-seller-route-notice small{display:block;margin-top:4px;color:var(--sari-dark-a63aa16d7c, #64748b);font-size:10px;line-height:1.45}
.sari-seller-route-legend{display:flex;align-items:center;flex-wrap:wrap;gap:8px 18px;padding:12px 1px 0;color:var(--sari-dark-a63aa16d7c, #64748b);font-size:10px;font-weight:500}
.sari-seller-route-legend>span{display:inline-flex;align-items:center;gap:7px;white-space:nowrap}
.sari-route-legend-line{display:inline-block;width:20px;border-top:4px solid var(--sari-dark-d8c2716225, #64748b);border-radius:4px}
.sari-route-legend-line.is-confirmed{border-top-color:#c88912}
.sari-route-legend-line.is-next{border-top:3px dashed #c88912}
.sari-seller-route-legend>small{flex-basis:100%;color:var(--sari-dark-a63aa16d7c, #8290a0);font-size:9.5px}
.sari-seller-route-side{min-width:0}
.sari-seller-route-party{padding:0 0 12px;border-bottom:1px solid var(--sari-dark-d8c2716225, #edf0f3)}
.sari-seller-route-party+.sari-seller-route-party{padding-top:13px}
.sari-seller-route-label{display:block;color:var(--sari-dark-a63aa16d7c, #8c97a5);font-size:9px;font-weight:750;letter-spacing:.08em}
.sari-seller-route-party strong{display:block;margin-top:5px;color:var(--sari-dark-e23e64dd1e, #2e3947);font-size:12px;font-weight:700;line-height:1.5;overflow-wrap:anywhere}
.sari-seller-route-party small{display:block;margin-top:4px;color:var(--sari-dark-a63aa16d7c, #7b8795);font-size:10px;line-height:1.55;overflow-wrap:anywhere}
.sari-seller-last-checkpoint{padding:12px 0;border-bottom:1px solid var(--sari-dark-d8c2716225, #edf0f3)}
.sari-seller-last-checkpoint strong{display:block;margin-top:5px;color:var(--sari-dark-e23e64dd1e, #293442);font-size:11px;line-height:1.45}
.sari-seller-last-checkpoint small{display:block;margin-top:3px;color:var(--sari-dark-a63aa16d7c, #8190a0);font-size:9.5px}
.sari-seller-route-stops-head{display:flex;align-items:baseline;justify-content:space-between;gap:7px;margin:15px 0 9px}
.sari-seller-route-stops-head strong{color:var(--sari-dark-e23e64dd1e, #2e3947);font-size:12px;font-weight:700}
.sari-seller-route-stops-head small{color:var(--sari-dark-a63aa16d7c, #8a95a2);font-size:9px}
.sari-seller-route-stops{margin:0;padding:0;list-style:none}
.sari-seller-route-stop{display:grid;grid-template-columns:24px minmax(0,1fr) auto;gap:9px;align-items:center;min-height:46px;padding:7px 0;border-bottom:1px solid var(--sari-dark-d8c2716225, #f0f2f5)}
.sari-seller-route-stop-dot{display:grid;place-items:center;width:23px;height:23px;border:1px solid var(--sari-dark-d8c2716225, #e0e5ea);border-radius:50%;color:var(--sari-dark-a63aa16d7c, #8a95a2);background:var(--sari-dark-950baec030, #f8fafc);font-size:10px;font-weight:750}
.sari-seller-route-stop.is-confirmed .sari-seller-route-stop-dot{background:#c88912;border-color:#c88912;color:#fff}
.sari-seller-route-stop.is-current .sari-seller-route-stop-dot{border-color:#c88912;color:var(--sari-dark-6e784b4931, #a46b0c);background:var(--sari-dark-950baec030, #fff)}
.sari-seller-route-stop div{min-width:0}
.sari-seller-route-stop div strong{display:block;color:var(--sari-dark-e23e64dd1e, #475569);font-size:10.5px;font-weight:700}
.sari-seller-route-stop div span{display:block;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--sari-dark-a63aa16d7c, #8a95a2);font-size:9.5px}
.sari-seller-route-stop>small{font-size:9px;font-weight:650;color:var(--sari-dark-a63aa16d7c, #8b95a3)}
.sari-seller-route-stop.is-confirmed>small{color:var(--sari-dark-9409b20329, #15803d)}
.sari-seller-route-stop.is-current>small{color:var(--sari-dark-af18afcaa4, #a56d0a)}
.sari-seller-route-stop-empty{padding:12px 0;color:var(--sari-dark-a63aa16d7c, #8793a2);font-size:10px}
.sari-seller-route-disclaimer{margin-top:14px;color:var(--sari-dark-a63aa16d7c, #8893a0);font-size:9px;line-height:1.65}
@media(max-width:1100px){.sari-seller-route-grid{grid-template-columns:1fr}.sari-seller-route-side{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px 18px}.sari-seller-route-party+.sari-seller-route-party{padding-top:0}.sari-seller-last-checkpoint,.sari-seller-route-stops-head,.sari-seller-route-stops,.sari-seller-route-disclaimer{grid-column:1/-1}}
@media(max-width:640px){.seller-shipping-detail-page .sari-seller-route-card{padding:15px;border-radius:15px}.sari-seller-route-header{flex-direction:column;gap:10px}.sari-seller-route-header h2{font-size:17px}.sari-seller-route-count{white-space:normal}.sari-seller-route-map-wrap{height:335px;border-radius:12px}#sariSellerDeliveryMap{min-height:335px}.sari-seller-route-side{display:block}.sari-seller-route-party+.sari-seller-route-party{padding-top:12px}.sari-seller-route-stops-head{margin-top:15px}.sari-seller-route-legend{gap:9px 13px}.sari-seller-route-notice{right:52px;top:10px;left:10px}}
@media(prefers-reduced-motion:reduce){.sari-seller-route-card *{animation:none!important;transition:none!important}}
</style>
