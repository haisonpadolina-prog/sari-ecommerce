@extends('layouts.admin')

@section('title', 'Users — SARI Admin')
@section('page-title', 'User Management')
@include('admin.partials.users.summary-icon-styles')
@section('content')
@include('admin.partials.users.page-styles')
@include('admin.partials.users.table-styles')
@include('admin.partials.users.modal-styles')

{{-- User Management visual parity with Seller Orders; scoped to this page only. --}}
<style id="sari-user-management-orders-parity">
.user-page {
    width: 100% !important;
    max-width: 1520px !important;
    margin-inline: auto !important;
    padding-bottom: 28px !important;
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important;
    color: var(--sari-dark-e23e64dd1e, #2F3742) !important;
    --um-gold: #C88912;
    --um-gray: #667383;
    --um-border: var(--sari-dark-d8c2716225, #E5E7EB);
}
.user-page .user-page-header {
    min-height: 0 !important;
    align-items: flex-end !important;
    gap: 14px 24px !important;
    margin: 0 0 20px !important;
    padding: 0 0 14px !important;
    border-bottom: 1px solid var(--sari-dark-d8c2716225, #E9EDF1) !important;
}
.user-page .user-page-header-icon { display: none !important; }
.user-page .user-page-eyebrow {
    font-size: 10.5px !important;
    font-weight: 800 !important;
    line-height: 1.4 !important;
    letter-spacing: .14em !important;
    color: var(--sari-dark-685eb4fc40, #A87316) !important;
}
.user-page .user-page-title {
    margin-top: 6px !important;
    font-size: clamp(27px, 2.3vw, 31px) !important;
    font-weight: 760 !important;
    line-height: 1.08 !important;
    letter-spacing: -.04em !important;
    color: var(--sari-dark-e23e64dd1e, #2E343B) !important;
}
.user-page .user-page-title-accent { color: var(--um-gold) !important; }
.user-page .user-page-subtitle {
    margin-top: 6px !important;
    max-width: 780px !important;
    font-size: 11.5px !important;
    line-height: 1.5 !important;
    color: var(--sari-dark-a63aa16d7c, #8793A1) !important;
}
.user-page .user-page-export {
    min-height: 40px !important;
    padding-inline: 15px !important;
    border: 1px solid var(--sari-dark-d8c2716225, #DCE2E8) !important;
    border-radius: 11px !important;
    background: var(--sari-dark-950baec030, #fff) !important;
    color: var(--sari-dark-a63aa16d7c, #59636E) !important;
    font-size: 11px !important;
    box-shadow: none !important;
    transform: none !important;
}
.user-page .user-page-export:hover { border-color: var(--sari-dark-d8c2716225, #C5CED8) !important; background: var(--sari-dark-950baec030, #F8FAFC) !important; color: var(--sari-dark-e23e64dd1e, #293440) !important; }
.user-page > section.grid { gap: 14px !important; }
.user-page .metric-card,
.user-page .metric-card:hover {
    min-height: 110px !important;
    border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB) !important;
    border-radius: 18px !important;
    background: var(--sari-dark-950baec030, #fff) !important;
    padding: 14px 16px !important;
    box-shadow: 0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)),0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055)) !important;
    transform: none !important;
}
.user-page .metric-card > div > div > p:first-child { color: var(--sari-dark-a63aa16d7c, #637081) !important; font-size: 13px !important; font-weight: 650 !important; line-height: 1.4 !important; }
.user-page .metric-card > div > div > p:nth-child(2) { margin-top: 7px !important; font-size: clamp(25px,1.9vw,30px) !important; font-weight: 650 !important; line-height: 1.12 !important; color: var(--sari-dark-e23e64dd1e, #1F2937) !important; }
.user-page .metric-card > div > div > p:nth-child(3) { margin-top: 6px !important; font-size: 11px !important; color: var(--sari-dark-a63aa16d7c, #8995A3) !important; }
/* Freestanding colored icons, without a colored icon tile. */
.user-page .metric-card > div > span:first-child {
    top: 16px !important; right: 16px !important;
    width: 27px !important; height: 27px !important;
    padding: 0 !important; border: 0 !important;
    border-radius: 0 !important; background: transparent !important;
    box-shadow: none !important;
}
.user-page .metric-card > div > span:first-child svg { width: 27px !important; height: 27px !important; stroke-width: 1.8 !important; }
/* Standalone filter controls rather than a floating cream container. */
.user-page > section.user-surface.mt-4.p-3 {
    z-index: 20 !important;
    margin-top: 20px !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    overflow: visible !important;
}
.user-page > section.user-surface.mt-4.p-3 > div {
    display: grid !important;
    grid-template-columns: minmax(0,1fr) 210px 190px auto auto !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
.user-page [data-role-dropdown], .user-page [data-status-dropdown] { width: 100% !important; min-width: 0 !important; }
.user-page #userSearch, .user-page [data-role-toggle], .user-page [data-status-toggle],
.user-page #applyFilter, .user-page #resetFilter {
    height: 48px !important; min-height: 48px !important;
    border-radius: 14px !important; font-size: 11.5px !important;
    transform: none !important;
}
.user-page #userSearch, .user-page [data-role-toggle], .user-page [data-status-toggle] {
    border: 1px solid var(--sari-dark-d8c2716225, #DCE2E8) !important;
    background: var(--sari-dark-950baec030, #fff) !important;
    color: var(--sari-dark-e23e64dd1e, #3F4B59) !important;
    font-weight: 600 !important;
    box-shadow: none !important;
}
.user-page #userSearch { padding: 0 14px 0 46px !important; }
.user-page #userSearch::placeholder { color: var(--sari-dark-a63aa16d7c, #A0AAB6) !important; font-size: 11.5px !important; }
.user-page > section.user-surface.mt-4.p-3 .relative > svg.pointer-events-none {
    left: 16px !important; width: 21px !important; height: 21px !important;
    color: var(--sari-dark-a63aa16d7c, #97A3B2) !important;
}
.user-page [data-role-toggle], .user-page [data-status-toggle] { padding-inline: 16px !important; }
.user-page [data-role-toggle] > svg, .user-page [data-status-toggle] > svg {
    width: 17px !important; height: 17px !important; color: var(--sari-dark-a63aa16d7c, #7C8794) !important;
}
.user-page #userSearch:hover, .user-page [data-role-toggle]:hover, .user-page [data-status-toggle]:hover {
    background: var(--sari-dark-950baec030, #F8FAFC) !important; border-color: var(--sari-dark-d8c2716225, #CFD6DE) !important;
}
.user-page #userSearch:focus, .user-page [data-role-toggle]:focus-visible, .user-page [data-status-toggle]:focus-visible {
    background: var(--sari-dark-950baec030, #fff) !important; border-color: var(--um-gold) !important;
    box-shadow: 0 0 0 2px var(--sari-dark-8eadf403a5, rgba(200,137,18,.1)) !important;
}
.user-page .custom-menu {
    top: calc(100% + 8px) !important;
    padding: 7px !important;
    border: 1px solid var(--sari-dark-d8c2716225, #E2E8F0) !important;
    border-radius: 14px !important;
    background: var(--sari-dark-950baec030, #fff) !important;
    box-shadow: 0 16px 32px var(--sari-dark-3b2338cbf0, rgba(15,23,42,.12)),0 2px 6px var(--sari-dark-1df26971b5, rgba(15,23,42,.05)) !important;
}
.user-page [data-role-option], .user-page [data-status-option] {
    min-height: 38px !important; padding: 0 12px !important;
    border-radius: 10px !important; background: var(--sari-dark-950baec030, #fff) !important;
    color: var(--sari-dark-e23e64dd1e, #475569) !important; font-size: 11.5px !important;
    font-weight: 600 !important;
}
.user-page [data-role-option]:hover, .user-page [data-status-option]:hover,
.user-page [data-role-option]:focus-visible, .user-page [data-status-option]:focus-visible {
    outline: none !important; background: var(--sari-dark-4d21eb669b, #F3F5F7) !important; color: var(--sari-dark-e23e64dd1e, #293440) !important;
}
.user-page [data-role-option]:has([data-check]:not(.opacity-0)),
.user-page [data-status-option]:has([data-check]:not(.opacity-0)) {
    background: var(--sari-dark-950baec030, #F8FAFC) !important; color: var(--sari-dark-e23e64dd1e, #293440) !important;
}
.user-page [data-role-option] [data-check], .user-page [data-status-option] [data-check] { background: #7C8794 !important; }
.user-page #applyFilter, .user-page #resetFilter {
    padding-inline: 18px !important; font-size: 12px !important; font-weight: 700 !important;
    box-shadow: none !important;
}
.user-page #applyFilter { min-width: 130px !important; border-color: var(--um-gold) !important; background: var(--um-gold) !important; color: #fff !important; }
.user-page #applyFilter:hover { border-color: #B47B0E !important; background: #B47B0E !important; }
.user-page #resetFilter { min-width: 86px !important; border: 1px solid var(--sari-dark-d8c2716225, #DCE2E8) !important; background: var(--sari-dark-950baec030, #fff) !important; color: var(--sari-dark-a63aa16d7c, #5F6D7C) !important; }
.user-page #resetFilter:hover { background: var(--sari-dark-950baec030, #F8FAFC) !important; border-color: var(--sari-dark-d8c2716225, #CFD6DE) !important; }
/* One full-width accounts workspace. */
.user-page > section.user-surface.mt-4.overflow-hidden {
    margin-top: 20px !important;
    overflow: hidden !important;
    border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB) !important;
    border-radius: 18px !important;
    background: var(--sari-dark-950baec030, #fff) !important;
    box-shadow: 0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)),0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055)) !important;
    contain: none !important;
}
.user-page .user-table-titlebar {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
    padding: 21px 23px; border-bottom: 1px solid var(--sari-dark-d8c2716225, #E9EDF1); background: var(--sari-dark-950baec030, #fff);
}
.user-page .user-table-titlebar h3 { margin: 0; color: var(--sari-dark-e23e64dd1e, #2F3742); font-size: 18px; font-weight: 780; }
.user-page .user-table-titlebar p { margin: 4px 0 0; color: var(--sari-dark-a63aa16d7c, #8B96A4); font-size: 11.5px; }
.user-page .user-table-counter { color: var(--sari-dark-a63aa16d7c, #7B8794); font-size: 12px; font-weight: 600; }
.user-page .user-selection {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    padding: 10px 18px; border-bottom: 1px solid var(--sari-dark-d8c2716225, #E5E7EB);
    background: var(--sari-dark-950baec030, #F8FAFC); color: var(--sari-dark-e23e64dd1e, #465362); font-size: 11.5px; font-weight: 600;
}
.user-page .user-selection[hidden] { display: none !important; }
.user-page .user-selection-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 7px; }
.user-page .user-selection-actions button {
    min-height: 34px; padding: 0 12px; border: 1px solid var(--sari-dark-d8c2716225, #DCE2E8);
    border-radius: 9px; background: var(--sari-dark-950baec030, #fff); color: var(--sari-dark-e23e64dd1e, #465362); font: 650 11px 'Poppins',sans-serif;
    cursor: pointer;
}
.user-page .user-selection-actions button:hover { border-color: var(--sari-dark-d8c2716225, #A9B3BF); background: var(--sari-dark-950baec030, #fff); }
.user-page .user-selection-actions [data-user-view-selected] {
    border-color: var(--um-gold); background: var(--um-gold); color: #fff;
}
.user-page .user-selection-actions [data-user-view-selected]:hover { background: #B47B0E; border-color: #B47B0E; }
.user-page .user-table-scroll { width: 100%; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; }
.user-page .user-table-scroll::-webkit-scrollbar { display: none; width: 0; height: 0; }
.user-page table { min-width: 1210px !important; width: 100% !important; background: var(--sari-dark-950baec030, #fff) !important; }
.user-page table thead tr { background: var(--sari-dark-950baec030, #F8FAFC) !important; }
.user-page table thead th {
    height: 48px !important; padding: 13px 14px !important;
    color: var(--sari-dark-a63aa16d7c, #8591A0) !important; font-size: 11px !important;
    font-weight: 750 !important; letter-spacing: .075em !important;
    line-height: 1.4 !important;
    background: var(--sari-dark-950baec030, #F8FAFC) !important; white-space: nowrap !important;
}
.user-page #userRows > tr[data-user-row] > td {
    padding: 17px 14px !important; vertical-align: middle !important;
    border-bottom: 1px solid var(--sari-dark-d8c2716225, #EDF0F3) !important;
    color: var(--sari-dark-e23e64dd1e, #475467) !important;
}
.user-page #userRows > tr[data-user-row] { background: var(--sari-dark-950baec030, #fff) !important; }
.user-page #userRows > tr[data-user-row]:hover { background: var(--sari-dark-950baec030, #F8FAFC) !important; box-shadow: none !important; }
.user-page #userRows > tr[data-user-row].is-selected { background: var(--sari-dark-950baec030, #F3F6FA) !important; }
.user-page #userRows > tr[data-user-row] > td.user-select-col {
    width: 56px !important; min-width: 56px !important; max-width: 56px !important;
    padding-left: 17px !important; padding-right: 8px !important;
}
.user-page table thead th.user-select-col { width: 56px !important; min-width: 56px !important; padding-left: 17px !important; padding-right: 8px !important; }
.user-page #userRows > tr[data-user-row] > td:nth-child(2) { min-width: 245px !important; width: 24% !important; }
.user-page #userRows > tr[data-user-row] > td:nth-child(4) { min-width: 215px !important; }
.user-page #userRows > tr[data-user-row] > td:nth-child(6),
.user-page #userRows > tr[data-user-row] > td:nth-child(7) { min-width: 125px !important; }
.user-page #userRows .user-account-identity { gap: 12px !important; }
.user-page #userRows .user-account-avatar {
    width: 44px !important; height: 44px !important;
    border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB) !important; border-radius: 10px !important;
    background: var(--sari-dark-950baec030, #F8FAFC) !important;
}
.user-page #userRows .user-account-name { color: var(--sari-dark-e23e64dd1e, #293440) !important; font-size: 12.5px !important; font-weight: 750 !important; line-height: 1.5 !important; }
.user-page #userRows .user-account-reference { margin-top: 3px !important; font-size: 11px !important; color: var(--sari-dark-a63aa16d7c, #8793A1) !important; }
.user-page #userRows > tr[data-user-row] > td:nth-child(3) > .user-role-badge,
.user-page #userRows > tr[data-user-row] > td:nth-child(5) > span { font-size: 10.5px !important; font-weight: 700 !important; padding: 5px 10px !important; }
.user-page #userRows > tr[data-user-row] > td:nth-child(4) p:first-child,
.user-page #userRows > tr[data-user-row] > td:nth-child(6) p:first-child,
.user-page #userRows > tr[data-user-row] > td:nth-child(7) p:first-child { font-size: 11.5px !important; font-weight: 600 !important; color: var(--sari-dark-e23e64dd1e, #293440) !important; }
.user-page #userRows > tr[data-user-row] > td:nth-child(4) p:last-child,
.user-page #userRows > tr[data-user-row] > td:nth-child(6) p:last-child,
.user-page #userRows > tr[data-user-row] > td:nth-child(7) p:last-child { font-size: 11px !important; color: var(--sari-dark-a63aa16d7c, #8793A1) !important; }
.user-page #userRows .user-actions-trigger { width: 34px !important; height: 34px !important; border-radius: 10px !important; }
/* Consistent select-all/checkmark design. */
.user-page .user-check-wrap { display: inline-flex; align-items: center; justify-content: center; width: 22px; min-height: 34px; cursor: pointer; }
.user-page .user-check-wrap input[type="checkbox"] {
    appearance: none !important; -webkit-appearance: none !important;
    display: inline-grid !important; place-items: center !important;
    width: 18px !important; height: 18px !important; flex: 0 0 18px;
    margin: 0 !important; padding: 0 !important; border: 1.5px solid var(--sari-dark-d8c2716225, #CBD5E1) !important;
    border-radius: 5px !important; background: var(--sari-dark-950baec030, #fff) !important; cursor: pointer !important;
}
.user-page .user-check-wrap input[type="checkbox"]::after {
    content: ''; width: 4px; height: 8px; border: solid var(--sari-dark-d8c2716225, #fff); border-width: 0 2px 2px 0;
    transform: translateY(-1px) rotate(45deg); opacity: 0;
}
.user-page .user-check-wrap input[type="checkbox"]:checked,
.user-page .user-check-wrap input[type="checkbox"]:indeterminate { background: var(--um-gold) !important; border-color: var(--um-gold) !important; }
.user-page .user-check-wrap input[type="checkbox"]:checked::after { opacity: 1; }
.user-page .user-check-wrap input[type="checkbox"]:indeterminate::after { opacity: 1; width: 9px; height: 2px; border: 0; border-radius: 999px; background: var(--sari-dark-950baec030, #fff); transform: none; }
.user-page .user-check-wrap input[type="checkbox"]:focus-visible { outline: 2px solid var(--um-gold) !important; outline-offset: 3px; }
/* Neutral, unboxed empty states, including search/filter no matches. */
.user-page .user-empty-state { padding: 65px 20px 68px !important; background: var(--sari-dark-950baec030, #fff) !important; text-align: center; }
.user-page .user-empty-state svg { display: block; width: 70px !important; height: 70px !important; margin-inline: auto; color: var(--sari-dark-a63aa16d7c, #98A2B3) !important; stroke-width: 1.6; }
.user-page .user-empty-state h4 { margin-top: 12px !important; color: var(--sari-dark-a63aa16d7c, #59636E) !important; font-size: 22px !important; font-weight: 700 !important; }
.user-page .user-empty-state p { margin: 7px auto 0; max-width: 520px; color: var(--sari-dark-a63aa16d7c, #8793A1) !important; font-size: 13px !important; line-height: 1.6; }
.user-page #userRows > tr[hidden] { display: none !important; }
/* Pagination uses 34px square numbered controls; fixed at 10 accounts per page. */
.user-page .user-table-footer {
    min-height: 64px !important; padding: 13px 20px !important;
    background: var(--sari-dark-950baec030, #fff) !important; border-top: 1px solid var(--sari-dark-d8c2716225, #E9EDF1) !important;
}
.user-page .user-table-footer #resultCount { font-size: 11px !important; color: var(--sari-dark-a63aa16d7c, #7B8794) !important; }
.user-page .user-table-footer .user-pagination { display: flex !important; align-items: center !important; gap: 7px !important; }
.user-page .user-table-footer .user-pagination #userPreviousPage,
.user-page .user-table-footer .user-pagination #userNextPage,
.user-page .user-table-footer .user-pagination .user-page-number {
    display: inline-grid !important; place-items: center !important;
    width: 34px !important; min-width: 34px !important;
    height: 34px !important; min-height: 34px !important;
    padding: 0 !important; border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB) !important;
    border-radius: 10px !important; background: var(--sari-dark-950baec030, #fff) !important;
    color: var(--sari-dark-a63aa16d7c, #667383) !important; font-size: 11.5px !important;
    line-height: 1 !important; box-shadow: none !important; cursor: pointer;
}
.user-page .user-table-footer .user-pagination #userPreviousPage svg,
.user-page .user-table-footer .user-pagination #userNextPage svg { width: 16px; height: 16px; }
.user-page .user-table-footer .user-pagination #userPreviousPage:hover:not(:disabled),
.user-page .user-table-footer .user-pagination #userNextPage:hover:not(:disabled),
.user-page .user-table-footer .user-pagination .user-page-number:hover:not(.is-current) { background: var(--sari-dark-950baec030, #F8FAFC) !important; border-color: var(--sari-dark-d8c2716225, #CAD3DE) !important; }
.user-page .user-table-footer .user-pagination .user-page-number.is-current {
    background: var(--um-gold) !important; border-color: var(--um-gold) !important; color: #fff !important;
}
.user-page .user-table-footer .user-pagination :is(#userPreviousPage,#userNextPage):disabled { opacity: .42 !important; background: var(--sari-dark-950baec030, #fff) !important; color: var(--sari-dark-a63aa16d7c, #A0AAB6) !important; cursor: not-allowed; }
.user-page .user-page-numbers { display: flex; align-items: center; gap: 6px; }
.user-page .user-table-footer .user-pagination #userPageNumber {
    position: absolute !important; width: 1px !important; height: 1px !important;
    min-width: 1px !important; margin: -1px !important; padding: 0 !important;
    overflow: hidden !important; clip: rect(0,0,0,0) !important; white-space: nowrap !important;
    border: 0 !important; line-height: 1 !important;
}
@media (max-width: 1180px) { .user-page > section.grid { grid-template-columns: repeat(2,minmax(0,1fr)) !important; } }
@media (max-width: 1023px) {
    .user-page > section.user-surface.mt-4.p-3 > div { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
    .user-page > section.user-surface.mt-4.p-3 > div > div:first-child { grid-column: 1 / -1; }
    .user-page [data-role-dropdown], .user-page [data-status-dropdown] { grid-column: auto; }
    .user-page #applyFilter, .user-page #resetFilter { width: 100% !important; }
}
@media (max-width: 767px) {
    .user-page .user-page-header { align-items: flex-start !important; gap: 12px !important; }
    .user-page .user-page-title { font-size: 25px !important; }
    .user-page > section.user-surface.mt-4.p-3 > div { grid-template-columns: 1fr !important; }
    .user-page > section.user-surface.mt-4.p-3 > div > div:first-child { grid-column: auto; }
    .user-page .user-table-titlebar { padding: 17px 16px; }
    .user-page .user-table-footer { align-items: flex-start !important; flex-direction: column !important; }
    .user-page .user-table-footer .user-pagination { width: 100% !important; flex-wrap: wrap !important; justify-content: flex-start !important; }
    .user-page .user-empty-state { padding: 52px 16px 56px !important; }
    .user-page .user-empty-state svg { width: 60px !important; height: 60px !important; }
    .user-page .user-empty-state h4 { font-size: 20px !important; }
}
@media (max-width: 540px) {
    .user-page > section.grid { grid-template-columns: 1fr !important; }
    .user-page .user-selection-actions { width: 100%; }
    .user-page .user-selection-actions button { flex: 1 1 auto; }
}
</style>

@php
    $metricCards = [
        ['Total Accounts', $stats['total'] ?? 0, 'All accounts', 'all', 'text-[#b98112] bg-[#fff7e8] border-[#efdfbf]', 'users'],
        ['Buyers', $stats['buyers'] ?? 0, 'Buyer accounts', 'Buyer', 'text-[#298b53] bg-[#eef8f1] border-[#d8ebde]', 'user'],
        ['Sellers', $stats['sellers'] ?? 0, 'Seller accounts', 'Seller', 'text-[#377ab7] bg-[#eef6fd] border-[#d9e7f3]', 'store'],
        ['Logistics', $stats['logistics'] ?? 0, 'Logistics accounts', 'Logistics', 'text-[#c26f20] bg-[#fff5ed] border-[#f0dfd0]', 'truck'],
    ];

    $roleTone = fn ($role) => 'user-role-badge ' . match (strtolower((string) $role)) {
        'buyer' => 'user-role-badge--buyer',
        'seller' => 'user-role-badge--seller',
        'logistics' => 'user-role-badge--logistics',
        default => 'user-role-badge--other',
    };
@endphp

<div class="user-page mx-auto w-full max-w-[1880px] pb-8">
    @if(session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-[14px] border border-[#d5e6da] bg-[#f4faf6] px-4 py-3 shadow-[0_8px_22px_rgba(50,95,66,.05)]">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-[#45805b]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
            </span>
            <div><p class="text-[9px] font-bold text-[#426b50]">Success</p><p class="mt-0.5 text-[9px] text-[#617668]">{{ session('success') }}</p></div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-[14px] border border-[#ead2d2] bg-[#fff7f7] px-4 py-3 text-[9px] text-[#8d6262] shadow-[0_8px_22px_rgba(120,60,60,.04)]">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PAGE HEADER — SELLER COMPLIANCE STYLE --}}
    <section class="user-page-header flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="user-page-header-main">
            <span class="user-page-header-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="9" cy="8" r="3"></circle>
                    <path d="M3 20a6 6 0 0 1 12 0"></path>
                    <path d="M17 11h4"></path>
                    <path d="M19 9v4"></path>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="user-page-eyebrow">User Management</p>

                <h2 class="user-page-title">
                    <span class="user-page-title-base">Approved</span>
                    <span class="user-page-title-accent">Accounts</span>
                </h2>

              
            </div>
        </div>

        <button
            id="exportUsers"
            type="button"
            class="user-page-export inline-flex items-center justify-center gap-2 self-start border border-[#e6ddd2] bg-white text-[#5f574e] transition hover:border-[#d8c4a3] hover:bg-[#fffaf2] hover:text-[#a8731f]"
        >
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 3v12"></path>
                <path d="m8 11 4 4 4-4"></path>
                <path d="M5 19h14"></path>
            </svg>
            Export PDF
        </button>
    </section>

    {{-- SUMMARY --}}
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach($metricCards as [$label, $value, $helper, $role, $tone, $icon])
            <button type="button" data-summary-role="{{ $role }}" class="metric-card group relative min-h-[108px] p-4 pr-20 text-left sm:p-[18px] sm:pr-20">
                <div class="flex h-full items-center gap-4">
                    <span class="absolute right-4 top-4 grid h-12 w-12 shrink-0 place-items-center rounded-[12px] border {{ $tone }} sm:right-[18px] sm:top-[18px]">
                        @if($icon === 'users')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="7" r="3"></circle><path d="M3 20a6 6 0 0 1 12 0"></path><path d="M17 6a3 3 0 0 1 0 6"></path><path d="M18 15a5 5 0 0 1 3 5"></path></svg>
                        @elseif($icon === 'user')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20a7 7 0 0 1 14 0"></path></svg>
                        @elseif($icon === 'store')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 9h16"></path><path d="M5 4h14l1 5v2a3 3 0 0 1-3 3 3 3 0 0 1-3-3 3 3 0 0 1-6 0 3 3 0 0 1-3 3V9Z"></path><path d="M6 14v6h12v-6"></path></svg>
                        @elseif($icon === 'rider')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="6" cy="17" r="3"></circle><circle cx="18" cy="17" r="3"></circle><path d="M9 17h6"></path><path d="m10 10 3 4h3l2-4"></path><path d="M8 10h4"></path></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h11v11H3z"></path><path d="M14 10h4l3 3v4h-7z"></path><circle cx="7" cy="18" r="2"></circle><circle cx="18" cy="18" r="2"></circle></svg>
                        @endif
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-medium text-[#7d746a]">{{ $label }}</p>
                        <p class="mt-1 text-[24px] font-bold leading-none tracking-[-.04em] text-[#1c1712]">{{ $value }}</p>
                        <p class="mt-2 truncate text-[8px] text-[#9b9288]">{{ $helper }}</p>
                    </div>
                </div>
            </button>
        @endforeach
    </section>

    {{-- FILTER BAR --}}
    <section class="user-surface mt-4 p-3">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
            <div class="relative min-w-0 flex-1">
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9d8f7e]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg>
                <input id="userSearch" type="search" placeholder="Search name, email, contact, or role..." class="clean-control h-11 w-full rounded-[12px] pl-11 pr-4 text-[10px]">
            </div>

            <div class="relative xl:w-[185px]" data-role-dropdown>
                <button type="button" data-role-toggle class="clean-control flex h-11 w-full items-center justify-between rounded-[12px] px-4 text-[9px] font-medium"><span data-role-label>All Roles</span><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#8b8175]" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 10 5 5 5-5"></path></svg></button>
                <div class="custom-menu absolute left-0 right-0 top-full z-40 rounded-[14px] border border-[#e7dfd4] bg-white p-1.5 shadow-[0_18px_40px_rgba(47,37,25,.13)]" data-role-menu>
                    @foreach(['all' => 'All Roles', 'Buyer' => 'Buyer', 'Seller' => 'Seller', 'Logistics' => 'Logistics'] as $value => $label)
                        <button type="button" data-role-option="{{ $value }}" class="flex w-full items-center justify-between rounded-[10px] px-3 py-2.5 text-left text-[9px] font-medium text-[#5c534a] transition hover:bg-[#fff7e8] hover:text-[#a8731f]">{{ $label }}<span class="h-1.5 w-1.5 rounded-full bg-[#d99500] opacity-0" data-check></span></button>
                    @endforeach
                </div>
            </div>

            <div class="relative xl:w-[175px]" data-status-dropdown>
                <button type="button" data-status-toggle class="clean-control flex h-11 w-full items-center justify-between rounded-[12px] px-4 text-[9px] font-medium"><span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#3f9a61]"></span><span data-status-label>All Status</span></span><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#8b8175]" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 10 5 5 5-5"></path></svg></button>
                <div class="custom-menu absolute left-0 right-0 top-full z-40 rounded-[14px] border border-[#e7dfd4] bg-white p-1.5 shadow-[0_18px_40px_rgba(47,37,25,.13)]" data-status-menu>
                    @foreach(['all' => 'All Status', 'active' => 'Active', 'deactivated' => 'Suspended', 'banned' => 'Banned'] as $value => $label)
                        <button type="button" data-status-option="{{ $value }}" class="flex w-full items-center justify-between rounded-[10px] px-3 py-2.5 text-left text-[9px] font-medium text-[#5c534a] transition hover:bg-[#fff7e8] hover:text-[#a8731f]">{{ $label }}<span class="h-1.5 w-1.5 rounded-full bg-[#d99500] opacity-0" data-check></span></button>
                    @endforeach
                </div>
            </div>

            <button id="applyFilter" type="button" class="inline-flex h-11 items-center justify-center gap-2 rounded-[12px] bg-[#d99500] px-5 text-[9px] font-semibold text-white shadow-[0_10px_22px_rgba(217,149,0,.18)] transition hover:bg-[#bd8205]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>Apply Filter</button>
            <button id="resetFilter" type="button" class="inline-flex h-11 items-center justify-center rounded-[12px] border border-[#e6ddd2] bg-white px-4 text-[9px] font-semibold text-[#6f665b] transition hover:bg-[#faf8f4]">Reset</button>
        </div>
    </section>

    {{-- TABLE --}}
    <section class="user-surface mt-4 overflow-hidden">
        <div class="user-table-titlebar">
            <div>
                <h3>Approved Accounts</h3>
                <p>Review account information and manage access using the actions menu.</p>
            </div>
            <span id="userTableCount" class="user-table-counter" aria-live="polite">{{ count($accounts) }} accounts</span>
        </div>
        <div id="userSelectionBar" class="user-selection" hidden>
            <span><strong id="userSelectionCount">0</strong> selected <span class="sr-only">accounts</span></span>
            <div class="user-selection-actions">
                <button type="button" id="userShowSelected" aria-pressed="false">Show selected</button>
                <button type="button" data-user-view-selected>View first selected</button>
                <button type="button" id="userClearSelection">Clear selection</button>
            </div>
        </div>
        <div class="user-table-scroll overflow-x-auto">
            <table class="w-full min-w-[1060px] border-collapse text-left">
                <thead><tr class="border-b border-[#eee8df] bg-[#fcfbf8]">
                    <th scope="col" class="user-select-col"><label class="user-check-wrap"><input type="checkbox" id="userSelectAll" aria-label="Select all accounts on this page"></label></th>

                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Account</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Role</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Contact</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Status</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Last sign-in</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Joined</th>
                    <th class="px-5 py-3.5 text-right text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Actions</th>
                </tr></thead>
                <tbody id="userRows" class="divide-y divide-[#f0ebe4]">
                @forelse($accounts as $account)
                    @php
                        $name = (string) ($account['name'] ?? 'Unnamed User');
                        $role = (string) ($account['role'] ?? 'User');
                        $roleKey = (string) ($account['role_key'] ?? strtolower($role));
                        $email = (string) ($account['email'] ?? '');
                        $status = strtolower((string) ($account['status'] ?? 'active'));
                        $id = $account['id'] ?? null;
                        $initials = collect(preg_split('/\s+/', trim($name)) ?: [])->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                        $created = $account['created_at'] ?? null;
                        $joinedDate = $created?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? '—';
                        $joinedTime = $created?->copy()->timezone('Asia/Manila')->format('h:i A') ?? '';
                        $reference = $account['account_reference'] ?? strtoupper($roleKey) . '-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
                        $photoUrl = $account['profile_photo_url'] ?? null;
                        $lastSignIn = $account['last_sign_in_at'] ?? null;
                        $lastSignInDate = $lastSignIn?->copy()->timezone('Asia/Manila')->format('M d, Y');
                        $lastSignInTime = $lastSignIn?->copy()->timezone('Asia/Manila')->format('h:i A');
                        $activityPayload = collect($account['activity'] ?? [])->map(fn ($event) => [
                            'action' => $event['action'] ?? 'activity',
                            'description' => $event['description'] ?? 'Account activity',
                            'time' => optional($event['created_at'] ?? null)->format('M d, Y h:i A'),
                            'relative' => optional($event['created_at'] ?? null)->diffForHumans(),
                        ])->values();
                    @endphp
                    <tr
                        data-user-row
                        data-name="{{ strtolower($name) }}"
                        data-display-name="{{ $name }}"
                        data-account-reference="{{ $reference }}"
                        data-photo-url="{{ $photoUrl }}"
                        data-last-sign-in="{{ $lastSignIn ? $lastSignInDate . ' ' . $lastSignInTime : 'No recorded sign-in' }}"
                        data-role="{{ $role }}"
                        data-role-key="{{ $roleKey }}"
                        data-email="{{ strtolower($email) }}"
                        data-email-display="{{ $email }}"
                        data-contact="{{ $account['contact_no'] ?? '' }}"
                        data-status="{{ $status }}"
                        data-id="{{ $id }}"
                        data-joined="{{ $joinedDate }}"
                        data-first-name="{{ $account['first_name'] ?? '' }}"
                        data-last-name="{{ $account['last_name'] ?? '' }}"
                        data-store-name="{{ $account['store_name'] ?? '' }}"
                        data-update-url="{{ route('admin.users.update', [$roleKey, $id]) }}"
                        data-suspend-url="{{ route('admin.users.suspend', [$roleKey, $id]) }}"
                        data-restore-url="{{ route('admin.users.restore', [$roleKey, $id]) }}"
                        data-ban-url="{{ route('admin.users.ban', [$roleKey, $id]) }}"
                        data-unban-url="{{ route('admin.users.unban', [$roleKey, $id]) }}"
                        data-delete-url="{{ route('admin.users.destroy', [$roleKey, $id]) }}"
                        data-note-url="{{ route('admin.users.note', [$roleKey, $id]) }}"
                        data-message-url="{{ $roleKey === 'seller' ? route('admin.messages', ['seller' => $id]) : '' }}"
                        data-profile='@json($account['profile'] ?? [])'
                        data-activity='@json($activityPayload)'
                    >
                        <td class="user-select-col">
                            <label class="user-check-wrap"><input type="checkbox" data-user-checkbox aria-label="Select account {{ $name }}"></label>
                        </td>
                        <td class="px-4 py-4">
                            <div class="user-account-identity">
                                <span class="user-account-avatar"><span aria-hidden="true">{{ $initials ?: 'U' }}</span>
                                    @if($photoUrl)<img src="{{ $photoUrl }}" alt="Profile photo of {{ $name }}" loading="eager" decoding="async" data-user-photo>@endif
                                </span>
                                <div class="min-w-0"><p class="user-account-name">{{ $name }}</p><p class="user-account-reference">{{ $reference }}</p></div>
                            </div>
                        </td>
                        <td class="px-4 py-4"><span class="inline-flex rounded-full border px-3 py-1.5 text-[8px] font-semibold {{ $roleTone($role) }}">{{ $role }}</span></td>
                        <td class="px-4 py-4"><p class="max-w-[230px] truncate text-[9px] font-medium text-[#514a42]">{{ $email ?: 'No email' }}</p><p class="mt-0.5 text-[8px] text-[#958c80]">{{ $account['contact_no'] ?: 'No contact number' }}</p></td>
                        <td class="px-4 py-4">
                            @if($status === 'active')
                                <span class="inline-flex items-center gap-2 rounded-full border border-[#d6e9dc] bg-[#eef8f1] px-3 py-1.5 text-[8px] font-semibold text-[#36805a]"><span class="h-1.5 w-1.5 rounded-full bg-[#2f9b5c]"></span>Active</span>
                            @elseif($status === 'banned')
                                <span class="inline-flex items-center gap-2 rounded-full border border-[#efc8c3] bg-[#fff1ef] px-3 py-1.5 text-[8px] font-semibold text-[#bd4438]"><span class="h-1.5 w-1.5 rounded-full bg-[#d94f42]"></span>Banned</span>
                            @else
                                <span class="inline-flex items-center gap-2 rounded-full border border-[#ead5d0] bg-[#fff4f1] px-3 py-1.5 text-[8px] font-semibold text-[#a95d45]"><span class="h-1.5 w-1.5 rounded-full bg-[#bd6a4e]"></span>Suspended</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 user-account-last-login">
                            @if($lastSignIn)
                                <p>{{ $lastSignInDate }}</p><span>{{ $lastSignInTime }} PHT</span>
                            @else
                                <p class="user-account-no-login">No recorded sign-in</p>
                            @endif
                        </td>
                        <td class="px-4 py-4"><p class="text-[9px] font-medium text-[#514a42]">{{ $joinedDate }}</p>@if($joinedTime)<p class="mt-0.5 text-[8px] text-[#958c80]">{{ $joinedTime }}</p>@endif</td>
                        <td class="px-5 py-4">
                            <div class="user-actions-cell">
                                <button type="button" class="user-actions-trigger" popovertarget="user-actions-{{ $roleKey }}-{{ $id }}" aria-label="Actions for {{ $name }}" aria-expanded="false">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="19" cy="12" r="1.7"/></svg>
                                </button>
                                <div id="user-actions-{{ $roleKey }}-{{ $id }}" class="user-actions-menu" popover="auto" aria-label="Account actions for {{ $name }}">

                                {{-- View profile --}}
                                <button type="button" data-view-user class="account-action" title="View profile" aria-label="View profile">
                                    
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                            <circle cx="12" cy="12" r="2.5"></circle>
                                        </svg>
                                <span>View profile</span></button>

                                {{-- Message: current backend supports Admin ↔ Seller messaging --}}
                                @if($roleKey === 'seller')
                                    <a href="{{ route('admin.messages', ['seller' => $id]) }}" class="account-action account-action--message" title="Message seller" aria-label="Message seller">
                                        
                                            <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M20 15a4 4 0 0 1-4 4H8l-4 2V7a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v8Z"></path>
                                                <path d="M8 9h8"></path>
                                                <path d="M8 13h5"></path>
                                            </svg>
                                    <span>Message seller</span>
                                    </a>
                                @endif

                                {{-- Recent activity --}}
                                <button type="button" data-activity-user class="account-action account-action--activity" title="Recent activity" aria-label="Recent activity">
                                    
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8"></circle>
                                            <path d="M12 7v5l3 2"></path>
                                            <path d="M5 5 3 8h4"></path>
                                        </svg>
                                <span>Recent activity</span></button>

                                {{-- Internal admin note --}}
                                <button type="button" data-note-user class="account-action account-action--note" title="Add admin note" aria-label="Add admin note">
                                    
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M6 3h12v18H6z"></path>
                                            <path d="M9 8h6"></path>
                                            <path d="M9 12h6"></path>
                                            <path d="M9 16h4"></path>
                                        </svg>
                                <span>Add admin note</span></button>

                                {{-- Suspend / Restore --}}
                                @if($status === 'active')
                                    <button type="button" data-access-user data-access-mode="suspend" class="account-action account-action--suspend" title="Suspend account" aria-label="Suspend account">
                                        
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8"></circle>
                                            <path d="m6.5 6.5 11 11"></path>
                                        </svg>
                                    <span>Suspend account</span></button>
                                @else
                                    <button type="button" data-access-user data-access-mode="restore" class="account-action account-action--restore" title="Restore account" aria-label="Restore account">
                                        
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M20 7v5h-5"></path>
                                            <path d="M18 16a7 7 0 1 1 1-8l1 4"></path>
                                        </svg>
                                    <span>Restore account</span></button>
                                @endif

                                @if($status === 'banned')
                                    <button type="button" data-ban-user data-ban-mode="unban" class="account-action account-action--restore" title="Cancel ban" aria-label="Cancel ban">
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 7v5h-5"></path><path d="M18 16a7 7 0 1 1 1-8l1 4"></path></svg>
                                    <span>Cancel ban</span></button>
                                @else
                                    <button type="button" data-ban-user data-ban-mode="ban" class="account-action account-action--suspend" title="Ban email and account" aria-label="Ban email and account">
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 5l14 14"></path><circle cx="12" cy="12" r="9"></circle></svg>
                                    <span>Ban email and account</span></button>
                                @endif

                                <button type="button" data-delete-user class="account-action" style="color:var(--sari-dark-beb15fc81e, #d95745)" title="Delete account" aria-label="Delete account">
                                    <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path><path d="M10 11v5M14 11v5"></path></svg>
                                <span>Delete account</span></button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="user-empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg>
                        <h4>No approved accounts yet</h4>
                        <p>Approved platform users will appear here.</p>
                    </td></tr>
                @endforelse
                    <tr id="userSearchEmpty" hidden><td colspan="8" class="user-empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg>
                        <h4>No matching accounts</h4>
                        <p>Try another name, role, or status to find the account you need.</p>
                    </td></tr>
                </tbody>
            </table>
        </div>
        <div class="user-table-footer">
            <p id="resultCount" aria-live="polite">Showing {{ count($accounts) }} results</p>
            <div class="user-pagination" aria-label="Account table pagination">
                <button type="button" id="userPreviousPage" aria-label="Previous page" title="Previous page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button>
                <div id="userPageNumbers" class="user-page-numbers" aria-label="Page numbers"></div>
                <span id="userPageNumber" aria-live="polite">Page 1 of 1</span>
                <button type="button" id="userNextPage" aria-label="Next page" title="Next page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg></button>
            </div>
        </div>
    </section>
</div>

@include('admin.partials.users.account-modal')

<script>
(function () {
    const rows = Array.from(document.querySelectorAll('[data-user-row]'));
    const search = document.getElementById('userSearch');
    const resultCount = document.getElementById('resultCount');
    const summaryCards = Array.from(document.querySelectorAll('[data-summary-role]'));
    let selectedRole = 'all';
    let selectedStatus = 'all';
    let currentPage = 1;
    let filteredRows = rows.slice();
    const allCheck = document.getElementById('userSelectAll');
    const rowChecks = rows.map(row => ({ row, checkbox: row.querySelector('[data-user-checkbox]') }));
    const selectedRows = new Set();
    const selectionBar = document.getElementById('userSelectionBar');
    const selectionCount = document.getElementById('userSelectionCount');
    const showSelectedButton = document.getElementById('userShowSelected');
    const emptyFiltered = document.getElementById('userSearchEmpty');
    const pageNumbers = document.getElementById('userPageNumbers');
    const tableCount = document.getElementById('userTableCount');
    let showSelectedOnly = false;
    let pageRows = [];

    const normalize = value => (value || '').toString().trim().toLowerCase();

    /*
     * PERFORMANCE:
     * Build the searchable text once instead of reconstructing and
     * lower-casing it again for every row on every keystroke.
     */
    const rowIndex = rows.map(row => ({
        row,
        role: normalize(row.dataset.role),
        status: normalize(row.dataset.status),
        searchText: normalize([
            row.dataset.name,
            row.dataset.accountReference,
            row.dataset.emailDisplay,
            row.dataset.contact,
            row.dataset.role
        ].join(' '))
    }));

    function debounce(callback, wait = 130) {
        let timer = null;

        return function (...args) {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => callback.apply(this, args), wait);
        };
    }

    let filterFrame = 0;

    function updateSelection() {
        const visibleSelected = pageRows.filter(row => selectedRows.has(row));
        if (allCheck) {
            allCheck.checked = pageRows.length > 0 && visibleSelected.length === pageRows.length;
            allCheck.indeterminate = visibleSelected.length > 0 && visibleSelected.length < pageRows.length;
            allCheck.disabled = pageRows.length === 0;
        }
        rowChecks.forEach(({ row, checkbox }) => {
            if (checkbox) checkbox.checked = selectedRows.has(row);
            row.classList.toggle('is-selected', selectedRows.has(row));
        });
        if (selectionBar) selectionBar.hidden = selectedRows.size === 0;
        if (selectionCount) selectionCount.textContent = String(selectedRows.size);
        if (showSelectedButton) {
            showSelectedButton.setAttribute('aria-pressed', showSelectedOnly ? 'true' : 'false');
            showSelectedButton.textContent = showSelectedOnly ? 'Show all matches' : 'Show selected';
        }
    }

    function paintPageNumbers(count) {
        if (!pageNumbers) return;
        pageNumbers.replaceChildren();
        const first = Math.max(1, Math.min(currentPage - 2, count - 4));
        const last = Math.min(count, first + 4);
        for (let number = first; number <= last; number++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'user-page-number' + (number === currentPage ? ' is-current' : '');
            button.textContent = String(number);
            button.setAttribute('aria-label', 'Page ' + number);
            if (number === currentPage) button.setAttribute('aria-current', 'page');
            button.addEventListener('click', () => {
                currentPage = number;
                applyFilters(false);
            });
            pageNumbers.appendChild(button);
        }
    }

    function applyFilters(resetPage = true) {
        if (resetPage) currentPage = 1;
        const query = normalize(search?.value);
        const normalizedRole = normalize(selectedRole);
        const normalizedStatus = normalize(selectedStatus);

        window.cancelAnimationFrame(filterFrame);

        filterFrame = window.requestAnimationFrame(() => {
            filteredRows = rowIndex.filter(item =>
                (normalizedRole === 'all' || item.role === normalizedRole)
                && (normalizedStatus === 'all' || item.status === normalizedStatus)
                && (query === '' || item.searchText.includes(query))
            ).map(item => item.row);
            if (showSelectedOnly) filteredRows = filteredRows.filter(row => selectedRows.has(row));
            const pageSize = 10;
            const pageCount = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(currentPage, 1), pageCount);
            const offset = (currentPage - 1) * pageSize;
            pageRows = filteredRows.slice(offset, offset + pageSize);
            const visibleSet = new Set(pageRows);
            rows.forEach(row => { row.hidden = !visibleSet.has(row); });
            if (emptyFiltered) emptyFiltered.hidden = rows.length === 0 || filteredRows.length !== 0;
            if (tableCount) tableCount.textContent = filteredRows.length + (filteredRows.length === 1 ? ' account' : ' accounts');
            if (resultCount) resultCount.textContent = filteredRows.length
                ? `Showing ${offset + 1}–${Math.min(offset + pageSize, filteredRows.length)} of ${filteredRows.length} results`
                : 'No matching accounts';
            document.getElementById('userPageNumber').textContent = `Page ${currentPage} of ${pageCount}`;
            document.getElementById('userPreviousPage').disabled = currentPage <= 1;
            document.getElementById('userNextPage').disabled = currentPage >= pageCount;
            paintPageNumbers(pageCount);
            updateSelection();

            summaryCards.forEach(card => {
                card.classList.toggle(
                    'is-active',
                    normalize(card.dataset.summaryRole) === normalizedRole
                );
            });
        });
    }

    // Bulk selection is read-only; account access changes stay in individual action menus.
    allCheck?.addEventListener('change', () => {
        pageRows.forEach(row => {
            if (allCheck.checked) selectedRows.add(row);
            else selectedRows.delete(row);
        });
        if (showSelectedOnly) applyFilters(false);
        else updateSelection();
    });
    rowChecks.forEach(({ row, checkbox }) => checkbox?.addEventListener('change', () => {
        if (checkbox.checked) selectedRows.add(row);
        else selectedRows.delete(row);
        if (showSelectedOnly) {
            if (selectedRows.size === 0) showSelectedOnly = false;
            applyFilters(false);
        } else updateSelection();
    }));
    showSelectedButton?.addEventListener('click', () => {
        showSelectedOnly = !showSelectedOnly;
        applyFilters(true);
    });
    document.getElementById('userClearSelection')?.addEventListener('click', () => {
        selectedRows.clear();
        showSelectedOnly = false;
        applyFilters(true);
    });
    document.querySelector('[data-user-view-selected]')?.addEventListener('click', () => {
        const first = rows.find(row => selectedRows.has(row));
        first?.querySelector('[data-view-user]')?.click();
    });

    const debouncedApplyFilters = debounce(applyFilters, 130);

    search?.addEventListener('input', debouncedApplyFilters, { passive: true });

    summaryCards.forEach(card => card.addEventListener('click', () => {
        selectedRole = card.dataset.summaryRole || 'all';
        syncRoleDropdown();
        applyFilters();
    }));

    function setupDropdown(rootSelector, toggleSelector, menuSelector, optionSelector, labelSelector, onSelect) {
        const root = document.querySelector(rootSelector);
        const toggle = root?.querySelector(toggleSelector);
        const menu = root?.querySelector(menuSelector);
        const label = root?.querySelector(labelSelector);
        const options = Array.from(root?.querySelectorAll(optionSelector) || []);
        toggle?.addEventListener('click', event => { event.stopPropagation(); menu?.classList.toggle('is-open'); });
        options.forEach(option => option.addEventListener('click', () => { onSelect(option, label, options); menu?.classList.remove('is-open'); applyFilters(); }));
        document.addEventListener('click', event => { if (!event.target.closest(rootSelector)) menu?.classList.remove('is-open'); });
        return { root, label, options };
    }

    const roleDropdown = setupDropdown('[data-role-dropdown]', '[data-role-toggle]', '[data-role-menu]', '[data-role-option]', '[data-role-label]', (option, label, options) => {
        selectedRole = option.dataset.roleOption || 'all';
        if (label) label.textContent = option.textContent.trim();
        options.forEach(o => o.querySelector('[data-check]')?.classList.toggle('opacity-0', o !== option));
    });

    const statusDropdown = setupDropdown('[data-status-dropdown]', '[data-status-toggle]', '[data-status-menu]', '[data-status-option]', '[data-status-label]', (option, label, options) => {
        selectedStatus = option.dataset.statusOption || 'all';
        if (label) label.textContent = option.textContent.trim();
        options.forEach(o => o.querySelector('[data-check]')?.classList.toggle('opacity-0', o !== option));
    });

    function syncRoleDropdown() {
        roleDropdown.options.forEach(option => {
            const active = normalize(option.dataset.roleOption) === normalize(selectedRole);
            option.querySelector('[data-check]')?.classList.toggle('opacity-0', !active);
            if (active && roleDropdown.label) roleDropdown.label.textContent = option.textContent.trim();
        });
    }

    document.getElementById('applyFilter')?.addEventListener('click', applyFilters);
    document.getElementById('resetFilter')?.addEventListener('click', () => {
        selectedRole = 'all'; selectedStatus = 'all'; if (search) search.value = '';
        roleDropdown.options[0]?.click(); statusDropdown.options[0]?.click(); applyFilters();
    });

    // Real PDF download. Send only server-side account identifiers, never PII.
    // This preserves the existing filteredRows export semantics (including
    // Show selected mode) and exports all matching rows, not only this page.
    document.getElementById('exportUsers')?.addEventListener('click', () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = @json(route('admin.users.export-pdf'));
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = @json(csrf_token());
        form.appendChild(csrf);

        const currentQuery = normalize(search?.value);
        const role = normalize(selectedRole);
        const status = normalize(selectedStatus);
        // Compute the final filter synchronously so fast clicks after typing
        // cannot export a stale debounced search result.
        const exportRows = rowIndex.filter(item =>
            (role === 'all' || item.role === role)
            && (status === 'all' || item.status === status)
            && (!currentQuery || item.searchText.includes(currentQuery))
            && (!showSelectedOnly || selectedRows.has(item.row))
        ).map(item => item.row);

        // One compact JSON field avoids PHP max_input_vars limits for large
        // filtered exports; the server still validates every identifier.
        const selection = document.createElement('input');
        selection.type = 'hidden';
        selection.name = 'records_json';
        selection.value = JSON.stringify(exportRows.map(row => row.dataset.roleKey + ':' + row.dataset.id));
        form.appendChild(selection);

        document.body.appendChild(form);
        form.submit();
    });

    document.getElementById('userPreviousPage')?.addEventListener('click', () => { currentPage--; applyFilters(false); });
    document.getElementById('userNextPage')?.addEventListener('click', () => { currentPage++; applyFilters(false); });
    document.querySelectorAll('[data-user-photo]').forEach(image => {
        image.addEventListener('error', () => { image.hidden = true; });
        image.addEventListener('load', () => { image.hidden = false; });
        // A deferred image can be complete with zero width before a request starts.
        // Hide only after an actual error event, never while it is waiting to load.
        if (image.complete && image.naturalWidth > 0) image.hidden = false;
    });
    document.querySelectorAll('.user-actions-menu').forEach(menu => {
        const trigger = menu.parentElement.querySelector('[popovertarget]');
        menu.addEventListener('beforetoggle', event => {
            const open = event.newState === 'open';
            trigger?.setAttribute('aria-expanded', String(open));
            if (!open || !trigger) return;
            const rect = trigger.getBoundingClientRect();
            menu.style.left = Math.max(8, Math.min(window.innerWidth - 224, rect.right - 216)) + 'px';
            menu.style.top = 'auto'; menu.style.bottom = 'auto';
            if (window.innerHeight - rect.bottom > 310) menu.style.top = rect.bottom + 5 + 'px';
            else menu.style.bottom = Math.max(8, window.innerHeight - rect.top + 5) + 'px';
        });
        menu.addEventListener('click', event => {
            if (event.target.closest('button, a')) menu.hidePopover();
        });
    });

    syncRoleDropdown(); applyFilters();
})();
</script>
<!-- Action icons are inline SVGs to avoid an extra render-blocking icon-font stylesheet. -->
@include('admin.partials.users.modal-script')
@endsection
