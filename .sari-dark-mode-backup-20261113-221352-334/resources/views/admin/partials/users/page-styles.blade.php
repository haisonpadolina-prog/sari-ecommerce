<style>
.fi {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        vertical-align: middle;
    }

    .account-action .fi {
        font-size: 14px;
    }
    .user-page {
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: #211c16;
    }

    .user-surface {
        background: #fff;
        border: 1px solid #ebe4da;
        border-radius: 18px;
        box-shadow: 0 15px 36px rgba(69, 55, 38, .065), 0 2px 8px rgba(69, 55, 38, .025), inset 0 1px 0 rgba(255,255,255,.96);
    }

    .metric-card {
        background: #fff;
        border: 1px solid #ece5dc;
        border-radius: 18px;
        box-shadow: 0 13px 28px rgba(63, 49, 32, .06), inset 0 1px 0 rgba(255,255,255,.96);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        border-color: #dfd1bd;
        box-shadow: 0 17px 36px rgba(63, 49, 32, .085), inset 0 1px 0 rgba(255,255,255,.96);
    }

    .metric-card.is-active {
        border-color: #d9b76b;
        box-shadow: 0 16px 34px rgba(188, 131, 17, .11), inset 0 0 0 1px rgba(217,149,0,.07);
    }

    .clean-control {
        background: #fff;
        border: 1px solid #e8e0d5;
        color: #332c25;
        transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
    }

    .clean-control:focus {
        outline: none;
        border-color: #d9a33a;
        box-shadow: 0 0 0 4px rgba(217,149,0,.08);
    }

    .custom-menu {
        opacity: 0;
        visibility: hidden;
        transform: translateY(-5px) scale(.985);
        transition: opacity .14s ease, transform .14s ease, visibility .14s ease;
        transform-origin: top;
        pointer-events: none;
    }

    .custom-menu.is-open {
        opacity: 1;
        visibility: visible;
        transform: translateY(6px) scale(1);
        pointer-events: auto;
    }

    [data-user-row] { transition: background-color .14s ease; }
    [data-user-row]:hover { background: #fdfbf7; }
    [data-user-row][hidden] { display: none !important; }

    #profileBackdrop {
        opacity: 0;
        pointer-events: none;
        transition: opacity .18s ease;
    }

    #profileBackdrop.is-open {
        opacity: 1;
        pointer-events: auto;
    }

    #profileDrawer {
        transform: translateX(102%);
        transition: transform .22s cubic-bezier(.22,.61,.36,1);
        box-shadow: -22px 0 60px rgba(39, 30, 20, .12);
    }

    #profileDrawer.is-open { transform: translateX(0); }
    body.user-drawer-open { overflow: hidden; }

    .drawer-tab {
        color: #746b61;
        border-bottom: 2px solid transparent;
    }

    .drawer-tab.is-active {
        color: #a8731f;
        border-bottom-color: #d99500;
    }

    [data-drawer-panel][hidden] { display: none !important; }

    .account-action {
        display: inline-grid;
        height: 36px;
        width: 36px;
        place-items: center;
        border: 1px solid #e6dfd6;
        border-radius: 10px;
        background: #fff;
        color: #6f675e;
        box-shadow: 0 4px 10px rgba(52, 41, 27, .025), inset 0 1px 0 rgba(255,255,255,.96);
        transition: transform .14s ease, border-color .14s ease, background-color .14s ease, color .14s ease, box-shadow .14s ease;
    }

    .account-action:hover {
        transform: translateY(-1px);
        border-color: #d8c8b1;
        background: #fffaf2;
        color: #9c6c1f;
        box-shadow: 0 8px 18px rgba(88, 64, 31, .075);
    }

    .account-action--message:hover {
        border-color: #cddfef;
        background: #f3f8fc;
        color: #3d79a8;
    }

    .account-action--edit:hover {
        border-color: #ead09b;
        background: #fff8e9;
        color: #a97314;
    }

    .account-action--activity:hover,
    .account-action--note:hover {
        border-color: #d9d5e8;
        background: #f8f6fc;
        color: #705da2;
    }

    .account-action--suspend {
        border-color: #ead8d1;
        color: #a96952;
        background: #fffaf8;
    }

    .account-action--suspend:hover {
        border-color: #dfb8aa;
        background: #fff3ef;
        color: #b45f42;
    }

    .account-action--restore {
        border-color: #d7e7dc;
        color: #4f8160;
        background: #f8fcf9;
    }

    .account-action--restore:hover {
        border-color: #bcd9c6;
        background: #f0f8f2;
        color: #3f7653;
    }

    @media (prefers-reduced-motion: reduce) {
        .metric-card, .custom-menu, #profileDrawer, #profileBackdrop, [data-user-row] { transition: none !important; }
    }

    /* =========================================================
       SARI SOFT FLOATING DEPTH
       Subtle elevation only — no layout or functionality changes.
       ========================================================= */

    .user-surface {
        border-color: #e9e1d7 !important;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .035),
            0 12px 28px rgba(61, 43, 22, .07),
            0 24px 50px rgba(61, 43, 22, .032) !important;
    }

    .metric-card {
        border-color: #e9e1d7 !important;
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .035),
            0 10px 24px rgba(61, 43, 22, .065),
            0 20px 38px rgba(61, 43, 22, .026) !important;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        border-color: #ddcfb8 !important;
        box-shadow:
            0 3px 6px rgba(61, 43, 22, .04),
            0 15px 32px rgba(61, 43, 22, .085),
            0 26px 46px rgba(61, 43, 22, .035) !important;
    }

    .metric-card.is-active {
        border-color: #dcbf7d !important;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .035),
            0 13px 30px rgba(154, 104, 14, .095),
            0 24px 44px rgba(154, 104, 14, .038) !important;
    }

    /* Filter/search controls stay crisp inside the floating outer surface. */
    .clean-control {
        box-shadow:
            0 1px 2px rgba(61, 43, 22, .018),
            0 4px 10px rgba(61, 43, 22, .025);
    }

    .clean-control:focus {
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 6px 16px rgba(61, 43, 22, .045) !important;
    }

    .custom-menu {
        box-shadow:
            0 8px 18px rgba(47, 37, 25, .08),
            0 20px 42px rgba(47, 37, 25, .12) !important;
    }

    #exportUsers {
        box-shadow:
            0 2px 4px rgba(63, 49, 32, .025),
            0 9px 20px rgba(63, 49, 32, .065) !important;
    }

    .account-action {
        box-shadow:
            0 2px 4px rgba(52, 41, 27, .025),
            0 6px 14px rgba(52, 41, 27, .04) !important;
    }

    .account-action:hover {
        box-shadow:
            0 3px 6px rgba(52, 41, 27, .03),
            0 9px 20px rgba(88, 64, 31, .075) !important;
    }

    #profileDrawer {
        box-shadow:
            -10px 0 24px rgba(31, 24, 17, .055),
            -28px 0 60px rgba(31, 24, 17, .105) !important;
    }

    /* Slightly reduce elevation on small screens. */
    @media (max-width: 767px) {
        .user-surface,
        .metric-card {
            box-shadow:
                0 2px 5px rgba(61, 43, 22, .03),
                0 10px 24px rgba(61, 43, 22, .06) !important;
        }
    }

    /* Clean selected state: filtering stays functional, visual effect stays minimal. */
    .metric-card.is-active {
        border-color: #e9e1d7 !important;
        background: #fff !important;
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .035),
            0 10px 24px rgba(61, 43, 22, .065),
            0 20px 38px rgba(61, 43, 22, .026) !important;
    }

    .metric-card.is-active::after {
        display: none !important;
        content: none !important;
    }



    /* =========================================================
       USER MANAGEMENT — READABLE TYPOGRAPHY PASS
       Increases information density readability without changing logic/layout.
       ========================================================= */

    /* Summary cards */
    .user-page .metric-card {
        min-height: 110px;
    }

    .user-page .metric-card > div > div > p:nth-child(1) {
        font-size: 11.5px !important;
        line-height: 1.35 !important;
        font-weight: 500 !important;
    }

    .user-page .metric-card > div > div > p:nth-child(2) {
        font-size: 26px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
    }

    .user-page .metric-card > div > div > p:nth-child(3) {
        font-size: 9px !important;
        line-height: 1.35 !important;
        font-weight: 400 !important;
    }

    /* Filter bar */
    .user-page #userSearch {
        font-size: 11px !important;
    }

    .user-page [data-role-toggle],
    .user-page [data-status-toggle],
    .user-page #applyFilter,
    .user-page #resetFilter {
        font-size: 11px !important;
    }

    .user-page [data-role-option],
    .user-page [data-status-option] {
        font-size: 10px !important;
    }

    /* Table headings */
    .user-page table thead th {
        font-size: 10px !important;
        line-height: 1.35 !important;
        letter-spacing: .07em !important;
    }

    /* Row number */
    .user-page [data-user-row] > td:first-child {
        font-size: 11px !important;
    }

    /* Name + account ID */
    .user-page [data-user-row] > td:nth-child(2) p:first-child {
        font-size: 12px !important;
        line-height: 1.35 !important;
    }

    .user-page [data-user-row] > td:nth-child(2) p:last-child {
        font-size: 9.5px !important;
        line-height: 1.35 !important;
    }

    /* Role badge */
    .user-page [data-user-row] > td:nth-child(3) > span {
        font-size: 9.5px !important;
    }

    /* Contact */
    .user-page [data-user-row] > td:nth-child(4) p:first-child {
        font-size: 10.5px !important;
        line-height: 1.4 !important;
    }

    .user-page [data-user-row] > td:nth-child(4) p:last-child {
        font-size: 9px !important;
        line-height: 1.4 !important;
    }

    /* Status badge */
    .user-page [data-user-row] > td:nth-child(5) > span {
        font-size: 9.5px !important;
    }

    /* Joined */
    .user-page [data-user-row] > td:nth-child(6) p:first-child {
        font-size: 10px !important;
        line-height: 1.4 !important;
    }

    .user-page [data-user-row] > td:nth-child(6) p:last-child {
        font-size: 9px !important;
        line-height: 1.4 !important;
    }

    /* Recent activity */
    .user-page [data-user-row] > td:nth-child(7) p:first-child {
        font-size: 10px !important;
        line-height: 1.45 !important;
    }

    .user-page [data-user-row] > td:nth-child(7) p:last-child {
        font-size: 8.5px !important;
        line-height: 1.4 !important;
    }

    /* Footer / pagination */
    .user-page #resultCount {
        font-size: 10px !important;
    }

    .user-page #resultCount + div > span {
        font-size: 10px !important;
    }

    @media (min-width: 1536px) {
        .user-page .metric-card > div > div > p:nth-child(1) {
            font-size: 12px !important;
        }

        .user-page .metric-card > div > div > p:nth-child(2) {
            font-size: 27px !important;
        }

        .user-page table thead th {
            font-size: 10.5px !important;
        }

        .user-page [data-user-row] > td:nth-child(2) p:first-child {
            font-size: 12.5px !important;
        }
    }


    /* =========================================================
       STRONGER FLOATING DEPTH — USER MANAGEMENT
       Same elevated treatment used on the registrations page.
       Visual override only; no routes, IDs, forms, or JS changed.
       ========================================================= */

    .user-page .user-surface {
        border-color: #e7ddd1 !important;
        box-shadow:
            0 3px 8px rgba(61, 43, 22, .045),
            0 18px 42px rgba(61, 43, 22, .095),
            0 38px 78px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .user-page .metric-card {
        border-color: #e7ddd1 !important;
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .04),
            0 15px 34px rgba(61, 43, 22, .085),
            0 30px 58px rgba(61, 43, 22, .038),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .user-page .metric-card:hover {
        transform: translateY(-3px) !important;
        border-color: #d9c9b1 !important;
        box-shadow:
            0 4px 9px rgba(61, 43, 22, .05),
            0 21px 46px rgba(61, 43, 22, .115),
            0 40px 76px rgba(61, 43, 22, .048),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .user-page .metric-card.is-active {
        border-color: #e7ddd1 !important;
        background: #fff !important;
        box-shadow:
            0 3px 7px rgba(61, 43, 22, .04),
            0 15px 34px rgba(61, 43, 22, .085),
            0 30px 58px rgba(61, 43, 22, .038),
            inset 0 1px 0 rgba(255,255,255,.98) !important;
    }

    .user-page .clean-control {
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .025),
            0 7px 16px rgba(61, 43, 22, .045),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .user-page .clean-control:hover {
        border-color: #d8c8b1 !important;
        box-shadow:
            0 2px 5px rgba(61, 43, 22, .03),
            0 9px 20px rgba(61, 43, 22, .055),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .user-page .clean-control:focus {
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 10px 24px rgba(61, 43, 22, .07),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .user-page .custom-menu {
        box-shadow:
            0 8px 18px rgba(47, 37, 25, .09),
            0 24px 52px rgba(47, 37, 25, .15) !important;
    }

    .user-page #exportUsers {
        box-shadow:
            0 3px 7px rgba(63, 49, 32, .035),
            0 13px 28px rgba(63, 49, 32, .075),
            0 24px 44px rgba(63, 49, 32, .028) !important;
    }

    .user-page #exportUsers:hover {
        transform: translateY(-1px);
        box-shadow:
            0 4px 8px rgba(63, 49, 32, .04),
            0 16px 34px rgba(63, 49, 32, .095),
            0 28px 50px rgba(63, 49, 32, .032) !important;
    }

    .user-page #applyFilter {
        box-shadow:
            0 3px 7px rgba(183, 124, 0, .10),
            0 13px 28px rgba(217, 149, 0, .23) !important;
    }

    .user-page #applyFilter:hover {
        transform: translateY(-1px);
        box-shadow:
            0 4px 8px rgba(183, 124, 0, .12),
            0 16px 34px rgba(183, 124, 0, .26) !important;
    }

    .user-page #resetFilter {
        box-shadow:
            0 2px 4px rgba(61, 43, 22, .025),
            0 7px 16px rgba(61, 43, 22, .045) !important;
    }

    .user-page .account-action {
        box-shadow:
            0 2px 4px rgba(52, 41, 27, .03),
            0 8px 18px rgba(52, 41, 27, .055),
            inset 0 1px 0 rgba(255,255,255,.96) !important;
    }

    .user-page .account-action:hover {
        transform: translateY(-2px);
        box-shadow:
            0 3px 6px rgba(52, 41, 27, .04),
            0 12px 26px rgba(88, 64, 31, .10) !important;
    }

    #profileDrawer {
        box-shadow:
            -14px 0 32px rgba(31, 24, 17, .075),
            -38px 0 82px rgba(31, 24, 17, .145) !important;
    }

    #profileDrawer .user-surface,
    #profileDrawer section.rounded-\[16px\] {
        box-shadow:
            0 3px 7px rgba(67, 48, 24, .035),
            0 15px 34px rgba(67, 48, 24, .08),
            0 28px 54px rgba(67, 48, 24, .032) !important;
    }

    @media (max-width: 767px) {
        .user-page .user-surface,
        .user-page .metric-card {
            box-shadow:
                0 3px 7px rgba(61, 43, 22, .035),
                0 14px 32px rgba(61, 43, 22, .075),
                0 24px 46px rgba(61, 43, 22, .025) !important;
        }

        .user-page .metric-card:hover {
            transform: translateY(-1px) !important;
        }

        #profileDrawer .user-surface,
        #profileDrawer section.rounded-\[16px\] {
            box-shadow:
                0 2px 5px rgba(61, 43, 22, .03),
                0 10px 24px rgba(61, 43, 22, .06) !important;
        }
    }


    /* =========================================================
       EXACT SELLER COMPLIANCE HEADER PARITY — USER MANAGEMENT
       Header only. Existing cards, filters, table, drawer, and JS stay intact.
       ========================================================= */

    .user-page .user-page-header {
        margin-bottom: 16px !important;
    }

    .user-page .user-page-header-main {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .user-page .user-page-header-icon {
        display: grid;
        width: 44px !important;
        height: 44px !important;
        flex: 0 0 44px !important;
        place-items: center;
        border: 1px solid #eadfc9 !important;
        border-radius: 14px !important;
        background: #fff8eb !important;
        color: #b77c18 !important;
        box-shadow:
            0 2px 5px rgba(75, 54, 25, .03),
            0 9px 20px rgba(75, 54, 25, .06) !important;
    }

    .user-page .user-page-header-icon svg {
        width: 18px !important;
        height: 18px !important;
    }

    .user-page .user-page-eyebrow {
        margin: 0 !important;
        color: #9a7b43 !important;
        -webkit-text-fill-color: #9a7b43 !important;
        font-size: 9px !important;
        font-weight: 600 !important;
        line-height: 1.2 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .user-page .user-page-title {
        margin: 4px 0 0 !important;
        font-size: clamp(1.75rem, 1.55rem + .5vw, 2.15rem) !important;
        font-weight: 700 !important;
        line-height: 1.08 !important;
        letter-spacing: -.04em !important;
    }

    .user-page .user-page-title-base {
        color: #17130f !important;
        -webkit-text-fill-color: #17130f !important;
    }

    .user-page .user-page-title-accent {
        color: #d99500 !important;
        -webkit-text-fill-color: #d99500 !important;
    }

    .user-page .user-page-subtitle {
        max-width: 900px !important;
        margin: 6px 0 0 !important;
        color: #81786c !important;
        -webkit-text-fill-color: #81786c !important;
        font-size: clamp(.73rem, .70rem + .08vw, .81rem) !important;
        font-weight: 400 !important;
        line-height: 1.65 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
    }

    .user-page .user-page-export {
        min-height: 39px !important;
        border-radius: 10px !important;
        padding-inline: 16px !important;
        font-size: clamp(.72rem, .69rem + .06vw, .78rem) !important;
        font-weight: 600 !important;
    }

    @media (max-width: 767px) {
        .user-page .user-page-header-main {
            align-items: flex-start;
            gap: 12px;
        }

        .user-page .user-page-header-icon {
            width: 42px !important;
            height: 42px !important;
            flex-basis: 42px !important;
            border-radius: 13px !important;
        }

        .user-page .user-page-eyebrow {
            font-size: 8.5px !important;
        }

        .user-page .user-page-title {
            font-size: 1.65rem !important;
        }

        .user-page .user-page-subtitle {
            font-size: .72rem !important;
        }

        .user-page .user-page-export {
            width: 100%;
        }
    }


    /* =========================================================
       SUMMARY ICON CONTAINERS — ROUNDED BOX STYLE
       Replaces circular icon containers with compact enterprise boxes.
       ========================================================= */

    .user-page .metric-card > div > span:first-child {
        border-radius: 12px !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.025),
            0 7px 16px rgba(61,43,22,.045),
            inset 0 1px 0 rgba(255,255,255,.92);
    }

    .user-page .metric-card > div > span:last-child {
        border-radius: 10px !important;
        box-shadow:
            0 2px 4px rgba(61,43,22,.02),
            0 6px 14px rgba(61,43,22,.035);
    }


    /* =========================================================
       ACTION ICONS — VISIBLE COLOR + STRONGER HOVER FEEDBACK
       Keeps the same button sizing and behavior; only visual color states change.
       ========================================================= */

    /* View profile — warm gold */
    .user-page .account-action:not(.account-action--message):not(.account-action--edit):not(.account-action--activity):not(.account-action--note):not(.account-action--suspend):not(.account-action--restore) {
        border-color: #ead8b9 !important;
        background: #fffaf1 !important;
        color: #b47a19 !important;
    }

    .user-page .account-action:not(.account-action--message):not(.account-action--edit):not(.account-action--activity):not(.account-action--note):not(.account-action--suspend):not(.account-action--restore):hover {
        border-color: #d9b974 !important;
        background: #fff3dc !important;
        color: #8f5f0d !important;
    }

    /* Message — blue */
    .user-page .account-action--message {
        border-color: #d6e6f3 !important;
        background: #f6fbff !important;
        color: #4f89b8 !important;
    }

    .user-page .account-action--message:hover {
        border-color: #b9d4e8 !important;
        background: #edf7fe !important;
        color: #2f73aa !important;
    }

    /* Edit — amber */
    .user-page .account-action--edit {
        border-color: #ead9b5 !important;
        background: #fffaf0 !important;
        color: #b77a13 !important;
    }

    .user-page .account-action--edit:hover {
        border-color: #d8bd7b !important;
        background: #fff3dc !important;
        color: #925d08 !important;
    }

    /* Recent activity — violet */
    .user-page .account-action--activity {
        border-color: #ddd7ed !important;
        background: #faf8ff !important;
        color: #7564a9 !important;
    }

    .user-page .account-action--activity:hover {
        border-color: #c8bee3 !important;
        background: #f3effd !important;
        color: #5d4999 !important;
    }

    /* Admin note — teal */
    .user-page .account-action--note {
        border-color: #d4e6e3 !important;
        background: #f6fbfa !important;
        color: #4e887f !important;
    }

    .user-page .account-action--note:hover {
        border-color: #bcd7d2 !important;
        background: #eef8f6 !important;
        color: #34746a !important;
    }

    /* Suspend — coral/red */
    .user-page .account-action--suspend {
        border-color: #ead4cc !important;
        background: #fff8f5 !important;
        color: #b8684b !important;
    }

    .user-page .account-action--suspend:hover {
        border-color: #deb7aa !important;
        background: #fff0eb !important;
        color: #a84f32 !important;
    }

    /* Restore — green */
    .user-page .account-action--restore {
        border-color: #d1e3d7 !important;
        background: #f6fbf7 !important;
        color: #56856a !important;
    }

    .user-page .account-action--restore:hover {
        border-color: #b7d4c1 !important;
        background: #eef8f1 !important;
        color: #3f7653 !important;
    }

    /* Make the icon strokes visually clearer without making buttons heavier */
    .user-page .account-action .fi {
        font-size: 14px !important;
        opacity: .96;
        transition:
            color .15s ease,
            transform .15s ease,
            opacity .15s ease;
    }

    .user-page .account-action:hover .fi {
        opacity: 1;
        transform: scale(1.06);
    }


    /* =========================================================
       ACTION ICONS — CLEAN ICON-ONLY STYLE
       No visible containers, borders, backgrounds, or shadows.
       Color and micro-interaction live on the icon itself.
       ========================================================= */

    .user-page td .account-action {
        display: inline-grid !important;
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        place-items: center !important;

        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;

        color: #746f69 !important;
        opacity: 1 !important;

        transition:
            color .15s ease,
            transform .15s ease,
            opacity .15s ease !important;
    }

    .user-page td .account-action:hover,
    .user-page td .account-action:focus-visible {
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        transform: translateY(-1px) scale(1.07) !important;
        outline: none !important;
    }

    .user-page td .account-action .fi {
        font-size: 15px !important;
        line-height: 1 !important;
        color: currentColor !important;
        opacity: 1 !important;
        transform: none !important;
        transition: color .15s ease !important;
    }

    .user-page td .account-action:hover .fi,
    .user-page td .account-action:focus-visible .fi {
        transform: none !important;
    }

    /* View profile — clean neutral */
    .user-page td .account-action:not(.account-action--message):not(.account-action--edit):not(.account-action--activity):not(.account-action--note):not(.account-action--suspend):not(.account-action--restore) {
        color: #6f716f !important;
    }

    .user-page td .account-action:not(.account-action--message):not(.account-action--edit):not(.account-action--activity):not(.account-action--note):not(.account-action--suspend):not(.account-action--restore):hover,
    .user-page td .account-action:not(.account-action--message):not(.account-action--edit):not(.account-action--activity):not(.account-action--note):not(.account-action--suspend):not(.account-action--restore):focus-visible {
        color: #343634 !important;
    }

    /* Message — blue */
    .user-page td .account-action--message {
        color: #5c8fba !important;
    }

    .user-page td .account-action--message:hover,
    .user-page td .account-action--message:focus-visible {
        color: #2f73aa !important;
    }

    /* Edit — amber */
    .user-page td .account-action--edit {
        color: #c0831b !important;
    }

    .user-page td .account-action--edit:hover,
    .user-page td .account-action--edit:focus-visible {
        color: #945f0a !important;
    }

    /* Activity — violet */
    .user-page td .account-action--activity {
        color: #7b6aab !important;
    }

    .user-page td .account-action--activity:hover,
    .user-page td .account-action--activity:focus-visible {
        color: #594493 !important;
    }

    /* Note — teal */
    .user-page td .account-action--note {
        color: #5b928a !important;
    }

    .user-page td .account-action--note:hover,
    .user-page td .account-action--note:focus-visible {
        color: #34756b !important;
    }

    /* Suspend — coral / red */
    .user-page td .account-action--suspend {
        color: #c16d55 !important;
    }

    .user-page td .account-action--suspend:hover,
    .user-page td .account-action--suspend:focus-visible {
        color: #a84d32 !important;
    }

    /* Restore — green */
    .user-page td .account-action--restore {
        color: #5f8e6f !important;
    }

    .user-page td .account-action--restore:hover,
    .user-page td .account-action--restore:focus-visible {
        color: #3f7552 !important;
    }

    /* Slightly cleaner spacing between icon-only actions */
    .user-page [data-user-row] td:last-child > div {
        gap: 7px !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .user-page td .account-action,
        .user-page td .account-action:hover,
        .user-page td .account-action:focus-visible {
            transform: none !important;
            transition: none !important;
        }
    }


    /* =========================================================
       ACTION ICONS — BLACK BY DEFAULT / BRIGHT COLOR ON HOVER
       Final specificity override for the Users table action column.
       No visible container, border, background, or shadow.
       ========================================================= */

    .user-page #userRows .account-action {
        display: inline-grid !important;
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        place-items: center !important;
        padding: 0 !important;

        border: 0 !important;
        border-color: transparent !important;
        border-radius: 0 !important;
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;

        color: #3f3b37 !important;

        transition:
            color .15s ease,
            transform .15s ease !important;
    }

    .user-page #userRows .account-action .fi {
        color: currentColor !important;
        font-size: 15px !important;
        opacity: 1 !important;
        filter: none !important;
        transform: none !important;
        transition: color .15s ease !important;
    }

    /* Remove every old tinted container state, including the View icon. */
    .user-page #userRows .account-action:hover,
    .user-page #userRows .account-action:focus,
    .user-page #userRows .account-action:focus-visible,
    .user-page #userRows .account-action:active {
        border: 0 !important;
        border-color: transparent !important;
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;
        outline: none !important;
    }

    /* Default state: every action is dark/black. */
    .user-page #userRows [data-view-user].account-action,
    .user-page #userRows .account-action--message,
    .user-page #userRows .account-action--edit,
    .user-page #userRows .account-action--activity,
    .user-page #userRows .account-action--note,
    .user-page #userRows .account-action--restore {
        color: #3f3b37 !important;
    }

    /* Ban / Suspend remains red even before hover. */
    .user-page #userRows .account-action--suspend {
        color: #d95745 !important;
    }

    /* Bright line colors appear only on hover / keyboard focus. */
    .user-page #userRows [data-view-user].account-action:hover,
    .user-page #userRows [data-view-user].account-action:focus-visible {
        color: #e09a00 !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    .user-page #userRows .account-action--message:hover,
    .user-page #userRows .account-action--message:focus-visible {
        color: #188fe8 !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    .user-page #userRows .account-action--edit:hover,
    .user-page #userRows .account-action--edit:focus-visible {
        color: #f39a0a !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    .user-page #userRows .account-action--activity:hover,
    .user-page #userRows .account-action--activity:focus-visible {
        color: #8057e8 !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    .user-page #userRows .account-action--note:hover,
    .user-page #userRows .account-action--note:focus-visible {
        color: #15a39a !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    .user-page #userRows .account-action--suspend:hover,
    .user-page #userRows .account-action--suspend:focus-visible {
        color: #f04438 !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    .user-page #userRows .account-action--restore:hover,
    .user-page #userRows .account-action--restore:focus-visible {
        color: #20a45b !important;
        transform: translateY(-1px) scale(1.08) !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .user-page #userRows .account-action,
        .user-page #userRows .account-action:hover,
        .user-page #userRows .account-action:focus-visible {
            transform: none !important;
            transition: none !important;
        }
    }


    /* =========================================================
       SUMMARY CARDS — NO ARROW AFFORDANCE
       Entire metric card remains clickable for filtering.
       ========================================================= */

    .user-page .metric-card > div {
        justify-content: flex-start !important;
    }

    .user-page .metric-card {
        cursor: pointer;
    }

    .user-page .metric-card:focus-visible {
        outline: none;
        border-color: #d9b76b !important;
        box-shadow:
            0 0 0 4px rgba(217,149,0,.08),
            0 15px 34px rgba(61,43,22,.085),
            0 30px 58px rgba(61,43,22,.038) !important;
    }


    /* ============================================================
       USER MANAGEMENT — FINAL COMPACT + PAINT PERFORMANCE LAYER
       Visual sizing only; backend/routes/forms stay untouched.
       ============================================================ */

    .user-page {
        max-width: 1640px !important;
        padding-bottom: 18px !important;
    }

    /* Reduce repaint cost from the previous multi-layer shadow stack. */
    .user-page .user-surface,
    .user-page .metric-card {
        box-shadow: 0 8px 24px rgba(61, 43, 22, .055) !important;
    }

    .user-page .metric-card:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 10px 28px rgba(61, 43, 22, .075) !important;
    }

    .user-page .metric-card,
    .user-page .user-surface {
        contain: paint;
    }

    /* Header */
    .user-page .user-page-header {
        margin-bottom: 12px !important;
        gap: 10px !important;
    }

    .user-page .user-page-header-main {
        gap: 10px !important;
    }

    .user-page .user-page-header-icon {
        width: 36px !important;
        height: 36px !important;
        flex-basis: 36px !important;
        border-radius: 10px !important;
    }

    .user-page .user-page-header-icon svg {
        width: 15px !important;
        height: 15px !important;
    }

    .user-page .user-page-eyebrow {
        font-size: 7px !important;
        letter-spacing: .13em !important;
    }

    .user-page .user-page-title {
        margin-top: 3px !important;
        font-size: clamp(22px, 1.55vw, 27px) !important;
        line-height: 1.08 !important;
        letter-spacing: -.035em !important;
    }

    .user-page .user-page-subtitle {
        max-width: 760px !important;
        margin-top: 5px !important;
        font-size: 9.5px !important;
        line-height: 1.55 !important;
    }

    .user-page .user-page-export {
        min-height: 34px !important;
        border-radius: 9px !important;
        padding-inline: 12px !important;
        font-size: 9px !important;
        box-shadow: none !important;
    }

    .user-page .user-page-export svg {
        width: 12px !important;
        height: 12px !important;
    }

    /* Metric cards */
    .user-page > section.grid {
        gap: 9px !important;
    }

    .user-page .metric-card {
        min-height: 78px !important;
        border-radius: 13px !important;
        padding: 11px 13px !important;
        padding-right: 52px !important;
    }

    .user-page .metric-card > div {
        gap: 9px !important;
    }

    .user-page .metric-card > div > span:first-child {
        right: 12px !important;
        top: 12px !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 9px !important;
    }

    .user-page .metric-card > div > span:first-child svg {
        width: 14px !important;
        height: 14px !important;
    }

    .user-page .metric-card > div > div > p:nth-child(1) {
        font-size: 8.5px !important;
        line-height: 1.3 !important;
    }

    .user-page .metric-card > div > div > p:nth-child(2) {
        margin-top: 3px !important;
        font-size: 20px !important;
    }

    .user-page .metric-card > div > div > p:nth-child(3) {
        margin-top: 6px !important;
        font-size: 7.5px !important;
    }

    /* Filter bar */
    .user-page > section.user-surface.mt-4.p-3 {
        margin-top: 11px !important;
        border-radius: 14px !important;
        padding: 9px !important;
    }

    .user-page > section.user-surface.mt-4.p-3 > div {
        gap: 8px !important;
    }

    .user-page #userSearch,
    .user-page [data-role-toggle],
    .user-page [data-status-toggle],
    .user-page #applyFilter,
    .user-page #resetFilter {
        height: 38px !important;
        border-radius: 9px !important;
        font-size: 9.5px !important;
    }

    .user-page #userSearch {
        padding-left: 36px !important;
        padding-right: 10px !important;
    }

    .user-page #userSearch::placeholder {
        font-size: 9.5px !important;
    }

    .user-page #userSearch + * {
        font-size: inherit;
    }

    .user-page > section.user-surface.mt-4.p-3 .relative > svg.pointer-events-none {
        left: 12px !important;
        width: 14px !important;
        height: 14px !important;
    }

    .user-page [data-role-dropdown] {
        width: 150px !important;
    }

    .user-page [data-status-dropdown] {
        width: 145px !important;
    }

    .user-page [data-role-toggle],
    .user-page [data-status-toggle] {
        padding-inline: 10px !important;
    }

    .user-page [data-role-toggle] svg,
    .user-page [data-status-toggle] svg {
        width: 12px !important;
        height: 12px !important;
    }

    .user-page .custom-menu {
        border-radius: 10px !important;
        padding: 4px !important;
        box-shadow: 0 12px 28px rgba(47, 37, 25, .11) !important;
    }

    .user-page [data-role-option],
    .user-page [data-status-option] {
        min-height: 30px !important;
        border-radius: 7px !important;
        padding: 0 8px !important;
        font-size: 8.5px !important;
    }

    .user-page #applyFilter {
        padding-inline: 12px !important;
        box-shadow: 0 5px 14px rgba(217, 149, 0, .15) !important;
    }

    .user-page #resetFilter {
        padding-inline: 11px !important;
        box-shadow: none !important;
    }

    /* Users table */
    .user-page > section.user-surface.mt-4.overflow-hidden {
        margin-top: 11px !important;
        border-radius: 14px !important;
    }

    .user-page table {
        min-width: 1080px !important;
    }

    .user-page table thead th {
        padding-top: 8px !important;
        padding-bottom: 8px !important;
        font-size: 7.5px !important;
        letter-spacing: .065em !important;
    }

    .user-page [data-user-row] > td {
        padding-top: 9px !important;
        padding-bottom: 9px !important;
    }

    .user-page [data-user-row] > td:first-child {
        font-size: 8px !important;
    }

    .user-page [data-user-row] > td:nth-child(2) > div {
        gap: 8px !important;
    }

    .user-page [data-user-row] > td:nth-child(2) > div > span {
        width: 30px !important;
        height: 30px !important;
        font-size: 8px !important;
    }

    .user-page [data-user-row] > td:nth-child(2) p:first-child {
        font-size: 9px !important;
    }

    .user-page [data-user-row] > td:nth-child(2) p:last-child,
    .user-page [data-user-row] > td:nth-child(4) p:last-child,
    .user-page [data-user-row] > td:nth-child(6) p:last-child,
    .user-page [data-user-row] > td:nth-child(7) p:last-child {
        font-size: 7px !important;
    }

    .user-page [data-user-row] > td:nth-child(3) > span,
    .user-page [data-user-row] > td:nth-child(5) > span {
        padding: 3px 7px !important;
        font-size: 7px !important;
    }

    .user-page [data-user-row] > td:nth-child(4) p:first-child,
    .user-page [data-user-row] > td:nth-child(6) p:first-child,
    .user-page [data-user-row] > td:nth-child(7) p:first-child {
        font-size: 8px !important;
    }

    .user-page #userRows .account-action {
        width: 26px !important;
        height: 26px !important;
        min-width: 26px !important;
    }

    .user-page #userRows .account-action-svg {
        width: 14px !important;
        height: 14px !important;
        display: block;
        color: currentColor;
    }

    .user-page #userRows [data-user-row] td:last-child > div {
        gap: 5px !important;
    }

    /* Footer / pagination */
    .user-page > section.user-surface.mt-4.overflow-hidden > div:last-child {
        padding: 10px 14px !important;
    }

    .user-page #resultCount,
    .user-page #resultCount + div > span {
        font-size: 8px !important;
    }

    .user-page #resultCount + div > button,
    .user-page #resultCount + div > span {
        width: 30px !important;
        min-width: 30px !important;
        height: 30px !important;
        border-radius: 8px !important;
    }

    /* Drawer */
    #profileDrawer {
        max-width: 560px !important;
        box-shadow: -12px 0 34px rgba(31, 24, 17, .11) !important;
    }

    #profileDrawer > div > div:first-child {
        padding: 11px 14px !important;
    }

    #profileDrawer #drawerName {
        margin-top: 3px !important;
        font-size: 15px !important;
    }

    #profileDrawer #closeProfile {
        width: 32px !important;
        height: 32px !important;
        border-radius: 9px !important;
    }

    #profileDrawer .drawer-tab {
        padding-top: 9px !important;
        padding-bottom: 9px !important;
        font-size: 8px !important;
    }

    #profileDrawer [data-drawer-panel] {
        padding: 14px !important;
    }

    #profileDrawer .user-surface,
    #profileDrawer section.rounded-\[16px\] {
        border-radius: 13px !important;
        padding: 13px !important;
        box-shadow: 0 5px 16px rgba(61, 43, 22, .045) !important;
    }

    #profileDrawer #drawerInitials {
        width: 50px !important;
        height: 50px !important;
        font-size: 13px !important;
    }

    #profileDrawer input.clean-control,
    #profileDrawer textarea.clean-control {
        font-size: 8.5px !important;
    }

    #profileDrawer input.clean-control {
        height: 36px !important;
    }

    #profileDrawer button,
    #profileDrawer a {
        font-size: 8px !important;
    }

    /* Laptop tuning */
    @media (max-height: 850px) and (min-width: 900px) {
        .user-page .user-page-title {
            font-size: 22px !important;
        }

        .user-page .metric-card {
            min-height: 72px !important;
            padding-top: 9px !important;
            padding-bottom: 9px !important;
        }

        .user-page .metric-card > div > div > p:nth-child(2) {
            font-size: 18px !important;
        }
    }

    /* Tablet/mobile remains readable and tappable. */
    @media (max-width: 767px) {
        .user-page .user-page-header-icon {
            width: 34px !important;
            height: 34px !important;
            flex-basis: 34px !important;
        }

        .user-page .user-page-title {
            font-size: 22px !important;
        }

        .user-page .user-page-subtitle {
            font-size: 9.5px !important;
        }

        .user-page .user-page-export {
            min-height: 40px !important;
            font-size: 10px !important;
        }

        .user-page .metric-card {
            min-height: 76px !important;
        }

        .user-page [data-role-dropdown],
        .user-page [data-status-dropdown] {
            width: 100% !important;
        }

        .user-page #userSearch,
        .user-page [data-role-toggle],
        .user-page [data-status-toggle],
        .user-page #applyFilter,
        .user-page #resetFilter {
            height: 42px !important;
            font-size: 10px !important;
        }

        #profileDrawer {
            max-width: 100% !important;
        }
    }


    /* ============================================================
       USER MANAGEMENT — CENTERED ENTERPRISE MODAL
       UI/UX only. Existing IDs, forms, routes and JS hooks remain.
       ============================================================ */

    #profileBackdrop {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        background: rgba(24, 20, 15, .42) !important;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        transition: opacity .18s ease, visibility 0s linear .18s;
    }

    #profileBackdrop.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition: opacity .18s ease;
    }

    #profileDrawer {
        top: 50% !important;
        left: 50% !important;
        right: auto !important;
        bottom: auto !important;
        width: min(920px, calc(100vw - 44px)) !important;
        max-width: 920px !important;
        height: min(720px, calc(100vh - 44px)) !important;
        max-height: calc(100vh - 44px) !important;
        border: 1px solid #e7e0d7 !important;
        border-radius: 18px !important;
        background: #fff !important;
        box-shadow:
            0 30px 90px rgba(31, 24, 17, .20),
            0 10px 28px rgba(31, 24, 17, .08) !important;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translate(-50%, -47%) scale(.985) !important;
        transform-origin: center !important;
        transition:
            opacity .18s ease,
            transform .22s cubic-bezier(.22, 1, .36, 1),
            visibility 0s linear .22s !important;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif !important;
    }

    #profileDrawer.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translate(-50%, -50%) scale(1) !important;
        transition:
            opacity .18s ease,
            transform .22s cubic-bezier(.22, 1, .36, 1) !important;
    }

    .enterprise-user-modal {
        min-height: 0;
        background: #f8f7f4;
    }

    .enterprise-user-modal-header {
        display: flex;
        min-height: 66px;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid #ece6de;
        background: #fff;
        padding: 12px 16px;
    }

    .enterprise-user-modal-header-icon {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border: 1px solid #eadfc9;
        border-radius: 9px;
        background: #fff8eb;
        color: #b77c18;
    }

    .enterprise-user-modal-header-icon svg {
        width: 15px;
        height: 15px;
    }

    .enterprise-user-modal-eyebrow,
    .enterprise-section-eyebrow {
        margin: 0;
        color: #9a7b43;
        font-size: 7px !important;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .enterprise-user-modal-title {
        margin: 0;
        color: #1e1a16;
        font-size: 15px !important;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    .enterprise-user-modal-context {
        display: inline-flex;
        align-items: center;
        min-height: 20px;
        border: 1px solid #e9e2d9;
        border-radius: 999px;
        background: #faf9f7;
        padding: 0 7px;
        color: #8a8177;
        font-size: 7px;
        font-weight: 600;
    }

    .enterprise-modal-close {
        display: grid;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        place-items: center;
        border: 1px solid #e5ddd2;
        border-radius: 9px;
        background: #fff;
        color: #71685e;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease;
    }

    .enterprise-modal-close:hover,
    .enterprise-modal-close:focus-visible {
        outline: none;
        border-color: #d5c7b5;
        background: #faf8f4;
        color: #2f2923;
    }

    .enterprise-modal-close svg {
        width: 13px;
        height: 13px;
    }

    .enterprise-user-modal-tabs {
        display: flex;
        gap: 4px;
        overflow-x: auto;
        border-bottom: 1px solid #ece6de;
        background: #fff;
        padding: 7px 16px 0;
        scrollbar-width: none;
    }

    .enterprise-user-modal-tabs::-webkit-scrollbar {
        display: none;
    }

    #profileDrawer .drawer-tab {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        gap: 7px;
        border-bottom: 2px solid transparent;
        padding: 0 10px;
        color: #756d64;
        font-size: 8.5px !important;
        font-weight: 600;
        white-space: nowrap;
        transition: color .15s ease, border-color .15s ease, background-color .15s ease;
    }

    #profileDrawer .drawer-tab svg {
        width: 13px;
        height: 13px;
    }

    #profileDrawer .drawer-tab:hover {
        color: #9a6a12;
    }

    #profileDrawer .drawer-tab.is-active {
        border-bottom-color: #d99500;
        color: #9a6a12;
    }

    .enterprise-user-modal-scroll {
        min-height: 0;
        scrollbar-width: thin;
        scrollbar-color: #d7cfc5 transparent;
    }

    .enterprise-user-panel {
        padding: 14px;
    }

    .enterprise-overview-grid,
    .enterprise-manage-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(280px, .75fr);
        gap: 12px;
        align-items: start;
    }

    .enterprise-manage-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    }

    .enterprise-card {
        border: 1px solid #e9e2d9;
        border-radius: 13px;
        background: #fff;
        padding: 14px;
        box-shadow: 0 4px 14px rgba(61, 43, 22, .035);
    }

    .enterprise-section-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .enterprise-section-heading h4,
    .enterprise-access-card h4 {
        margin: 4px 0 0;
        color: #28231e;
        font-size: 11px !important;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: -.015em;
    }

    .enterprise-section-heading--with-icon {
        align-items: center;
    }

    .enterprise-section-copy {
        margin: 5px 0 0;
        color: #8b8278;
        font-size: 8px !important;
        line-height: 1.55;
    }

    .enterprise-section-icon {
        display: grid;
        width: 31px;
        height: 31px;
        flex: 0 0 31px;
        place-items: center;
        border: 1px solid #eadfc9;
        border-radius: 9px;
        background: #fff8eb;
        color: #b77c18;
    }

    .enterprise-section-icon svg {
        width: 14px;
        height: 14px;
    }

    .enterprise-profile-hero {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        margin-top: 14px;
    }

    .enterprise-profile-avatar {
        display: grid;
        width: 54px;
        height: 54px;
        flex: 0 0 54px;
        place-items: center;
        border: 1px solid #e7dfd5;
        border-radius: 14px;
        background: #faf8f4;
        color: #655d55;
        font-size: 14px !important;
        font-weight: 700;
        letter-spacing: -.03em;
    }

    .enterprise-role-badge,
    .enterprise-status-badge {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        border: 1px solid #e6e0d8;
        border-radius: 999px;
        background: #f7f5f2;
        padding: 0 8px;
        color: #6f665b;
        font-size: 7px !important;
        font-weight: 700;
    }

    .enterprise-contact-list {
        display: grid;
        gap: 6px;
        margin-top: 11px;
    }

    .enterprise-contact-row {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 7px;
        color: #5d554d;
        font-size: 8.5px !important;
        line-height: 1.4;
    }

    .enterprise-contact-row--muted {
        color: #91887d;
        font-size: 8px !important;
    }

    .enterprise-contact-icon {
        display: grid;
        width: 22px;
        height: 22px;
        flex: 0 0 22px;
        place-items: center;
        border-radius: 7px;
        background: #faf8f5;
        color: #8a8177;
    }

    .enterprise-contact-icon svg {
        width: 11px;
        height: 11px;
    }

    .enterprise-account-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-top: 14px;
        border-top: 1px solid #eee8df;
        padding-top: 12px;
    }

    .enterprise-meta-tile {
        border: 1px solid #eee8df;
        border-radius: 10px;
        background: #faf9f7;
        padding: 9px 10px;
    }

    .enterprise-meta-tile p {
        margin: 0;
        color: #9b9288;
        font-size: 6.8px !important;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .enterprise-meta-tile strong {
        display: block;
        margin-top: 4px;
        color: #4a433c;
        font-size: 8.5px !important;
        font-weight: 700;
    }

    .enterprise-quick-actions {
        display: grid;
        gap: 8px;
        margin-top: 14px;
    }

    .enterprise-quick-action {
        display: grid;
        grid-template-columns: 30px minmax(0, 1fr) 14px;
        align-items: center;
        gap: 9px;
        min-height: 58px;
        border: 1px solid #e8e1d8;
        border-radius: 11px;
        background: #fff;
        padding: 8px 10px;
        text-align: left;
        transition: border-color .15s ease, background-color .15s ease, transform .15s ease;
    }

    .enterprise-quick-action:hover,
    .enterprise-quick-action:focus-visible {
        outline: none;
        transform: translateY(-1px);
        border-color: #dcc89e;
        background: #fffaf1;
    }

    .enterprise-quick-action--primary {
        border-color: #eadbbd;
        background: #fffcf5;
    }

    .enterprise-quick-action-icon {
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 1px solid #eadfc9;
        border-radius: 8px;
        background: #fff8eb;
        color: #b77c18;
    }

    .enterprise-quick-action-icon svg {
        width: 13px;
        height: 13px;
    }

    .enterprise-quick-action strong,
    .enterprise-message-card strong {
        display: block;
        color: #3f3831;
        font-size: 8.5px !important;
        font-weight: 700;
    }

    .enterprise-quick-action small,
    .enterprise-message-card small {
        display: block;
        margin-top: 2px;
        color: #948b81;
        font-size: 7px !important;
        line-height: 1.4;
        font-weight: 400;
    }

    .enterprise-quick-action-arrow,
    .enterprise-message-arrow {
        width: 13px;
        height: 13px;
        color: #9b9288;
    }

    .enterprise-activity-timeline {
        margin-top: 14px;
    }

    .enterprise-form-card {
        padding: 14px;
    }

    .enterprise-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }

    .enterprise-field-label {
        display: block;
        margin-bottom: 5px;
        color: #6c6359;
        font-size: 7.5px !important;
        font-weight: 600;
    }

    .enterprise-field-control {
        width: 100%;
        height: 38px !important;
        border-radius: 9px !important;
        padding: 0 10px !important;
        font-size: 8.5px !important;
    }

    .enterprise-field-textarea {
        width: 100%;
        min-height: 82px;
        margin-top: 0;
        resize: vertical;
        border-radius: 9px !important;
        padding: 9px 10px !important;
        font-size: 8.5px !important;
        line-height: 1.5;
    }

    .enterprise-field-textarea::placeholder,
    .enterprise-field-control::placeholder {
        font-size: 8.5px !important;
    }

    .enterprise-field-textarea--danger {
        border-color: #ead8d2 !important;
    }

    .enterprise-button {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid transparent;
        border-radius: 9px;
        padding: 0 12px;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        font-size: 8.5px !important;
        font-weight: 700;
        transition: transform .15s ease, background-color .15s ease, border-color .15s ease, box-shadow .15s ease;
    }

    .enterprise-button:hover {
        transform: translateY(-1px);
    }

    .enterprise-button:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(217,149,0,.10);
    }

    .enterprise-button svg {
        width: 13px;
        height: 13px;
    }

    .enterprise-button--primary {
        border-color: #d99500;
        background: #d99500;
        color: #fff;
        box-shadow: 0 6px 16px rgba(217,149,0,.16);
    }

    .enterprise-button--primary:hover {
        border-color: #bd8205;
        background: #bd8205;
    }

    .enterprise-button--secondary {
        border-color: #ddd5ca;
        background: #fff;
        color: #62594e;
    }

    .enterprise-button--secondary:hover {
        border-color: #d0c1ad;
        background: #faf8f4;
        color: #403931;
    }

    .enterprise-button--danger {
        border-color: #b96545;
        background: #b96545;
        color: #fff;
        box-shadow: 0 5px 14px rgba(185,101,69,.13);
    }

    .enterprise-button--danger:hover {
        border-color: #a4573c;
        background: #a4573c;
    }

    .enterprise-button--success {
        border-color: #4f8362;
        background: #4f8362;
        color: #fff;
        box-shadow: 0 5px 14px rgba(79,131,98,.13);
    }

    .enterprise-button--success:hover {
        border-color: #447355;
        background: #447355;
    }

    .enterprise-message-card {
        min-height: 66px;
        gap: 10px;
        padding: 12px 14px;
        color: #514a42;
        text-decoration: none;
        transition: border-color .15s ease, background-color .15s ease, transform .15s ease;
    }

    .enterprise-message-card:hover {
        transform: translateY(-1px);
        border-color: #d9c59d;
        background: #fffaf1;
    }

    .enterprise-message-icon {
        display: grid;
        width: 31px;
        height: 31px;
        flex: 0 0 31px;
        place-items: center;
        border: 1px solid #eadfc9;
        border-radius: 8px;
        background: #fff8eb;
        color: #b77c18;
    }

    .enterprise-message-icon svg {
        width: 14px;
        height: 14px;
    }

    .enterprise-access-card {
        border-color: #eadfd9;
    }

    .enterprise-access-icon {
        display: grid;
        width: 31px;
        height: 31px;
        flex: 0 0 31px;
        place-items: center;
        border: 1px solid #ead8d2;
        border-radius: 8px;
        background: #fff6f2;
        color: #b66445;
    }

    .enterprise-access-icon svg {
        width: 14px;
        height: 14px;
    }

    .enterprise-section-eyebrow--danger {
        color: #9e654f;
    }

    @media (max-height: 820px) and (min-width: 768px) {
        #profileDrawer {
            width: min(880px, calc(100vw - 36px)) !important;
            height: min(650px, calc(100vh - 30px)) !important;
            max-height: calc(100vh - 30px) !important;
        }

        .enterprise-user-modal-header {
            min-height: 60px;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .enterprise-user-panel {
            padding: 12px;
        }

        .enterprise-card {
            padding: 12px;
        }
    }

    @media (max-width: 767px) {
        #profileDrawer {
            width: calc(100vw - 16px) !important;
            height: calc(100vh - 16px) !important;
            max-height: calc(100vh - 16px) !important;
            border-radius: 14px !important;
        }

        .enterprise-user-modal-header {
            padding: 10px 12px;
        }

        .enterprise-user-modal-context {
            display: none;
        }

        .enterprise-user-modal-tabs {
            padding-left: 10px;
            padding-right: 10px;
        }

        .enterprise-overview-grid,
        .enterprise-manage-grid,
        .enterprise-form-grid {
            grid-template-columns: 1fr;
        }

        .enterprise-user-panel {
            padding: 10px;
        }

        .enterprise-card {
            padding: 12px;
        }

        .enterprise-profile-avatar {
            width: 48px;
            height: 48px;
            flex-basis: 48px;
        }

        .enterprise-button {
            min-height: 42px;
            font-size: 9px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #profileBackdrop,
        #profileDrawer,
        .enterprise-button,
        .enterprise-quick-action,
        .enterprise-message-card {
            transition: none !important;
        }
    }


    /* ============================================================
       USER MANAGEMENT — ENTERPRISE MODAL V2
       Content-adaptive, denser, calmer, large-company admin style.
       Functionality, forms, routes, IDs and JS hooks are unchanged.
       ============================================================ */

    #profileDrawer {
        width: min(840px, calc(100vw - 40px)) !important;
        max-width: 840px !important;
        height: auto !important;
        max-height: calc(100vh - 40px) !important;
        border-radius: 16px !important;
        box-shadow:
            0 26px 74px rgba(31, 24, 17, .18),
            0 8px 24px rgba(31, 24, 17, .07) !important;
    }

    .enterprise-user-modal {
        height: auto !important;
        max-height: calc(100vh - 40px) !important;
        background: #f8f7f4 !important;
    }

    /* Header — tighter and more executive */
    .enterprise-user-modal-header {
        min-height: 58px !important;
        padding: 10px 14px !important;
    }

    .enterprise-user-modal-header-icon {
        width: 31px !important;
        height: 31px !important;
        flex-basis: 31px !important;
        border-radius: 8px !important;
    }

    .enterprise-user-modal-header-icon svg {
        width: 14px !important;
        height: 14px !important;
    }

    .enterprise-user-modal-eyebrow {
        font-size: 6.5px !important;
        letter-spacing: .12em !important;
    }

    .enterprise-user-modal-title {
        font-size: 14px !important;
        letter-spacing: -.02em !important;
    }

    .enterprise-user-modal-context {
        min-height: 18px !important;
        padding-inline: 6px !important;
        font-size: 6.5px !important;
    }

    .enterprise-modal-close {
        width: 30px !important;
        height: 30px !important;
        flex-basis: 30px !important;
        border-radius: 8px !important;
    }

    /* Tabs — compact segmented navigation */
    .enterprise-user-modal-tabs {
        gap: 2px !important;
        padding: 5px 14px 0 !important;
    }

    #profileDrawer .drawer-tab {
        min-height: 34px !important;
        gap: 6px !important;
        padding-inline: 9px !important;
        font-size: 7.8px !important;
    }

    #profileDrawer .drawer-tab svg {
        width: 12px !important;
        height: 12px !important;
    }

    /* Content now grows only as much as needed */
    .enterprise-user-modal-scroll {
        flex: 0 1 auto !important;
        max-height: calc(100vh - 132px) !important;
        overflow-y: auto !important;
    }

    .enterprise-user-panel {
        padding: 12px !important;
    }

    .enterprise-overview-grid {
        grid-template-columns: minmax(0, 1.45fr) minmax(245px, .75fr) !important;
        gap: 10px !important;
    }

    .enterprise-manage-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
        gap: 10px !important;
    }

    /* Cards — flatter enterprise surfaces */
    .enterprise-card {
        border-color: #e7e1d9 !important;
        border-radius: 11px !important;
        padding: 12px !important;
        box-shadow: 0 2px 8px rgba(61, 43, 22, .028) !important;
    }

    .enterprise-section-heading h4,
    .enterprise-access-card h4 {
        margin-top: 3px !important;
        font-size: 10.5px !important;
    }

    .enterprise-section-copy {
        margin-top: 4px !important;
        font-size: 7.5px !important;
        line-height: 1.5 !important;
    }

    .enterprise-section-eyebrow {
        font-size: 6.4px !important;
        letter-spacing: .11em !important;
    }

    .enterprise-section-icon {
        width: 28px !important;
        height: 28px !important;
        flex-basis: 28px !important;
        border-radius: 8px !important;
    }

    .enterprise-section-icon svg {
        width: 13px !important;
        height: 13px !important;
    }

    /* Profile summary */
    .enterprise-profile-hero {
        gap: 11px !important;
        margin-top: 11px !important;
    }

    .enterprise-profile-avatar {
        width: 48px !important;
        height: 48px !important;
        flex-basis: 48px !important;
        border-radius: 12px !important;
        font-size: 12.5px !important;
    }

    .enterprise-role-badge,
    .enterprise-status-badge {
        min-height: 21px !important;
        padding-inline: 7px !important;
        font-size: 6.5px !important;
    }

    .enterprise-contact-list {
        gap: 5px !important;
        margin-top: 9px !important;
    }

    .enterprise-contact-row {
        gap: 6px !important;
        font-size: 7.8px !important;
    }

    .enterprise-contact-row--muted {
        font-size: 7.3px !important;
    }

    .enterprise-contact-icon {
        width: 20px !important;
        height: 20px !important;
        flex-basis: 20px !important;
        border-radius: 6px !important;
    }

    .enterprise-contact-icon svg {
        width: 10px !important;
        height: 10px !important;
    }

    .enterprise-account-meta-grid {
        gap: 7px !important;
        margin-top: 11px !important;
        padding-top: 10px !important;
    }

    .enterprise-meta-tile {
        border-radius: 9px !important;
        padding: 8px 9px !important;
    }

    .enterprise-meta-tile p {
        font-size: 6px !important;
    }

    .enterprise-meta-tile strong {
        margin-top: 3px !important;
        font-size: 8px !important;
    }

    /* Quick actions — less card-like, more enterprise command rows */
    .enterprise-quick-actions {
        gap: 7px !important;
        margin-top: 11px !important;
    }

    .enterprise-quick-action {
        grid-template-columns: 27px minmax(0, 1fr) 12px !important;
        min-height: 50px !important;
        gap: 8px !important;
        border-radius: 9px !important;
        padding: 7px 9px !important;
    }

    .enterprise-quick-action-icon {
        width: 27px !important;
        height: 27px !important;
        border-radius: 7px !important;
    }

    .enterprise-quick-action-icon svg {
        width: 12px !important;
        height: 12px !important;
    }

    .enterprise-quick-action strong,
    .enterprise-message-card strong {
        font-size: 7.8px !important;
    }

    .enterprise-quick-action small,
    .enterprise-message-card small {
        font-size: 6.5px !important;
    }

    /* Activity panel — centered reading width; no giant empty canvas */
    [data-drawer-panel="activity"] .enterprise-card {
        max-width: 720px !important;
        margin-inline: auto !important;
    }

    .enterprise-activity-timeline {
        margin-top: 11px !important;
    }

    #activityTimeline > div {
        gap: 8px !important;
    }

    #activityTimeline > div > div:last-child {
        border-radius: 9px !important;
        padding: 9px 10px !important;
    }

    /* Manage forms */
    .enterprise-form-card {
        padding: 12px !important;
    }

    .enterprise-form-grid {
        gap: 8px !important;
        margin-top: 11px !important;
    }

    .enterprise-field-label {
        margin-bottom: 4px !important;
        font-size: 7px !important;
    }

    .enterprise-field-control {
        height: 36px !important;
        border-radius: 8px !important;
        padding-inline: 9px !important;
        font-size: 8px !important;
    }

    .enterprise-field-textarea {
        min-height: 76px !important;
        border-radius: 8px !important;
        padding: 8px 9px !important;
        font-size: 8px !important;
    }

    .enterprise-field-textarea::placeholder,
    .enterprise-field-control::placeholder {
        font-size: 8px !important;
    }

    /* Buttons — professional, less bulky */
    .enterprise-button {
        min-height: 34px !important;
        border-radius: 8px !important;
        padding-inline: 11px !important;
        font-size: 7.8px !important;
        box-shadow: none !important;
    }

    .enterprise-button svg {
        width: 12px !important;
        height: 12px !important;
    }

    .enterprise-button--primary {
        box-shadow: 0 4px 10px rgba(217,149,0,.11) !important;
    }

    .enterprise-button--danger {
        border-color: #d8b6aa !important;
        background: #fff !important;
        color: #aa573d !important;
        box-shadow: none !important;
    }

    .enterprise-button--danger:hover {
        border-color: #c9907d !important;
        background: #fff6f2 !important;
        color: #96452f !important;
    }

    .enterprise-button--success {
        box-shadow: none !important;
    }

    .enterprise-message-card {
        min-height: 58px !important;
        border-radius: 10px !important;
        padding: 10px 12px !important;
    }

    .enterprise-message-icon,
    .enterprise-access-icon {
        width: 28px !important;
        height: 28px !important;
        flex-basis: 28px !important;
        border-radius: 7px !important;
    }

    .enterprise-message-icon svg,
    .enterprise-access-icon svg {
        width: 13px !important;
        height: 13px !important;
    }

    /* Access control looks like a controlled risk zone, not a giant red CTA card */
    .enterprise-access-card {
        border-color: #eadfd9 !important;
        background: #fffdfc !important;
    }

    .enterprise-section-eyebrow--danger {
        color: #9c624c !important;
    }

    /* Reduce visual height in Manage tab */
    [data-drawer-panel="manage"] .space-y-3 {
        row-gap: 10px !important;
    }

    @media (max-height: 820px) and (min-width: 768px) {
        #profileDrawer {
            width: min(820px, calc(100vw - 32px)) !important;
            max-height: calc(100vh - 28px) !important;
        }

        .enterprise-user-modal {
            max-height: calc(100vh - 28px) !important;
        }

        .enterprise-user-modal-scroll {
            max-height: calc(100vh - 118px) !important;
        }
    }

    @media (max-width: 767px) {
        #profileDrawer {
            width: calc(100vw - 14px) !important;
            max-height: calc(100vh - 14px) !important;
            border-radius: 13px !important;
        }

        .enterprise-user-modal {
            max-height: calc(100vh - 14px) !important;
        }

        .enterprise-user-modal-scroll {
            max-height: calc(100vh - 108px) !important;
        }

        .enterprise-overview-grid,
        .enterprise-manage-grid,
        .enterprise-form-grid {
            grid-template-columns: 1fr !important;
        }

        .enterprise-user-panel {
            padding: 9px !important;
        }

        .enterprise-button {
            min-height: 40px !important;
            font-size: 8.8px !important;
        }
    }


    /* ============================================================
       USER MANAGEMENT — FILTER DROPDOWN VISIBILITY FIX
       The filter surface must not use paint containment because
       dropdown menus extend outside its visual bounds.
       ============================================================ */

    .user-page > section.user-surface.mt-4.p-3{
        position:relative !important;
        z-index:30 !important;
        overflow:visible !important;
        contain:none !important;
        isolation:auto !important;
    }

    .user-page [data-role-dropdown],
    .user-page [data-status-dropdown]{
        position:relative !important;
        z-index:35 !important;
        overflow:visible !important;
        contain:none !important;
        flex:0 0 auto !important;
    }

    .user-page [data-role-toggle],
    .user-page [data-status-toggle]{
        display:flex !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:10px !important;
        overflow:visible !important;
        padding-left:11px !important;
        padding-right:9px !important;
        cursor:pointer !important;
    }

    .user-page [data-role-label],
    .user-page [data-status-label]{
        min-width:0 !important;
        overflow:hidden !important;
        text-overflow:ellipsis !important;
        white-space:nowrap !important;
    }

    /* Keep the dropdown chevron clearly visible. */
    .user-page [data-role-toggle] > svg,
    .user-page [data-status-toggle] > svg{
        display:block !important;
        width:14px !important;
        height:14px !important;
        min-width:14px !important;
        flex:0 0 14px !important;
        color:#6f665b !important;
        opacity:1 !important;
        visibility:visible !important;
        pointer-events:none !important;
        transition:transform .14s ease,color .14s ease !important;
    }

    .user-page [data-role-toggle]:hover > svg,
    .user-page [data-status-toggle]:hover > svg,
    .user-page [data-role-toggle]:focus-visible > svg,
    .user-page [data-status-toggle]:focus-visible > svg{
        color:#a8731f !important;
    }

    /* Menu must render above the table and neighboring controls. */
    .user-page [data-role-menu],
    .user-page [data-status-menu]{
        position:absolute !important;
        top:calc(100% + 6px) !important;
        left:0 !important;
        right:0 !important;
        z-index:100 !important;
        min-width:100% !important;
        overflow:visible !important;
    }

    .user-page [data-role-menu].is-open,
    .user-page [data-status-menu].is-open{
        opacity:1 !important;
        visibility:visible !important;
        pointer-events:auto !important;
        transform:translateY(0) scale(1) !important;
    }

    /* Rotate arrow while open for clearer affordance. */
    .user-page [data-role-dropdown]:has([data-role-menu].is-open) [data-role-toggle] > svg,
    .user-page [data-status-dropdown]:has([data-status-menu].is-open) [data-status-toggle] > svg{
        transform:rotate(180deg) !important;
        color:#a8731f !important;
    }

    /* Table stays below the open filters. */
    .user-page > section.user-surface.mt-4.overflow-hidden{
        position:relative !important;
        z-index:1 !important;
    }

    @media(max-width:1279px){
        .user-page [data-role-dropdown],
        .user-page [data-status-dropdown]{
            width:100% !important;
        }
    }


    /* ============================================================
       USER MANAGEMENT — HEADER SCALE MATCH
       Matches Account Registrations / Seller Compliance /
       Platform Settings / Commissions / Complaints.
       Visual-only; user management behavior remains untouched.
       ============================================================ */

    .user-page .user-page-header{
        display:flex !important;
        align-items:center !important;
        justify-content:space-between !important;
        gap:20px !important;
        margin-bottom:16px !important;
    }

    .user-page .user-page-header-main{
        display:flex !important;
        min-width:0 !important;
        align-items:center !important;
        gap:13px !important;
    }

    .user-page .user-page-header-icon{
        display:grid !important;
        width:44px !important;
        height:44px !important;
        flex:0 0 44px !important;
        place-items:center !important;
        border:1px solid #eadfc9 !important;
        border-radius:12px !important;
        background:#fff8eb !important;
        color:#b77c18 !important;
        box-shadow:0 4px 12px rgba(75,54,25,.045) !important;
    }

    .user-page .user-page-header-icon svg{
        width:17px !important;
        height:17px !important;
    }

    .user-page .user-page-eyebrow{
        margin:0 !important;
        color:#9a6f23 !important;
        -webkit-text-fill-color:#9a6f23 !important;
        font-size:8px !important;
        font-weight:700 !important;
        line-height:1.15 !important;
        letter-spacing:.13em !important;
        text-transform:uppercase !important;
    }

    .user-page .user-page-title{
        margin:5px 0 0 !important;
        font-size:29px !important;
        font-weight:700 !important;
        line-height:1.02 !important;
        letter-spacing:-.045em !important;
    }

    .user-page .user-page-title-base{
        color:#17130f !important;
        -webkit-text-fill-color:#17130f !important;
    }

    .user-page .user-page-title-accent{
        color:#d99500 !important;
        -webkit-text-fill-color:#d99500 !important;
    }

    .user-page .user-page-subtitle{
        max-width:900px !important;
        margin:7px 0 0 !important;
        color:#7f756a !important;
        -webkit-text-fill-color:#7f756a !important;
        font-size:11px !important;
        font-weight:400 !important;
        line-height:1.5 !important;
        letter-spacing:0 !important;
    }

    .user-page .user-page-export{
        min-height:42px !important;
        height:42px !important;
        gap:7px !important;
        border-radius:10px !important;
        padding:0 13px !important;
        font-size:8.5px !important;
        box-shadow:0 4px 10px rgba(217,149,0,.08) !important;
    }

    .user-page .user-page-export svg{
        width:13px !important;
        height:13px !important;
    }

    @media(max-height:850px) and (min-width:900px){
        .user-page .user-page-header{
            margin-bottom:14px !important;
        }

        .user-page .user-page-header-icon{
            width:42px !important;
            height:42px !important;
            flex-basis:42px !important;
        }

        .user-page .user-page-title{
            font-size:27px !important;
        }

        .user-page .user-page-subtitle{
            font-size:10.5px !important;
        }

        .user-page .user-page-export{
            min-height:40px !important;
            height:40px !important;
        }
    }

    @media(max-width:767px){
        .user-page .user-page-header{
            align-items:flex-start !important;
            gap:12px !important;
        }

        .user-page .user-page-header-main{
            align-items:flex-start !important;
            gap:11px !important;
        }

        .user-page .user-page-header-icon{
            width:40px !important;
            height:40px !important;
            flex-basis:40px !important;
            border-radius:11px !important;
        }

        .user-page .user-page-title{
            font-size:24px !important;
        }

        .user-page .user-page-subtitle{
            font-size:10px !important;
        }

        .user-page .user-page-export{
            width:100% !important;
            min-height:40px !important;
            height:40px !important;
            font-size:9px !important;
        }
    }



    /* ============================================================
       SELLER VISUAL SYSTEM — FINAL USER MANAGEMENT OVERRIDE
       Compiled from the active Seller Center visual language.
       Visual-only: preserves routes, PHP account data, filters, export,
       messaging, account actions, forms, modal IDs, and JavaScript hooks.
       ============================================================ */

    .user-page {
        --seller-canvas: #F4F5F7;
        --seller-surface: #FFFFFF;
        --seller-text: #111827;
        --seller-text-secondary: #344054;
        --seller-muted: #7C8592;
        --seller-gold: #D59617;
        --seller-gold-strong: #B97805;
        --seller-border: #E5E7EB;
        --seller-border-strong: #D1D5DB;
        --seller-divider: #ECEFF2;
        --seller-radius: 14px;
        --seller-surface-shadow:
            0 2px 5px rgba(15, 23, 42, .045),
            0 10px 24px rgba(15, 23, 42, .075),
            0 24px 54px rgba(15, 23, 42, .095),
            inset 0 1px 0 rgba(255, 255, 255, .92);
        --seller-summary-shadow:
            0 1px 3px rgba(15, 23, 42, .035),
            0 7px 18px rgba(15, 23, 42, .06),
            0 15px 32px rgba(15, 23, 42, .065),
            inset 0 1px 0 rgba(255, 255, 255, .90);

        width: 100% !important;
        max-width: 1800px !important;
        padding-inline: 0 !important;
        padding-bottom: 24px !important;
        color: var(--seller-text) !important;
    }

    /* ---------------- OPEN SELLER-STYLE PAGE HEADER ---------------- */
    .user-page .user-page-header {
        display: flex !important;
        min-height: 66px !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 20px !important;
        margin: 0 0 12px !important;
        padding: 2px 2px 13px !important;
        border-bottom: 1px solid var(--seller-divider) !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .user-page .user-page-header-main {
        display: block !important;
        min-width: 0 !important;
    }

    .user-page .user-page-header-icon {
        display: none !important;
    }

    .user-page .user-page-eyebrow {
        margin: 0 0 5px !important;
        color: var(--seller-gold-strong) !important;
        -webkit-text-fill-color: var(--seller-gold-strong) !important;
        font-size: 8px !important;
        font-weight: 800 !important;
        line-height: 1.15 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
    }

    .user-page .user-page-title {
        margin: 0 !important;
        color: var(--seller-text) !important;
        font-size: clamp(29px, 2.2vw, 36px) !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        letter-spacing: -.035em !important;
    }

    .user-page .user-page-title-base {
        color: var(--seller-text) !important;
        -webkit-text-fill-color: var(--seller-text) !important;
    }

    .user-page .user-page-title-accent {
        color: var(--seller-gold) !important;
        -webkit-text-fill-color: var(--seller-gold) !important;
    }

    .user-page .user-page-subtitle {
        max-width: 760px !important;
        margin: 7px 0 0 !important;
        color: var(--seller-muted) !important;
        -webkit-text-fill-color: var(--seller-muted) !important;
        font-size: 10px !important;
        font-weight: 400 !important;
        line-height: 1.55 !important;
        letter-spacing: 0 !important;
    }

    .user-page .user-page-export {
        min-height: 40px !important;
        height: 40px !important;
        flex: 0 0 auto !important;
        gap: 7px !important;
        border: 1px solid var(--seller-border) !important;
        border-radius: 10px !important;
        background: #FFFFFF !important;
        padding: 0 13px !important;
        color: #475467 !important;
        font-size: 9.5px !important;
        font-weight: 650 !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .user-page .user-page-export:hover,
    .user-page .user-page-export:focus-visible {
        outline: none !important;
        border-color: #D5B66D !important;
        background: #FFFAF0 !important;
        color: var(--seller-gold-strong) !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .user-page .user-page-export svg {
        width: 13px !important;
        height: 13px !important;
    }

    /* ---------------- TOP-LEVEL SURFACE HIERARCHY ---------------- */
    .user-page > section.user-surface {
        border: 1px solid var(--seller-border) !important;
        border-radius: var(--seller-radius) !important;
        background: var(--seller-surface) !important;
        box-shadow: var(--seller-surface-shadow) !important;
        transform: none !important;
        contain: none !important;
    }

    .user-page .metric-card,
    .user-page .metric-card.is-active {
        min-height: 92px !important;
        overflow: hidden !important;
        border: 1px solid var(--seller-border) !important;
        border-radius: var(--seller-radius) !important;
        background: var(--seller-surface) !important;
        box-shadow: var(--seller-summary-shadow) !important;
        transform: none !important;
        contain: none !important;
        transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease !important;
    }

    .user-page .metric-card:hover,
    .user-page .metric-card.is-active:hover {
        border-color: #D8DDE5 !important;
        background: #FFFFFF !important;
        box-shadow: var(--seller-summary-shadow) !important;
        transform: none !important;
    }

    .user-page .metric-card.is-active {
        border-color: var(--seller-border) !important;
    }

    .user-page .metric-card:focus-visible {
        outline: none !important;
        border-color: var(--seller-gold) !important;
        box-shadow:
            0 0 0 3px rgba(213, 150, 23, .10),
            var(--seller-summary-shadow) !important;
    }

    .user-page > section.grid {
        gap: 12px !important;
    }

    .user-page .metric-card {
        padding: 15px 58px 15px 16px !important;
    }

    .user-page .metric-card > div {
        gap: 10px !important;
    }

    .user-page .metric-card > div > span:first-child {
        right: 14px !important;
        top: 14px !important;
        width: 36px !important;
        height: 36px !important;
        border-radius: 10px !important;
        box-shadow: none !important;
    }

    .user-page .metric-card > div > span:first-child svg {
        width: 15px !important;
        height: 15px !important;
    }

    .user-page .metric-card > div > div > p:nth-child(1) {
        color: #667085 !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.35 !important;
    }

    .user-page .metric-card > div > div > p:nth-child(2) {
        margin-top: 5px !important;
        color: #101828 !important;
        font-size: 24px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: -.035em !important;
    }

    .user-page .metric-card > div > div > p:nth-child(3) {
        margin-top: 8px !important;
        color: #98A2B3 !important;
        font-size: 8.5px !important;
        line-height: 1.35 !important;
    }

    /* ---------------- FILTER TOOLBAR ---------------- */
    .user-page > section.user-surface.mt-4.p-3 {
        position: relative !important;
        z-index: 30 !important;
        margin-top: 12px !important;
        padding: 12px !important;
        overflow: visible !important;
        contain: none !important;
    }

    .user-page > section.user-surface.mt-4.p-3 > div {
        gap: 9px !important;
    }

    .user-page #userSearch,
    .user-page [data-role-toggle],
    .user-page [data-status-toggle],
    .user-page #applyFilter,
    .user-page #resetFilter {
        height: 40px !important;
        border-radius: 10px !important;
        font-size: 10px !important;
    }

    .user-page #userSearch,
    .user-page [data-role-toggle],
    .user-page [data-status-toggle],
    .user-page #resetFilter {
        border: 1px solid var(--seller-border) !important;
        background: #FFFFFF !important;
        color: #344054 !important;
        box-shadow: none !important;
    }

    .user-page #userSearch {
        padding: 0 12px 0 38px !important;
    }

    .user-page #userSearch::placeholder {
        color: #98A2B3 !important;
        font-size: 10px !important;
    }

    .user-page > section.user-surface.mt-4.p-3 .relative > svg.pointer-events-none {
        left: 13px !important;
        width: 14px !important;
        height: 14px !important;
        color: #98A2B3 !important;
    }

    .user-page [data-role-dropdown] {
        width: 160px !important;
    }

    .user-page [data-status-dropdown] {
        width: 155px !important;
    }

    .user-page [data-role-toggle],
    .user-page [data-status-toggle] {
        padding-inline: 11px !important;
        font-weight: 600 !important;
    }

    .user-page #userSearch:hover,
    .user-page [data-role-toggle]:hover,
    .user-page [data-status-toggle]:hover,
    .user-page #resetFilter:hover {
        border-color: var(--seller-border-strong) !important;
        box-shadow: none !important;
    }

    .user-page #userSearch:focus,
    .user-page [data-role-toggle]:focus-visible,
    .user-page [data-status-toggle]:focus-visible {
        outline: none !important;
        border-color: var(--seller-gold) !important;
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .10) !important;
    }

    .user-page [data-role-toggle] > svg,
    .user-page [data-status-toggle] > svg {
        width: 14px !important;
        height: 14px !important;
        min-width: 14px !important;
        flex: 0 0 14px !important;
        color: #98A2B3 !important;
    }

    .user-page .custom-menu {
        top: calc(100% + 7px) !important;
        padding: 5px !important;
        border: 1px solid var(--seller-border) !important;
        border-radius: 11px !important;
        background: #FFFFFF !important;
        box-shadow:
            0 8px 18px rgba(15, 23, 42, .08),
            0 18px 40px rgba(15, 23, 42, .12) !important;
    }

    .user-page [data-role-option],
    .user-page [data-status-option] {
        min-height: 32px !important;
        border-radius: 8px !important;
        padding: 0 9px !important;
        color: #475467 !important;
        font-size: 9px !important;
        font-weight: 500 !important;
    }

    .user-page [data-role-option]:hover,
    .user-page [data-status-option]:hover,
    .user-page [data-role-option]:focus-visible,
    .user-page [data-status-option]:focus-visible {
        outline: none !important;
        background: #FFFAF0 !important;
        color: var(--seller-gold-strong) !important;
    }

    .user-page #applyFilter,
    .user-page #resetFilter {
        padding-inline: 13px !important;
        font-weight: 650 !important;
        transform: none !important;
    }

    .user-page #applyFilter {
        min-width: 108px !important;
        border: 1px solid var(--seller-gold) !important;
        background: var(--seller-gold) !important;
        color: #FFFFFF !important;
        box-shadow: 0 5px 12px rgba(213, 150, 23, .16) !important;
    }

    .user-page #applyFilter:hover,
    .user-page #applyFilter:focus-visible {
        outline: none !important;
        border-color: var(--seller-gold-strong) !important;
        background: var(--seller-gold-strong) !important;
        box-shadow: 0 6px 14px rgba(185, 120, 5, .18) !important;
        transform: none !important;
    }

    .user-page #resetFilter {
        min-width: 68px !important;
        color: #667085 !important;
    }

    /* ---------------- USERS TABLE ---------------- */
    .user-page > section.user-surface.mt-4.overflow-hidden {
        position: relative !important;
        z-index: 1 !important;
        margin-top: 14px !important;
        overflow: hidden !important;
    }

    .user-page table {
        min-width: 1180px !important;
    }

    .user-page table thead tr {
        background: #F9FAFB !important;
    }

    .user-page table thead th {
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        color: #667085 !important;
        font-size: 8px !important;
        font-weight: 700 !important;
        line-height: 1.35 !important;
        letter-spacing: .075em !important;
    }

    .user-page [data-user-row] {
        background: #FFFFFF !important;
        box-shadow: none !important;
        transition: background-color .14s ease, box-shadow .14s ease !important;
    }

    .user-page [data-user-row]:hover {
        background: #FFFCF5 !important;
        box-shadow: inset 2px 0 0 var(--seller-gold) !important;
    }

    .user-page [data-user-row] > td {
        padding-top: 11px !important;
        padding-bottom: 11px !important;
        color: #475467 !important;
    }

    .user-page [data-user-row] > td:first-child {
        font-size: 8.5px !important;
    }

    .user-page [data-user-row] > td:nth-child(2) > div {
        gap: 9px !important;
    }

    .user-page [data-user-row] > td:nth-child(2) > div > span {
        width: 32px !important;
        height: 32px !important;
        font-size: 8.5px !important;
        box-shadow: none !important;
    }

    .user-page [data-user-row] > td:nth-child(2) p:first-child {
        color: #344054 !important;
        font-size: 9.5px !important;
        font-weight: 700 !important;
    }

    .user-page [data-user-row] > td:nth-child(2) p:last-child,
    .user-page [data-user-row] > td:nth-child(4) p:last-child,
    .user-page [data-user-row] > td:nth-child(6) p:last-child,
    .user-page [data-user-row] > td:nth-child(7) p:last-child {
        color: #98A2B3 !important;
        font-size: 7.5px !important;
    }

    .user-page [data-user-row] > td:nth-child(3) > span,
    .user-page [data-user-row] > td:nth-child(5) > span {
        padding: 4px 8px !important;
        font-size: 7.5px !important;
        box-shadow: none !important;
    }

    .user-page [data-user-row] > td:nth-child(4) p:first-child,
    .user-page [data-user-row] > td:nth-child(6) p:first-child,
    .user-page [data-user-row] > td:nth-child(7) p:first-child {
        color: #475467 !important;
        font-size: 8.5px !important;
    }

    .user-page #userRows [data-user-row] td:last-child > div {
        gap: 5px !important;
    }

    .user-page #userRows .account-action {
        width: 28px !important;
        height: 28px !important;
        min-width: 28px !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .user-page #userRows .account-action-svg {
        width: 14px !important;
        height: 14px !important;
    }

    .user-page #userRows .account-action:hover,
    .user-page #userRows .account-action:focus-visible {
        outline: none !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        transform: translateY(-1px) scale(1.06) !important;
    }

    .user-page > section.user-surface.mt-4.overflow-hidden > div:last-child {
        padding: 11px 16px !important;
        border-top-color: var(--seller-divider) !important;
        background: #FFFFFF !important;
    }

    .user-page #resultCount,
    .user-page #resultCount + div > span {
        color: #667085 !important;
        font-size: 8.5px !important;
    }

    .user-page #resultCount + div > button,
    .user-page #resultCount + div > span {
        width: 31px !important;
        min-width: 31px !important;
        height: 31px !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }

    /* ---------------- ALERTS ---------------- */
    .user-page > .mb-4 {
        border-radius: var(--seller-radius) !important;
        box-shadow: var(--seller-summary-shadow) !important;
    }

    /* ---------------- ACCOUNT MODAL: FOCAL SURFACE ---------------- */
    #profileBackdrop {
        background: rgba(15, 23, 42, .48) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    #profileDrawer {
        width: min(900px, calc(100vw - 40px)) !important;
        max-width: 900px !important;
        max-height: calc(100vh - 40px) !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 18px !important;
        background: #FFFFFF !important;
        box-shadow:
            0 3px 7px rgba(15, 23, 42, .055),
            0 14px 32px rgba(15, 23, 42, .095),
            0 30px 68px rgba(15, 23, 42, .11) !important;
    }

    #profileDrawer .enterprise-user-modal {
        max-height: calc(100vh - 40px) !important;
        background: #F9FAFB !important;
    }

    #profileDrawer .enterprise-user-modal-header,
    #profileDrawer .enterprise-user-modal-tabs {
        border-color: var(--seller-divider) !important;
        background: #FFFFFF !important;
        box-shadow: none !important;
    }

    #profileDrawer .enterprise-user-modal-header-icon,
    #profileDrawer .enterprise-section-icon,
    #profileDrawer .enterprise-quick-action-icon,
    #profileDrawer .enterprise-message-icon {
        border-color: #F0E2BF !important;
        background: #FFFAF0 !important;
        color: var(--seller-gold-strong) !important;
        box-shadow: none !important;
    }

    #profileDrawer .enterprise-access-icon {
        box-shadow: none !important;
    }

    #profileDrawer .enterprise-user-modal-eyebrow,
    #profileDrawer .enterprise-section-eyebrow {
        color: #98A2B3 !important;
    }

    #profileDrawer .enterprise-user-modal-title,
    #profileDrawer .enterprise-section-heading h4,
    #profileDrawer .enterprise-access-card h4 {
        color: #101828 !important;
    }

    #profileDrawer .drawer-tab {
        color: #667085 !important;
    }

    #profileDrawer .drawer-tab:hover,
    #profileDrawer .drawer-tab.is-active {
        color: var(--seller-gold-strong) !important;
    }

    #profileDrawer .drawer-tab.is-active {
        border-bottom-color: var(--seller-gold) !important;
    }

    #profileDrawer .enterprise-card,
    #profileDrawer .enterprise-quick-action,
    #profileDrawer .enterprise-message-card,
    #profileDrawer .enterprise-meta-tile {
        border-color: #E5E7EB !important;
        background: #FFFFFF !important;
        box-shadow: none !important;
    }

    #profileDrawer .enterprise-meta-tile {
        background: #F9FAFB !important;
    }

    #profileDrawer .enterprise-quick-action:hover,
    #profileDrawer .enterprise-quick-action:focus-visible,
    #profileDrawer .enterprise-message-card:hover,
    #profileDrawer .enterprise-message-card:focus-visible {
        outline: none !important;
        border-color: #D5B66D !important;
        background: #FFFAF0 !important;
        transform: none !important;
    }

    #profileDrawer .clean-control {
        border-color: var(--seller-border) !important;
        background: #FFFFFF !important;
        color: #344054 !important;
        box-shadow: none !important;
    }

    #profileDrawer .clean-control:hover {
        border-color: var(--seller-border-strong) !important;
        box-shadow: none !important;
    }

    #profileDrawer .clean-control:focus {
        border-color: var(--seller-gold) !important;
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .10) !important;
    }

    #profileDrawer .enterprise-button {
        box-shadow: none !important;
        transform: none !important;
    }

    #profileDrawer .enterprise-button--primary {
        border-color: var(--seller-gold) !important;
        background: var(--seller-gold) !important;
        color: #FFFFFF !important;
        box-shadow: 0 5px 12px rgba(213, 150, 23, .16) !important;
    }

    #profileDrawer .enterprise-button--primary:hover {
        border-color: var(--seller-gold-strong) !important;
        background: var(--seller-gold-strong) !important;
        transform: none !important;
    }

    #profileDrawer .enterprise-button--secondary {
        border-color: var(--seller-border) !important;
        background: #FFFFFF !important;
        color: #475467 !important;
    }

    #profileDrawer .enterprise-button--secondary:hover {
        border-color: var(--seller-border-strong) !important;
        background: #F9FAFB !important;
        transform: none !important;
    }

    /* ---------------- RESPONSIVE ---------------- */
    @media (max-width: 1279px) {
        .user-page > section.user-surface.mt-4.p-3 > div {
            flex-wrap: wrap !important;
        }

        .user-page [data-role-dropdown],
        .user-page [data-status-dropdown] {
            width: 160px !important;
        }
    }

    @media (max-width: 767px) {
        .user-page {
            padding-bottom: 18px !important;
        }

        .user-page .user-page-header {
            min-height: auto !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 11px !important;
            padding: 1px 0 12px !important;
        }

        .user-page .user-page-title {
            font-size: clamp(28px, 8vw, 34px) !important;
        }

        .user-page .user-page-subtitle {
            max-width: 100% !important;
            font-size: 10px !important;
        }

        .user-page .user-page-export {
            width: 100% !important;
            min-height: 42px !important;
            height: 42px !important;
            font-size: 10px !important;
        }

        .user-page > section.grid {
            gap: 9px !important;
        }

        .user-page .metric-card {
            min-height: 86px !important;
            padding: 13px 52px 13px 13px !important;
        }

        .user-page .metric-card > div > div > p:nth-child(2) {
            font-size: 22px !important;
        }

        .user-page > section.user-surface {
            border-radius: 13px !important;
            box-shadow:
                0 2px 5px rgba(15, 23, 42, .04),
                0 9px 22px rgba(15, 23, 42, .07) !important;
        }

        .user-page [data-role-dropdown],
        .user-page [data-status-dropdown] {
            width: 100% !important;
        }

        .user-page #userSearch,
        .user-page [data-role-toggle],
        .user-page [data-status-toggle],
        .user-page #applyFilter,
        .user-page #resetFilter {
            height: 42px !important;
            font-size: 10px !important;
        }

        .user-page #applyFilter,
        .user-page #resetFilter {
            width: 100% !important;
        }

        #profileDrawer {
            width: calc(100vw - 16px) !important;
            max-height: calc(100vh - 16px) !important;
            border-radius: 14px !important;
        }

        #profileDrawer .enterprise-overview-grid,
        #profileDrawer .enterprise-manage-grid,
        #profileDrawer .enterprise-form-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .user-page .metric-card,
        .user-page .user-page-export,
        .user-page #applyFilter,
        .user-page #resetFilter,
        .user-page #userRows .account-action,
        #profileDrawer,
        #profileDrawer .enterprise-quick-action,
        #profileDrawer .enterprise-message-card,
        #profileDrawer .enterprise-button {
            transition-duration: .01ms !important;
        }
    }

</style>
