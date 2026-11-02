<style id="sariSellerOrderModalCss">
@verbatim
/* Standalone modal design — scoped to seller Order Management. */
#sellerOrderDetailModal {
    position: fixed !important;
    inset: 0 !important;
    z-index: 120 !important;
    align-items: center;
    justify-content: center;
    background: rgba(22, 28, 38, .46) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
    padding: 18px !important;
    font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
}
#sellerOrderDetailModal *, #sellerOrderDetailModal *::before, #sellerOrderDetailModal *::after { box-sizing: border-box; }
#sellerOrderDetailModal .sari-order-dialog__backdrop { position: absolute; inset: 0; border: 0; background: transparent; cursor: default; }
#sellerOrderDetailModal .sari-order-dialog__mount { position: relative; z-index: 1; display: flex; width: 100%; max-height: 100%; justify-content: center; pointer-events: none; }
#sellerOrderDetailModal .sari-order-dialog {
    display: flex; flex-direction: column; width: min(840px, 100%); min-width: 0;
    max-height: min(880px, calc(100dvh - 36px)); overflow: hidden; pointer-events: auto;
    border: 1px solid #e4e8ee; border-radius: 22px; background: #fff;
    box-shadow: 0 30px 90px rgba(15,23,42,.25), 0 5px 20px rgba(15,23,42,.07);
    color: #293440;
}
#sellerOrderDetailModal .sari-order-dialog__head {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 20px;
    flex: 0 0 auto; padding: 25px 28px 18px; background: #fff;
}
#sellerOrderDetailModal .sari-order-dialog__head-copy { min-width: 0; }
#sellerOrderDetailModal .sari-order-dialog__eyebrow { margin: 0 0 6px; font-size: 10px; font-weight: 800; letter-spacing: .12em; color: #a87316; }
#sellerOrderDetailModal .sari-order-dialog__title { margin: 0; font-size: 26px; line-height: 1.2; font-weight: 750; letter-spacing: -.035em; color: #20252c; }
#sellerOrderDetailModal .sari-order-dialog__intro { margin: 7px 0 0; color: #747d89; font-size: 11.5px; line-height: 1.55; }
#sellerOrderDetailModal .sari-order-dialog__close {
    display: grid; width: 36px; height: 36px; flex: 0 0 36px; place-items: center;
    border: 1px solid #e4e8ee; border-radius: 10px; background: #fff; color: #677384;
    cursor: pointer; transition: background .15s ease, border-color .15s ease, color .15s ease;
}
#sellerOrderDetailModal .sari-order-dialog__close svg { width: 17px; height: 17px; }
#sellerOrderDetailModal .sari-order-dialog__close:hover { background: #f8fafc; border-color: #cbd5e1; color: #293440; }
#sellerOrderDetailModal .sari-order-dialog__scroll { flex: 1 1 auto; min-height: 0; overflow: auto; padding: 0 28px 23px; overscroll-behavior: contain; }
#sellerOrderDetailModal .sari-order-dialog__section { padding-top: 18px; padding-bottom: 20px; border-top: 1px solid #edf0f3; }
#sellerOrderDetailModal .sari-order-dialog__section-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
#sellerOrderDetailModal .sari-order-dialog__section h3 { margin: 0 0 15px; font-size: 14px; font-weight: 750; line-height: 1.3; color: #313943; }
#sellerOrderDetailModal .sari-order-dialog__section-head h3 { margin-bottom: 15px; }
#sellerOrderDetailModal .sari-order-dialog__section-head .sari-order-dialog__status,
#sellerOrderDetailModal .sari-order-dialog__section-head .sari-order-dialog__subtle,
#sellerOrderDetailModal .sari-order-dialog__section-head .sari-order-dialog__percent { margin-bottom: 15px; }
#sellerOrderDetailModal .sari-order-dialog__status {
    display: inline-flex; min-height: 28px; align-items: center; padding: 4px 12px;
    border: 1px solid #e0e6ec; border-radius: 99px; background: #f8fafc; font-size: 10px; font-weight: 700;
}
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="new"],
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="ready_for_pickup"] { border-color: #e7d5b0; color: #a87316; background: #fff; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="preparing"],
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="in_transit"],
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="arrived_buyer"] { border-color: #d3e2f1; color: #47749b; background: #f7fbff; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="delivered"] { border-color: #cfe4d7; color: #317a55; background: #f4fbf7; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="cancelled"] { border-color: #efcece; color: #a65353; background: #fff6f6; }
#sellerOrderDetailModal .sari-order-dialog__subtle { font-size: 11px; font-weight: 600; color: #8994a2; }
#sellerOrderDetailModal .sari-order-dialog__fields { display: grid; gap: 15px 18px; }
#sellerOrderDetailModal .sari-order-dialog__fields--two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
#sellerOrderDetailModal .sari-order-dialog__field { display: flex; flex-direction: column; gap: 7px; min-width: 0; }
#sellerOrderDetailModal .sari-order-dialog__field--full { grid-column: 1 / -1; }
#sellerOrderDetailModal .sari-order-dialog__label { display: block; font-size: 11.5px; font-weight: 600; color: #5c6470; }
#sellerOrderDetailModal .sari-order-dialog__readout {
    display: flex; align-items: center; min-height: 48px; min-width: 0;
    padding: 11px 15px; border: 1px solid #dfe4eb; border-radius: 12px;
    background: #fff; color: #303946; font-size: 12px; font-weight: 500; line-height: 1.55;
    overflow-wrap: anywhere;
}
#sellerOrderDetailModal .sari-order-dialog__readout--strong { font-weight: 700; color: #1f2937; }
#sellerOrderDetailModal .sari-order-dialog__readout--multiline { min-height: 66px; align-items: flex-start; }
#sellerOrderDetailModal .sari-order-dialog__items { display: grid; gap: 9px; }
#sellerOrderDetailModal .sari-order-dialog__item {
    display: grid; grid-template-columns: 54px minmax(0,1fr) auto; gap: 12px; align-items: center;
    min-width: 0; padding: 10px 13px; border: 1px solid #e5e9ee; border-radius: 12px; background: #fcfdfe;
}
#sellerOrderDetailModal .sari-order-dialog__item-image { width: 54px; height: 54px; overflow: hidden; border-radius: 9px; border: 1px solid #e7eaf0; background: #fff; }
#sellerOrderDetailModal .sari-order-dialog__item-image img { width: 100%; height: 100%; object-fit: contain; }
#sellerOrderDetailModal .sari-order-dialog__item-placeholder { display: grid; width: 100%; height: 100%; place-items: center; color: #9ca6b2; }
#sellerOrderDetailModal .sari-order-dialog__item-placeholder svg { width: 20px; height: 20px; }
#sellerOrderDetailModal .sari-order-dialog__item-placeholder.hidden, #sellerOrderDetailModal .sari-order-dialog__item-image img.hidden { display: none !important; }
#sellerOrderDetailModal .sari-order-dialog__item-copy { display: grid; min-width: 0; gap: 3px; }
#sellerOrderDetailModal .sari-order-dialog__item-copy strong { font-size: 12px; font-weight: 700; line-height: 1.4; color: #293440; overflow-wrap: anywhere; }
#sellerOrderDetailModal .sari-order-dialog__item-copy span { font-size: 10.5px; color: #778392; line-height: 1.4; overflow-wrap: anywhere; }
#sellerOrderDetailModal .sari-order-dialog__item-price { color: #293440; font-size: 12px; font-weight: 750; white-space: nowrap; }
#sellerOrderDetailModal .sari-order-dialog__items-empty { padding: 28px 12px; text-align: center; font-size: 12px; color: #7f8996; }
#sellerOrderDetailModal .sari-order-dialog__lower { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
#sellerOrderDetailModal .sari-order-dialog__lower .sari-order-dialog__section { min-width: 0; }
#sellerOrderDetailModal .sari-order-dialog__money { display: grid; gap: 13px; margin: 0; }
#sellerOrderDetailModal .sari-order-dialog__money > div { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; font-size: 11.5px; color: #647184; }
#sellerOrderDetailModal .sari-order-dialog__money dt { margin: 0; }
#sellerOrderDetailModal .sari-order-dialog__money dd { margin: 0; color: #293440; font-weight: 650; white-space: nowrap; }
#sellerOrderDetailModal .sari-order-dialog__money-total { padding-top: 15px; border-top: 1px solid #e9edf2; font-weight: 750; }
#sellerOrderDetailModal .sari-order-dialog__money-total dt { color: #303b48; }
#sellerOrderDetailModal .sari-order-dialog__money-total dd { color: #c88912; font-size: 17px; font-weight: 800; }
#sellerOrderDetailModal .sari-order-dialog__percent { color: #a87316; font-size: 12px; font-weight: 750; }
#sellerOrderDetailModal .sari-order-dialog__progress { height: 6px; overflow: hidden; border-radius: 99px; background: #edf0f4; }
#sellerOrderDetailModal .sari-order-dialog__progress span { display: block; height: 100%; border-radius: inherit; background: #c88912; }
#sellerOrderDetailModal .sari-order-dialog__steps { display: grid; gap: 7px; margin: 13px 0 0; padding: 0; list-style: none; }
#sellerOrderDetailModal .sari-order-dialog__steps li { display: flex; gap: 8px; align-items: center; color: #8a95a3; font-size: 10.5px; line-height: 1.4; }
#sellerOrderDetailModal .sari-order-dialog__step-dot { width: 7px; height: 7px; flex: 0 0 7px; border-radius: 50%; background: #d7dee6; }
#sellerOrderDetailModal .sari-order-dialog__steps li.is-complete { color: #465362; font-weight: 600; }
#sellerOrderDetailModal .sari-order-dialog__steps li.is-complete .sari-order-dialog__step-dot { background: #c88912; }
#sellerOrderDetailModal .sari-order-dialog__footer {
    display: flex; flex: 0 0 auto; align-items: center; justify-content: space-between; gap: 16px;
    padding: 16px 28px 20px; border-top: 1px solid #e9edf2; background: #fff;
}
#sellerOrderDetailModal .sari-order-dialog__next { display: grid; min-width: 0; gap: 3px; }
#sellerOrderDetailModal .sari-order-dialog__next-label { color: #a87316; font-size: 9px; letter-spacing: .1em; font-weight: 800; }
#sellerOrderDetailModal .sari-order-dialog__next strong { color: #303b48; font-size: 11px; font-weight: 700; }
#sellerOrderDetailModal .sari-order-dialog__footer-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; align-items: center; gap: 8px; }
#sellerOrderDetailModal .sari-order-dialog__footer-actions form { margin: 0; }
#sellerOrderDetailModal .sari-order-dialog__button {
    display: inline-flex; align-items: center; justify-content: center; min-height: 42px;
    padding: 0 14px; border: 1px solid transparent; border-radius: 11px;
    font: 700 11px/1.2 Poppins, sans-serif; text-decoration: none; white-space: nowrap;
    cursor: pointer; transition: background-color .14s ease, border-color .14s ease, color .14s ease;
}
#sellerOrderDetailModal .sari-order-dialog__button--light { border-color: #dfe5eb; background: #fff; color: #4e5968; }
#sellerOrderDetailModal .sari-order-dialog__button--light:hover { border-color: #cbd5e1; background: #f8fafc; }
#sellerOrderDetailModal .sari-order-dialog__button--gold { border-color: #c88912; background: #c88912; color: #fff; }
#sellerOrderDetailModal .sari-order-dialog__button--gold:hover { border-color: #b47b0e; background: #b47b0e; }
#sellerOrderDetailModal .sari-order-dialog__current-status { font-size: 11px; font-weight: 600; color: #657184; }
#sellerOrderDetailModal button:focus-visible, #sellerOrderDetailModal a:focus-visible {
    outline: 2px solid #c88912 !important; outline-offset: 3px;
}
@media (max-width: 700px) {
    #sellerOrderDetailModal { padding: 10px !important; }
    #sellerOrderDetailModal .sari-order-dialog { border-radius: 16px; max-height: calc(100dvh - 20px); }
    #sellerOrderDetailModal .sari-order-dialog__head { padding: 18px 18px 15px; }
    #sellerOrderDetailModal .sari-order-dialog__title { font-size: 22px; }
    #sellerOrderDetailModal .sari-order-dialog__scroll { padding: 0 18px 14px; }
    #sellerOrderDetailModal .sari-order-dialog__fields--two, #sellerOrderDetailModal .sari-order-dialog__lower { grid-template-columns: 1fr; gap: 12px; }
    #sellerOrderDetailModal .sari-order-dialog__field--full { grid-column: auto; }
    #sellerOrderDetailModal .sari-order-dialog__section { padding-top: 16px; padding-bottom: 17px; }
    #sellerOrderDetailModal .sari-order-dialog__footer { align-items: stretch; flex-direction: column; padding: 13px 18px 17px; }
    #sellerOrderDetailModal .sari-order-dialog__footer-actions { justify-content: stretch; }
    #sellerOrderDetailModal .sari-order-dialog__footer-actions > *, #sellerOrderDetailModal .sari-order-dialog__footer-actions form { flex: 1 1 auto; }
    #sellerOrderDetailModal .sari-order-dialog__footer-actions form .sari-order-dialog__button { width: 100%; }
}
@media (max-width: 420px) {
    #sellerOrderDetailModal .sari-order-dialog__item { grid-template-columns: 46px minmax(0,1fr); gap: 8px; }
    #sellerOrderDetailModal .sari-order-dialog__item-image { width: 46px; height: 46px; }
    #sellerOrderDetailModal .sari-order-dialog__item-price { grid-column: 2; }
    #sellerOrderDetailModal .sari-order-dialog__footer-actions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    #sellerOrderDetailModal .sari-order-dialog__footer-actions form { grid-column: 1 / -1; }
}
@media (prefers-reduced-motion: reduce) {
    #sellerOrderDetailModal .sari-order-dialog__button, #sellerOrderDetailModal .sari-order-dialog__close { transition: none; }
}

/* V2: clear action, refined product rows, address map, payment and fulfillment */
#sellerOrderDetailModal .sari-order-dialog { width: min(900px,100%); border-radius: 18px; }
#sellerOrderDetailModal .sari-order-dialog__head { padding: 20px 25px 16px; }
#sellerOrderDetailModal .sari-order-dialog__scroll { padding: 0 25px 20px; }
#sellerOrderDetailModal .sari-order-dialog__section { padding: 16px 0 18px; }
#sellerOrderDetailModal .sari-order-dialog__section h3 { font-size: 14px; margin-bottom: 11px; }
#sellerOrderDetailModal .sari-order-dialog__section-head h3 { margin-bottom: 8px; }
#sellerOrderDetailModal .sari-order-dialog__section-head .sari-order-dialog__subtle,
#sellerOrderDetailModal .sari-order-dialog__section-head .sari-order-dialog__percent,
#sellerOrderDetailModal .sari-order-dialog__section-head .sari-order-dialog__status { margin-bottom: 8px; }
#sellerOrderDetailModal .sari-order-dialog__section-caption { font-size: 10.5px; line-height: 1.5; margin: 0 0 11px; color: #8793A1; }
#sellerOrderDetailModal .sari-order-dialog__map-section .sari-order-dialog__section-head { align-items: start; }
#sellerOrderDetailModal .sari-order-dialog__map-link { display: inline-flex; gap: 6px; align-items: center; min-height: 26px; color: #A56D0A; font-size: 11px; font-weight: 700; text-decoration: none; white-space: nowrap; }
#sellerOrderDetailModal .sari-order-dialog__map-link:hover { text-decoration: underline; }
#sellerOrderDetailModal .sari-order-dialog__map-frame { height: 210px; border: 1px solid #E2E7ED; border-radius: 12px; background: #F2F5F8; overflow: hidden; }
#sellerOrderDetailModal .sari-order-dialog__map-frame iframe { width: 100%; height: 100%; border: 0; display: block; }
#sellerOrderDetailModal .sari-order-dialog__map-unavailable { display: grid; place-items: center; padding: 30px; min-height: 90px; border: 1px dashed #DCE2E8; border-radius: 12px; background: #F8FAFC; color: #748293; font-size: 11px; }
#sellerOrderDetailModal .sari-order-dialog__items { border: 1px solid #E5E9EE; border-radius: 13px; overflow: hidden; gap: 0; background: #fff; }
#sellerOrderDetailModal .sari-order-dialog__item { border: 0; border-radius: 0; border-bottom: 1px solid #EDF0F3; padding: 13px; gap: 13px; background: #fff; grid-template-columns: 66px minmax(0,1fr) auto; }
#sellerOrderDetailModal .sari-order-dialog__item:last-child { border-bottom: 0; }
#sellerOrderDetailModal .sari-order-dialog__item-image { width: 66px; height: 66px; border-radius: 10px; background: #F8FAFC; }
#sellerOrderDetailModal .sari-order-dialog__item-copy { gap: 5px; }
#sellerOrderDetailModal .sari-order-dialog__item-copy strong { font-size: 12.5px; }
#sellerOrderDetailModal .sari-order-dialog__item-copy .sari-order-dialog__item-variant { width: fit-content; color: #6F7B8B; font-size: 10px; line-height: 1.45; }
#sellerOrderDetailModal .sari-order-dialog__item-copy .sari-order-dialog__item-qty { font-size: 10.5px; color: #6D7887; }
#sellerOrderDetailModal .sari-order-dialog__item-qty b { font-weight: 700; color: #334155; }
#sellerOrderDetailModal .sari-order-dialog__item-amount { align-self: center; display: grid; text-align: right; gap: 5px; }
#sellerOrderDetailModal .sari-order-dialog__item-amount span { font-size: 10px; color: #98A2B3; }
#sellerOrderDetailModal .sari-order-dialog__item-price { font-size: 13px; font-weight: 800; color: #293440; }
#sellerOrderDetailModal .sari-order-dialog__lower { align-items: stretch; gap: 12px; }
#sellerOrderDetailModal .sari-order-dialog__lower .sari-order-dialog__section { padding: 15px 17px 17px; border: 1px solid #E4E9EF; border-radius: 13px; background: #FFF; }
#sellerOrderDetailModal .sari-order-dialog__money { gap: 12px; }
#sellerOrderDetailModal .sari-order-dialog__money > div { font-size: 11.5px; }
#sellerOrderDetailModal .sari-order-dialog__money-total { margin-top: 4px; border-color: #E9EEF3; }
#sellerOrderDetailModal .sari-order-dialog__money-total dd { font-size: 19px; }
#sellerOrderDetailModal .sari-order-dialog__progress-caption { margin: 9px 0 8px; }
#sellerOrderDetailModal .sari-order-dialog__steps { display: grid; grid-template-columns: repeat(5,minmax(0,1fr)); gap: 5px; margin-top: 10px; }
#sellerOrderDetailModal .sari-order-dialog__steps li { flex-direction: column; align-items: center; gap: 5px; font-size: 9px; text-align: center; }
#sellerOrderDetailModal .sari-order-dialog__step-dot { display: grid; place-items: center; width: 19px; height: 19px; border-radius: 50%; flex: 0 0 19px; background: #E6EBF0; color: #fff; font-size: 10px; line-height: 1; font-weight: 800; }
#sellerOrderDetailModal .sari-order-dialog__steps li.is-complete .sari-order-dialog__step-dot { background: #C88912; }
#sellerOrderDetailModal .sari-order-dialog__footer { padding: 12px 25px 14px; }
#sellerOrderDetailModal .sari-order-dialog__button--gold,
#sellerOrderDetailModal .sari-order-dialog__button--gold:visited,
#sellerOrderDetailModal .sari-order-dialog__button--gold:focus-visible,
#sellerOrderDetailModal [data-ready-for-pickup] { background-color: #C88912 !important; border: 1px solid #C88912 !important; color: #FFFFFF !important; font-weight: 750 !important; box-shadow: 0 4px 12px rgba(200,137,18,.12); }
#sellerOrderDetailModal .sari-order-dialog__button--gold:hover,
#sellerOrderDetailModal [data-ready-for-pickup]:hover { background: #B47B0E !important; border-color: #B47B0E !important; color: #FFFFFF !important; }
@media (max-width: 700px) {
    #sellerOrderDetailModal .sari-order-dialog { width: 100%; }
    #sellerOrderDetailModal .sari-order-dialog__head { padding: 17px 17px 14px; }
    #sellerOrderDetailModal .sari-order-dialog__scroll { padding: 0 17px 16px; }
    #sellerOrderDetailModal .sari-order-dialog__map-frame { height: 190px; }
    #sellerOrderDetailModal .sari-order-dialog__lower { grid-template-columns: 1fr; }
    #sellerOrderDetailModal .sari-order-dialog__footer { padding: 12px 17px 15px; }
}
@media (max-width: 420px) {
    #sellerOrderDetailModal .sari-order-dialog__item { grid-template-columns: 52px minmax(0,1fr); gap: 10px; }
    #sellerOrderDetailModal .sari-order-dialog__item-image { width: 52px; height: 52px; }
    #sellerOrderDetailModal .sari-order-dialog__item-amount { grid-column: 2; text-align: left; }
}


/* V3 status semantics mirror the order table, without modifying modal workflow. */
#sellerOrderDetailModal .sari-order-dialog__status {
    border: 1px solid #DCE3EC !important;
    background: #F1F5F9 !important;
    color: #475569 !important;
}
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="preparing"] { background: #EFF6FF !important; border-color: #BFDBFE !important; color: #1D4ED8 !important; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="ready_for_pickup"] { background: #F8FAFC !important; border-color: #E2D3B1 !important; color: #A56D0A !important; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="courier_accepted"],
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="heading_pickup"],
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="arrived_pickup"] { background: #F5F3FF !important; border-color: #DDD6FE !important; color: #6D28D9 !important; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="in_transit"] { background: #E8F4FF !important; border-color: #BAE0FF !important; color: #075985 !important; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="arrived_buyer"] { background: #ECFEFF !important; border-color: #A5F3FC !important; color: #0E7490 !important; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="delivered"] { background: #ECFDF3 !important; border-color: #BBF7D0 !important; color: #166534 !important; }
#sellerOrderDetailModal .sari-order-dialog__status[data-order-status="cancelled"] { background: #FEF2F2 !important; border-color: #FECACA !important; color: #B91C1C !important; }

@endverbatim
</style>
