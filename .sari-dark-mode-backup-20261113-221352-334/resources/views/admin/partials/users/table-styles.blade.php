<style>
.user-page table { min-width: 1060px !important; background: #fff; }
.user-page table thead tr { background: #f8fafc !important; }
.user-page table th { padding: 13px 16px !important; color: #78818f !important; font-size: 10px !important; font-weight: 600 !important; letter-spacing: .055em !important; white-space: nowrap; }
.user-page #userRows > tr > td { padding: 13px 16px !important; vertical-align: middle !important; text-align: left !important; border-color: #eef0f3 !important; }
.user-page #userRows > tr:hover { background: #fafbfc !important; }
.user-page #userRows > tr > td:first-child { min-width: 230px; width: 24% !important; }
.user-page #userRows > tr > td:nth-child(3) { min-width: 200px; }
.user-page #userRows > tr > td:nth-child(5), .user-page #userRows > tr > td:nth-child(6) { min-width: 120px; }
.user-account-identity { display: flex; align-items: center; gap: 11px; }
.user-account-avatar { position: relative; display: grid; place-items: center; flex: none; width: 38px; height: 38px; overflow: hidden; border: 1px solid #e4e7ec; border-radius: 50%; background: #f2f4f7; color: #697586; font-size: 11px; font-weight: 600; }
.user-account-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; background: #f2f4f7; }
.user-account-avatar img[hidden] { display: none !important; }
.user-page #userRows .user-account-name { max-width: 230px; margin: 0 !important; font-size: 12px !important; font-weight: 600 !important; color: #263244 !important; line-height: 1.5 !important; overflow-wrap: anywhere; }
.user-page #userRows .user-account-reference { margin: 3px 0 0 !important; color: #87909d !important; font-size: 10px !important; font-weight: 400 !important; letter-spacing: .02em !important; }
.user-page #userRows > tr > td:nth-child(2) > span, .user-page #userRows > tr > td:nth-child(4) > span { font-size: 10px !important; font-weight: 500 !important; padding: 5px 9px !important; }
.user-page #userRows > tr > td:nth-child(3) p:first-child, .user-page #userRows .user-account-last-login p, .user-page #userRows > tr > td:nth-child(6) p:first-child { margin: 0 !important; color: #455267 !important; font-size: 11px !important; font-weight: 400 !important; line-height: 1.6 !important; }
.user-page #userRows > tr > td:nth-child(3) p:last-child, .user-page #userRows .user-account-last-login > span, .user-page #userRows > tr > td:nth-child(6) p:last-child { color: #88919e !important; font-size: 10px !important; font-weight: 400 !important; line-height: 1.6 !important; }
.user-page #userRows .user-account-last-login .user-account-no-login { color: #929aa5 !important; font-size: 10px !important; }
.user-actions-cell { display: flex; justify-content: flex-end; }
.user-actions-trigger { display: grid; place-items: center; width: 32px; height: 32px; border: 1px solid #e5e8ed; border-radius: 8px; background: #fff; color: #637083; cursor: pointer; }
.user-actions-trigger:hover { background: #f4f6f8; color: #253246; }
.user-actions-menu[popover] { position: fixed; inset: auto; width: 216px; max-height: calc(100dvh - 16px); overflow-y: auto; margin: 0; padding: 6px; border: 1px solid #e4e7ec; border-radius: 12px; background: #fff; box-shadow: 0 14px 40px rgb(16 24 40 / 13%); }
.user-page #userRows .user-actions-menu .account-action { display: flex !important; align-items: center !important; justify-content: flex-start !important; gap: 10px !important; width: 100% !important; height: auto !important; min-height: 35px !important; padding: 8px 10px !important; border: 0 !important; border-radius: 7px !important; background: #fff !important; color: #4b5769 !important; box-shadow: none !important; transform: none !important; text-decoration: none; }
.user-page #userRows .user-actions-menu .account-action:hover { background: #f4f6f8 !important; }
.user-page #userRows .user-actions-menu .account-action span { display: inline !important; font-size: 11px !important; line-height: 1.4 !important; font-weight: 400 !important; white-space: normal; text-align: left; }
.user-page #userRows .user-actions-menu .account-action svg { width: 16px !important; height: 16px !important; flex: none; }
.user-page #userRows .user-actions-menu [data-delete-user], .user-page #userRows .user-actions-menu [data-ban-mode="ban"] { color: #b54747 !important; }
.user-table-footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 15px 20px; border-top: 1px solid #eef0f3; background: #fff; }
.user-table-footer #resultCount { margin: 0; color: #7c8795 !important; font-size: 11px !important; }
.user-pagination { display: flex; align-items: center; gap: 9px; font-size: 11px; color: #768293; }
.user-pagination button, .user-pagination select { min-height: 32px; padding: 5px 9px; border: 1px solid #e1e6ec; border-radius: 7px; background: #fff; font: inherit; color: #526074; }
.user-pagination button:disabled { opacity: .45; cursor: default; }
.user-pagination button:not(:disabled) { cursor: pointer; }
.user-pagination button:not(:disabled):hover { background: #f5f7f9; }
.user-actions-trigger:focus-visible, .user-actions-menu .account-action:focus-visible, .user-pagination :is(button,select):focus-visible { outline: 2px solid #c08b21 !important; outline-offset: 2px; }
@media (max-width: 640px) { .user-table-footer { padding: 13px 15px; } .user-pagination { flex-wrap: wrap; gap: 7px; } }

<style>
/* Override legacy icon-only pagination sizing on this page. */
.user-page #resultCount + .user-pagination > button { width: auto !important; min-width: 65px !important; height: 32px !important; padding: 5px 9px !important; font-size: 11px !important; }
.user-page #resultCount + .user-pagination > span { width: auto !important; min-width: 42px !important; height: auto !important; font-size: 11px !important; background: transparent !important; color: #768293 !important; box-shadow: none !important; }

<style>
.user-page table { min-width: 1060px !important; background: #fff; }
.user-page table thead tr { background: #f8fafc !important; }
.user-page table th { padding: 13px 16px !important; color: #78818f !important; font-size: 10px !important; font-weight: 600 !important; letter-spacing: .055em !important; white-space: nowrap; }
.user-page #userRows > tr > td { padding: 13px 16px !important; vertical-align: middle !important; text-align: left !important; border-color: #eef0f3 !important; }
.user-page #userRows > tr:hover { background: #fafbfc !important; }
.user-page #userRows > tr > td:first-child { min-width: 230px; width: 24% !important; }
.user-page #userRows > tr > td:nth-child(3) { min-width: 200px; }
.user-page #userRows > tr > td:nth-child(5), .user-page #userRows > tr > td:nth-child(6) { min-width: 120px; }
.user-account-identity { display: flex; align-items: center; gap: 11px; }
.user-account-avatar { position: relative; display: grid; place-items: center; flex: none; width: 38px; height: 38px; overflow: hidden; border: 1px solid #e4e7ec; border-radius: 50%; background: #f2f4f7; color: #697586; font-size: 11px; font-weight: 600; }
.user-account-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; background: #f2f4f7; }
.user-account-avatar img[hidden] { display: none !important; }
.user-page #userRows .user-account-name { max-width: 230px; margin: 0 !important; font-size: 12px !important; font-weight: 600 !important; color: #263244 !important; line-height: 1.5 !important; overflow-wrap: anywhere; }
.user-page #userRows .user-account-reference { margin: 3px 0 0 !important; color: #87909d !important; font-size: 10px !important; font-weight: 400 !important; letter-spacing: .02em !important; }
.user-page #userRows > tr > td:nth-child(2) > span, .user-page #userRows > tr > td:nth-child(4) > span { font-size: 10px !important; font-weight: 500 !important; padding: 5px 9px !important; }
.user-page #userRows > tr > td:nth-child(3) p:first-child, .user-page #userRows .user-account-last-login p, .user-page #userRows > tr > td:nth-child(6) p:first-child { margin: 0 !important; color: #455267 !important; font-size: 11px !important; font-weight: 400 !important; line-height: 1.6 !important; }
.user-page #userRows > tr > td:nth-child(3) p:last-child, .user-page #userRows .user-account-last-login > span, .user-page #userRows > tr > td:nth-child(6) p:last-child { color: #88919e !important; font-size: 10px !important; font-weight: 400 !important; line-height: 1.6 !important; }
.user-page #userRows .user-account-last-login .user-account-no-login { color: #929aa5 !important; font-size: 10px !important; }
.user-actions-cell { display: flex; justify-content: flex-end; }
.user-actions-trigger { display: grid; place-items: center; width: 32px; height: 32px; border: 1px solid #e5e8ed; border-radius: 8px; background: #fff; color: #637083; cursor: pointer; }
.user-actions-trigger:hover { background: #f4f6f8; color: #253246; }
.user-actions-menu[popover] { position: fixed; inset: auto; width: 216px; max-height: calc(100dvh - 16px); overflow-y: auto; margin: 0; padding: 6px; border: 1px solid #e4e7ec; border-radius: 12px; background: #fff; box-shadow: 0 14px 40px rgb(16 24 40 / 13%); }
.user-page #userRows .user-actions-menu .account-action { display: flex !important; align-items: center !important; justify-content: flex-start !important; gap: 10px !important; width: 100% !important; height: auto !important; min-height: 35px !important; padding: 8px 10px !important; border: 0 !important; border-radius: 7px !important; background: #fff !important; color: #4b5769 !important; box-shadow: none !important; transform: none !important; text-decoration: none; }
.user-page #userRows .user-actions-menu .account-action:hover { background: #f4f6f8 !important; }
.user-page #userRows .user-actions-menu .account-action span { display: inline !important; font-size: 11px !important; line-height: 1.4 !important; font-weight: 400 !important; white-space: normal; text-align: left; }
.user-page #userRows .user-actions-menu .account-action svg { width: 16px !important; height: 16px !important; flex: none; }
.user-page #userRows .user-actions-menu [data-delete-user], .user-page #userRows .user-actions-menu [data-ban-mode="ban"] { color: #b54747 !important; }
.user-table-footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 15px 20px; border-top: 1px solid #eef0f3; background: #fff; }
.user-table-footer #resultCount { margin: 0; color: #7c8795 !important; font-size: 11px !important; }
.user-pagination { display: flex; align-items: center; gap: 9px; font-size: 11px; color: #768293; }
.user-pagination button, .user-pagination select { min-height: 32px; padding: 5px 9px; border: 1px solid #e1e6ec; border-radius: 7px; background: #fff; font: inherit; color: #526074; }
.user-pagination button:disabled { opacity: .45; cursor: default; }
.user-pagination button:not(:disabled) { cursor: pointer; }
.user-pagination button:not(:disabled):hover { background: #f5f7f9; }
.user-actions-trigger:focus-visible, .user-actions-menu .account-action:focus-visible, .user-pagination :is(button,select):focus-visible { outline: 2px solid #c08b21 !important; outline-offset: 2px; }
@media (max-width: 640px) { .user-table-footer { padding: 13px 15px; } .user-pagination { flex-wrap: wrap; gap: 7px; } }

<style>
/* Override legacy icon-only pagination sizing on this page. */
.user-page #resultCount + .user-pagination > button { width: auto !important; min-width: 65px !important; height: 32px !important; padding: 5px 9px !important; font-size: 11px !important; }
.user-page #resultCount + .user-pagination > span { width: auto !important; min-width: 42px !important; height: auto !important; font-size: 11px !important; background: transparent !important; color: #768293 !important; box-shadow: none !important; }



/* Bright role badges. Status badges and summary icons retain their existing palette. */
.user-page #userRows > tr > td:nth-child(2) > .user-role-badge {
 display: inline-flex !important; align-items: center !important; justify-content: center !important;
 min-height: 26px; min-width: 64px; padding: 4px 11px !important; border-radius: 999px !important;
 background: var(--role-fill, #475569) !important; border: 1px solid var(--role-fill, #475569) !important;
 color: #fff !important; font-size: 11px !important; line-height: 1.5 !important; font-weight: 600 !important; box-shadow: none !important;
}
.user-role-badge--buyer { --role-fill: #2563eb; }
.user-role-badge--seller { --role-fill: #c94c08; }
.user-role-badge--rider { --role-fill: #0f766e; }
.user-role-badge--logistics { --role-fill: #7c3aed; }
.user-role-badge--other { --role-fill: #475569; }
/* Override legacy icon-only hover scaling inside the actions popover. */
.user-page #userRows .user-actions-menu[popover] {
 box-sizing: border-box !important; width: 216px !important; max-width: calc(100vw - 16px) !important;
 overflow-x: hidden !important; overflow-y: auto !important; padding: 6px !important; overscroll-behavior: contain;
 border: 1px solid #e4e7ec !important; border-radius: 12px !important; background: #fff !important;
 box-shadow: 0 8px 24px rgb(16 24 40 / 12%) !important;
}
.user-page #userRows .user-actions-menu[popover] .account-action,
.user-page #userRows .user-actions-menu[popover] .account-action:is(:hover,:focus,:focus-visible,:active) {
 box-sizing: border-box !important; display: flex !important; align-items: center !important;
 justify-content: flex-start !important; gap: 10px !important; width: 100% !important; min-width: 0 !important; max-width: 100% !important;
 min-height: 36px !important; height: auto !important; padding: 9px 10px !important; margin: 0 !important;
 border: 0 !important; border-radius: 7px !important; transform: none !important; scale: 1 !important;
 box-shadow: none !important; background: #fff !important; color: #414b5a !important; text-decoration: none !important;
 transition: background-color .15s ease, color .15s ease !important;
}
.user-page #userRows .user-actions-menu[popover] .account-action:is(:hover,:focus-visible) { background: #f3f4f6 !important; color: #202124 !important; }
.user-page #userRows .user-actions-menu[popover] .account-action:focus-visible { outline: 2px solid #a77818 !important; outline-offset: -2px !important; }
.user-page #userRows .user-actions-menu[popover] .account-action > span { display: block !important; flex: 1; min-width: 0 !important; white-space: normal !important; overflow-wrap: anywhere; font-size: 12px !important; line-height: 1.5 !important; }
.user-page #userRows .user-actions-menu[popover] .account-action > svg { flex: 0 0 16px !important; width: 16px !important; height: 16px !important; transform: none !important; color: currentColor !important; }
.user-page #userRows .user-actions-menu[popover] :is([data-delete-user],[data-ban-mode="ban"]) { color: #c73535 !important; }
.user-page #userRows .user-actions-menu[popover] :is([data-delete-user],[data-ban-mode="ban"]):is(:hover,:focus-visible) { background: #fff1f1 !important; color: #aa2525 !important; }
@media (prefers-reduced-motion: reduce) { .user-page #userRows .user-actions-menu[popover] .account-action { transition: none !important; } }


/* Bare filter row: individual controls remain, without the enclosing card. */
.user-page > section.user-surface.mt-4.p-3 {
    padding: 0 !important;
    background: transparent !important;
    background-color: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    overflow: visible !important;
}
.user-page > section.user-surface.mt-4.p-3 > div {
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    gap: 12px !important;
}
.user-page > section.user-surface.mt-4.p-3 :is(#userSearch,[data-role-toggle],[data-status-toggle],#resetFilter) {
    background: #fff !important;
    box-shadow: none !important;
}
</style>

<style>
/* Keep account table pagination readable under inherited button styles. */
.user-page .user-table-footer {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
    overflow: visible !important;
}
.user-page .user-table-footer #resultCount {
    flex: 1 1 220px;
    line-height: 1.5;
}
.user-page .user-table-footer .user-pagination {
    display: flex !important;
    flex: 0 1 auto;
    flex-wrap: wrap !important;
    align-items: center !important;
    justify-content: flex-end;
    gap: 10px !important;
    width: auto !important;
    min-width: 0;
    height: auto !important;
}
.user-page .user-pagination > * {
    position: static !important;
    flex: 0 0 auto !important;
    transform: none !important;
    margin: 0 !important;
    white-space: nowrap !important;
}
.user-page .user-pagination button {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    width: auto !important;
    min-width: 68px !important;
    min-height: 36px !important;
    height: auto !important;
    padding: 8px 12px !important;
    line-height: 18px !important;
    font-size: 11px !important;
    box-sizing: border-box;
    overflow: visible !important;
}
.user-page .user-pagination #userPreviousPage { min-width: 82px !important; }
.user-page .user-pagination #userPageNumber {
    display: inline-block !important;
    width: auto !important;
    min-width: 46px !important;
    padding: 0 4px !important;
    text-align: center;
    line-height: 36px !important;
    font-size: 11px !important;
}
.user-page .user-pagination #userPageSize {
    width: 66px !important;
    min-width: 66px !important;
    height: 36px !important;
    padding: 6px 26px 6px 10px !important;
    box-sizing: border-box;
    font-size: 11px !important;
    line-height: normal !important;
}
@media (max-width: 640px) {
    .user-page .user-table-footer .user-pagination {
        flex-basis: 100%;
        justify-content: flex-start;
        gap: 8px !important;
    }
}
</style>

<style>
/* Unified account pagination: neutral Previous, gold Next. */
.user-page .user-table-footer .user-pagination {
    gap: 8px !important;
    font-size: 12px !important;
}
.user-page .user-pagination label {
    color: #667085 !important;
    font-size: 12px !important;
    line-height: 36px !important;
}
.user-page .user-table-footer .user-pagination :is(button, #userPageSize) {
    height: 36px !important;
    min-height: 36px !important;
    border: 1px solid #E4E7EC !important;
    border-radius: 8px !important;
    box-shadow: none !important;
    font-family: inherit !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    line-height: 18px !important;
    letter-spacing: normal !important;
    text-indent: 0 !important;
    opacity: 1 !important;
    box-sizing: border-box !important;
}
.user-page .user-pagination #userPageSize {
    appearance: auto !important;
    -webkit-appearance: auto !important;
    width: 72px !important;
    min-width: 72px !important;
    padding: 6px 10px !important;
    background: #FFFFFF !important;
    background-image: none !important;
    color: #344054 !important;
    -webkit-text-fill-color: #344054 !important;
    cursor: pointer;
}
.user-page .user-pagination #userPreviousPage,
.user-page .user-pagination #userNextPage {
    width: 88px !important;
    min-width: 88px !important;
    padding: 8px 12px !important;
    background: #FFFFFF !important;
    color: #475467 !important;
    cursor: pointer;
}
.user-page .user-pagination #userPreviousPage:not(:disabled):hover {
    background: #F9FAFB !important;
    border-color: #CDD3DB !important;
}
.user-page .user-pagination #userNextPage {
    background: #D99500 !important;
    border-color: #D99500 !important;
    color: #211A0B !important;
    -webkit-text-fill-color: #211A0B !important;
    font-weight: 600 !important;
}
.user-page .user-pagination #userNextPage:not(:disabled):hover {
    background: #C78A00 !important;
    border-color: #C78A00 !important;
}
.user-page .user-pagination #userPreviousPage:disabled {
    background: #F9FAFB !important;
    border-color: #EAECF0 !important;
    color: #98A2B3 !important;
    -webkit-text-fill-color: #98A2B3 !important;
    cursor: not-allowed !important;
}
.user-page .user-pagination #userNextPage:disabled {
    background: #F8E8BE !important;
    border-color: #EED59B !important;
    color: #8A6B2C !important;
    -webkit-text-fill-color: #8A6B2C !important;
    cursor: not-allowed !important;
}
.user-page .user-pagination #userPageNumber {
    min-width: 52px !important;
    color: #667085 !important;
    font-size: 12px !important;
    font-variant-numeric: tabular-nums;
    line-height: 36px !important;
}
.user-page .user-table-footer .user-pagination :is(button, select):focus-visible {
    outline: 2px solid #B77C00 !important;
    outline-offset: 3px !important;
}
@media (max-width: 420px) {
    .user-page .user-table-footer .user-pagination { gap: 6px !important; }
    .user-page .user-pagination #userPreviousPage,
    .user-page .user-pagination #userNextPage {
        width: 76px !important;
        min-width: 76px !important;
        padding-inline: 8px !important;
    }
    .user-page .user-pagination #userPageSize { width: 60px !important; min-width: 60px !important; }
    .user-page .user-pagination #userPageNumber { min-width: 40px !important; }
}
</style>

<style>
/* Match Next to the Apply Filter gold and white label. */
.user-page .user-pagination #userNextPage:not(:disabled) {
    background: #D99500 !important;
    border-color: #D99500 !important;
    color: #FFFFFF !important;
    -webkit-text-fill-color: #FFFFFF !important;
}
.user-page .user-pagination #userNextPage:not(:disabled):hover {
    background: #B77C00 !important;
    border-color: #B77C00 !important;
    color: #FFFFFF !important;
    -webkit-text-fill-color: #FFFFFF !important;
}
</style>
