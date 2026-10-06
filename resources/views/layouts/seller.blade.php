<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    {{-- SELLER FIRST-PAINT CANVAS — prevents warm/cream route flashes. --}}
    <style id="sariSellerFirstPaintCanvas">
        :root,
        html,
        body {
            min-height: 100%;
            background: #F4F5F7 !important;
            background-color: #F4F5F7 !important;
            background-image: none !important;
        }

        body {
            margin: 0;
        }

        #sellerContent {
            min-height: 100vh;
            background: #F4F5F7 !important;
            background-color: #F4F5F7 !important;
            background-image: none !important;
        }

        #sellerContent > main {
            background: transparent !important;
            background-color: transparent !important;
            background-image: none !important;
        }

        #sariSellerInstantSnapshot {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
            background: #F4F5F7 !important;
        }

        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none !important;
            mix-blend-mode: normal !important;
            background: #F4F5F7 !important;
        }
    

}



        /* ============================================================
           UNIFIED HEADER NOTIFICATIONS
           Bell is visually icon-only; dropdown is the elevated surface.
           ============================================================ */
        #sellerNotificationBell,
        #sellerNotificationBell:hover,
        #sellerNotificationBell:active {
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            transform: none !important;
        }

        #sellerNotificationBell {
            color: #475467 !important;
        }

        #sellerNotificationBell:hover,
        #sellerNotificationBell:focus-visible {
            color: #C9890B !important;
        }

        #sellerNotificationBell:focus-visible {
            outline: 2px solid rgba(213, 150, 23, .20) !important;
            outline-offset: 2px !important;
        }

        .seller-shell-notification-item {
            border-bottom: 1px solid #EEF0F3;
            background: #FFFFFF;
        }

        .seller-shell-notification-item[data-unread="true"] {
            background: #FFFCF5;
        }

        .seller-shell-notification-item:hover {
            background: #F8FAFC !important;
        }

        .seller-shell-notification-icon {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 auto;
            place-items: center;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            background: #FFFFFF;
            color: #B97805;
            box-shadow: none !important;
        }

        .seller-shell-notification-icon svg {
            width: 15px;
            height: 15px;
        }

        #sellerNotificationMarkAllRead[disabled] {
            cursor: default;
            opacity: .45;
        }

</style>

    <title>@yield('title', 'SARI Seller')</title>

    @php
        /*
         * Fast seller shell assets:
         * Use normal cacheable asset URLs instead of reading and base64-encoding
         * both logo files on every seller page request.
         */
        $sellerFullLogoSrc = asset('images/sari-logo.png');
        $sellerCompactLogoSrc = asset('images/sari-main-logo.png');
    @endphp

    {{-- Restore desktop compact state before first paint. No logo JavaScript is needed. --}}
    <script>
        (function () {
            try {
                if (
                    window.matchMedia('(min-width: 1024px)').matches &&
                    window.localStorage.getItem('sari:seller-sidebar-collapsed') === '1'
                ) {
                    document.documentElement.classList.add('seller-sidebar-collapsed');
                }
            } catch (error) {}
        })();
    </script>

    {{-- Faster first paint: compiled Tailwind CSS instead of the browser CDN compiler. --}}
    <link rel="preload" as="image" href="{{ asset('images/sari-logo.png') }}" fetchpriority="high">
    <link rel="preload" as="image" href="{{ asset('images/sari-main-logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- Seller loads compiled CSS + seller-only realtime JS. --}}
    @vite(['resources/css/app.css', 'resources/js/seller.js'])

    {{-- Livewire Navigate / @persist support --}}
    @livewireStyles

    <style>
        :root {
            --seller-sidebar-expanded: 238px;
            --seller-sidebar-collapsed: 76px;
            --seller-sidebar-width: var(--seller-sidebar-expanded);
            --seller-sidebar-ease: cubic-bezier(.22, 1, .36, 1);
        }

        /*
         * Critical first-paint shell sizing.
         * This prevents the sidebar/logo/header from flashing at the wrong size
         * during a fresh login while the compiled stylesheet is being parsed.
         */
        html,
        body {
            min-height: 100%;
            background: #F4F5F7;
        }

        #sellerSidebarFullLogo,
        #sellerSidebarCompactLogo {
            object-fit: contain;
        }


        @media (min-width: 1024px) {
            html.seller-sidebar-collapsed {
                --seller-sidebar-width: var(--seller-sidebar-collapsed);
            }

            #sellerSidebar {
                width: var(--seller-sidebar-width);
                overflow-x: hidden;
                transition: width 130ms var(--seller-sidebar-ease);
            }

            #sellerContent {
                padding-left: var(--seller-sidebar-width);
                transition: padding-left 130ms var(--seller-sidebar-ease);
            }

            #sellerSidebarBrand {
                min-height: 110px;
                transition: min-height 220ms var(--seller-sidebar-ease), padding 220ms var(--seller-sidebar-ease);
            }

            /*
             * Admin-style logo behavior:
             * - no load handlers
             * - no opacity/cross-fade
             * - no waiting for DOMContentLoaded
             * The browser decides which already-present image to paint from the
             * root class that was restored in <head>.
             */
            #sellerSidebarFullLogo {
                display: block;
            }

            #sellerSidebarCompactLogo {
                display: none;
            }

            .seller-sidebar-label {
                display: inline-block;
                max-width: 158px;
                overflow: hidden;
                opacity: 1;
                transform: translateX(0);
                transition: max-width 180ms var(--seller-sidebar-ease), opacity 110ms ease, transform 180ms var(--seller-sidebar-ease);
            }

            .seller-sidebar-extra {
                max-width: 72px;
                overflow: hidden;
                opacity: 1;
                transition: max-width 160ms var(--seller-sidebar-ease), opacity 100ms ease, padding 160ms var(--seller-sidebar-ease);
            }

            [data-seller-sidebar-item] {
                transition: padding 190ms var(--seller-sidebar-ease), gap 190ms var(--seller-sidebar-ease), background-color 150ms ease, color 150ms ease, box-shadow 150ms ease;
            }

            #sellerSidebarNav,
            #sellerSidebarProfile {
                transition: padding 190ms var(--seller-sidebar-ease);
            }

            html.seller-sidebar-collapsed #sellerSidebarBrand {
                min-height: 88px;
                padding-left: 12px;
                padding-right: 12px;
            }

            html.seller-sidebar-collapsed #sellerSidebarFullLogo {
                display: none;
            }

            html.seller-sidebar-collapsed #sellerSidebarCompactLogo {
                display: block;
            }

            html.seller-sidebar-collapsed .seller-sidebar-label {
                max-width: 0;
                opacity: 0;
                transform: translateX(-6px);
                pointer-events: none;
            }

            html.seller-sidebar-collapsed .seller-sidebar-extra {
                width: 0 !important;
                min-width: 0 !important;
                max-width: 0;
                padding-left: 0 !important;
                padding-right: 0 !important;
                opacity: 0;
                pointer-events: none;
            }

            html.seller-sidebar-collapsed [data-seller-sidebar-item] {
                justify-content: center !important;
                gap: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            html.seller-sidebar-collapsed [data-seller-sidebar-item] > span:first-child {
                justify-content: center !important;
                gap: 0 !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarNav {
                padding-left: 12px;
                padding-right: 12px;
            }

            html.seller-sidebar-collapsed #sellerSidebarProfile {
                padding: 10px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            #sellerSidebar,
            #sellerContent,
            #sellerSidebarBrand,
            .seller-sidebar-label,
            .seller-sidebar-extra,
            [data-seller-sidebar-item] {
                transition-duration: 1ms !important;
            }
        }

        [data-seller-notification-scroll] {
            scrollbar-width: thin;
            scrollbar-color: #d8c7a7 transparent;
        }

        [data-seller-notification-scroll]::-webkit-scrollbar {
            width: 5px;
        }

        [data-seller-notification-scroll]::-webkit-scrollbar-track {
            background: transparent;
        }

        [data-seller-notification-scroll]::-webkit-scrollbar-thumb {
            background: #d8c7a7;
            border-radius: 999px;
        }

        /* Persistent Seller shell */
        #sellerSidebar {
            contain: layout paint;
        }

        [data-seller-shell-loading="true"] #sellerContent main {
            opacity: 1 !important;
            transition: none !important;
        }
    
        /* ============================================================
           FINAL SELLER SHELL SIZING — MATCH ADMIN UI
           This block intentionally lives inside layouts/seller.blade.php
           because this layout renders the Seller sidebar + header directly.
           ============================================================ */

        /* ---------- SIDEBAR ---------- */
        #sellerSidebar {
            width: var(--seller-sidebar-width, 275px) !important;
        }

        #sellerSidebarBrand {
            min-height: 70px !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        #sellerSidebarLogo {
            height: 46px !important;
        }

        #sellerSidebarFullLogo {
            width: 104px !important;
            height: auto !important;
        }

        #sellerSidebarCompactLogo {
            width: 38px !important;
            height: 38px !important;
        }

        #sellerSidebarClose {
            right: 12px !important;
            width: 32px !important;
            height: 32px !important;
            border-radius: 10px !important;
        }

        #sellerSidebarClose svg {
            width: 17px !important;
            height: 17px !important;
        }

        #sellerSidebarNav {
            padding: 12px 10px !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item],
        #sellerSidebarNav > form > button[data-seller-sidebar-item] {
            min-height: 39px !important;
            gap: 8px !important;
            border-radius: 10px !important;
            padding: 8px 10px !important;
            font-size: 10px !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item] + a[data-seller-sidebar-item],
        #sellerSidebarNav > a[data-seller-sidebar-item] + form,
        #sellerSidebarNav > form + a[data-seller-sidebar-item] {
            margin-top: 3px !important;
        }

        #sellerSidebarNav [data-seller-sidebar-item] svg {
            width: 15px !important;
            height: 15px !important;
        }

        #sellerSidebarNav [data-seller-sidebar-item] > span:first-child {
            gap: 10px !important;
        }

        #sellerSidebarNav > div.my-4 {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        #sellerSidebarMessageBadge {
            min-width: 18px !important;
            height: 18px !important;
            padding-inline: 5px !important;
            font-size: 8px !important;
        }

        #sellerSidebarProfile {
            padding: 9px 10px !important;
        }

        #sellerSidebarProfile > a {
            gap: 9px !important;
            border-radius: 10px !important;
            padding: 9px !important;
        }

        #sellerSidebarProfile > a > div:first-child {
            width: 36px !important;
            height: 36px !important;
            font-size: 10.5px !important;
        }

        #sellerSidebarProfile .seller-sidebar-label p:first-child {
            font-size: 10.5px !important;
        }

        #sellerSidebarProfile .seller-sidebar-label p:last-child {
            font-size: 8.5px !important;
        }

        #sellerSidebarProfile .seller-sidebar-extra {
            width: 13px !important;
            height: 13px !important;
        }

        /* ---------- HEADER ---------- */
        #sellerContent > header > div:first-child {
            min-height: 72px !important;
            gap: 10px !important;
            padding-left: 18px !important;
            padding-right: 18px !important;
        }

        #sellerContent > header > div:first-child > div:first-child {
            gap: 10px !important;
        }

        #sellerMenuButton,
        #sellerSidebarToggle,
        #sellerNotificationBell {
            width: 38px !important;
            height: 38px !important;
            border-radius: 10px !important;
        }

        #sellerMenuButton svg,
        #sellerSidebarToggle svg,
        #sellerNotificationBell > svg {
            width: 17px !important;
            height: 17px !important;
        }

        #sellerContent > header h1 {
            font-size: 17px !important;
            line-height: 1.15 !important;
        }

        #sellerContent > header h1 + div {
            margin-top: 3px !important;
            gap: 5px !important;
            font-size: 9px !important;
        }

        #sellerContent > header h1 + div svg {
            width: 10px !important;
            height: 10px !important;
        }

        #sellerContent > header > div:first-child > div:last-child {
            gap: 8px !important;
        }

        #sellerContent > header input[type="search"] {
            height: 38px !important;
            width: 220px !important;
            border-radius: 11px !important;
            padding-left: 38px !important;
            padding-right: 12px !important;
            font-size: 10px !important;
        }

        #sellerContent > header input[type="search"] + * {
            font-size: inherit;
        }

        #sellerContent > header .relative.hidden.md\:block > svg {
            left: 13px !important;
            width: 15px !important;
            height: 15px !important;
        }

        #sellerBellNotificationBadge {
            right: -5px !important;
            top: -5px !important;
            min-width: 18px !important;
            height: 18px !important;
            padding-inline: 4px !important;
            font-size: 8px !important;
        }

        /* Seller account shortcut in header */
        #sellerContent > header a[href*="/seller/account"] {
            gap: 8px !important;
            border-radius: 10px !important;
            padding: 4px 6px !important;
        }

        #sellerContent > header a[href*="/seller/account"] > div:first-child {
            width: 35px !important;
            height: 35px !important;
            font-size: 10px !important;
        }

        #sellerContent > header a[href*="/seller/account"] > div:last-child p:first-child {
            font-size: 10.5px !important;
        }

        #sellerContent > header a[href*="/seller/account"] > div:last-child p:last-child {
            font-size: 8px !important;
        }

        @media (min-width: 1024px) {
            html.seller-sidebar-collapsed #sellerSidebarBrand {
                min-height: 76px !important;
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarNav {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarNav [data-seller-sidebar-item] {
                min-height: 42px !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarProfile {
                padding: 9px 10px !important;
            }

            #sellerContent > header input[type="search"] {
                width: 235px !important;
            }
        }

        @media (min-width: 1280px) {
            #sellerContent > header input[type="search"] {
                width: 260px !important;
            }
        }

        @media (min-width: 1536px) {
            #sellerContent > header input[type="search"] {
                width: 285px !important;
            }
        }

        /* Same laptop-height compaction used by Admin. */
        @media (max-height: 820px) and (min-width: 1024px) {
            #sellerSidebarBrand {
                min-height: 76px !important;
            }

            #sellerSidebarFullLogo {
                width: 98px !important;
            }

            #sellerSidebarNav {
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }

            #sellerSidebarNav > a[data-seller-sidebar-item],
            #sellerSidebarNav > form > button[data-seller-sidebar-item] {
                min-height: 38px !important;
                padding-top: 7px !important;
                padding-bottom: 7px !important;
                font-size: 10.5px !important;
            }

            #sellerSidebarProfile {
                padding-top: 9px !important;
                padding-bottom: 9px !important;
            }
        }

        @media (max-width: 767px) {
            #sellerContent > header > div:first-child {
                min-height: 68px !important;
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            #sellerMenuButton,
            #sellerNotificationBell {
                width: 40px !important;
                height: 40px !important;
            }

            #sellerContent > header h1 {
                font-size: 16px !important;
            }
        }


        /*
         * ============================================================
         * MODERN GROUPED SELLER SIDEBAR
         * Clean reference-inspired navigation with stronger hierarchy.
         * ============================================================
         */
        #sellerSidebar {
            background: #ffffff !important;
            border-right-color: #ece5da !important;
        }

        #sellerSidebarBrand {
            min-height: 74px !important;
            border-bottom-color: #eee7dc !important;
            background: #ffffff !important;
        }

        #sellerSidebarLogo {
            height: 44px !important;
        }

        #sellerSidebarFullLogo {
            width: 102px !important;
        }

        #sellerSidebarNav {
            padding: 14px 10px 16px !important;
            scrollbar-width: thin;
            scrollbar-color: #ddd4c7 transparent;
        }

        #sellerSidebarNav::-webkit-scrollbar {
            width: 4px;
        }

        #sellerSidebarNav::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: #ddd4c7;
        }

        .seller-sidebar-section-label {
            margin: 10px 8px 7px !important;
            color: #9b7a43 !important;
            font-size: 7.5px !important;
            line-height: 1 !important;
            font-weight: 700 !important;
            letter-spacing: .13em !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
            user-select: none !important;
        }

        .seller-sidebar-section-label:first-child {
            margin-top: 4px !important;
        }

        .seller-sidebar-section-divider {
            height: 1px !important;
            margin: 11px 6px 9px !important;
            background: #eee7dd !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item],
        #sellerSidebarNav > form > button[data-seller-sidebar-item] {
            min-height: 39px !important;
            width: 100% !important;
            gap: 10px !important;
            border: 1px solid transparent !important;
            border-radius: 10px !important;
            padding: 8px 10px !important;
            font-size: 10.5px !important;
            line-height: 1.15 !important;
            box-shadow: none !important;
            transform: none !important;
            transition:
                background-color .12s ease,
                border-color .12s ease,
                color .12s ease,
                box-shadow .12s ease !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item] + a[data-seller-sidebar-item],
        #sellerSidebarNav > form + a[data-seller-sidebar-item],
        #sellerSidebarNav > a[data-seller-sidebar-item] + form {
            margin-top: 2px !important;
        }

        #sellerSidebarNav [data-seller-sidebar-item] svg {
            width: 16px !important;
            height: 16px !important;
            stroke-width: 1.75 !important;
        }

        #sellerSidebarNav [data-seller-sidebar-item] > span:first-child {
            gap: 10px !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item]:not(.bg-\[\#d9930a\]):hover,
        #sellerSidebarNav > form > button[data-seller-sidebar-item]:hover {
            border-color: #eadfcf !important;
            background: #fff9ef !important;
            color: #9a680b !important;
            box-shadow: none !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item].bg-\[\#d9930a\] {
            border-color: #d9950b !important;
            background: #d9950b !important;
            color: #ffffff !important;
            box-shadow: 0 5px 12px rgba(194, 132, 10, .12) !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item].bg-\[\#d9930a\] svg,
        #sellerSidebarNav > a[data-seller-sidebar-item].bg-\[\#d9930a\] .seller-sidebar-label {
            color: #ffffff !important;
            stroke: currentColor !important;
        }

        #sellerSidebarMessageBadge {
            min-width: 18px !important;
            height: 18px !important;
            padding-inline: 5px !important;
            font-size: 7.5px !important;
        }

        #sellerSidebarProfile {
            border-top-color: #eee7dc !important;
            background: #ffffff !important;
            padding: 9px 10px !important;
        }

        #sellerSidebarProfile > a {
            min-height: 54px !important;
            gap: 8px !important;
            border-color: #ebe2d4 !important;
            border-radius: 11px !important;
            background: #ffffff !important;
            padding: 8px 9px !important;
            box-shadow: 0 3px 10px rgba(40, 31, 21, .02) !important;
        }

        #sellerSidebarProfile > a:hover {
            border-color: #ddccb0 !important;
            background: #fffdf8 !important;
            box-shadow: 0 4px 12px rgba(40, 31, 21, .03) !important;
        }

        #sellerSidebarProfile > a > div:first-child {
            width: 32px !important;
            height: 32px !important;
            font-size: 9.5px !important;
        }

        #sellerSidebarProfile .seller-sidebar-label p:first-child {
            font-size: 9.5px !important;
            font-weight: 650 !important;
        }

        #sellerSidebarProfile .seller-sidebar-label p:last-child {
            font-size: 7.5px !important;
        }

        @media (min-width: 1024px) {
            html.seller-sidebar-collapsed .seller-sidebar-section-label,
            html.seller-sidebar-collapsed .seller-sidebar-section-divider {
                display: none !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarNav {
                padding: 12px 9px !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarNav > a[data-seller-sidebar-item],
            html.seller-sidebar-collapsed #sellerSidebarNav > form > button[data-seller-sidebar-item] {
                min-height: 40px !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarProfile {
                padding: 9px !important;
            }

            html.seller-sidebar-collapsed #sellerSidebarProfile > a {
                justify-content: center !important;
                padding: 8px !important;
            }
        }

        @media (max-height: 820px) and (min-width: 1024px) {
            #sellerSidebarNav {
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }

            .seller-sidebar-section-label {
                margin-top: 8px !important;
                margin-bottom: 6px !important;
            }

            .seller-sidebar-section-divider {
                margin-top: 8px !important;
                margin-bottom: 7px !important;
            }

            #sellerSidebarNav > a[data-seller-sidebar-item],
            #sellerSidebarNav > form > button[data-seller-sidebar-item] {
                min-height: 36px !important;
                padding-top: 7px !important;
                padding-bottom: 7px !important;
                font-size: 10px !important;
            }
        }


        /*
         * ============================================================
         * SIDEBAR FINAL VISUAL PASS — SOLID GOLD, NO GRADIENTS
         * ============================================================
         */
        #sellerSidebar,
        #sellerSidebar * {
            background-image: none !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item].bg-\[\#d9930a\] {
            background-color: #d9950b !important;
            border-color: #d9950b !important;
            color: #ffffff !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item].bg-\[\#d9930a\]:hover {
            background-color: #cf8d08 !important;
            border-color: #cf8d08 !important;
            color: #ffffff !important;
        }

        #sellerSidebarNav > a[data-seller-sidebar-item]:not(.bg-\[\#d9930a\]):hover,
        #sellerSidebarNav > form > button[data-seller-sidebar-item]:hover {
            background-color: #fff9ef !important;
            border-color: #eadfcf !important;
            color: #94620a !important;
        }

        .seller-sidebar-section-label {
            color: #9a7840 !important;
        }

        .seller-sidebar-section-divider {
            background: #eee5d7 !important;
        }

        #sellerSidebarProfile > a {
            background-color: #ffffff !important;
            border-color: #e9e0d3 !important;
        }

        #sellerSidebarProfile > a:hover {
            background-color: #fffdf9 !important;
            border-color: #dcc9a7 !important;
        }

        #sellerSidebarProfile > a > div:first-child {
            background-color: #d9950b !important;
        }

    
        /*
         * ============================================================
         * CENTERED SELLER ACTION / REVIEW NOTICE
         * ============================================================
         * No full-screen fade. Only a clean centered notice card.
         */
        .seller-action-toast {
            position: fixed !important;
            left: 50% !important;
            top: 20px !important;
            right: auto !important;
            z-index: 10050 !important;
            width: min(440px, calc(100vw - 32px)) !important;
            transform: translateX(-50%) !important;
            pointer-events: none !important;
        }

        .seller-action-toast-card {
            display: grid !important;
            grid-template-columns: 42px minmax(0, 1fr) 28px !important;
            align-items: start !important;
            gap: 12px !important;
            border: 1px solid #e7dfd4 !important;
            border-radius: 18px !important;
            background: #ffffff !important;
            padding: 16px 16px 16px 17px !important;
            box-shadow:
                0 22px 55px rgba(31, 26, 20, .16),
                0 3px 12px rgba(31, 26, 20, .05) !important;
            pointer-events: auto !important;
        }

        .seller-action-toast:not(.hidden) .seller-action-toast-card {
            animation: sellerActionToastIn .16s ease-out both;
        }

        @keyframes sellerActionToastIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(.985);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .seller-action-toast-icon {
            display: grid !important;
            width: 42px !important;
            height: 42px !important;
            place-items: center !important;
            border-radius: 13px !important;
            background: #f2f8f4 !important;
            color: #4d7b62 !important;
        }

        .seller-action-toast-icon svg {
            width: 18px !important;
            height: 18px !important;
        }

        .seller-action-toast-copy {
            min-width: 0 !important;
            padding-top: 2px !important;
        }

        .seller-action-toast-title {
            margin: 0 !important;
            color: #2d4f3a !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            line-height: 1.35 !important;
            letter-spacing: -.015em !important;
        }

        .seller-action-toast-message {
            margin-top: 5px !important;
            color: #6f746f !important;
            font-size: 9px !important;
            font-weight: 500 !important;
            line-height: 1.7 !important;
        }

        .seller-action-toast-close {
            display: grid !important;
            width: 28px !important;
            height: 28px !important;
            place-items: center !important;
            border: 0 !important;
            border-radius: 8px !important;
            background: transparent !important;
            color: #9a9288 !important;
            cursor: pointer !important;
            transition: background-color .12s ease, color .12s ease !important;
        }

        .seller-action-toast-close:hover {
            background: #f5f2ed !important;
            color: #514a42 !important;
        }

        .seller-action-toast[data-toast-type="warning"] .seller-action-toast-card {
            border-color: #ead7b5 !important;
        }

        .seller-action-toast[data-toast-type="warning"] .seller-action-toast-icon {
            background: #fff7e8 !important;
            color: #ae7415 !important;
        }

        .seller-action-toast[data-toast-type="warning"] .seller-action-toast-title {
            color: #8a5d12 !important;
        }

        .seller-action-toast[data-toast-type="warning"] .seller-action-toast-message {
            color: #756958 !important;
        }

        .seller-action-toast[data-toast-type="error"] .seller-action-toast-card {
            border-color: #edd2d2 !important;
        }

        .seller-action-toast[data-toast-type="error"] .seller-action-toast-icon {
            background: #fff2f2 !important;
            color: #b45a5a !important;
        }

        .seller-action-toast[data-toast-type="error"] .seller-action-toast-title {
            color: #9f4c4c !important;
        }

        .seller-action-toast[data-toast-type="error"] .seller-action-toast-message {
            color: #7f6666 !important;
        }

        @media (max-width: 520px) {
            .seller-action-toast {
                top: 12px !important;
                width: min(400px, calc(100vw - 24px)) !important;
            }

            .seller-action-toast-card {
                grid-template-columns: 38px minmax(0,1fr) 26px !important;
                gap: 10px !important;
                border-radius: 15px !important;
                padding: 14px !important;
            }

            .seller-action-toast-icon {
                width: 38px !important;
                height: 38px !important;
                border-radius: 11px !important;
            }
        }


        /*
         * ============================================================
         * FAST SELLER NAVIGATION
         * Route changes should paint immediately; only sidebar collapse
         * itself is animated.
         * ============================================================
         */
        #sellerContent {
            transition-property: padding-left !important;
            transition-duration: 130ms !important;
            transition-timing-function: var(--seller-sidebar-ease) !important;
        }

        #sellerSidebar {
            transition-property: width, transform !important;
            transition-duration: 130ms !important;
            transition-timing-function: var(--seller-sidebar-ease) !important;
        }

        #sellerContent main {
            opacity: 1 !important;
            transition: none !important;
        }

        html[data-seller-shell-loading="true"] #sellerContent main,
        [data-seller-shell-loading="true"] #sellerContent main {
            opacity: 1 !important;
            transition: none !important;
        }

        /* Avoid expensive broad transitions on persistent shell elements. */
        #sellerSidebarNav [data-seller-sidebar-item] {
            transition:
                background-color 90ms ease,
                border-color 90ms ease,
                color 90ms ease !important;
        }

        #sellerContent {
            background: #F4F5F7 !important;
        }

        #sellerContent main {
            background: transparent !important;
        }

        #sellerContent > header {
            transition: none !important;
        }

        @media (prefers-reduced-motion: reduce) {
            #sellerSidebar,
            #sellerContent {
                transition-duration: 1ms !important;
            }
        }


        /*
         * ============================================================
         * NAVIGATION PROGRESS VISUAL — DISABLED
         * Official source-of-truth should also be:
         * config/livewire.php -> navigate.show_progress_bar = false
         * ============================================================
         */
        #nprogress,
        #nprogress .bar,
        #nprogress .peg,
        #nprogress .spinner,
        [data-livewire-navigate-progress],
        [data-navigate-progress] {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        /* Destination content paints immediately after Livewire swaps it. */
        #sellerContent main {
            content-visibility: visible !important;
            opacity: 1 !important;
            transition: none !important;
        }


        /* ============================================================
           SARI SELLER — UNIFIED DESIGN TOKENS
           Shared neutral canvas + white surfaces + restrained gold.
           ============================================================ */
        :root {
            --sari-page-bg: #F4F5F7;
            --sari-surface: #FFFFFF;
            --sari-surface-soft: #FAFAFB;

            --sari-gold: #D89B10;
            --sari-gold-dark: #B67A08;
            --sari-gold-soft: #FFF7E6;

            --sari-text: #202124;
            --sari-text-secondary: #4B5563;
            --sari-text-muted: #8A919B;

            --sari-border: #E7E9EE;

            --sari-success: #63A375;
            --sari-info: #6C8FB5;
            --sari-warning: #D89B10;
            --sari-danger: #D97C6C;

            --sari-card-shadow:
                0 1px 2px rgba(32, 33, 36, .025),
                0 7px 18px rgba(32, 33, 36, .045),
                0 16px 34px rgba(32, 33, 36, .050);
        }

        html,
        body,
        #sellerContent {
            background: var(--sari-page-bg) !important;
            color: var(--sari-text);
        }

        #sellerSidebar,
        #sellerSidebarBrand,
        #sellerSidebarProfile,
        #sellerContent > header {
            background: var(--sari-surface) !important;
        }

        #sellerSidebar,
        #sellerSidebarBrand,
        #sellerSidebarProfile,
        #sellerContent > header {
            border-color: var(--sari-border) !important;
        }

        #sellerContent main {
            background: transparent !important;
            color: var(--sari-text);
        }

        /* Shared form language across Seller pages. */
        #sellerContent main input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="file"]),
        #sellerContent main select,
        #sellerContent main textarea {
            border-color: var(--sari-border);
            background-color: var(--sari-surface);
            color: var(--sari-text);
        }

        #sellerContent main input::placeholder,
        #sellerContent main textarea::placeholder {
            color: #A1A7B0;
        }

        #sellerContent main input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="file"]):focus,
        #sellerContent main select:focus,
        #sellerContent main textarea:focus {
            border-color: var(--sari-gold) !important;
            box-shadow: 0 0 0 3px rgba(216, 155, 16, .08) !important;
            outline: none !important;
        }

        /* Shared table language: quiet separators instead of boxed rows. */
        #sellerContent main table {
            color: var(--sari-text-secondary);
        }

        #sellerContent main thead {
            background: var(--sari-surface-soft);
        }

        #sellerContent main th {
            color: var(--sari-text-secondary);
            border-color: var(--sari-border) !important;
        }

        #sellerContent main td {
            border-color: var(--sari-border) !important;
        }

        /* Text hierarchy helpers for shared shell elements. */
        #sellerContent > header h1 {
            color: var(--sari-text) !important;
        }

        #sellerContent > header h1 + div {
            color: var(--sari-text-muted) !important;
        }

        /* Neutral scrollbar treatment. */
        #sellerSidebarNav,
        [data-seller-notification-scroll] {
            scrollbar-color: #C9CED6 transparent;
        }

        #sellerSidebarNav::-webkit-scrollbar-thumb,
        [data-seller-notification-scroll]::-webkit-scrollbar-thumb {
            background: #C9CED6 !important;
        }


        /* ============================================================
           SELLER LOGOUT CONFIRMATION MODAL — PREMIUM RED PASS
           ============================================================ */
        #sellerLogoutConfirmModal[hidden] {
            display: none !important;
        }

        #sellerLogoutConfirmModal {
            position: fixed;
            inset: 0;
            z-index: 11000;
            display: grid;
            place-items: center;
            padding: 22px;
        }

        .seller-logout-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(24, 28, 34, .54);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            opacity: 0;
            transition: opacity 150ms ease;
        }

        .seller-logout-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(100%, 390px);
            overflow: hidden;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            background: #FFFFFF;
            box-shadow:
                0 30px 80px rgba(15, 23, 42, .24),
                0 8px 24px rgba(15, 23, 42, .08);
            opacity: 0;
            transform: translateY(10px) scale(.985);
            transition:
                opacity 160ms ease,
                transform 160ms cubic-bezier(.22, 1, .36, 1);
        }

        #sellerLogoutConfirmModal.is-open .seller-logout-modal-backdrop {
            opacity: 1;
        }

        #sellerLogoutConfirmModal.is-open .seller-logout-modal-dialog {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .seller-logout-modal-body {
            padding: 21px 21px 18px;
        }

        .seller-logout-modal-icon {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border: 1px solid #FFB7BE;
            border-radius: 8px;
            background: #FFF0F2;
            color: #F04438;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.8);
        }

        .seller-logout-modal-icon svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .seller-logout-modal-title {
            margin: 14px 0 0;
            color: #1F2937;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.28;
            letter-spacing: -.035em;
        }

        .seller-logout-modal-copy {
            margin: 6px 0 0;
            max-width: 410px;
            color: #667085;
            font-size: 10px;
            font-weight: 500;
            line-height: 1.6;
        }

        .seller-logout-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #EEF0F3;
            background: #FAFBFC;
            padding: 10px 12px;
        }

        .seller-logout-modal-btn {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 8px;
            padding: 0 13px;
            font: inherit;
            font-size: 9px;
            font-weight: 700;
            cursor: pointer;
            transition:
                background-color 120ms ease,
                border-color 120ms ease,
                color 120ms ease,
                box-shadow 120ms ease,
                transform 120ms ease;
        }

        .seller-logout-modal-btn svg {
            width: 13px;
            height: 13px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .seller-logout-modal-btn-cancel {
            min-width: 84px;
            border: 1px solid #D8DEE6;
            background: #FFFFFF;
            color: #475467;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .03);
        }

        .seller-logout-modal-btn-cancel:hover {
            border-color: #C7CED8;
            background: #F7F8FA;
            color: #253142;
        }

        .seller-logout-modal-btn-danger {
            min-width: 96px;
            border: 1px solid #ff0b0b;
            background: #ff0b0b;
            color: #FFFFFF;
            box-shadow:
                0 6px 14px rgba(127, 29, 45, .20),
                inset 0 1px 0 rgba(255,255,255,.12);
        }

        .seller-logout-modal-btn-danger:hover {
            border-color: #ff0b0b;
            background: #ff0b0b;
            box-shadow:
                0 8px 18px rgba(107, 23, 38, .25),
                inset 0 1px 0 rgba(255,255,255,.10);
            transform: translateY(-1px);
        }

        .seller-logout-modal-btn-danger:active {
            border-color: #ff0b0b;
            background: #ff0b0b;
            box-shadow:
                0 4px 10px rgba(86, 17, 31, .22),
                inset 0 1px 0 rgba(255,255,255,.08);
            transform: translateY(0);
        }

        .seller-logout-modal-btn-danger:disabled {
            border-color: #9B5360;
            background: #9B5360;
            box-shadow: none;
            opacity: .78;
            cursor: wait;
            transform: none;
        }

        .seller-logout-modal-btn:focus-visible {
            outline: 3px solid rgba(127, 29, 45, .18);
            outline-offset: 2px;
        }

        @media (max-width: 520px) {
            #sellerLogoutConfirmModal {
                align-items: end;
                padding: 10px;
            }

            .seller-logout-modal-dialog {
                width: 100%;
                border-radius: 8px;
            }

            .seller-logout-modal-body {
                padding: 20px 18px 17px;
            }

            .seller-logout-modal-title {
                font-size: 17px;
            }

            .seller-logout-modal-copy {
                font-size: 9px;
            }

            .seller-logout-modal-footer {
                display: grid;
                grid-template-columns: 1fr 1fr;
                padding: 10px;
            }

            .seller-logout-modal-btn {
                width: 100%;
                min-width: 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .seller-logout-modal-backdrop,
            .seller-logout-modal-dialog,
            .seller-logout-modal-btn {
                transition: none !important;
            }
        }

        /* ============================================================
           SELLER SHELL PROFILE AVATAR — SHARED HEADER + SIDEBAR
           The parent keeps the existing shell dimensions.
           ============================================================ */
        .seller-shell-profile-avatar {
            position: relative !important;
            overflow: hidden !important;
            flex: 0 0 auto !important;
            border: 1px solid #E5E7EB !important;
            background: #D9950B !important;
            isolation: isolate;
        }

        .seller-shell-profile-avatar__image {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
            object-fit: cover !important;
            object-position: center !important;
            border-radius: inherit !important;
            background: #F8FAFC !important;
        }

        .seller-shell-profile-avatar__fallback {
            position: relative !important;
            z-index: 1 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            color: #FFFFFF !important;
            line-height: 1 !important;
        }

        .seller-shell-profile-avatar__image[hidden],
        .seller-shell-profile-avatar__fallback[hidden] {
            display: none !important;
        }

    

/* ======================================================================
   SELLER SHELL — FLAT SIDEBAR + HEADER
   Outer shell surfaces stay flat. No layout or functionality changes.
   ====================================================================== */

#sellerSidebar,
#sellerSidebar:hover,
#sellerSidebar:focus-within {
    box-shadow: none !important;
    filter: none !important;
}

#sellerContent > header,
#sellerContent > header:hover,
#sellerContent > header:focus-within {
    box-shadow: none !important;
    filter: none !important;
}

/* Keep structural separation with neutral borders only. */
#sellerSidebar {
    border-right-color: #E7E9EE !important;
}

#sellerContent > header {
    border-bottom-color: #E7E9EE !important;
}

/* Do not allow responsive overrides to re-add shell elevation. */
@media (max-width: 1023px) {
    #sellerSidebar {
        box-shadow: none !important;
    }
}

@media (max-width: 639px) {
    #sellerContent > header {
        box-shadow: none !important;
    }
}


/* ======================================================================
   UNIFIED NOTIFICATION DROPDOWN — CLEAN / MODERN / PROFESSIONAL
   UI-only refinement. No backend/realtime/read-state behavior changed.
   Sidebar + header remain flat.
   ====================================================================== */

#sellerNotificationDropdown {
    width: min(470px, calc(100vw - 32px)) !important;
    top: 52px !important;
    right: -10px !important;
    overflow: visible !important;
    border: 1px solid #E4E7EC !important;
    border-radius: 20px !important;
    background: #FFFFFF !important;
    box-shadow:
        0 8px 20px rgba(15, 23, 42, .07),
        0 24px 60px rgba(15, 23, 42, .14) !important;
}

/* Small pointer toward the bell. */
#sellerNotificationDropdown::before {
    content: "";
    position: absolute;
    top: -8px;
    right: 30px;
    width: 15px;
    height: 15px;
    border-left: 1px solid #E4E7EC;
    border-top: 1px solid #E4E7EC;
    background: #FFFFFF;
    transform: rotate(45deg);
    z-index: 0;
}

#sellerNotificationDropdown > * {
    position: relative;
    z-index: 1;
}

/* Header */
#sellerNotificationDropdown > div:first-child {
    min-height: 88px !important;
    align-items: center !important;
    border-bottom: 1px solid #ECEFF2 !important;
    padding: 18px 22px !important;
}

#sellerNotificationDropdown > div:first-child > div:first-child > p:first-child {
    color: #101828 !important;
    font-size: 18px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    letter-spacing: -.025em !important;
}

#sellerNotificationUnreadText {
    margin-top: 5px !important;
    color: #98A2B3 !important;
    font-size: 10px !important;
    line-height: 1.45 !important;
}

#sellerNotificationMarkAllRead,
#sellerNotificationDropdown a[href*="/notifications"] {
    font-weight: 600 !important;
}

#sellerNotificationMarkAllRead {
    color: #475467 !important;
    font-size: 10px !important;
}

#sellerNotificationMarkAllRead:hover {
    color: #B97805 !important;
}

#sellerNotificationDropdown > div:first-child a[href*="/notifications"] {
    color: #B97805 !important;
    font-size: 10px !important;
}

#sellerNotificationDropdown > div:first-child a[href*="/notifications"]:hover {
    color: #8E5905 !important;
}

/* List */
#sellerNotificationList {
    max-height: 430px !important;
    overflow-y: auto !important;
    background: #FFFFFF !important;
}

.seller-shell-notification-item {
    min-height: 92px !important;
    padding: 16px 22px !important;
    border-bottom: 1px solid #EEF1F4 !important;
    background: #FFFFFF !important;
    box-shadow: none !important;
    transform: none !important;
}

.seller-shell-notification-item:last-child {
    border-bottom: 0 !important;
}

.seller-shell-notification-item[data-unread="true"] {
    background: #FFFCF5 !important;
}

.seller-shell-notification-item:hover {
    background: #F8FAFC !important;
}

.seller-shell-notification-item > div {
    gap: 14px !important;
}

.seller-shell-notification-icon {
    width: 48px !important;
    height: 48px !important;
    border: 1px solid #E5E7EB !important;
    border-radius: 14px !important;
    background: #FFFBF3 !important;
    color: #C98506 !important;
    box-shadow: none !important;
}

.seller-shell-notification-icon svg {
    width: 20px !important;
    height: 20px !important;
}

/* Notification title/time/message hierarchy */
.seller-shell-notification-item p.line-clamp-1 {
    color: #344054 !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
}

.seller-shell-notification-item span.shrink-0.text-\[7px\] {
    color: #98A2B3 !important;
    font-size: 9px !important;
    line-height: 1.35 !important;
}

.seller-shell-notification-item p.line-clamp-2 {
    margin-top: 5px !important;
    color: #667085 !important;
    font-size: 10px !important;
    line-height: 1.65 !important;
}

.seller-shell-notification-item [data-notification-unread-dot] {
    width: 9px !important;
    height: 9px !important;
    margin-top: 5px !important;
    background: #D9930A !important;
}

/* Empty state */
#sellerNotificationEmpty {
    padding: 44px 28px !important;
}

#sellerNotificationEmpty > div:first-child {
    color: #B97805 !important;
}

#sellerNotificationEmpty > p:first-of-type {
    margin-top: 12px !important;
    color: #344054 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
}

#sellerNotificationEmpty > p:last-child {
    margin-top: 5px !important;
    max-width: 320px !important;
    color: #98A2B3 !important;
    font-size: 9px !important;
    line-height: 1.6 !important;
}

/* Footer */
#sellerNotificationDropdown > a[href*="/notifications"] {
    min-height: 64px !important;
    margin: 12px 16px 16px !important;
    border: 1px solid #E1B24E !important;
    border-radius: 14px !important;
    background: #FFFDF8 !important;
    color: #A66F10 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    box-shadow: none !important;
    transform: none !important;
}

#sellerNotificationDropdown > a[href*="/notifications"]:hover {
    border-color: #D9930A !important;
    background: #FFF8E8 !important;
    color: #8E5905 !important;
    transform: none !important;
}

/* Keep bell itself visually icon-only. */
#sellerNotificationBell,
#sellerNotificationBell:hover,
#sellerNotificationBell:active {
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    transform: none !important;
}

/* Responsive sizing */
@media (max-width: 639px) {
    #sellerNotificationDropdown {
        position: fixed !important;
        top: 72px !important;
        left: 12px !important;
        right: 12px !important;
        width: auto !important;
        max-width: none !important;
        border-radius: 18px !important;
    }

    #sellerNotificationDropdown::before {
        display: none !important;
    }

    #sellerNotificationDropdown > div:first-child {
        min-height: 80px !important;
        padding: 16px 18px !important;
    }

    #sellerNotificationDropdown > div:first-child > div:first-child > p:first-child {
        font-size: 16px !important;
    }

    .seller-shell-notification-item {
        min-height: 86px !important;
        padding: 14px 18px !important;
    }

    .seller-shell-notification-icon {
        width: 44px !important;
        height: 44px !important;
        border-radius: 13px !important;
    }

    #sellerNotificationDropdown > a[href*="/notifications"] {
        min-height: 56px !important;
        margin: 10px 12px 12px !important;
    }
}


/* ======================================================================
   UNIFIED NOTIFICATION DROPDOWN — COMPACT PROFESSIONAL PASS
   Keeps the modern visual language but reduces width/height/spacing.
   UI-only. Realtime/read-state/backend behavior unchanged.
   ====================================================================== */

#sellerNotificationDropdown {
    width: min(380px, calc(100vw - 28px)) !important;
    top: 50px !important;
    right: -8px !important;
    border-radius: 16px !important;
    box-shadow:
        0 6px 16px rgba(15, 23, 42, .06),
        0 18px 42px rgba(15, 23, 42, .12) !important;
}

#sellerNotificationDropdown::before {
    top: -7px !important;
    right: 27px !important;
    width: 13px !important;
    height: 13px !important;
}

/* Compact header */
#sellerNotificationDropdown > div:first-child {
    min-height: 68px !important;
    padding: 13px 16px !important;
}

#sellerNotificationDropdown > div:first-child > div:first-child > p:first-child {
    font-size: 14px !important;
    line-height: 1.15 !important;
}

#sellerNotificationUnreadText {
    margin-top: 3px !important;
    font-size: 8.5px !important;
}

#sellerNotificationMarkAllRead,
#sellerNotificationDropdown > div:first-child a[href*="/notifications"] {
    font-size: 8.5px !important;
}

/* Compact list */
#sellerNotificationList {
    max-height: 330px !important;
}

.seller-shell-notification-item {
    min-height: 72px !important;
    padding: 12px 16px !important;
}

.seller-shell-notification-item > div {
    gap: 11px !important;
}

.seller-shell-notification-icon {
    width: 38px !important;
    height: 38px !important;
    border-radius: 11px !important;
}

.seller-shell-notification-icon svg {
    width: 16px !important;
    height: 16px !important;
}

.seller-shell-notification-item p.line-clamp-1 {
    font-size: 10px !important;
    line-height: 1.3 !important;
}

.seller-shell-notification-item span.shrink-0.text-\[7px\] {
    font-size: 7.5px !important;
}

.seller-shell-notification-item p.line-clamp-2 {
    margin-top: 3px !important;
    font-size: 8.5px !important;
    line-height: 1.5 !important;
}

.seller-shell-notification-item [data-notification-unread-dot] {
    width: 7px !important;
    height: 7px !important;
    margin-top: 4px !important;
}

/* Compact empty state */
#sellerNotificationEmpty {
    padding: 30px 20px !important;
}

#sellerNotificationEmpty > p:first-of-type {
    margin-top: 9px !important;
    font-size: 9.5px !important;
}

#sellerNotificationEmpty > p:last-child {
    margin-top: 4px !important;
    max-width: 280px !important;
    font-size: 8px !important;
    line-height: 1.5 !important;
}

/* Compact footer */
#sellerNotificationDropdown > a[href*="/notifications"] {
    min-height: 46px !important;
    margin: 9px 12px 12px !important;
    border-radius: 11px !important;
    font-size: 9px !important;
}

/* Keep bell icon-only. */
#sellerNotificationBell,
#sellerNotificationBell:hover,
#sellerNotificationBell:active {
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}

/* Mobile remains compact but readable. */
@media (max-width: 639px) {
    #sellerNotificationDropdown {
        top: 68px !important;
        left: 10px !important;
        right: 10px !important;
        width: auto !important;
        border-radius: 15px !important;
    }

    #sellerNotificationDropdown > div:first-child {
        min-height: 64px !important;
        padding: 12px 14px !important;
    }

    .seller-shell-notification-item {
        min-height: 68px !important;
        padding: 11px 14px !important;
    }

    .seller-shell-notification-icon {
        width: 36px !important;
        height: 36px !important;
    }

    #sellerNotificationDropdown > a[href*="/notifications"] {
        min-height: 44px !important;
        margin: 8px 10px 10px !important;
    }
}


/* Notification badge absolute zero-state safety.
   When JS says there are no unread notifications, the badge must not render. */
#sellerBellNotificationBadge[data-has-unread="false"],
#sellerBellNotificationBadge[aria-hidden="true"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
}

#sellerBellNotificationBadge[data-has-unread="true"] {
    visibility: visible !important;
    opacity: 1 !important;
}


/* Zero unread must hide every header notification badge copy,
   including transient Livewire DOM instances. */
[data-seller-notification-badge][data-has-unread="false"],
[data-seller-notification-badge][aria-hidden="true"],
[data-seller-notification-badge][hidden] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
    animation: none !important;
}

</style>

    @stack('styles')

</head>

@php
    /*
    |--------------------------------------------------------------------------
    | SELLER LAYOUT REQUEST CONTEXT
    |--------------------------------------------------------------------------
    |
    | The Seller middleware already resolves and refreshes the account once.
    | Reuse that same model here instead of querying SellerAccount again.
    |
    | Message counters/history are loaded asynchronously after first paint by
    | seller.layout-state, so the sidebar/logo never waits for ChatMessage SQL.
    |
    */

    $sellerLayoutAccount = request()->attributes->get('sellerAccount');

    if (
        !$sellerLayoutAccount
        && session('is_seller')
        && session('seller_account_id')
    ) {
        /*
        | Safe fallback for any Seller view that is intentionally rendered
        | without the normal Seller middleware.
        */
        $sellerLayoutAccount = \App\Models\Accounts\SellerAccount::find(
            session('seller_account_id')
        );
    }

    /*
     * Shared Seller shell profile photo.
     *
     * SellerAccountController already owns the secure profile-photo response.
     * Use the controller action URL directly so the sidebar/header always read
     * from the same persisted source as Account Management.
     *
     * The updated_at query value prevents a replaced image from being stuck in
     * the browser cache after the seller saves Personal Account changes.
     */
    $sellerShellProfilePhotoUrl = null;

    if ($sellerLayoutAccount) {
        try {
            $sellerShellProfilePhotoUrl = action([
                \App\Http\Controllers\Seller\SellerAccountController::class,
                'profilePhoto',
            ]);

            $sellerShellProfilePhotoVersion = optional(
                $sellerLayoutAccount->updated_at
            )->timestamp ?: 0;

            $sellerShellProfilePhotoUrl .= (
                str_contains($sellerShellProfilePhotoUrl, '?') ? '&' : '?'
            ) . 'v=' . $sellerShellProfilePhotoVersion;
        } catch (\Throwable $error) {
            /*
             * Keep the shell resilient if the profile-photo route is not
             * registered in an unusual test/fallback context.
             * The initials fallback remains available.
             */
            $sellerShellProfilePhotoUrl = null;
        }
    }

    $sellerShellProfileInitials = strtoupper(
        mb_substr(
            trim((string) ($sellerLayoutAccount?->store_name ?: 'SARI Seller')),
            0,
            2
        )
    ) ?: 'SS';

    $sellerUnreadMessages = 0;
    $sellerRecentAdminMessages = collect();

    $sellerActiveSuspension = false;

    if ($sellerLayoutAccount && $sellerLayoutAccount->suspended_until) {
        $sellerActiveSuspension = now()->lt(
            \Illuminate\Support\Carbon::parse(
                $sellerLayoutAccount->suspended_until
            )
        );
    }

    $sellerCriticalLocked = $sellerLayoutAccount
        && (
            (int) ($sellerLayoutAccount->warning_count ?? 0) >= 3
            || $sellerActiveSuspension
        );

    $sellerManualSuspension = $sellerCriticalLocked
        && $sellerActiveSuspension
        && (int) ($sellerLayoutAccount->warning_count ?? 0) < 3;
@endphp

<body data-no-progress-bar class="m-0 min-h-screen bg-[#F4F5F7] font-poppins text-[#1f1b16] antialiased {{ $sellerCriticalLocked ? 'overflow-hidden' : '' }}">



    {{-- =========================================================
        CRITICAL SELLER LOCK — 3 WARNINGS / ACTIVE SUSPENSION
        This overlay has no close button by design.
    ========================================================== --}}
    @if ($sellerCriticalLocked)
        <div
            id="sellerCriticalRestriction"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-[#2a1717]/55 p-4 backdrop-blur-[5px]"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="sellerCriticalRestrictionTitle"
        >
            <div class="w-full max-w-[540px] overflow-hidden rounded-[24px] border border-[#efcaca] bg-white shadow-[0_35px_110px_rgba(72,25,25,.34)]">
                <div class="border-b border-[#f1d9d9] bg-[#fff5f5] px-6 py-5 sm:px-7">
                    <div class="flex items-start gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-[#efcccc] bg-white text-[#c55353]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path d="M12 3 3 20h18L12 3Z"></path>
                                <path d="M12 9v5"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <span class="inline-flex rounded-full border border-[#efcccc] bg-white px-2.5 py-1 text-[8px] font-bold uppercase tracking-[.12em] text-[#b95151]">
                                {{ $sellerManualSuspension ? 'Administrator Suspension' : 'Account Restricted' }}
                            </span>

                            <h2 id="sellerCriticalRestrictionTitle" class="mt-2.5 text-[20px] font-bold tracking-[-0.03em] text-[#4d2929]">
                                Selling Access Temporarily Restricted
                            </h2>

                            <p class="mt-2 text-[10px] leading-5 text-[#876565]">
                                @if ($sellerManualSuspension)
                                    Your seller account was <strong class="text-[#a94d4d]">manually suspended by the SARI Administrator</strong>.
                                    Seller tools and page navigation are temporarily locked until the suspension is lifted or the restriction period ends.
                                @else
                                    Your seller account has reached <strong class="text-[#a94d4d]">{{ (int) $sellerLayoutAccount->warning_count }} / 3 compliance warnings</strong>.
                                    Seller tools and page navigation are temporarily locked. Please message the administrator to request a review.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 sm:px-7 sm:py-6">
                    @if ($sellerLayoutAccount->suspended_until)
                        <div class="mb-4 rounded-[14px] border border-[#efd9d9] bg-[#fff9f9] px-4 py-3">
                            <p class="text-[8px] uppercase tracking-[.08em] text-[#a68181]">Restriction period</p>
                            <p class="mt-1 text-[10px] font-semibold text-[#704747]">
                                Until {{ $sellerLayoutAccount->suspended_until->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 rounded-[13px] border border-[#cfe3d7] bg-[#f5faf7] px-4 py-3 text-[9px] font-medium text-[#4f7c60]">
                            Your message was sent to the administrator.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('seller.compliance.message') }}">
                        @csrf

                        <label for="sellerRestrictionMessage" class="block text-[9px] font-semibold text-[#5c4141]">
                            Message Administrator
                        </label>

                        <textarea
                            id="sellerRestrictionMessage"
                            name="message"
                            rows="4"
                            required
                            autofocus
                            placeholder="Explain your concern or request an account review..."
                            class="mt-2 w-full resize-none rounded-[14px] border border-[#e8d2d2] bg-[#fffafa] px-4 py-3 text-[10px] leading-5 text-[#514242] outline-none transition placeholder:text-[#b59a9a] focus:border-[#cf7b7b] focus:ring-4 focus:ring-[#cf7b7b]/10"
                        ></textarea>

                        <button
                            type="submit"
                            class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-[#b95555] px-5 text-[10px] font-semibold text-white transition hover:bg-[#a54848]"
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                            </svg>
                            Message Administrator
                        </button>
                    </form>

                    <p class="mt-4 text-center text-[8px] leading-4 text-[#a58e8e]">
                        Access will remain locked until the account restriction is cleared by the administrator or the suspension period ends.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================
        SELLER SIDEBAR
    ========================================================== --}}
    @persist('seller-sidebar')
    <aside
        id="sellerSidebar"
        class="
            fixed inset-y-0 left-0 z-50
            flex w-[238px] -translate-x-full flex-col
            border-r border-[#eee4d3]
            bg-white
            transition-all duration-300 ease-out
            lg:translate-x-0
        "
    >
        {{-- LOGO --}}
        <div id="sellerSidebarBrand" class="relative flex min-h-[110px] items-center justify-center border-b border-[#eee4d3] px-4">
            <a
                id="sellerSidebarLogo"
                href="{{ route('seller.dashboard') }}"
                class="relative flex h-[64px] w-full items-center justify-center"
                aria-label="SARI Seller Dashboard"
                wire:navigate.hover
            >
                {{-- Same full logo rendering as the Admin sidebar. --}}
                <img
                    id="sellerSidebarFullLogo"
                    src="{{ $sellerFullLogoSrc }}"
                    alt="SARI"
                    width="145"
                    height="52"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                    style="width:145px;height:auto;object-fit:contain;filter:brightness(0);"
                    class="h-auto w-[145px] object-contain brightness-0"
                >

                {{-- Compact mark. It is already in the DOM and is shown only in compact mode. --}}
                <img
                    id="sellerSidebarCompactLogo"
                    src="{{ $sellerCompactLogoSrc }}"
                    alt="SARI"
                    width="52"
                    height="52"
                    loading="eager"
                    decoding="async"
                    style="width:52px;height:52px;object-fit:contain;"
                    class="h-[52px] w-[52px] object-contain"
                >
            </a>

            <button
                id="sellerSidebarClose"
                type="button"
                class="
                    absolute right-4 top-1/2
                    grid h-10 w-10 -translate-y-1/2
                    place-items-center rounded-xl
                    text-[#6e6558]
                    transition hover:bg-[#f7eedf] hover:text-[#b97805]
                    lg:hidden
                "
                aria-label="Close seller sidebar"
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6 6 18"></path>
                </svg>
            </button>
        </div>

        {{-- NAVIGATION --}}
        <nav id="sellerSidebarNav" wire:navigate:scroll class="flex-1 overflow-y-auto">

            <p class="seller-sidebar-section-label">MAIN MENU</p>

            {{-- DASHBOARD --}}
            <a
                href="{{ route('seller.dashboard') }}"
                title="Dashboard Overview"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-semibold transition-all duration-200
                    {{ request()->routeIs('seller.dashboard')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 11.5 12 4l9 7.5"></path>
                    <path d="M5.5 10v10h13V10"></path>
                    <path d="M9.5 20v-6h5v6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Dashboard Overview
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">CATALOG</p>

            {{-- PRODUCT MANAGEMENT --}}
            <a
                href="{{ route('seller.products.index') }}"
                title="Product Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.products.index')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Product Management
                </span>
            </a>

            {{-- ADD PRODUCT --}}
            <a
                href="{{ route('seller.products.create') }}"
                title="Add Product"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.products.create')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                    <path d="M12 11v6"></path>
                    <path d="M9 14h6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Add Product
                </span>
            </a>

            {{-- INVENTORY --}}
            <a
                href="{{ route('seller.inventory.index') }}"
                title="Inventory"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.inventory.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 5h16v14H4z"></path>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h8"></path>
                    <path d="M8 17h5"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Inventory
                </span>
            </a>

            {{-- ARCHIVED PRODUCTS --}}
            <a
                href="{{ route('seller.products.archive') }}"
                title="Archived Products"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.products.archive')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h16"></path>
                    <path d="M6 7v12h12V7"></path>
                    <path d="M9 11h6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Archived Products
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">ORDERS &amp; FULFILLMENT</p>

            {{-- ORDERS --}}
            <a
                href="{{ route('seller.orders') }}"
                title="Order Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.orders*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 7h14l-1 13H6L5 7Z"></path>
                    <path d="M9 7a3 3 0 0 1 6 0"></path>
                    <path d="M8 12h8"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Order Management
                </span>
            </a>

            {{-- SHIPPING --}}
            <a
                href="{{ route('seller.shipping.index') }}"
                title="Shipping Tracking"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.shipping.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 7h11v10H3z"></path>
                    <path d="M14 10h3l4 4v3h-7z"></path>
                    <circle cx="7" cy="18" r="1.8"></circle>
                    <circle cx="18" cy="18" r="1.8"></circle>
                    <path d="M5 11h6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Shipping
                </span>
            </a>

            {{-- RETURNS & REFUNDS --}}
            <a
                href="{{ route('seller.returns.index') }}"
                title="Returns & Refunds"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.returns.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M9 7H5v4"></path>
                    <path d="M5 11c1.8-4.5 8-6.2 12-2.8 4.1 3.5 2.4 10.2-2.8 11.2-3.1.6-6.1-.8-7.7-3.2"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Returns &amp; Refunds
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">MARKETING</p>

            {{-- PROMOTIONS & VOUCHERS --}}
            <a
                href="{{ route('seller.vouchers.index') }}"
                title="Promotions & Vouchers"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.vouchers.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 12 12 3h7v7l-9 9-7-7Z"></path>
                    <circle cx="16" cy="7" r="1"></circle>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Promotions &amp; Vouchers
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">BUSINESS</p>

            {{-- FINANCE & EARNINGS --}}
            <a
                href="{{ route('seller.finance.index') }}"
                title="Finance & Earnings"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.finance.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 19h16"></path>
                    <path d="M6 16v-5"></path>
                    <path d="M12 16V6"></path>
                    <path d="M18 16V9"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Finance &amp; Earnings
                </span>
            </a>

            {{-- REVIEWS & RATINGS --}}
            <a
                href="{{ route('seller.reviews-center.index') }}"
                title="Reviews & Ratings"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.reviews-center.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Reviews &amp; Ratings
                </span>
            </a>

            {{-- REPORTS --}}
            <a
                href="{{ route('seller.reports') }}"
                title="Generate Report"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.reports*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 3h10l4 4v14H5z"></path>
                    <path d="M15 3v5h5"></path>
                    <path d="M9 13h6"></path>
                    <path d="M9 17h4"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Generate Report
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">SUPPORT</p>

            {{-- CHAT / MESSAGING --}}
            <a
                href="{{ route('seller.messages') }}"
                title="Chat / Messaging"
                data-seller-sidebar-item
                class="
                    flex items-center justify-between gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.messages*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <span class="flex items-center gap-3">
                    <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                        <path d="M8 10h8"></path>
                        <path d="M8 14h5"></path>
                    </svg>

                    <span class="seller-sidebar-label whitespace-nowrap">
                        Chat / Messaging
                    </span>
                </span>

                {{-- REAL-TIME SIDEBAR UNREAD BADGE --}}
                <span
                    id="sellerSidebarMessageBadge"
                    data-seller-unread-badge
                    data-has-unread="{{ $sellerUnreadMessages > 0 ? 'true' : 'false' }}"
                    class="
                        seller-sidebar-extra
                        {{ $sellerUnreadMessages > 0 ? 'grid' : 'hidden' }}
                        h-5 min-w-[20px]
                        place-items-center rounded-full
                        px-1.5 text-[9px] font-bold
                        {{ request()->routeIs('seller.messages*')
                            ? 'bg-white text-[#d9930a]'
                            : 'bg-[#d9930a] text-white' }}
                    "
                >
                    {{ $sellerUnreadMessages > 99 ? '99+' : $sellerUnreadMessages }}
                </span>
            </a>

            {{-- NOTIFICATIONS --}}
            <a
                href="{{ route('seller.notifications.index') }}"
                title="Notifications"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.notifications.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Notifications
                </span>
            </a>

            {{-- COMPLIANCE CENTER --}}
            <a
                href="{{ route('seller.compliance-center.index') }}"
                title="Compliance Center"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.compliance-center.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 5 6v5c0 4.5 2.8 7.8 7 10 4.2-2.2 7-5.5 7-10V6l-7-3Z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Compliance Center
                </span>
            </a>

            <div class="seller-sidebar-section-divider"></div>
            <p class="seller-sidebar-section-label">ACCOUNT</p>

            {{-- STORE MANAGEMENT --}}
            <a
                href="{{ route('seller.store.index') }}"
                title="Store Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.store.*')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 9h16l-1-5H5L4 9Z"></path>
                    <path d="M5 9v11h14V9"></path>
                    <path d="M9 20v-6h6v6"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Store Management
                </span>
            </a>

            {{-- ACCOUNT --}}
            <a
                href="{{ route('seller.account') }}"
                title="Account Management"
                data-seller-sidebar-item
                class="
                    flex items-center gap-3 rounded-xl px-4 py-3.5
                    text-[13px] font-medium transition-all duration-200
                    {{ request()->routeIs('seller.account')
                        ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                        : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}
                "
                wire:navigate.hover
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="3.5"></circle>
                    <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                </svg>

                <span class="seller-sidebar-label whitespace-nowrap">
                    Account Management
                </span>
            </a>

            {{-- LOGOUT --}}
            <form
                id="sellerLogoutForm"
                method="POST"
                action="{{ route('seller.logout') }}"
                data-seller-logout-form
            >
                @csrf

                <button
                    type="submit"
                    title="Logout"
                    data-seller-sidebar-item
                    class="
                        flex w-full items-center gap-3 rounded-xl px-4 py-3.5
                        text-[13px] font-medium text-[#514b42]
                        transition-all duration-200
                        hover:bg-red-50 hover:text-red-600
                    "
                >
                    <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M14 3h7v18h-7"></path>
                    </svg>

                    <span class="seller-sidebar-label whitespace-nowrap">
                        Logout
                    </span>
                </button>
            </form>

        </nav>

        {{-- PROFILE --}}
        <div
            id="sellerSidebarProfile"
            class="border-t border-[#eee4d3] bg-white p-4 transition-all duration-300"
        >
            <a
                href="{{ route('seller.account') }}"
                title="{{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}"
                data-seller-sidebar-item
                class="
                    flex w-full items-center gap-3 rounded-2xl
                    border border-[#eadfca] bg-white p-3.5
                    transition hover:border-[#dbc396] hover:shadow-sm
                "
                wire:navigate.hover
            >
                <div
                    id="sellerSidebarProfileAvatar"
                    class="seller-shell-profile-avatar grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[12px] font-semibold text-white"
                    data-profile-photo-url="{{ $sellerShellProfilePhotoUrl }}"
                >
                    <img
                        class="seller-shell-profile-avatar__image"
                        src="{{ $sellerShellProfilePhotoUrl ?: '' }}"
                        alt="{{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }} profile photo"
                        loading="eager"
                        decoding="async"
                        @if(!$sellerShellProfilePhotoUrl) hidden @endif
                    >
                    <span
                        class="seller-shell-profile-avatar__fallback"
                        @if($sellerShellProfilePhotoUrl) hidden @endif
                    >
                        {{ $sellerShellProfileInitials }}
                    </span>
                </div>

                <div class="seller-sidebar-label min-w-0 flex-1">
                    <p class="truncate text-[12px] font-semibold text-[#211d17]">
                        {{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}
                    </p>

                    <p id="sellerSidebarStoreStatus" class="mt-0.5 truncate text-[9px] text-[#8d8272]">
                        {{ $sellerActiveSuspension ? 'Suspended Store' : 'Verified Store' }}
                    </p>
                </div>

                <svg
                    viewBox="0 0 24 24"
                    class="seller-sidebar-extra h-4 w-4 shrink-0 text-[#82796b]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </a>
        </div>
    </aside>
    @endpersist


    {{-- =========================================================
        MAIN CONTENT WRAPPER
    ========================================================== --}}
    <div id="sellerContent" class="min-h-screen transition-all duration-300 ease-out">

        {{-- =====================================================
            SELLER HEADER
        ====================================================== --}}
        <header class="sticky top-0 z-40 border-b border-[#eee4d3] bg-white">
            <div class="flex min-h-[86px] w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 xl:px-10">

                {{-- LEFT --}}
                <div class="flex min-w-0 items-center gap-3 sm:gap-4">

                    {{-- MOBILE MENU --}}
                    <button
                        id="sellerMenuButton"
                        type="button"
                        class="
                            grid h-11 w-11 shrink-0 place-items-center
                            rounded-xl border border-[#e8dfd0] bg-white
                            text-[#4f473c] shadow-sm transition
                            hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]
                            lg:hidden
                        "
                        aria-label="Open seller sidebar"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 17h16"></path>
                        </svg>
                    </button>

                    {{-- DESKTOP COLLAPSE --}}
                    <button
                        id="sellerSidebarToggle"
                        type="button"
                        class="
                            hidden h-11 w-11 shrink-0 place-items-center
                            rounded-xl border border-[#e8dfd0] bg-white
                            text-[#4f473c] shadow-sm transition-all duration-200
                            hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]
                            active:scale-95 lg:grid
                        "
                        aria-label="Collapse sidebar"
                        aria-expanded="true"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 17h16"></path>
                        </svg>
                    </button>

                    {{-- PAGE TITLE --}}
                    <div class="min-w-0">
                        <h1 class="truncate text-[18px] font-bold tracking-[-0.025em] text-[#17140e] sm:text-[20px] xl:text-[22px]">
                            @yield('page-title', 'Seller Dashboard')
                        </h1>

                        <div class="mt-1 hidden items-center gap-1.5 text-[11px] text-[#978d7d] sm:flex">
                            <a
                                href="{{ route('seller.dashboard') }}"
                                class="transition hover:text-[#b97805]"
                wire:navigate.hover
            >
                                Seller
                            </a>

                            <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>

                            <span class="font-medium text-[#5c5448]">
                                @yield('page-title', 'Dashboard')
                            </span>
                        </div>
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="flex items-center gap-2 sm:gap-3">

                    {{-- SEARCH --}}
                    <div class="relative hidden md:block">
                        <svg
                            viewBox="0 0 24 24"
                            class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#9a9184]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input
                            type="search"
                            placeholder="Search orders, products..."
                            class="
                                h-11 w-[220px] rounded-[14px]
                                border border-[#e6ddcf] bg-white
                                pl-11 pr-4 text-[11px] outline-none
                                placeholder:text-[#a89f92]
                                focus:border-[#d89a25] focus:ring-4 focus:ring-[#d89a25]/10
                                xl:w-[300px]
                            "
                        >
                    </div>

                    {{-- UNIFIED REAL-TIME NOTIFICATION CENTER --}}
                    <div class="relative">
                        <button
                            id="sellerNotificationBell"
                            type="button"
                            aria-label="Open notifications"
                            aria-expanded="false"
                            class="
                                relative grid h-11 w-11 place-items-center
                                border-0 bg-transparent
                                text-[#475467] transition-colors
                                hover:text-[#c9890b]
                            "
                        >
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                <path d="M10 21h4"></path>
                            </svg>

                            <span
                                id="sellerBellNotificationBadge"
                                data-seller-notification-badge
                                data-has-unread="false"
                                class="
                                    absolute -right-1 -top-1 hidden
                                    h-[19px] min-w-[19px]
                                    place-items-center rounded-full
                                    border-2 border-white
                                    bg-[#d9930a]
                                    px-1 text-[8px] font-bold text-white
                                "
                            >0</span>
                        </button>

                        <div
                            id="sellerNotificationDropdown"
                            class="
                                absolute right-0 top-[54px] z-[80]
                                hidden w-[350px] overflow-hidden
                                rounded-[18px] border border-[#e5e7eb]
                                bg-white shadow-[0_18px_50px_rgba(15,23,42,0.16)]
                                sm:w-[390px]
                            "
                        >
                            <div class="flex items-start justify-between gap-4 border-b border-[#eceff2] px-4 py-3.5">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold text-[#202124]">
                                        Notifications
                                    </p>
                                    <p id="sellerNotificationUnreadText" class="mt-0.5 text-[8px] text-[#8a919b]">
                                        No unread notifications
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-3">
                                    <button
                                        id="sellerNotificationMarkAllRead"
                                        type="button"
                                        class="text-[8px] font-semibold text-[#8a6a2d] transition-colors hover:text-[#b97805]"
                                    >
                                        Mark all read
                                    </button>

                                    <a
                                        href="{{ route('seller.notifications.index') }}"
                                        class="text-[8px] font-semibold text-[#a66f13] transition-colors hover:text-[#7e5007]"
                                        wire:navigate.hover
                                    >
                                        View all
                                    </a>
                                </div>
                            </div>

                            <div
                                id="sellerNotificationList"
                                data-seller-notification-scroll
                                class="max-h-[390px] overflow-y-auto"
                            >
                                <div id="sellerNotificationEmpty" class="px-5 py-9 text-center">
                                    <div class="mx-auto text-[#b47e1e]">
                                        <svg viewBox="0 0 24 24" class="mx-auto h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                            <path d="M10 21h4"></path>
                                        </svg>
                                    </div>

                                    <p class="mt-3 text-[9px] font-semibold text-[#475467]">
                                        No notifications yet
                                    </p>

                                    <p class="mx-auto mt-1 max-w-[250px] text-[8px] leading-4 text-[#98a2b3]">
                                        Buyer/Admin messages, orders, shipping, reviews, returns,
                                        inventory and account activity will appear here.
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ route('seller.notifications.index') }}"
                                class="
                                    flex items-center justify-center
                                    border-t border-[#eceff2]
                                    bg-[#fafbfc] px-4 py-3
                                    text-[9px] font-semibold text-[#9a6817]
                                    transition-colors hover:bg-[#f8fafc]
                                "
                                wire:navigate.hover
                            >
                                Open Notification Center
                            </a>
                        </div>
                    </div>

                    <div class="hidden h-9 w-px bg-[#eee4d5] xl:block"></div>

                    {{-- ACCOUNT SHORTCUT --}}
                    <a
                        href="{{ route('seller.account') }}"
                        class="hidden items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-[#fff7e9] xl:flex"
                wire:navigate.hover
            >
                        <div
                            id="sellerHeaderProfileAvatar"
                            class="seller-shell-profile-avatar grid h-10 w-10 place-items-center rounded-full bg-[#d9930a] text-[11px] font-bold text-white"
                            data-profile-photo-url="{{ $sellerShellProfilePhotoUrl }}"
                        >
                            <img
                                class="seller-shell-profile-avatar__image"
                                src="{{ $sellerShellProfilePhotoUrl ?: '' }}"
                                alt="{{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }} profile photo"
                                loading="eager"
                                decoding="async"
                                @if(!$sellerShellProfilePhotoUrl) hidden @endif
                            >
                            <span
                                class="seller-shell-profile-avatar__fallback"
                                @if($sellerShellProfilePhotoUrl) hidden @endif
                            >
                                {{ $sellerShellProfileInitials }}
                            </span>
                        </div>

                        <div>
                            <p class="max-w-[160px] truncate text-[11px] font-semibold text-[#28231c]">
                                {{ $sellerLayoutAccount?->store_name ?: 'SARI Seller' }}
                            </p>

                            <p class="mt-0.5 text-[9px] text-[#908779]">
                                {{ $sellerActiveSuspension ? 'Suspended Store' : 'Verified Store' }}
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="w-full px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-7 xl:px-10">
            @yield('content')
        </main>
    </div>



    {{-- =========================================================
        GLOBAL SARI SELLER AI ASSISTANT
        Persistent across Seller Livewire navigation.
    ========================================================== --}}
    @persist('seller-ai-assistant')
        @include('seller.partials.ai-assistant', [
            'sellerAiAccount' => $sellerLayoutAccount,
        ])
    @endpersist

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div
        id="sellerSidebarOverlay"
        class="fixed inset-0 z-40 hidden bg-black/30 backdrop-blur-[1px] lg:hidden"
    ></div>


    {{-- =========================================================
        SELLER LOGOUT CONFIRMATION
        Global modal used by the existing seller.logout POST form.
    ========================================================== --}}
    <div
        id="sellerLogoutConfirmModal"
        hidden
        aria-hidden="true"
    >
        <div
            class="seller-logout-modal-backdrop"
            data-seller-logout-cancel
            aria-hidden="true"
        ></div>

        <section
            class="seller-logout-modal-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sellerLogoutModalTitle"
            aria-describedby="sellerLogoutModalDescription"
        >
            <div class="seller-logout-modal-body">
                <div class="seller-logout-modal-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M14 3h7v18h-7"></path>
                    </svg>
                </div>

                <h2
                    id="sellerLogoutModalTitle"
                    class="seller-logout-modal-title"
                >
                    Log out of SARI?
                </h2>

                <p
                    id="sellerLogoutModalDescription"
                    class="seller-logout-modal-copy"
                >
                    Are you sure you want to end your Seller session?
                    You will need to sign in again to access Seller Center.
                </p>
            </div>

            <div class="seller-logout-modal-footer">
                <button
                    type="button"
                    class="seller-logout-modal-btn seller-logout-modal-btn-cancel"
                    data-seller-logout-cancel
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="sellerLogoutConfirmButton"
                    class="seller-logout-modal-btn seller-logout-modal-btn-danger"
                    data-seller-logout-confirm
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M14 3h7v18h-7"></path>
                    </svg>
                    <span data-seller-logout-confirm-label>Logout</span>
                </button>
            </div>
        </section>
    </div>


    {{-- REAL-TIME MESSAGE TOAST --}}
    <div
        id="sellerMessageToast"
        class="
            pointer-events-none fixed right-4 top-[100px] z-[100]
            hidden w-[calc(100%-2rem)] max-w-[350px]
            translate-y-2 rounded-[16px]
            border border-[#e8ddca] bg-white
            p-4 opacity-0
            shadow-[0_18px_45px_rgba(55,43,25,0.16)]
            transition-all duration-300
            sm:right-6
        "
    >
        <div class="flex gap-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#fbf5e9] text-[10px] font-bold text-[#a8731f]">
                SA
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[9px] font-bold text-[#3d362d]">
                        New message from Admin
                    </p>

                    <span class="h-2 w-2 shrink-0 rounded-full bg-[#d9930a]"></span>
                </div>

                <p
                    id="sellerMessageToastText"
                    class="mt-1 line-clamp-2 text-[8px] leading-4 text-[#7f766a]"
                ></p>

                <a
                    href="{{ route('seller.messages') }}"
                    class="pointer-events-auto mt-2 inline-flex text-[8px] font-semibold text-[#9e6811] hover:text-[#734803]"
                wire:navigate.hover
            >
                    Open message →
                </a>
            </div>
        </div>
    </div>



    {{-- SELLER ORDER STATUS TOAST --}}
    @if ($sellerLayoutAccount)
        <div id="sellerOrderStatusToast" class="pointer-events-none fixed right-4 top-[100px] z-[170] hidden w-[calc(100%-2rem)] max-w-[390px] translate-y-2 rounded-[18px] border border-[#d4e1eb] bg-white p-4 opacity-0 shadow-[0_18px_50px_rgba(50,66,82,.16)] transition-all duration-300 sm:right-6">
            <div class="flex gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#f1f7fb] text-[#3C6E91]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h11v10H3z"></path><path d="M14 10h4l3 3v4h-7z"></path></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p id="sellerOrderToastTitle" class="text-[10px] font-bold text-[#302920]">Order Update</p>
                    <p id="sellerOrderToastMessage" class="mt-1 text-[8px] leading-4 text-[#786f64]"></p>
                    <a href="{{ route('seller.orders') }}" class="pointer-events-auto mt-2 inline-flex text-[8px] font-semibold text-[#3C6E91] hover:text-[#2e5b79]"
                wire:navigate.hover
            >Open Order Management →</a>
                </div>
            </div>
        </div>
    @endif

    {{-- =========================================================
        PERSISTENT SELLER SHELL
        Sidebar is preserved by Livewire @persist.
        All shared listeners are registered once and work across navigation.
    ========================================================== --}}
    <script data-navigate-once>
        (function () {
            const sellerChannel = @json(
                $sellerLayoutAccount && $sellerLayoutAccount->realtime_token
                    ? 'sari.seller.' . $sellerLayoutAccount->realtime_token
                    : null
            );

            const sellerId = @json((int) ($sellerLayoutAccount?->id ?? 0));
            const dashboardUrl = @json(route('seller.dashboard'));
            const ordersUrl = @json(route('seller.orders'));
            const shippingUrl = @json(route('seller.shipping.index'));
            const messagesUrl = @json(route('seller.messages'));
            const loginUrl = @json(route('login'));
            const accountStateUrl = @json(route('seller.account-state'));
            const layoutStateUrl = @json(route('seller.layout-state'));
            const notificationIndexUrl = @json(route('seller.notifications.index'));
            const notificationReadAllUrl = @json(route('seller.notifications.read-all'));
            const buyerMessagesUrl = @json(route('seller.buyer-messages'));
            const csrfToken = @json(csrf_token());

            const desktopBreakpoint = 1024;
            const sidebarStorageKey = 'sari:seller-sidebar-collapsed';

            const pathOf = (url) => {
                try {
                    return new URL(url, window.location.origin).pathname.replace(/\/+$/, '') || '/';
                } catch (_) {
                    return '/';
                }
            };

            const currentPath = () =>
                (window.location.pathname.replace(/\/+$/, '') || '/');

            const dashboardPath = pathOf(dashboardUrl);
            const ordersPath = pathOf(ordersUrl);
            const shippingPath = pathOf(shippingUrl);
            const messagesPath = pathOf(messagesUrl);
            const buyerMessagesPath = pathOf(buyerMessagesUrl);
            const notificationPath = pathOf(notificationIndexUrl);

            window.__SARI_SELLER_SHELL_STATE__ =
                window.__SARI_SELLER_SHELL_STATE__ || {
                    messageUnreadCount: 0,
                    notificationUnreadCount: 0,
                    processedMessageIds: new Set(),
                    processedNotificationIds: new Set(),
                    messageSubscribed: false,
                    notificationSubscribed: false,
                    complianceSubscribed: false,
                    accountSubscribed: false,
                    orderSubscribed: false,
                    lastOrderRealtimeAt: 0,
                    lastOrderRealtimeRevision: null,
                    toastTimer: null,
                    orderToastTimer: null,
                    statusBusy: false,
                    orderBusy: false,
                    navigationActive: false,
                    layoutStateLoadedAt: 0,
                    layoutStateAbort: null,
                    layoutStateRequestSeq: 0,
                    notificationMutationSeq: 0,
                    accountStateAbort: null,
                    orderPollAbort: null,
                    uiAbort: null,
                };

            const state = window.__SARI_SELLER_SHELL_STATE__;

            state.messageUnreadCount = Math.max(
                0,
                Number(
                    state.messageUnreadCount
                    ?? state.unreadCount
                    ?? 0
                )
            );

            state.notificationUnreadCount = Math.max(
                0,
                Number(state.notificationUnreadCount || 0)
            );

            if (!(state.processedNotificationIds instanceof Set)) {
                state.processedNotificationIds = new Set();
            }

            state.layoutStateRequestSeq =
                Math.max(
                    0,
                    Number(state.layoutStateRequestSeq || 0)
                );

            state.notificationMutationSeq =
                Math.max(
                    0,
                    Number(state.notificationMutationSeq || 0)
                );

            function beginNotificationMutation() {
                state.notificationMutationSeq += 1;
                state.layoutStateRequestSeq += 1;
                state.layoutStateLoadedAt = 0;

                try {
                    state.layoutStateAbort?.abort();
                } catch (_) {}

                state.layoutStateAbort = null;

                return state.notificationMutationSeq;
            }

            function syncSellerShellProfileAvatar() {
                const headerAvatar =
                    document.getElementById('sellerHeaderProfileAvatar');

                const sidebarAvatar =
                    document.getElementById('sellerSidebarProfileAvatar');

                const freshUrl =
                    headerAvatar?.dataset.profilePhotoUrl
                    || sidebarAvatar?.dataset.profilePhotoUrl
                    || '';

                [headerAvatar, sidebarAvatar]
                    .filter(Boolean)
                    .forEach(function (avatar) {
                        const image =
                            avatar.querySelector(
                                '.seller-shell-profile-avatar__image'
                            );

                        const fallback =
                            avatar.querySelector(
                                '.seller-shell-profile-avatar__fallback'
                            );

                        if (!image || !fallback) {
                            return;
                        }

                        avatar.dataset.profilePhotoUrl = freshUrl;

                        const showFallback = function () {
                            image.hidden = true;
                            fallback.hidden = false;
                        };

                        const showImage = function () {
                            image.hidden = false;
                            fallback.hidden = true;
                        };

                        image.onload = showImage;
                        image.onerror = showFallback;

                        if (!freshUrl) {
                            image.removeAttribute('src');
                            showFallback();
                            return;
                        }

                        if (image.getAttribute('src') !== freshUrl) {
                            image.setAttribute('src', freshUrl);
                        }

                        if (image.complete) {
                            if (image.naturalWidth > 0) {
                                showImage();
                            } else {
                                showFallback();
                            }
                        }
                    });
            }

            function savedCollapsed() {
                try {
                    return window.localStorage.getItem(sidebarStorageKey) === '1';
                } catch (_) {
                    return false;
                }
            }

            function saveCollapsed(value) {
                try {
                    window.localStorage.setItem(
                        sidebarStorageKey,
                        value ? '1' : '0'
                    );
                } catch (_) {}
            }

            function closeMobileSidebar() {
                document
                    .getElementById('sellerSidebar')
                    ?.classList.add('-translate-x-full');

                document
                    .getElementById('sellerSidebarOverlay')
                    ?.classList.add('hidden');

                if (!document.getElementById('sellerCriticalRestriction')) {
                    document.body.classList.remove('overflow-hidden');
                }
            }

            function syncCollapsedState() {
                const root = document.documentElement;
                const toggle = document.getElementById('sellerSidebarToggle');

                if (window.innerWidth >= desktopBreakpoint) {
                    root.classList.toggle(
                        'seller-sidebar-collapsed',
                        savedCollapsed()
                    );
                } else {
                    root.classList.remove('seller-sidebar-collapsed');
                }

                const collapsed =
                    root.classList.contains('seller-sidebar-collapsed');

                toggle?.setAttribute(
                    'aria-expanded',
                    collapsed ? 'false' : 'true'
                );

                toggle?.setAttribute(
                    'aria-label',
                    collapsed ? 'Expand sidebar' : 'Collapse sidebar'
                );
            }

            function syncSidebarActiveState(pathOverride = null) {
                const activeClasses = [
                    'bg-[#d9930a]',
                    'text-white',
                    'shadow-[0_5px_12px_rgba(217,147,10,0.14)]'
                ];

                const inactiveClasses = [
                    'text-[#514b42]',
                    'hover:bg-[#f9f1e3]',
                    'hover:text-[#a96e05]'
                ];

                const path = pathOverride || currentPath();

                document
                    .querySelectorAll(
                        '#sellerSidebarNav > a[data-seller-sidebar-item]'
                    )
                    .forEach(function (link) {
                        const linkPath = pathOf(link.href);
                        const active =
                            linkPath === path ||
                            (
                                linkPath === shippingPath &&
                                (
                                    path === shippingPath ||
                                    path.startsWith(shippingPath + '/')
                                )
                            );

                        activeClasses.forEach(function (className) {
                            link.classList.toggle(className, active);
                        });

                        inactiveClasses.forEach(function (className) {
                            link.classList.toggle(className, !active);
                        });

                        if (linkPath === messagesPath) {
                            const badge =
                                link.querySelector(
                                    '#sellerSidebarMessageBadge'
                                );

                            if (badge) {
                                badge.classList.toggle(
                                    'bg-white',
                                    active
                                );
                                badge.classList.toggle(
                                    'text-[#d9930a]',
                                    active
                                );
                                badge.classList.toggle(
                                    'bg-[#d9930a]',
                                    !active
                                );
                                badge.classList.toggle(
                                    'text-white',
                                    !active
                                );
                            }
                        }
                    });
            }

            function bindCurrentShellUi() {
                state.uiAbort?.abort();

                const controller = new AbortController();
                state.uiAbort = controller;
                const signal = controller.signal;

                const root = document.documentElement;
                const sidebar = document.getElementById('sellerSidebar');
                const overlay =
                    document.getElementById('sellerSidebarOverlay');
                const mobileMenu =
                    document.getElementById('sellerMenuButton');
                const mobileClose =
                    document.getElementById('sellerSidebarClose');
                const desktopToggle =
                    document.getElementById('sellerSidebarToggle');
                const bell =
                    document.getElementById('sellerNotificationBell');
                const dropdown =
                    document.getElementById('sellerNotificationDropdown');
                const markAllNotifications =
                    document.getElementById('sellerNotificationMarkAllRead');

                syncCollapsedState();
                syncSidebarActiveState();
                syncSellerShellProfileAvatar();

                mobileMenu?.addEventListener(
                    'click',
                    function () {
                        sidebar?.classList.remove('-translate-x-full');
                        overlay?.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    },
                    { signal }
                );

                mobileClose?.addEventListener(
                    'click',
                    closeMobileSidebar,
                    { signal }
                );

                overlay?.addEventListener(
                    'click',
                    closeMobileSidebar,
                    { signal }
                );

                desktopToggle?.addEventListener(
                    'click',
                    function () {
                        if (window.innerWidth < desktopBreakpoint) {
                            return;
                        }

                        const next =
                            !root.classList.contains(
                                'seller-sidebar-collapsed'
                            );

                        root.classList.toggle(
                            'seller-sidebar-collapsed',
                            next
                        );

                        saveCollapsed(next);
                        syncCollapsedState();
                    },
                    { signal }
                );

                window.addEventListener(
                    'resize',
                    function () {
                        if (window.innerWidth >= desktopBreakpoint) {
                            overlay?.classList.add('hidden');

                            if (
                                !document.getElementById(
                                    'sellerCriticalRestriction'
                                )
                            ) {
                                document.body.classList.remove(
                                    'overflow-hidden'
                                );
                            }
                        }

                        syncCollapsedState();
                    },
                    { signal }
                );

                document
                    .querySelectorAll('#sellerSidebar a[href]')
                    .forEach(function (link) {
                        link.addEventListener(
                            'click',
                            function () {
                                if (
                                    window.innerWidth <
                                    desktopBreakpoint
                                ) {
                                    closeMobileSidebar();
                                }
                            },
                            { signal }
                        );
                    });

                function closeNotificationDropdown() {
                    dropdown?.classList.add('hidden');
                    bell?.setAttribute('aria-expanded', 'false');
                }

                bell?.addEventListener(
                    'click',
                    function (event) {
                        event.stopPropagation();

                        if (!dropdown) {
                            return;
                        }

                        const open =
                            dropdown.classList.contains('hidden');

                        dropdown.classList.toggle('hidden', !open);

                        bell.setAttribute(
                            'aria-expanded',
                            open ? 'true' : 'false'
                        );
                    },
                    { signal }
                );

                dropdown?.addEventListener(
                    'click',
                    function (event) {
                        event.stopPropagation();
                    },
                    { signal }
                );

                markAllNotifications?.addEventListener(
                    'click',
                    function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        markAllNotificationsRead();
                    },
                    { signal }
                );

                document.addEventListener(
                    'click',
                    closeNotificationDropdown,
                    { signal }
                );

                document.addEventListener(
                    'keydown',
                    function (event) {
                        if (event.key === 'Escape') {
                            closeNotificationDropdown();
                            closeMobileSidebar();
                        }
                    },
                    { signal }
                );
            }

            function displayCount(count) {
                return count > 99 ? '99+' : String(count);
            }

            function updateBadge(badge, count) {
                if (!badge) {
                    return;
                }

                const safeCount =
                    Math.max(0, Number(count || 0));

                badge.dataset.hasUnread =
                    safeCount > 0 ? 'true' : 'false';

                if (safeCount < 1) {
                    /*
                     * Force-hide at DOM + inline-style level.
                     * This avoids any Tailwind/CSS cascade conflict between
                     * `hidden`, `grid`, and older badge rules.
                     */
                    badge.textContent = '';
                    badge.hidden = true;
                    badge.setAttribute('aria-hidden', 'true');

                    badge.classList.add('hidden');
                    badge.classList.remove('grid', 'flex', 'inline-grid');

                    badge.style.setProperty(
                        'display',
                        'none',
                        'important'
                    );

                    /*
                     * Cancel any Web Animations that may still be attached
                     * to the badge from an earlier unread state.
                     */
                    if (
                        typeof badge.getAnimations === 'function'
                    ) {
                        badge
                            .getAnimations()
                            .forEach(function (animation) {
                                animation.cancel();
                            });
                    }

                    return;
                }

                badge.hidden = false;
                badge.removeAttribute('aria-hidden');
                badge.style.removeProperty('display');

                badge.textContent =
                    displayCount(safeCount);

                badge.classList.remove('hidden');
                badge.classList.add('grid');
            }

            function syncUnreadUi() {
                updateBadge(
                    document.getElementById(
                        'sellerSidebarMessageBadge'
                    ),
                    state.messageUnreadCount
                );

                document
                    .querySelectorAll(
                        '[data-seller-notification-badge]'
                    )
                    .forEach(function (badge) {
                        updateBadge(
                            badge,
                            state.notificationUnreadCount
                        );
                    });

                const unreadText =
                    document.getElementById(
                        'sellerNotificationUnreadText'
                    );

                if (unreadText) {
                    unreadText.textContent =
                        state.notificationUnreadCount > 0
                            ? `${state.notificationUnreadCount} unread notification${state.notificationUnreadCount === 1 ? '' : 's'}`
                            : 'No unread notifications';
                }

                const markAll =
                    document.getElementById(
                        'sellerNotificationMarkAllRead'
                    );

                if (markAll) {
                    markAll.disabled =
                        state.notificationUnreadCount < 1;
                }

                syncSidebarActiveState();
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function notificationText(data) {
                return String(
                    data?.message
                    || data?.body
                    || data?.attachment_name
                    || 'Seller activity updated.'
                );
            }

            function notificationIcon(type) {
                const stroke =
                    'fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';

                switch (String(type || '')) {
                    case 'admin_message':
                    case 'buyer_message':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path><path d="M8 10h8"></path><path d="M8 14h5"></path></svg>`;
                    case 'shipment':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M3 7h11v10H3z"></path><path d="M14 10h3l4 4v3h-7z"></path><circle cx="7" cy="18" r="1.6"></circle><circle cx="18" cy="18" r="1.6"></circle></svg>`;
                    case 'review':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"></path></svg>`;
                    case 'return_request':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M9 7H5v4"></path><path d="M5 11c1.8-4.5 8-6.2 12-2.8 4.1 3.5 2.4 10.2-2.8 11.2-3.1.6-6.1-.8-7.7-3.2"></path></svg>`;
                    case 'compliance':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M12 3 5 6v5c0 4.5 2.8 7.8 7 10 4.2-2.2 7-5.5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>`;
                    case 'inventory':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M4 5h16v14H4z"></path><path d="M8 9h8"></path><path d="M8 13h8"></path></svg>`;
                    case 'voucher':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M3 12 12 3h7v7l-9 9-7-7Z"></path><circle cx="16" cy="7" r="1"></circle></svg>`;
                    case 'finance':
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M4 19h16"></path><path d="M6 16v-5"></path><path d="M12 16V6"></path><path d="M18 16V9"></path></svg>`;
                    case 'new_order':
                    case 'order_status':
                    default:
                        return `<svg viewBox="0 0 24 24" ${stroke}><path d="M5 7h14l-1 13H6L5 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path></svg>`;
                }
            }

            function isMessageNotification(type) {
                return [
                    'admin_message',
                    'buyer_message',
                ].includes(String(type || ''));
            }

            function isCurrentMessageDestination(data) {
                const type = String(data?.type || '');

                if (type === 'admin_message') {
                    return currentPath() === messagesPath;
                }

                if (type === 'buyer_message') {
                    return currentPath() === buyerMessagesPath;
                }

                return false;
            }

            function renderNotificationEmptyState() {
                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (!list) {
                    return;
                }

                list.innerHTML = `
                    <div id="sellerNotificationEmpty" class="px-5 py-9 text-center">
                        <div class="mx-auto text-[#b47e1e]">
                            <svg viewBox="0 0 24 24" class="mx-auto h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                <path d="M10 21h4"></path>
                            </svg>
                        </div>

                        <p class="mt-3 text-[9px] font-semibold text-[#475467]">
                            No notifications yet
                        </p>

                        <p class="mx-auto mt-1 max-w-[250px] text-[8px] leading-4 text-[#98a2b3]">
                            New Buyer/Admin messages, orders, shipping, reviews,
                            returns and account activity will appear here.
                        </p>
                    </div>
                `;
            }

            function removeNotificationItem(item) {
                if (!item) {
                    return;
                }

                item.remove();

                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (
                    list
                    && !list.querySelector(
                        '[data-notification-id]'
                    )
                ) {
                    renderNotificationEmptyState();
                }
            }

            function createNotificationItem(data) {
                if (!data?.id) {
                    return null;
                }

                const item = document.createElement('a');

                item.href =
                    data.action_url || notificationIndexUrl;

                /*
                 * Do not attach wire:navigate here.
                 * We persist the read state first, then navigate.
                 */
                item.dataset.notificationId = String(data.id);
                item.dataset.unread =
                    data.unread !== false ? 'true' : 'false';

                const unread = item.dataset.unread === 'true';

                item.className =
                    'seller-shell-notification-item block px-4 py-3.5 transition-colors';

                item.innerHTML = `
                    <div class="flex gap-3">
                        <div class="seller-shell-notification-icon">
                            ${notificationIcon(data.type)}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <p class="line-clamp-1 text-[9px] font-semibold text-[#344054]">
                                    ${escapeHtml(data.title || 'Seller notification')}
                                </p>

                                <span class="shrink-0 text-[7px] text-[#98a2b3]">
                                    ${escapeHtml(data.time || 'Now')}
                                </span>
                            </div>

                            <p class="mt-1 line-clamp-2 text-[8px] leading-4 text-[#667085]">
                                ${escapeHtml(notificationText(data))}
                            </p>
                        </div>

                        ${unread
                            ? '<span data-notification-unread-dot class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#d9930a]"></span>'
                            : ''}
                    </div>
                `;

                item.addEventListener(
                    'click',
                    async function (event) {
                        if (
                            item.dataset.unread !== 'true'
                        ) {
                            return;
                        }

                        event.preventDefault();

                        const destination =
                            item.href
                            || data.action_url
                            || notificationIndexUrl;

                        const persisted =
                            await markNotificationRead(
                                data,
                                item
                            );

                        /*
                         * Navigate only after the read POST finished.
                         * If persistence failed, we still let the Seller
                         * open the destination, but we do NOT falsely
                         * remove the unread notification locally.
                         */
                        if (destination) {
                            window.location.assign(
                                destination
                            );
                        }

                        return persisted;
                    },
                    { once: true }
                );

                return item;
            }

            function renderRecentNotifications(notifications) {
                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (!list) {
                    return;
                }

                const unreadNotifications =
                    notifications.filter(function (notification) {
                        return notification?.unread !== false;
                    });

                list.innerHTML = '';

                if (!unreadNotifications.length) {
                    renderNotificationEmptyState();
                    return;
                }

                unreadNotifications
                    .slice(0, 8)
                    .forEach(function (notification) {
                        const item =
                            createNotificationItem(
                                notification
                            );

                        if (item) {
                            list.appendChild(item);
                        }
                    });
            }

            async function markNotificationRead(data, item) {
                if (
                    !data?.read_url
                    || item?.dataset.unread !== 'true'
                ) {
                    return true;
                }

                const mutationSeq =
                    beginNotificationMutation();

                try {
                    const response = await fetch(
                        data.read_url,
                        {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Could not mark notification as read.'
                        );
                    }

                    const payload =
                        await response.json();

                    if (
                        mutationSeq !==
                        state.notificationMutationSeq
                    ) {
                        return false;
                    }

                    item.dataset.unread = 'false';

                    state.notificationUnreadCount =
                        Math.max(
                            0,
                            Number(
                                payload?.unread_count
                                ?? Math.max(
                                    0,
                                    state.notificationUnreadCount - 1
                                )
                            )
                        );

                    removeNotificationItem(item);
                    syncUnreadUi();

                    /*
                     * Read was persisted. The next layout-state response
                     * is authoritative and contains only unread records.
                     */
                    state.layoutStateLoadedAt = 0;

                    return true;
                } catch (_) {
                    /*
                     * Never fake a successful read. Keep the row/count
                     * visible and resync from the database.
                     */
                    if (
                        mutationSeq ===
                        state.notificationMutationSeq
                    ) {
                        state.layoutStateLoadedAt = 0;

                        window.setTimeout(function () {
                            loadLayoutState(true);
                        }, 80);
                    }

                    return false;
                }
            }

            async function markAllNotificationsRead() {
                if (!notificationReadAllUrl) {
                    return;
                }

                if (state.notificationUnreadCount < 1) {
                    syncUnreadUi();
                    return;
                }

                const mutationSeq =
                    beginNotificationMutation();

                const button =
                    document.getElementById(
                        'sellerNotificationMarkAllRead'
                    );

                if (button) {
                    button.disabled = true;
                }

                try {
                    const response = await fetch(
                        notificationReadAllUrl,
                        {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Could not mark notifications as read.'
                        );
                    }

                    const payload =
                        await response.json();

                    if (
                        mutationSeq !==
                        state.notificationMutationSeq
                    ) {
                        return;
                    }

                    state.notificationUnreadCount =
                        Math.max(
                            0,
                            Number(
                                payload?.unread_count
                                ?? 0
                            )
                        );

                    /*
                     * The read-all write is now committed.
                     * Header is an unread inbox, so clear the rows.
                     */
                    if (
                        state.notificationUnreadCount < 1
                    ) {
                        renderNotificationEmptyState();
                    }

                    syncUnreadUi();

                    state.layoutStateLoadedAt = 0;

                    /*
                     * Confirm from the dedicated notification state.
                     * Stale requests are blocked by mutation/request seq.
                     */
                    window.setTimeout(function () {
                        if (
                            mutationSeq ===
                            state.notificationMutationSeq
                        ) {
                            loadLayoutState(true);
                        }
                    }, 100);
                } catch (_) {
                    /*
                     * Keep current unread UI if the database write failed.
                     * Then ask the server for authoritative state.
                     */
                    if (
                        mutationSeq ===
                        state.notificationMutationSeq
                    ) {
                        state.layoutStateLoadedAt = 0;

                        window.setTimeout(function () {
                            loadLayoutState(true);
                        }, 80);
                    }
                } finally {
                    if (button) {
                        button.disabled =
                            state.notificationUnreadCount < 1;
                    }
                }
            }

            function prependNotification(data) {
                const list =
                    document.getElementById(
                        'sellerNotificationList'
                    );

                if (
                    !list
                    || !data?.id
                    || data?.unread === false
                ) {
                    return;
                }

                if (
                    list.querySelector(
                        `[data-notification-id="${data.id}"]`
                    )
                ) {
                    return;
                }

                document
                    .getElementById('sellerNotificationEmpty')
                    ?.remove();

                const item = createNotificationItem(data);

                if (!item) {
                    return;
                }

                list.prepend(item);

                const items =
                    list.querySelectorAll(
                        '[data-notification-id]'
                    );

                if (items.length > 8) {
                    items[items.length - 1].remove();
                }
            }

            function handleUnifiedNotification(data) {
                if (
                    !data
                    || data.id === undefined
                    || data.id === null
                ) {
                    return;
                }

                const id = String(data.id);

                if (state.processedNotificationIds.has(id)) {
                    return;
                }

                state.processedNotificationIds.add(id);

                if (data.unread !== false) {
                    state.notificationUnreadCount += 1;

                    if (
                        isMessageNotification(data.type)
                        && !isCurrentMessageDestination(data)
                    ) {
                        state.messageUnreadCount += 1;
                    }
                }

                prependNotification(data);
                syncUnreadUi();
                ringBell();

                window.dispatchEvent(
                    new CustomEvent(
                        'sari:seller-notification',
                        { detail: data }
                    )
                );
            }

            function ringBell() {
                const bell =
                    document.getElementById(
                        'sellerNotificationBell'
                    );

                if (!bell || typeof bell.animate !== 'function') {
                    return;
                }

                bell.animate(
                    [
                        { transform: 'rotate(0deg) scale(1)' },
                        { transform: 'rotate(-11deg) scale(1.08)' },
                        { transform: 'rotate(11deg) scale(1.08)' },
                        { transform: 'rotate(-7deg) scale(1.05)' },
                        { transform: 'rotate(7deg) scale(1.03)' },
                        { transform: 'rotate(0deg) scale(1)' }
                    ],
                    {
                        duration: 560,
                        easing: 'ease-out'
                    }
                );
            }

            function showMessageToast(data) {
                if (currentPath() === messagesPath) {
                    return;
                }

                const toast =
                    document.getElementById('sellerMessageToast');
                const text =
                    document.getElementById(
                        'sellerMessageToastText'
                    );

                if (!toast || !text) {
                    return;
                }

                text.textContent = notificationText(data);

                toast.classList.remove('hidden');

                requestAnimationFrame(function () {
                    toast.classList.remove(
                        'translate-y-2',
                        'opacity-0'
                    );
                    toast.classList.add(
                        'translate-y-0',
                        'opacity-100'
                    );
                });

                window.clearTimeout(state.toastTimer);

                state.toastTimer = window.setTimeout(function () {
                    toast.classList.remove(
                        'translate-y-0',
                        'opacity-100'
                    );
                    toast.classList.add(
                        'translate-y-2',
                        'opacity-0'
                    );

                    window.setTimeout(function () {
                        toast.classList.add('hidden');
                    }, 300);
                }, 4500);
            }

            function markLayoutAsRead() {
                state.messageUnreadCount = 0;
                syncUnreadUi();
            }

            function abortSellerBackgroundRequests() {
                [
                    'layoutStateAbort',
                    'accountStateAbort',
                    'orderPollAbort',
                ].forEach(function (key) {
                    try {
                        state[key]?.abort();
                    } catch (_) {}
                    state[key] = null;
                });
            }

            async function loadLayoutState(force = false) {
                if (
                    !layoutStateUrl ||
                    state.navigationActive
                ) {
                    return;
                }

                /*
                 * Realtime Echo is the primary source of message updates.
                 * Avoid hitting /seller/layout-state again on every route swap;
                 * this prevents a background request from competing with the
                 * next Seller navigation on local/single-worker servers.
                 */
                if (
                    !force &&
                    Date.now() - Number(state.layoutStateLoadedAt || 0) < 60000
                ) {
                    return;
                }

                state.layoutStateAbort?.abort();

                const requestSeq =
                    ++state.layoutStateRequestSeq;

                const mutationSeqAtStart =
                    state.notificationMutationSeq;

                const controller = new AbortController();
                state.layoutStateAbort = controller;

                try {
                    const response = await fetch(layoutStateUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store',
                        signal: controller.signal,
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();

                    if (
                        requestSeq !== state.layoutStateRequestSeq
                        || mutationSeqAtStart !==
                            state.notificationMutationSeq
                    ) {
                        return;
                    }

                    state.layoutStateLoadedAt = Date.now();

                    state.messageUnreadCount =
                        Math.max(
                            0,
                            Number(data?.message_unread_count || 0)
                        );

                    /*
                     * IMPORTANT:
                     * Bell count comes ONLY from SellerNotification state.
                     * Legacy `unread_count` historically represented unread
                     * Admin chat messages and must never drive the bell.
                     */
                    if (
                        Object.prototype.hasOwnProperty.call(
                            data || {},
                            'notification_unread_count'
                        )
                    ) {
                        state.notificationUnreadCount =
                            Math.max(
                                0,
                                Number(
                                    data.notification_unread_count
                                    || 0
                                )
                            );
                    }

                    const messages =
                        Array.isArray(data?.recent_messages)
                            ? data.recent_messages
                            : [];

                    messages.forEach(function (message) {
                        state.processedMessageIds.add(
                            String(message.id)
                        );
                    });

                    const hasNotificationFeed =
                        Array.isArray(
                            data?.recent_notifications
                        );

                    const notifications =
                        hasNotificationFeed
                            ? data.recent_notifications
                            : [];

                    notifications.forEach(function (notification) {
                        state.processedNotificationIds.add(
                            String(notification.id)
                        );
                    });

                    syncUnreadUi();

                    if (hasNotificationFeed) {
                        renderRecentNotifications(
                            notifications
                        );
                    }
                } catch (error) {
                    if (error?.name !== 'AbortError') {
                        console.debug(
                            'SARI layout notification state unavailable.'
                        );
                    }
                } finally {
                    if (state.layoutStateAbort === controller) {
                        state.layoutStateAbort = null;
                    }
                }
            }

            function handleChatMessage(data) {
                if (
                    !data
                    || data.id === undefined
                    || data.id === null
                ) {
                    return;
                }

                const messageId = String(data.id);

                if (state.processedMessageIds.has(messageId)) {
                    return;
                }

                state.processedMessageIds.add(messageId);

                window.dispatchEvent(
                    new CustomEvent(
                        'sari:seller-chat-message',
                        { detail: data }
                    )
                );

                if (
                    data.sender_role === 'admin'
                    && currentPath() !== messagesPath
                ) {
                    showMessageToast(data);
                }
            }

            function subscribeMessages(attempt = 0) {
                if (!sellerChannel || state.messageSubscribed) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeMessages(attempt + 1);
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen('.chat.message', handleChatMessage);

                state.messageSubscribed = true;
            }

            function subscribeUnifiedNotifications(attempt = 0) {
                if (
                    !sellerChannel
                    || state.notificationSubscribed
                ) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeUnifiedNotifications(
                                attempt + 1
                            );
                        }, 250);
                    }

                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.notification.created',
                        handleUnifiedNotification
                    );

                state.notificationSubscribed = true;
            }

            function subscribeCompliance(attempt = 0) {
                if (
                    !sellerChannel ||
                    state.complianceSubscribed
                ) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeCompliance(attempt + 1);
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.compliance.alert',
                        function (event) {
                            window.dispatchEvent(
                                new CustomEvent(
                                    'sari:seller-compliance-alert',
                                    { detail: event }
                                )
                            );

                            const warningNumber =
                                Number(
                                    event?.warning_number || 0
                                );

                            const suspended =
                                Boolean(
                                    event?.suspended_until
                                );

                            if (
                                warningNumber >= 3 ||
                                suspended
                            ) {
                                if (window.Livewire?.navigate) {
                                    window.Livewire.navigate(
                                        dashboardUrl
                                    );
                                } else {
                                    window.location.href =
                                        dashboardUrl;
                                }
                            }
                        }
                    );

                state.complianceSubscribed = true;
            }

            function handleRealtimeOrderUpdate(event) {
                if (!event) return;

                state.lastOrderRealtimeAt = Date.now();
                state.lastOrderRealtimeRevision =
                    event.revision || event.updated_at || null;

                window.dispatchEvent(
                    new CustomEvent(
                        'sari:seller-order-update',
                        { detail: event }
                    )
                );

                showOrderToast({
                    title: event.status === 'new'
                        ? 'New order received'
                        : 'Order updated',
                    message: [
                        event.order_number || 'Order',
                        event.status_label || event.status || 'Updated'
                    ].filter(Boolean).join(' · ')
                });
            }

            function subscribeOrders(attempt = 0) {
                if (!sellerChannel || state.orderSubscribed) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeOrders(attempt + 1);
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.order.updated',
                        handleRealtimeOrderUpdate
                    );

                state.orderSubscribed = true;
            }

            function subscribeAccountStatus(attempt = 0) {
                if (
                    !sellerChannel ||
                    state.accountSubscribed
                ) {
                    return;
                }

                if (!window.Echo) {
                    if (attempt < 24) {
                        window.setTimeout(function () {
                            subscribeAccountStatus(
                                attempt + 1
                            );
                        }, 250);
                    }
                    return;
                }

                window.Echo
                    .channel(sellerChannel)
                    .listen(
                        '.seller.account-status',
                        function (event) {
                            const action =
                                String(event?.action || '');

                            if (
                                action === 'banned' ||
                                action === 'deactivated'
                            ) {
                                window.location.replace(
                                    loginUrl
                                );
                                return;
                            }

                            if (
                                action === 'suspended' ||
                                action === 'suspension_lifted' ||
                                action === 'unbanned' ||
                                action === 'restored'
                            ) {
                                const sidebarStatus =
                                    document.getElementById(
                                        'sellerSidebarStoreStatus'
                                    );

                                if (sidebarStatus) {
                                    sidebarStatus.textContent =
                                        action === 'suspended'
                                            ? 'Suspended Store'
                                            : 'Verified Store';
                                }

                                if (window.Livewire?.navigate) {
                                    window.Livewire.navigate(
                                        dashboardUrl
                                    );
                                } else {
                                    window.location.href =
                                        dashboardUrl;
                                }
                            }
                        }
                    );

                state.accountSubscribed = true;
            }

            async function checkAccountState() {
                if (
                    state.statusBusy ||
                    state.navigationActive ||
                    document.hidden ||
                    document.getElementById(
                        'sellerCriticalRestriction'
                    )
                ) {
                    return;
                }

                state.statusBusy = true;
                state.accountStateAbort?.abort();
                const controller = new AbortController();
                state.accountStateAbort = controller;

                try {
                    const response =
                        await fetch(accountStateUrl, {
                            method: 'GET',
                            headers: {
                                'Accept':
                                    'application/json',
                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                            signal: controller.signal,
                        });

                    if (
                        response.status === 401 ||
                        response.status === 403
                    ) {
                        window.location.replace(loginUrl);
                        return;
                    }

                    if (!response.ok) {
                        return;
                    }

                    const account = await response.json();

                    if (
                        account.account_status === 'banned' ||
                        account.account_status === 'deactivated'
                    ) {
                        window.location.replace(loginUrl);
                        return;
                    }

                    if (account.restricted === true) {
                        if (window.Livewire?.navigate) {
                            window.Livewire.navigate(
                                dashboardUrl
                            );
                        } else {
                            window.location.href =
                                dashboardUrl;
                        }
                    }
                } catch (error) {
                    if (error?.name !== 'AbortError') {
                        console.debug(
                            'SARI seller status fallback unavailable.'
                        );
                    }
                } finally {
                    if (state.accountStateAbort === controller) {
                        state.accountStateAbort = null;
                    }
                    state.statusBusy = false;
                }
            }

            function showOrderToast(eventData) {
                const toast =
                    document.getElementById(
                        'sellerOrderStatusToast'
                    );
                const title =
                    document.getElementById(
                        'sellerOrderToastTitle'
                    );
                const message =
                    document.getElementById(
                        'sellerOrderToastMessage'
                    );

                if (
                    !toast ||
                    !title ||
                    !message ||
                    !eventData
                ) {
                    return;
                }

                title.textContent =
                    eventData.title || 'Order Update';

                message.textContent =
                    eventData.message ||
                    'Your order status changed.';

                toast.classList.remove('hidden');

                requestAnimationFrame(function () {
                    toast.classList.remove(
                        'translate-y-2',
                        'opacity-0'
                    );
                    toast.classList.add(
                        'translate-y-0',
                        'opacity-100'
                    );
                });

                window.clearTimeout(
                    state.orderToastTimer
                );

                state.orderToastTimer =
                    window.setTimeout(function () {
                        toast.classList.remove(
                            'translate-y-0',
                            'opacity-100'
                        );
                        toast.classList.add(
                            'translate-y-2',
                            'opacity-0'
                        );

                        window.setTimeout(function () {
                            toast.classList.add('hidden');
                        }, 300);
                    }, 5500);
            }

            async function pollOrders() {
                const path = currentPath();

                if (
                    !sellerId ||
                    state.orderBusy ||
                    state.navigationActive ||
                    document.hidden ||
                    (
                        path !== dashboardPath &&
                        path !== ordersPath
                    )
                ) {
                    return;
                }

                state.orderBusy = true;
                state.orderPollAbort?.abort();
                const controller = new AbortController();
                state.orderPollAbort = controller;

                try {
                    const response =
                        await fetch(
                            @json(route('seller.orders.live-state')),
                            {
                                headers: {
                                    'Accept':
                                        'application/json',
                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                },
                                credentials: 'same-origin',
                                cache: 'no-store',
                                signal: controller.signal,
                            }
                        );

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    const eventData = data.latest_event;

                    if (
                        state.lastOrderRealtimeRevision &&
                        data?.revision &&
                        String(data.revision) ===
                            String(state.lastOrderRealtimeRevision)
                    ) {
                        return;
                    }

                    if (!eventData?.id) {
                        return;
                    }

                    const storageKey =
                        'sari:seller-order-event:' +
                        sellerId;

                    const seen =
                        window.localStorage.getItem(
                            storageKey
                        );

                    if (!seen) {
                        window.localStorage.setItem(
                            storageKey,
                            String(eventData.id)
                        );
                        return;
                    }

                    if (
                        Number(eventData.id) >
                        Number(seen)
                    ) {
                        window.localStorage.setItem(
                            storageKey,
                            String(eventData.id)
                        );

                        window.dispatchEvent(
                            new CustomEvent(
                                'sari:seller-order-update',
                                { detail: eventData }
                            )
                        );

                        showOrderToast(eventData);
                    }
                } catch (error) {
                    if (error?.name !== 'AbortError') {
                        console.debug(
                            'SARI order polling temporarily unavailable.'
                        );
                    }
                } finally {
                    if (state.orderPollAbort === controller) {
                        state.orderPollAbort = null;
                    }
                    state.orderBusy = false;
                }
            }

            function syncRestrictionUi() {
                const locked =
                    Boolean(
                        document.getElementById(
                            'sellerCriticalRestriction'
                        )
                    );

                if (locked) {
                    document.body.classList.add(
                        'overflow-hidden'
                    );
                }

                if (
                    !window.__SARI_SELLER_LOCK_POPSTATE_BOUND__
                ) {
                    window.__SARI_SELLER_LOCK_POPSTATE_BOUND__ =
                        true;

                    window.addEventListener(
                        'popstate',
                        function () {
                            if (
                                document.getElementById(
                                    'sellerCriticalRestriction'
                                )
                            ) {
                                window.history.pushState(
                                    {
                                        sariSellerLocked:
                                            true
                                    },
                                    '',
                                    window.location.href
                                );
                            }
                        }
                    );
                }

                if (locked) {
                    window.history.replaceState(
                        {
                            ...(window.history.state || {}),
                            sariSellerLocked: true
                        },
                        '',
                        window.location.href
                    );
                }
            }


            /*
             * ============================================================
             * FAST SELLER NAVIGATION — USER INTENT ONLY
             * ============================================================
             * Do not background-prefetch every Seller route.
             *
             * Every sidebar link already uses wire:navigate.hover, so Livewire
             * prefetches the one destination the seller is actually hovering.
             * This avoids keeping Laravel/PHP busy with synthetic requests while
             * a real navigation is waiting.
             */
            function bindSellerNavigationPriority() {
                document
                    .querySelectorAll(
                        '#sellerSidebarNav a[wire\\:navigate\\.hover], ' +
                        '#sellerSidebarProfile a[wire\\:navigate\\.hover]'
                    )
                    .forEach(function (link) {
                        if (link.dataset.sariPriorityBound === '1') {
                            return;
                        }

                        link.dataset.sariPriorityBound = '1';

                        /*
                         * Stop our own polling/layout requests as soon as the
                         * seller commits to a destination. The Livewire request
                         * for the clicked page gets the cleanest possible lane.
                         */
                        link.addEventListener(
                            'pointerdown',
                            abortSellerBackgroundRequests,
                            { passive: true }
                        );

                        link.addEventListener(
                            'touchstart',
                            abortSellerBackgroundRequests,
                            { passive: true }
                        );
                    });
            }

            document.addEventListener(
                'livewire:navigate',
                function (event) {
                    state.navigationActive = true;
                    abortSellerBackgroundRequests();

                    /* Optimistic sidebar feedback before the network finishes. */
                    try {
                        const targetPath =
                            event.detail?.url?.pathname?.replace(/\/+$/, '') || '/';

                        if (targetPath) {
                            syncSidebarActiveState(targetPath);
                        }
                    } catch (_) {}
                }
            );

            document.addEventListener(
                'livewire:navigating',
                function () {
                    abortSellerBackgroundRequests();
                    closeMobileSidebar();

                    document
                        .getElementById(
                            'sellerNotificationDropdown'
                        )
                        ?.classList.add('hidden');
                }
            );

            document.addEventListener(
                'livewire:navigated',
                function () {
                    state.navigationActive = false;

                    bindCurrentShellUi();
                    bindSellerNavigationPriority();
                    syncRestrictionUi();
                    syncUnreadUi();
                    syncSellerShellProfileAvatar();

                    if (
                        [
                            messagesPath,
                            buyerMessagesPath,
                            notificationPath,
                        ].includes(currentPath())
                    ) {
                        window.setTimeout(
                            function () {
                                loadLayoutState(true);
                            },
                            80
                        );
                    } else if ('requestIdleCallback' in window) {
                        window.requestIdleCallback(
                            function () {
                                loadLayoutState();
                            },
                            { timeout: 1800 }
                        );
                    } else {
                        window.setTimeout(
                            loadLayoutState,
                            900
                        );
                    }

                    subscribeMessages();
                    subscribeUnifiedNotifications();
                    subscribeCompliance();
                    subscribeOrders();
                    subscribeAccountStatus();
                }
            );

            if (
                !window.__SARI_SELLER_STATUS_INTERVAL__
            ) {
                window.__SARI_SELLER_STATUS_INTERVAL__ =
                    window.setInterval(
                        checkAccountState,
                        60000
                    );

                window.setTimeout(
                    checkAccountState,
                    12000
                );
            }

            if (
                !window.__SARI_SELLER_ORDER_INTERVAL__
            ) {
                window.__SARI_SELLER_ORDER_INTERVAL__ =
                    window.setInterval(
                        pollOrders,
                        45000
                    );

                window.setTimeout(
                    pollOrders,
                    10000
                );
            }
        })();
    </script>



    {{-- =========================================================
        PERSISTENT SELLER ACTION TOAST
        Stays mounted while Livewire refreshes only page content.
    ========================================================== --}}
    @persist('seller-action-toast')
        <div
            id="sellerActionToast"
            class="seller-action-toast hidden"
            data-toast-type="success"
            aria-live="polite"
            aria-atomic="true"
        >
            <div id="sellerActionToastCard" class="seller-action-toast-card">
                <span id="sellerActionToastIcon" class="seller-action-toast-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m8 12 2.5 2.5L16 9"></path>
                    </svg>
                </span>

                <div class="seller-action-toast-copy">
                    <p id="sellerActionToastTitle" class="seller-action-toast-title">
                        Action completed
                    </p>
                    <p id="sellerActionToastMessage" class="seller-action-toast-message"></p>
                </div>

                <button
                    id="sellerActionToastClose"
                    type="button"
                    class="seller-action-toast-close"
                    aria-label="Close notification"
                >
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="m7 7 10 10"></path>
                        <path d="m17 7-10 10"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endpersist


    {{-- =========================================================
        FAST SELLER PRODUCT ACTION BRIDGE
        Product writes use fetch() so the persistent Seller shell/logo
        never tears down while Add/Edit/Archive/Delete/Restore runs.
    ========================================================== --}}
    <script>
        (function () {
            if (window.__SARI_SELLER_FAST_PRODUCT_ACTIONS__) {
                return;
            }

            window.__SARI_SELLER_FAST_PRODUCT_ACTIONS__ = true;

            let toastTimer = null;

            function showToast(message, type = 'success') {
                const toast = document.getElementById('sellerActionToast');
                const icon = document.getElementById('sellerActionToastIcon');
                const title = document.getElementById('sellerActionToastTitle');
                const body = document.getElementById('sellerActionToastMessage');

                if (!toast || !icon || !title || !body) {
                    return;
                }

                const warning = type === 'warning';
                const error = type === 'error';

                toast.dataset.toastType =
                    error ? 'error' : (warning ? 'warning' : 'success');

                if (error) {
                    title.textContent = 'Action not completed';
                    icon.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5"></path><path d="M12 16.5h.01"></path></svg>';
                } else if (warning) {
                    title.textContent = 'Review notice';
                    icon.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3 3 20h18L12 3Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path></svg>';
                } else {
                    title.textContent = 'Action completed';
                    icon.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>';
                }

                body.textContent =
                    message || 'Your seller action was saved.';

                toast.classList.remove('hidden');

                if (toastTimer) {
                    window.clearTimeout(toastTimer);
                }

                toastTimer = window.setTimeout(function () {
                    toast.classList.add('hidden');
                }, error ? 6500 : (warning ? 5600 : 4400));
            }

            function bindToastClose() {
                const close = document.getElementById('sellerActionToastClose');

                if (!close || close.dataset.sariBound === '1') {
                    return;
                }

                close.dataset.sariBound = '1';
                close.addEventListener('click', function () {
                    document.getElementById('sellerActionToast')?.classList.add('hidden');
                });
            }

            function isFastProductAction(form) {
                if (!(form instanceof HTMLFormElement)) return false;
                if (form.hasAttribute('data-no-seller-fast-action')) return false;

                const method = String(form.getAttribute('method') || 'get').toLowerCase();
                if (method !== 'post') return false;

                const action = new URL(form.action || window.location.href, window.location.origin);
                if (action.origin !== window.location.origin) return false;

                const path = action.pathname.replace(/\/+$/, '') || '/';

                if (path === '/seller/products') {
                    return true;
                }

                return /^\/seller\/products\/\d+(?:\/(?:archive|delete|restore))?$/.test(path);
            }

            function firstError(payload) {
                if (payload?.errors && typeof payload.errors === 'object') {
                    for (const value of Object.values(payload.errors)) {
                        if (Array.isArray(value) && value.length) {
                            return String(value[0]);
                        }

                        if (typeof value === 'string' && value.trim()) {
                            return value;
                        }
                    }
                }

                return String(payload?.message || 'The action could not be completed.');
            }

            function busyTextFor(form) {
                if (form.id === 'sellerAddProductForm' || form.id === 'sellerCreateProductForm') return 'Submitting...';
                if (form.id === 'sellerEditProductForm') return 'Saving changes...';
                if (form.id === 'productActionForm') return 'Processing...';
                return 'Saving...';
            }

            function setButtonBusy(button, form, busy) {
                if (!button) return;

                if (busy) {
                    if (!button.dataset.sariOriginalHtml) {
                        button.dataset.sariOriginalHtml = button.innerHTML;
                    }

                    button.disabled = true;
                    button.setAttribute('aria-busy', 'true');
                    button.classList.add('cursor-wait', 'opacity-70');
                    button.innerHTML = '<span class="inline-flex items-center gap-2"><span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-r-transparent"></span>' + busyTextFor(form) + '</span>';
                    return;
                }

                button.disabled = false;
                button.removeAttribute('aria-busy');
                button.classList.remove('cursor-wait', 'opacity-70');

                if (button.dataset.sariOriginalHtml) {
                    button.innerHTML = button.dataset.sariOriginalHtml;
                    delete button.dataset.sariOriginalHtml;
                }
            }

            function closeProductModals() {
                [
                    'sellerAddProductModal',
                    'sellerEditProductModal',
                    'sellerProductActionModal',
                    'sellerAllProductsModal'
                ].forEach(function (id) {
                    const modal = document.getElementById(id);
                    if (!modal) return;

                    modal.classList.add('hidden');
                    modal.classList.remove('flex', 'seller-modal-visible');
                });

                document.body.classList.remove('overflow-hidden');
            }

            function reviewSubmitFeedback(form) {
                if (!form || form.id !== 'sellerCreateProductForm') {
                    return null;
                }

                return window.__SARI_PRODUCT_REVIEW_FEEDBACK__ || null;
            }

            async function submitFast(form, submitter) {
                if (form.dataset.sariSubmitting === '1') {
                    return;
                }

                form.dataset.sariSubmitting = '1';

                const button = submitter || form.querySelector('button[type="submit"], input[type="submit"]');
                setButtonBusy(button, form, true);

                const reviewFeedback = reviewSubmitFeedback(form);
                reviewFeedback?.start?.();

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        credentials: 'same-origin',
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.redirected) {
                        const redirected = new URL(response.url, window.location.origin);

                        if (redirected.origin === window.location.origin && redirected.pathname.startsWith('/seller')) {
                            reviewFeedback?.hide?.();
                            showToast('Your seller access or page state changed. Refreshing securely.', 'warning');

                            window.setTimeout(function () {
                                if (window.Livewire?.navigate) {
                                    window.Livewire.navigate(redirected.toString());
                                } else {
                                    window.location.assign(redirected.toString());
                                }
                            }, 120);

                            return;
                        }

                        window.location.assign(response.url);
                        return;
                    }

                    const contentType = response.headers.get('content-type') || '';
                    let payload = {};

                    if (contentType.includes('application/json')) {
                        payload = await response.json();
                    }

                    if (!response.ok) {
                        reviewFeedback?.hide?.();
                        showToast(firstError(payload), 'error');
                        return;
                    }

                    if (reviewFeedback?.success) {
                        await reviewFeedback.success();
                    }

                    showToast(
                        payload.message || 'Your seller action was saved.',
                        payload.type || 'success'
                    );

                    closeProductModals();

                    window.setTimeout(function () {
                        if (window.Livewire?.navigate) {
                            window.Livewire.navigate(window.location.href);
                        } else {
                            window.location.reload();
                        }
                    }, reviewFeedback ? 700 : 160);
                } catch (error) {
                    reviewFeedback?.hide?.();
                    showToast('Network request failed. Please try again.', 'error');
                } finally {
                    form.dataset.sariSubmitting = '0';
                    setButtonBusy(button, form, false);
                }
            }

            document.addEventListener('submit', function (event) {
                const form = event.target;

                /*
                 * Dashboard validation handlers run on the form itself before
                 * this document-level bubble listener. Respect them first.
                 */
                if (event.defaultPrevented || !isFastProductAction(form)) {
                    return;
                }

                event.preventDefault();
                submitFast(form, event.submitter || null);
            });

            bindToastClose();

            document.addEventListener('livewire:navigated', function () {
                bindToastClose();
            });
        })();
    </script>





    {{-- Neutral-only navigation guard: keeps the persistent Seller canvas stable. --}}
    <script data-navigate-once>
        (function () {
            const SELLER_CANVAS = '#F4F5F7';

            function lockSellerCanvas() {
                document.documentElement.style.backgroundColor = SELLER_CANVAS;

                if (document.body) {
                    document.body.style.backgroundColor = SELLER_CANVAS;
                    document.body.style.backgroundImage = 'none';
                }

                const content = document.getElementById('sellerContent');
                if (content) {
                    content.style.backgroundColor = SELLER_CANVAS;
                    content.style.backgroundImage = 'none';
                }

                document.getElementById('sariSellerInstantSnapshot')?.remove();
            }

            lockSellerCanvas();
            document.addEventListener('livewire:navigate', lockSellerCanvas);
            document.addEventListener('livewire:navigating', lockSellerCanvas);
            document.addEventListener('livewire:navigated', function () {
                lockSellerCanvas();
                window.requestAnimationFrame(lockSellerCanvas);
            });
            window.addEventListener('pageshow', lockSellerCanvas);
        })();
    </script>


    {{-- =========================================================
        SELLER LOGOUT CONFIRMATION CONTROLLER
    ========================================================== --}}
    <script data-navigate-once>
        (function () {
            if (window.__SARI_SELLER_LOGOUT_MODAL_BOUND__) {
                return;
            }

            window.__SARI_SELLER_LOGOUT_MODAL_BOUND__ = true;

            let pendingForm = null;
            let returnFocus = null;
            let previousBodyOverflow = '';

            function modal() {
                return document.getElementById(
                    'sellerLogoutConfirmModal'
                );
            }

            function cancelButton() {
                return modal()?.querySelector(
                    '[data-seller-logout-cancel]'
                );
            }

            function confirmButton() {
                return document.getElementById(
                    'sellerLogoutConfirmButton'
                );
            }

            function openLogoutModal(form, trigger) {
                const element = modal();

                if (!element) {
                    HTMLFormElement.prototype.submit.call(form);
                    return;
                }

                pendingForm = form;
                returnFocus =
                    trigger instanceof HTMLElement
                        ? trigger
                        : document.activeElement;

                previousBodyOverflow =
                    document.body.style.overflow || '';

                document.body.style.overflow = 'hidden';

                element.hidden = false;
                element.setAttribute('aria-hidden', 'false');

                requestAnimationFrame(function () {
                    element.classList.add('is-open');

                    window.setTimeout(function () {
                        cancelButton()?.focus({
                            preventScroll: true
                        });
                    }, 20);
                });
            }

            function closeLogoutModal(restoreFocus = true) {
                const element = modal();

                if (!element || element.hidden) {
                    return;
                }

                element.classList.remove('is-open');
                element.setAttribute('aria-hidden', 'true');

                document.body.style.overflow =
                    previousBodyOverflow;

                window.setTimeout(function () {
                    element.hidden = true;

                    const confirm = confirmButton();
                    const label = confirm?.querySelector(
                        '[data-seller-logout-confirm-label]'
                    );

                    if (confirm) {
                        confirm.disabled = false;
                    }

                    if (label) {
                        label.textContent = 'Logout';
                    }

                    if (
                        restoreFocus
                        && returnFocus instanceof HTMLElement
                    ) {
                        returnFocus.focus({
                            preventScroll: true
                        });
                    }

                    if (restoreFocus) {
                        pendingForm = null;
                        returnFocus = null;
                    }
                }, 150);
            }

            document.addEventListener(
                'submit',
                function (event) {
                    const form = event.target;

                    if (
                        !(form instanceof HTMLFormElement)
                        || !form.matches(
                            '[data-seller-logout-form]'
                        )
                    ) {
                        return;
                    }

                    if (
                        form.dataset.sellerLogoutConfirmed === '1'
                    ) {
                        return;
                    }

                    event.preventDefault();
                    event.stopImmediatePropagation();

                    openLogoutModal(
                        form,
                        event.submitter || document.activeElement
                    );
                },
                true
            );

            document.addEventListener(
                'click',
                function (event) {
                    const cancel = event.target.closest(
                        '[data-seller-logout-cancel]'
                    );

                    if (cancel) {
                        event.preventDefault();
                        closeLogoutModal(true);
                        return;
                    }

                    const confirm = event.target.closest(
                        '[data-seller-logout-confirm]'
                    );

                    if (!confirm) {
                        return;
                    }

                    event.preventDefault();

                    if (
                        !(pendingForm instanceof HTMLFormElement)
                    ) {
                        closeLogoutModal(true);
                        return;
                    }

                    confirm.disabled = true;

                    const label = confirm.querySelector(
                        '[data-seller-logout-confirm-label]'
                    );

                    if (label) {
                        label.textContent = 'Logging out…';
                    }

                    pendingForm.dataset.sellerLogoutConfirmed = '1';

                    /*
                     * Native submit bypasses this confirmation listener
                     * while preserving the existing POST action + CSRF.
                     */
                    HTMLFormElement.prototype.submit.call(
                        pendingForm
                    );
                }
            );

            document.addEventListener(
                'keydown',
                function (event) {
                    const element = modal();

                    if (!element || element.hidden) {
                        return;
                    }

                    if (event.key === 'Escape') {
                        event.preventDefault();
                        closeLogoutModal(true);
                        return;
                    }

                    if (event.key !== 'Tab') {
                        return;
                    }

                    const focusable = Array.from(
                        element.querySelectorAll(
                            'button:not([disabled]), [href], input:not([disabled])'
                        )
                    ).filter(function (node) {
                        return !node.hasAttribute('hidden');
                    });

                    if (!focusable.length) {
                        return;
                    }

                    const first = focusable[0];
                    const last =
                        focusable[focusable.length - 1];

                    if (
                        event.shiftKey
                        && document.activeElement === first
                    ) {
                        event.preventDefault();
                        last.focus();
                    } else if (
                        !event.shiftKey
                        && document.activeElement === last
                    ) {
                        event.preventDefault();
                        first.focus();
                    }
                }
            );

            document.addEventListener(
                'livewire:navigating',
                function () {
                    const element = modal();

                    if (element && !element.hidden) {
                        element.classList.remove('is-open');
                        element.hidden = true;
                        element.setAttribute(
                            'aria-hidden',
                            'true'
                        );

                        document.body.style.overflow =
                            previousBodyOverflow;
                    }

                    pendingForm = null;
                    returnFocus = null;
                }
            );
        })();
    </script>

    {{-- PAGE-SPECIFIC SCRIPTS --}}
    @stack('scripts')

    {{-- Start Livewire after all navigation listeners are registered. --}}
    @livewireScripts
</body>
</html>
