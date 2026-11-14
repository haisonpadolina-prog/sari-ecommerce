<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Rider Dashboard') | SARI
    </title>

    {{-- SARI Rider notification UI V5: Buyer-style toast + original Rider chimes --}}
    <link rel="stylesheet" href="{{ asset('css/courier/sari-toast.css') }}?v=20261101-rider-sound-v5">
    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: [
                            'Poppins',
                            'sans-serif',
                        ],
                    },

                    colors: {
                        sari: {
                            gold: '#D9930A',
                            cream: '#FFFDF8',
                            ink: '#17140E',
                            muted: '#8D8272',
                            border: '#EEE4D3',
                            blue: '#3C6E91',
                            green: '#4F7D63',
                        },
                    },
                },
            },
        };
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #fbfaf7;
            color: #17140e;
        }

        #courierSidebar,
        #courierMain {
            transition:
                width .3s ease,
                padding-left .3s ease,
                transform .3s ease;
        }

        .courier-sidebar-label,
        .courier-sidebar-extra,
        .courier-sidebar-section {
            transition: all .2s ease;
        }

        body.courier-sidebar-collapsed #courierSidebar {
            width: 76px;
        }

        body.courier-sidebar-collapsed #courierMain {
            padding-left: 76px;
        }

        body.courier-sidebar-collapsed .courier-sidebar-label,
        body.courier-sidebar-collapsed .courier-sidebar-extra,
        body.courier-sidebar-collapsed .courier-sidebar-section {
            display: none;
        }

        body.courier-sidebar-collapsed [data-courier-sidebar-item] {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        body.courier-sidebar-collapsed #courierSidebarLogo img {
            width: 52px;
        }


        .reveal {
            opacity: 0;
            transform: translateY(12px);

            transition:
                opacity .5s cubic-bezier(.22, 1, .36, 1),
                transform .5s cubic-bezier(.22, 1, .36, 1);
        }

        .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes bellRing {
            0%,
            100% {
                transform: rotate(0);
            }

            20% {
                transform: rotate(13deg);
            }

            40% {
                transform: rotate(-11deg);
            }

            60% {
                transform: rotate(7deg);
            }

            80% {
                transform: rotate(-4deg);
            }
        }

        .bell-ring {
            animation: bellRing .7s ease;
            transform-origin: top center;
        }

        @media (max-width: 1023px) {
            body.courier-sidebar-collapsed #courierMain {
                padding-left: 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>

    @stack('styles')

    {{-- Buyer sidebar matched across all Courier pages, including legacy Dashboard CSS.
         Keep AFTER @stack('styles') to win view-level sidebar overrides. --}}
    <style id="sariCourierBuyerSidebarDesignV3">
        :root {
            --courier-buyer-sidebar-expanded: 238px;
            --courier-buyer-sidebar-mini: 76px;
            --courier-buyer-sidebar-gold: #d9930a;
            --courier-buyer-sidebar-border: #ece5da;
        }
        body:has(#courierSidebar) #courierSidebar {
            width: var(--courier-buyer-sidebar-expanded) !important;
            overflow-x: hidden;
            border-right: 1px solid var(--courier-buyer-sidebar-border) !important;
            background: #fff !important;
            background-image: none !important;
            box-shadow: none !important;
            transition: width 220ms cubic-bezier(.22,1,.36,1), transform 200ms ease-out !important;
        }
        #courierSidebar > div:first-child {
            min-height: 74px !important;
            height: 74px !important;
            flex: 0 0 74px !important;
            padding-inline: 12px !important;
            border-bottom: 1px solid #eee7dc !important;
            background: #fff !important;
        }
        #courierSidebarLogo {width: 100% !important; height:44px !important;}
        #courierSidebarLogoStage {width:100%;height:44px;}
        #courierSidebarLogoFull,
        #courierSidebarLogoCompact {
            display:block;
            transition: opacity 140ms ease, visibility 140ms ease !important;
        }
        #courierSidebarLogoFull {width:102px !important;height:auto !important;opacity:1 !important;visibility:visible !important;}
        #courierSidebarLogoCompact {width:38px !important;height:38px !important;opacity:0 !important;visibility:hidden !important;}
        #courierSidebarClose {height:32px;width:32px;right:12px;border-radius:10px;}
        #courierSidebarClose svg {width:17px;height:17px;}
        #courierSidebarNav {
            padding:14px 10px 16px !important;
            scrollbar-width:thin;
            scrollbar-color:#ddd4c7 transparent;
        }
        #courierSidebarNav::-webkit-scrollbar {width:4px;}
        #courierSidebarNav::-webkit-scrollbar-track {background:transparent;}
        #courierSidebarNav::-webkit-scrollbar-thumb {background:#ddd4c7;border-radius:999px;}
        #courierSidebarNav .courier-sidebar-section {
            margin:13px 9px 8px !important;
            padding:0 !important;
            font-size:9.5px !important;
            line-height:1.3 !important;
            font-weight:750 !important;
            color:#94713c !important;
            letter-spacing:.12em !important;
            text-transform:uppercase !important;
            white-space:nowrap !important;
            user-select:none;
        }
        #courierSidebarNav .courier-sidebar-section:first-child {margin-top:4px !important;}
        #courierSidebarNav > div.my-4.border-t {
            height:1px;
            margin:11px 6px 9px !important;
            border:0 !important;
            background:#eee7dd;
        }
        #courierSidebarNav .courier-sidebar-link-group > :not([hidden]) ~ :not([hidden]) {margin-top:2px !important;}
        #courierSidebarNav [data-courier-sidebar-item] {
            display:flex;
            width:100% !important;
            min-height:43px !important;
            align-items:center;
            gap:11px !important;
            border:1px solid transparent !important;
            border-radius:10px !important;
            padding:9px 11px !important;
            font-family:'Poppins',system-ui,sans-serif !important;
            font-size:12.5px !important;
            line-height:1.3 !important;
            font-weight:600 !important;
            letter-spacing:-.008em !important;
            color:#514b42 !important;
            background:transparent !important;
            background-image:none !important;
            box-shadow:none !important;
            transform:none !important;
            transition:background-color .12s ease,border-color .12s ease,color .12s ease !important;
        }
        #courierSidebarNav [data-courier-sidebar-item] svg {
            display:block;
            width:18px !important;
            height:18px !important;
            flex:0 0 18px !important;
            stroke-width:1.8 !important;
        }
        #courierSidebarNav [data-courier-sidebar-item] > span:first-child:not(.courier-sidebar-label) {
            gap:11px !important;
            min-width:0;
        }
        /* Important: some existing Rider views have high-specificity sidebar overrides.
           These anchored selectors always keep the active item Buyer-gold/white. */
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="true"],
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="true"]:hover,
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="true"]:focus-visible {
            border:1px solid #d9930a !important;
            background:#d9930a !important;
            background-color:#d9930a !important;
            background-image:none !important;
            color:#fff !important;
            box-shadow:0 5px 12px rgba(194,132,10,.12) !important;
            font-weight:600 !important;
            opacity:1 !important;
        }
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="true"] svg,
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="true"] .courier-sidebar-label,
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="true"] .courier-sidebar-label * {
            color:#fff !important;
            stroke:currentColor !important;
            opacity:1 !important;
        }
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="false"] {
            color:#514b42 !important;
            background:transparent !important;
            border-color:transparent !important;
            box-shadow:none !important;
        }
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="false"]:hover {
            border-color:#eadfcf !important;
            background:#fff9ef !important;
            color:#9a680b !important;
        }
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="false"] svg,
        body:has(#courierSidebar) #courierSidebar #courierSidebarNav a[data-courier-sidebar-item][data-courier-nav-active="false"] .courier-sidebar-label {
            color:inherit !important;
            stroke:currentColor !important;
        }
        #courierSidebarNav .courier-sidebar-extra {
            min-width:18px !important;
            height:18px !important;
            padding-inline:5px !important;
            font-size:8px !important;
        }
        #courierSidebarNav a[data-courier-nav-active="true"] .courier-sidebar-extra {
            color:#fff !important;
            background:rgba(255,255,255,.20) !important;
        }
        #courierSidebarNav a[data-courier-nav-active="false"] .courier-sidebar-extra {
            background:#f3e4c6 !important;
            color:#936713 !important;
        }
        #courierSidebarNav form button[data-courier-sidebar-item] {color:#ef4444 !important;}
        #courierSidebarNav form button[data-courier-sidebar-item] svg,
        #courierSidebarNav form button[data-courier-sidebar-item] .courier-sidebar-label {color:#ef4444 !important;stroke:currentColor !important;}
        #courierSidebarNav form button[data-courier-sidebar-item]:hover,
        #courierSidebarNav form button[data-courier-sidebar-item]:focus-visible {
            border-color:#fee2e2 !important;
            background:#fef2f2 !important;
            color:#dc2626 !important;
        }
        #courierSidebarNav form button[data-courier-sidebar-item]:hover svg,
        #courierSidebarNav form button[data-courier-sidebar-item]:hover .courier-sidebar-label {
            color:#dc2626 !important;
        }
        #courierSidebar :is(a,button):focus-visible {outline:2px solid #c88912;outline-offset:2px;}
        @media(min-width:1024px) {
            body:has(#courierSidebar):not(.courier-sidebar-collapsed) #courierMain {padding-left:238px !important;}
            body:has(#courierSidebar).courier-sidebar-collapsed #courierSidebar {width:76px !important;}
            body:has(#courierSidebar).courier-sidebar-collapsed #courierMain {padding-left:76px !important;}
            body.courier-sidebar-collapsed #courierSidebarLogoFull {opacity:0 !important;visibility:hidden !important;pointer-events:none !important;}
            body.courier-sidebar-collapsed #courierSidebarLogoCompact {opacity:1 !important;visibility:visible !important;pointer-events:auto !important;}
            body.courier-sidebar-collapsed #courierSidebarLogoStage {width:58px !important;}
            body.courier-sidebar-collapsed #courierSidebarNav {padding:12px 9px !important;}
            body.courier-sidebar-collapsed #courierSidebarNav .courier-sidebar-section,
            body.courier-sidebar-collapsed #courierSidebarNav > div.my-4.border-t {display:none !important;}
            body.courier-sidebar-collapsed #courierSidebarNav [data-courier-sidebar-item] {
                justify-content:center !important;gap:0 !important;padding-inline:0 !important;
            }
            body.courier-sidebar-collapsed #courierSidebarNav [data-courier-sidebar-item] > span:first-child:not(.courier-sidebar-label) {
                justify-content:center !important;gap:0 !important;
            }
            body.courier-sidebar-collapsed #courierSidebar .courier-sidebar-label,
            body.courier-sidebar-collapsed #courierSidebar .courier-sidebar-extra {display:none !important;}
        }
        @media(max-width:1023px) {
            body:has(#courierSidebar) #courierSidebar {width:238px !important;}
            body:has(#courierSidebar):not(.courier-sidebar-collapsed) #courierMain,
            body:has(#courierSidebar).courier-sidebar-collapsed #courierMain {padding-left:0 !important;}
            #courierSidebarLogoFull {opacity:1 !important;visibility:visible !important;}
            #courierSidebarLogoCompact {opacity:0 !important;visibility:hidden !important;}
            #courierSidebar .courier-sidebar-label {display:inline !important;}
            #courierSidebarNav .courier-sidebar-extra {display:grid !important;}
        }
        @media(max-height:820px) and (min-width:1024px) {
            #courierSidebarNav {padding-block:10px !important;}
            #courierSidebarNav .courier-sidebar-section {margin-top:8px !important;margin-bottom:6px !important;}
            #courierSidebarNav > div.my-4.border-t {margin-top:8px !important;margin-bottom:7px !important;}
            #courierSidebarNav [data-courier-sidebar-item] {min-height:40px !important;padding-block:7px !important;font-size:12px !important;}
        }
        @media(prefers-reduced-motion:reduce) {
            #courierSidebar, #courierMain, #courierSidebar *, #courierSidebarToggle, #courierMenuButton {transition-duration:1ms !important;}
        }
    </style>



    {{-- Actual Buyer header styling, scoped to Courier layout and applied after
         page-specific overrides. No global search or dummy notification badge. --}}
    <style id="sariCourierBuyerHeaderV4">
        body:has(#courierSidebar) #courierMain > #courierHeader {
            position:sticky !important;top:0 !important;z-index:40 !important;
            background:#fff !important;border-bottom:1px solid #e9edf2 !important;
            box-shadow:none !important; backdrop-filter:none !important;
        }
        #courierHeader .courier-header-main {min-height:82px !important;gap:16px;}
        #courierMain #courierHeader .courier-header-title {
            margin:0 !important;font-size:clamp(21px,1.85vw,27px) !important;
            font-weight:700 !important;line-height:1.22 !important;
            letter-spacing:-.035em !important;color:#17212f !important;
        }
        #courierHeader .courier-header-breadcrumb {display:flex;align-items:center;gap:8px;color:#758092;font-size:11.5px;line-height:1.4;}
        #courierHeader .courier-header-breadcrumb svg {width:13px;height:13px;color:#b5bdc8;flex:none;}
        #courierHeader .courier-header-control {display:grid;place-items:center;width:40px;height:40px;flex:none;border:1px solid #e5eaf0;border-radius:11px;background:#fff;color:#344054;box-shadow:none;cursor:pointer;}
        #courierHeader .courier-header-control:hover {background:#f4f6f9;border-color:#d4dbe4;}
        #courierHeader .courier-header-control svg {width:19px;height:19px;}
        #courierHeader #courierMenuButton {display:none;}
        #courierHeader #courierSidebarToggle {display:grid;}
        #courierHeader .courier-header-bell {position:relative;width:40px;height:40px;display:grid;place-items:center;border-radius:11px;border:1px solid transparent;background:#fff;color:#475467;cursor:pointer;}
        #courierHeader .courier-header-bell:hover,#courierHeader .courier-header-bell[aria-expanded="true"] {background:#f6f8fb;border-color:#e5eaf0;color:#c88912;}
        #courierHeader .courier-header-bell svg {width:20px;height:20px;}
        #courierHeader .courier-header-profile {display:flex;align-items:center;gap:11px;min-width:0;padding:6px 9px;border:1px solid transparent;border-radius:13px;transition:background-color .15s,border-color .15s;text-decoration:none;}
        #courierHeader .courier-header-profile:hover {border-color:#e5eaf0;background:#f6f8fb;}
        /* Saved Account Management photo fills the Rider header avatar. */
        #courierHeader .courier-header-avatar {position:relative;isolation:isolate;overflow:hidden;width:39px;height:39px;flex:none;display:grid;place-items:center;border-radius:50%;background:#d9950b;color:#fff;font-size:12px;font-weight:700;box-shadow:0 0 0 2px #fff,0 0 0 3px #f0e5cd;}
        #courierHeader .courier-header-avatar-photo {position:absolute;inset:0;z-index:1;display:block;width:100%;height:100%;border-radius:inherit;object-fit:cover;object-position:center;}
        #courierHeader .courier-header-avatar-initial {display:grid;place-items:center;width:100%;height:100%;line-height:1;}
        #courierHeader .courier-header-profile-name {display:block;max-width:165px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#202b3a;font-size:12px;font-weight:650;line-height:1.3;}
        #courierHeader .courier-header-profile-status {display:block;margin-top:3px;color:#667085;font-size:10px;line-height:1.4;}
        #courierHeader .courier-header-separator {height:32px;width:1px;background:#e5eaf0;margin:0 4px;}
        #courierHeader .courier-count-badge {position:absolute;top:-5px;right:-5px;min-width:20px;height:20px;padding:0 4px;border-radius:999px;display:grid;place-items:center;border:2px solid #fff;background:#d9950b;color:#fff;font-size:9px;font-weight:800;line-height:1;}
        #courierHeader .courier-count-badge[hidden],.courier-live-count[hidden],#courierNotificationPopover[hidden] {display:none !important;}
        #courierSidebarNav .courier-notification-nav {position:relative;}
        #courierSidebarNav .courier-live-count {display:grid;place-items:center;margin-left:auto;min-width:19px;height:19px;padding:0 5px;border-radius:999px;background:#d9950b;color:#fff;font-size:9px;font-weight:800;line-height:1;flex:none;}
        #courierSidebarNav [data-courier-nav-active="true"] .courier-live-count {background:#fff;color:#b77808;}
        #courierSidebarNav a[title="Chat / Messaging"] {position:relative;}
        body.courier-sidebar-collapsed #courierSidebarNav .courier-live-count {position:absolute;top:3px;right:4px;margin:0;min-width:16px;height:16px;font-size:8px;display:grid !important;}
        body.courier-sidebar-collapsed #courierSidebarNav .courier-live-count[hidden] {display:none !important;}
        #courierHeader .courier-notification-popover {position:absolute;top:calc(100% + 12px);right:0;width:min(384px,calc(100vw - 24px));max-height:min(530px,calc(100vh - 100px));overflow:hidden;display:flex;flex-direction:column;border:1px solid #e5eaf0;border-radius:16px;background:#fff;box-shadow:0 20px 60px rgba(15,23,42,.16),0 4px 14px rgba(15,23,42,.06);z-index:65;}
        #courierHeader .courier-popover-heading {padding:15px 17px;display:flex;align-items:center;justify-content:space-between;gap:10px;border-bottom:1px solid #edf0f4;}
        #courierHeader .courier-popover-heading strong {font-size:14px;color:#253448;font-weight:750;}
        #courierHeader .courier-mark-all {border:0;background:transparent;padding:5px;color:#a16f16;font-size:11px;font-weight:650;cursor:pointer;}
        #courierHeader .courier-mark-all:hover {color:#815808;text-decoration:underline;}
        #courierHeader .courier-popover-list {min-height:55px;overflow-y:auto;}
        #courierHeader .courier-popover-item {display:block;width:100%;text-align:left;padding:12px 17px;border:0;border-bottom:1px solid #edf0f4;background:#fff;cursor:pointer;}
        #courierHeader .courier-popover-item[aria-label] {font-family:inherit;}
        #courierHeader .courier-popover-item:hover {background:#f8fafc;}
        #courierHeader .courier-popover-item.is-unread {background:#fbfcfe;border-left:3px solid #c88912;padding-left:14px;}
        #courierHeader .courier-popover-item-title {display:block;font-size:11.5px;font-weight:700;color:#2f3742;}
        #courierHeader .courier-popover-item-text {display:block;font-size:10.5px;color:#697789;margin-top:3px;line-height:1.6;overflow-wrap:anywhere;}
        #courierHeader .courier-popover-item-time {display:block;font-size:10px;color:#94a0ac;margin-top:5px;}
        #courierHeader .courier-popover-empty {padding:28px 16px;text-align:center;font-size:11.5px;color:#7b8794;}
        #courierHeader .courier-popover-foot {display:block;border-top:1px solid #e9edf2;padding:12px;text-align:center;font-size:11.5px;font-weight:700;color:#a16f16;text-decoration:none;}
        #courierHeader .courier-popover-foot:hover {background:#f8fafc;color:#875b0b;}
        #courierHeader :is(button,a):focus-visible {outline:2px solid #c88912;outline-offset:2px;}
        @media (max-width:1023px){#courierHeader #courierMenuButton {display:grid;}#courierHeader #courierSidebarToggle {display:none;}}
        @media (max-width:1279px){#courierHeader .courier-header-profile-details {display:none;}#courierHeader .courier-header-profile {padding:5px;}#courierHeader .courier-header-separator {display:none;}}
        @media (max-width:767px){#courierHeader .courier-header-main {min-height:74px !important;gap:10px;padding-inline:12px !important;}#courierMain #courierHeader .courier-header-title {font-size:21px !important;}#courierHeader .courier-header-breadcrumb {font-size:10px;}#courierHeader .courier-header-avatar {width:35px;height:35px;}}
        @media(max-width:380px){#courierHeader .courier-header-main {gap:5px;}#courierHeader .courier-header-control,#courierHeader .courier-header-bell {width:35px;height:35px;}#courierHeader .courier-header-breadcrumb {max-width:170px;}}
    </style>

    @vite('resources/js/app.js')
</head>


<body class="min-h-screen bg-[#fbfaf7] antialiased">

    @php
        $pickupBadge =
            (int) (
                $stats['available_requests']
                ?? $stats['pickup_waiting']
                ?? 0
            );

        $lastMileBadge =
            (int) (
                $stats['last_mile_waiting']
                ?? 0
            );

        $courierName =
            $courier['name']
            ?? session('courier_name')
            ?? 'SARI Rider';

        $courierVehicle =
            $courier['vehicle']
            ?? 'Motorcycle';

        $courierInitial =
            strtoupper(
                mb_substr(
                    trim(
                        (string) $courierName
                    ),
                    0,
                    1
                )
            );

        // Use the authenticated Rider account for the header identity; avoid
        // outdated session display names and hard-coded verification labels.
        $courierHeaderAccountId = (int) session('courier_account_id', 0);
        $courierHeaderAccount = $courierHeaderAccountId > 0
            ? \App\Models\Accounts\CourierAccount::query()
                ->select(['id', 'first_name', 'last_name', 'email', 'account_status', 'approved_at', 'profile_image_path'])
                ->find($courierHeaderAccountId)
            : null;
        if ($courierHeaderAccount) {
            $courierHeaderRealName = trim(
                (string) $courierHeaderAccount->first_name.' '.(string) $courierHeaderAccount->last_name
            );
            if ($courierHeaderRealName !== '') $courierName = $courierHeaderRealName;
        }
        $courierName = trim((string) $courierName) ?: 'SARI Rider';
        $courierInitial = mb_strtoupper(mb_substr($courierName, 0, 1));
        $courierHeaderVerified = $courierHeaderAccount !== null
            && $courierHeaderAccount->account_status === 'active'
            && $courierHeaderAccount->approved_at !== null;
        $courierHeaderStatus = $courierHeaderVerified ? 'Verified Rider' : 'Rider account';
        // Profile image uses the same public disk and saved field as Account Management.
        // The initials remain underneath as a fallback for missing/broken photos.
        $courierHeaderPhotoPath = trim((string) ($courierHeaderAccount?->profile_image_path ?? ''));
        $courierHeaderPhotoUrl = $courierHeaderPhotoPath !== ''
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($courierHeaderPhotoPath)
            : null;

    @endphp


    {{-- SARI-RIDER-HEADER-ACCOUNT-PHOTO-V1: photo mirrors Account Management public disk --}}
    {{-- =========================================================
        MOBILE OVERLAY
    ========================================================== --}}

    <div
        id="courierSidebarOverlay"
        class="
            fixed inset-0 z-40
            hidden
            bg-black/30
            backdrop-blur-[1px]
            lg:hidden
        "
    ></div>


    {{-- =========================================================
        SIDEBAR
    ========================================================== --}}

    <aside
        id="courierSidebar"
        class="
            fixed inset-y-0 left-0 z-50
            flex w-[238px]
            -translate-x-full
            flex-col
            border-r border-[#eee4d3]
            bg-[#fffdf8]
            transition-all duration-300 ease-out
            lg:translate-x-0
        "
    >

        {{-- LOGO --}}
        <div
            class="
                relative
                flex min-h-[110px]
                items-center
                justify-center
                border-b border-[#eee4d3]
                px-4
            "
        >

            <a
                id="courierSidebarLogo"
                href="{{ route('courier.dashboard') }}"
                class="relative flex w-full items-center justify-center"
                aria-label="SARI Rider Dashboard"
            >
                {{-- Buyer sidebar logo dimensions: 102px full, 38px icon-only. --}}
                <span id="courierSidebarLogoStage" class="relative block h-[44px] w-full">
                    <img
                        id="courierSidebarLogoFull"
                        src="{{ asset('images/sari-logo.png') }}"
                        alt="SARI"
                        width="104"
                        height="42"
                        decoding="async"
                        draggable="false"
                        class="absolute left-1/2 top-1/2 h-auto w-[104px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain brightness-0"
                    >
                    <img
                        id="courierSidebarLogoCompact"
                        src="{{ asset('images/sari-main-logo.png') }}"
                        alt="SARI"
                        width="38"
                        height="38"
                        decoding="async"
                        draggable="false"
                        class="pointer-events-none absolute left-1/2 top-1/2 h-[38px] w-[38px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain opacity-0"
                        style="visibility:hidden;"
                    >
                </span>
            </a>

            <button
                id="courierSidebarClose"
                type="button"
                class="
                    absolute right-4 top-1/2
                    grid h-10 w-10
                    -translate-y-1/2
                    place-items-center
                    rounded-xl
                    text-[#6e6558]
                    transition
                    hover:bg-[#f7eedf]
                    hover:text-[#b97805]
                    lg:hidden
                "
                aria-label="Close navigation"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6 6 18"></path>
                </svg>

            </button>

        </div>


        {{-- NAVIGATION --}}
        <nav
            id="courierSidebarNav"
            class="
                flex-1
                overflow-y-auto
                px-4 py-5
            "
        >

            {{-- =================================================
                MAIN MENU
            ================================================== --}}

            <p
                class="
                    courier-sidebar-section
                    mb-2
                    px-2
                    text-[7px]
                    font-semibold
                    uppercase
                    tracking-[.16em]
                    text-[#a69b8b]
                "
            >
                Main Menu
            </p>


            <div class="courier-sidebar-link-group space-y-1.5">

                {{-- Dashboard --}}
                <a
                    href="{{ route('courier.dashboard') }}"
                    title="Dashboard Overview"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.dashboard') ? 'true' : 'false' }}"
                    class="
                        flex items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-semibold
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.dashboard'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 11.5 12 4l9 7.5"></path>
                        <path d="M5.5 10v10h13V10"></path>
                        <path d="M9.5 20v-6h5v6"></path>
                    </svg>


                    <span class="courier-sidebar-label whitespace-nowrap">
                        Dashboard Overview
                    </span>

                </a>

            </div>


            <div class="my-4 border-t border-[#eee4d3]"></div>


            {{-- =================================================
                RIDER OPERATIONS
            ================================================== --}}

            <p
                class="
                    courier-sidebar-section
                    mb-2
                    px-2
                    text-[7px]
                    font-semibold
                    uppercase
                    tracking-[.16em]
                    text-[#a69b8b]
                "
            >
                Rider Operations
            </p>


            <div class="courier-sidebar-link-group space-y-1.5">

                {{-- Pickup Assignments --}}
                <a
                    href="{{ route('courier.requests') }}"
                    title="Pickup Assignments"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.requests') ? 'true' : 'false' }}"
                    class="
                        flex
                        items-center
                        justify-between
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.requests'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <span class="flex min-w-0 items-center gap-3">

                        <svg
                            viewBox="0 0 24 24"
                            class="h-[19px] w-[19px] shrink-0"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M6 3h9l3 3v15H6z"></path>
                            <path d="M9 11h6"></path>
                            <path d="M9 15h4"></path>
                            <path d="M15 3v4h4"></path>
                        </svg>


                        <span
                            class="
                                courier-sidebar-label
                                truncate
                                whitespace-nowrap
                            "
                        >
                            Pickup Assignments
                        </span>

                    </span>


                    @if($pickupBadge > 0)

                        <span
                            class="
                                courier-sidebar-extra
                                grid h-5 min-w-[20px]
                                place-items-center
                                rounded-full
                                px-1.5
                                text-[9px]
                                font-bold

                                {{
                                    request()->routeIs(
                                        'courier.requests'
                                    )
                                        ? 'bg-white/20 text-white'
                                        : 'bg-[#fff0cf] text-[#aa6d00]'
                                }}
                            "
                        >
                            {{ $pickupBadge }}
                        </span>

                    @endif

                </a>


                {{-- Seller Pickups --}}
                <a
                    href="{{ route('courier.pickups') }}"
                    title="Seller Pickups"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.pickups') ? 'true' : 'false' }}"
                    class="
                        flex items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.pickups'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M5 8h14l-1 12H6L5 8Z"></path>
                        <path d="M8 8a4 4 0 0 1 8 0"></path>
                        <path d="M9 13h6"></path>
                    </svg>


                    <span class="courier-sidebar-label whitespace-nowrap">
                        Seller Pickups
                    </span>

                </a>


                {{-- Last-Mile Deliveries --}}
                <a
                    href="{{ route('courier.last-mile') }}"
                    title="Last-Mile Deliveries"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.last-mile*') ? 'true' : 'false' }}"
                    class="
                        flex
                        items-center
                        justify-between
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.last-mile*'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <span class="flex min-w-0 items-center gap-3">

                        <svg
                            viewBox="0 0 24 24"
                            class="h-[19px] w-[19px] shrink-0"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 17h3"></path>
                            <path d="M17 17h3"></path>
                            <path d="M7 17h10"></path>
                            <circle cx="8" cy="17" r="2"></circle>
                            <circle cx="18" cy="17" r="2"></circle>
                            <path d="M6 15V8h8l3 4h2v3"></path>
                            <path d="M9 8V5h5v3"></path>
                        </svg>


                        <span
                            class="
                                courier-sidebar-label
                                truncate
                                whitespace-nowrap
                            "
                        >
                            Last-Mile Deliveries
                        </span>

                    </span>


                    @if($lastMileBadge > 0)

                        <span
                            class="
                                courier-sidebar-extra
                                grid h-5 min-w-[20px]
                                place-items-center
                                rounded-full
                                px-1.5
                                text-[9px]
                                font-bold

                                {{
                                    request()->routeIs(
                                        'courier.last-mile*'
                                    )
                                        ? 'bg-white/20 text-white'
                                        : 'bg-[#eaf2f7] text-[#3C6E91]'
                                }}
                            "
                        >
                            {{ $lastMileBadge }}
                        </span>

                    @endif

                </a>


                {{-- Scan QR Code: existing rider parcel-scanner workflow. --}}
                <a
                    href="{{ route('courier.parcel-scanner') }}"
                    title="Scan QR Code"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.parcel-scanner*') ? 'true' : 'false' }}"
                    class="
                        flex items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs('courier.parcel-scanner*')
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M3 16v3a2 2 0 0 0 2 2h3M21 16v3a2 2 0 0 1-2 2h-3"></path>
                        <rect x="8" y="8" width="3" height="3" rx=".3"></rect>
                        <path d="M15 8h2v2h-2zM8 15h2v2H8zM14 14h3v3h-2v-1"></path>
                    </svg>
                    <span class="courier-sidebar-label whitespace-nowrap">
                        Scan QR Code
                    </span>
                </a>


                {{-- Active Deliveries --}}
                <a
                    href="{{ route('courier.deliveries') }}"
                    title="Active Deliveries"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.deliveries') ? 'true' : 'false' }}"
                    class="
                        flex items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.deliveries'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <svg
                        viewBox="0 0 32 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="8" cy="18" r="4"></circle>
                        <circle cx="24" cy="18" r="4"></circle>
                        <path d="M8 18h6l4-8h4"></path>
                        <path d="M14 18l-4-9h5"></path>
                        <path d="M18 10l6 8"></path>
                    </svg>


                    <span class="courier-sidebar-label whitespace-nowrap">
                        Active Deliveries
                    </span>

                </a>

            </div>


            <div class="my-4 border-t border-[#eee4d3]"></div>


            {{-- =================================================
                MANAGEMENT
            ================================================== --}}

            <p
                class="
                    courier-sidebar-section
                    mb-2
                    px-2
                    text-[7px]
                    font-semibold
                    uppercase
                    tracking-[.16em]
                    text-[#a69b8b]
                "
            >
                Management
            </p>


            <div class="courier-sidebar-link-group space-y-1.5">

                {{-- Earnings --}}
                <a
                    href="{{ route('courier.earnings') }}"
                    title="Earnings"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.earnings*') ? 'true' : 'false' }}"
                    class="
                        flex items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.earnings*'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M12 2v20"></path>
                        <path d="M17 6.5c0-1.7-2.2-3-5-3s-5 1.3-5 3 2.2 3 5 3 5 1.3 5 3-2.2 3-5 3-5-1.3-5-3"></path>
                    </svg>


                    <span class="courier-sidebar-label whitespace-nowrap">
                        Earnings
                    </span>

                </a>


                {{-- COD Remittance --}}
                <a
                    href="{{ route('courier.remittances.index') }}"
                    title="COD Remittance"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.remittances.*') ? 'true' : 'false' }}"
                    class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('courier.remittances.*') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
                >
                    <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M7 9h.01M17 15h.01"></path>
                    </svg>
                    <span class="courier-sidebar-label whitespace-nowrap">COD Remittance</span>
                </a>

                {{-- History --}}
                <a
                    href="{{ route('courier.history') }}"
                    title="Delivery History"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.history*') ? 'true' : 'false' }}"
                    class="
                        flex items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.history*'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path>
                        <path d="M3 3v5h5"></path>
                        <path d="M12 7v5l3 2"></path>
                    </svg>


                    <span class="courier-sidebar-label whitespace-nowrap">
                        Delivery History
                    </span>

                </a>


                {{-- Messages --}}
                <a
                    href="{{ route('courier.messages') }}"
                    title="Chat / Messaging"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.messages*') ? 'true' : 'false' }}"
                    class="
                        flex items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.messages*'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                        <path d="M8 10h8"></path>
                        <path d="M8 14h5"></path>
                    </svg>


                    <span class="courier-sidebar-label whitespace-nowrap">
                        Chat / Messaging
                    </span>
                    <span id="courierSidebarMessagesCount" class="courier-live-count" hidden aria-label="Unread logistics messages">0</span>

                </a>


                {{-- Live, rider-scoped notification center. Count loads from the backend. --}}
                <a
                    href="{{ (\Illuminate\Support\Facades\Route::has('courier.notifications') ? route('courier.notifications') : url('/courier/notifications')) }}"
                    title="Notifications"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.notifications*') ? 'true' : 'false' }}"
                    class="courier-notification-nav flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('courier.notifications*') ? 'bg-[#d9930a] text-white' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
                >
                    <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
                    <span class="courier-sidebar-label whitespace-nowrap">Notifications</span>
                    <span id="courierSidebarNotificationCount" class="courier-live-count" hidden aria-label="Unread notifications">0</span>
                </a>

                {{-- Profile --}}
                <a
                    href="{{ route('courier.profile') }}"
                    title="Account Management"
                    data-courier-sidebar-item
                    data-courier-nav-active="{{ request()->routeIs('courier.profile*') ? 'true' : 'false' }}"
                    class="
                        flex items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        transition-all duration-200

                        {{
                            request()->routeIs(
                                'courier.profile*'
                            )
                                ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]'
                                : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                        }}
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="8" r="3.5"></circle>
                        <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                    </svg>


                    <span class="courier-sidebar-label whitespace-nowrap">
                        Account Management
                    </span>

                </a>

            </div>


            <div class="my-4 border-t border-[#eee4d3]"></div>


            {{-- Logout --}}
            <form
                method="POST"
                action="{{ route('courier.logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    title="Logout"
                    data-courier-sidebar-item
                    class="
                        flex w-full
                        items-center
                        gap-3
                        rounded-xl
                        px-4 py-3.5
                        text-[13px]
                        font-medium
                        text-[#514b42]
                        transition-all duration-200
                        hover:bg-red-50
                        hover:text-red-600
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[19px] w-[19px] shrink-0"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M14 3h7v18h-7"></path>
                    </svg>


                    <span class="courier-sidebar-label whitespace-nowrap">
                        Logout
                    </span>

                </button>
            </form>

        </nav>


        {{-- Sidebar footer profile card intentionally removed. Account Management remains in navigation. --}}

    </aside>


    {{-- =========================================================
        MAIN CONTENT
    ========================================================== --}}

    <div
        id="courierMain"
        class="
            min-h-screen
            lg:pl-[238px]
        "
    >

        {{-- =====================================================
             BUYER-STYLE COURIER HEADER: responsive & functional
        ====================================================== --}}
        <header id="courierHeader" class="sticky top-0 z-40 border-b border-[#e9edf2] bg-white">
            <div class="courier-header-main flex w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-5 xl:px-6">
                <div class="flex min-w-0 items-center gap-2.5">
                    <button id="courierMenuButton" type="button" class="courier-header-control lg:hidden" aria-label="Open navigation" aria-controls="courierSidebar" aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                    </button>
                    <button id="courierSidebarToggle" type="button" class="courier-header-control hidden lg:grid" aria-label="Collapse sidebar" aria-controls="courierSidebar" aria-expanded="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                    </button>
                    <div class="min-w-0">
                        <h1 class="courier-header-title truncate">@yield('header-title', 'Rider Dashboard')</h1>
                        <div class="courier-header-breadcrumb mt-1 min-w-0">
                            <a href="{{ route('courier.dashboard') }}" class="max-w-[120px] truncate font-medium hover:text-[#c88912]">Rider</a>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                            <span class="truncate font-semibold text-[#475467]">@yield('header-subtitle', 'Dashboard')</span>
                        </div>
                    </div>
                </div>
                <div class="flex min-w-0 shrink-0 items-center gap-1.5 sm:gap-2">
                    <div id="courierNotificationWrap" class="relative">
                        <button id="notificationBell" type="button" class="courier-header-bell" aria-label="Notifications" aria-haspopup="dialog" aria-expanded="false" aria-controls="courierNotificationPopover" title="Notifications">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
                            <span id="courierHeaderNotificationCount" class="courier-count-badge" hidden aria-label="Unread notifications">0</span>
                        </button>
                        <div id="courierNotificationPopover" class="courier-notification-popover" role="dialog" aria-label="Rider notifications" hidden>
                            <div class="courier-popover-heading">
                                <strong>Notifications</strong>
                                <button id="courierNotificationMarkAll" class="courier-mark-all" type="button">Mark all read</button>
                            </div>
                            <div class="courier-sound-controls" role="group" aria-label="Rider notification sound settings">
                                <button type="button" data-courier-sound-toggle aria-pressed="true" title="Mute Rider notification sounds">Sound on</button>
                                <button type="button" data-courier-sound-preview title="Play the Rider notification sound">Test sound</button>
                                <label for="courierSoundVolume">Volume
                                    <input id="courierSoundVolume" type="range" min="0" max="100" value="78" step="5" data-courier-sound-volume aria-label="Notification volume">
                                </label>
                            </div>
                            <div id="courierNotificationPreview" class="courier-popover-list" role="list" aria-live="polite"><div class="courier-popover-empty">Loading notifications…</div></div>
                            <a class="courier-popover-foot" href="{{ (\Illuminate\Support\Facades\Route::has('courier.notifications') ? route('courier.notifications') : url('/courier/notifications')) }}">View all notifications</a>
                        </div>
                    </div>
                    <div class="courier-header-separator hidden xl:block" aria-hidden="true"></div>
                    <a id="courierProfileButton" href="{{ route('courier.profile') }}" class="courier-header-profile" aria-label="Open Rider account management for {{ $courierName }}" title="{{ $courierName }} — Account Management">
                        <span class="courier-header-avatar" aria-hidden="true">
                            <span class="courier-header-avatar-initial">{{ $courierInitial ?: 'R' }}</span>
                            @if($courierHeaderPhotoUrl)
                                <img class="courier-header-avatar-photo"
                                     src="{{ $courierHeaderPhotoUrl }}"
                                     alt=""
                                     width="39" height="39"
                                     loading="eager" decoding="async"
                                     onerror="this.remove()">
                            @endif
                        </span>
                        <span class="courier-header-profile-details hidden min-w-0 xl:block">
                            <span class="courier-header-profile-name">{{ $courierName }}</span>
                            <span class="courier-header-profile-status">{{ $courierHeaderStatus }}</span>
                        </span>
                    </a>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main
            class="
                px-4 py-5
                sm:px-6
                lg:px-8
                xl:px-10
            "
        >
            @yield('content')
        </main>

    </div>


    {{-- Genuine redirect feedback: only show messages set by a server action. --}}
    <div id="sariCourierFlashNotifications" hidden
        data-success="{{ session('success') }}"
        data-error="{{ $errors->first() }}"
        data-status="{{ session('status') }}"></div>

    {{-- =========================================================
        TOAST
    ========================================================== --}}

    <div
        id="courierToast"
        class="
            fixed
            right-4 top-24
            z-[70]
            hidden
            w-[calc(100%-2rem)]
            max-w-sm
            rounded-2xl
            border border-[#eadfca]
            bg-[#fffdf8]
            p-4
            shadow-[0_18px_55px_rgba(66,52,25,.15)]
            sm:right-6
        "
    >

        <div class="flex items-start gap-3">

            <div
                class="
                    grid h-10 w-10
                    shrink-0
                    place-items-center
                    rounded-xl
                    bg-emerald-50
                    text-emerald-600
                "
            >

                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m5 12 4 4L19 6"></path>
                </svg>

            </div>


            <div class="min-w-0">

                <p
                    id="courierToastTitle"
                    class="
                        text-[12px]
                        font-semibold
                        text-[#201c16]
                    "
                >
                    Updated
                </p>


                <p
                    id="courierToastMessage"
                    class="
                        mt-1
                        text-[10px]
                        leading-5
                        text-[#7f7567]
                    "
                ></p>

            </div>

        </div>

    </div>


    {{-- =========================================================
        SHELL JAVASCRIPT
    ========================================================== --}}

    <script>
        (() => {
            'use strict';


            const body =
                document.body;

            const sidebar =
                document.getElementById(
                    'courierSidebar'
                );

            const overlay =
                document.getElementById(
                    'courierSidebarOverlay'
                );

            const mobileMenuButton =
                document.getElementById(
                    'courierMenuButton'
                );

            const mobileCloseButton =
                document.getElementById(
                    'courierSidebarClose'
                );

            const desktopToggle =
                document.getElementById(
                    'courierSidebarToggle'
                );


            const openMobileSidebar = () => {

                sidebar
                    ?.classList
                    .remove(
                        '-translate-x-full'
                    );

                overlay
                    ?.classList
                    .remove(
                        'hidden'
                    );

            };


            const closeMobileSidebar = () => {

                sidebar
                    ?.classList
                    .add(
                        '-translate-x-full'
                    );

                overlay
                    ?.classList
                    .add(
                        'hidden'
                    );

            };


            mobileMenuButton
                ?.addEventListener(
                    'click',
                    openMobileSidebar
                );


            mobileCloseButton
                ?.addEventListener(
                    'click',
                    closeMobileSidebar
                );


            overlay
                ?.addEventListener(
                    'click',
                    closeMobileSidebar
                );


            /*
            |--------------------------------------------------------------------------
            | Persistent desktop sidebar state
            |--------------------------------------------------------------------------
            */

            const savedState =
                localStorage.getItem(
                    'sariCourierSidebarCollapsed'
                );


            if (
                savedState === '1'
                &&
                window.innerWidth >= 1024
            ) {

                body
                    .classList
                    .add(
                        'courier-sidebar-collapsed'
                    );

                desktopToggle
                    ?.setAttribute(
                        'aria-expanded',
                        'false'
                    );

            }


            desktopToggle
                ?.addEventListener(
                    'click',
                    () => {

                        body
                            .classList
                            .toggle(
                                'courier-sidebar-collapsed'
                            );


                        const collapsed =
                            body
                                .classList
                                .contains(
                                    'courier-sidebar-collapsed'
                                );


                        localStorage.setItem(
                            'sariCourierSidebarCollapsed',
                            collapsed
                                ? '1'
                                : '0'
                        );


                        desktopToggle
                            .setAttribute(
                                'aria-expanded',
                                collapsed
                                    ? 'false'
                                    : 'true'
                            );

                    }
                );


            window.addEventListener(
                'resize',
                () => {

                    if (
                        window.innerWidth
                        >= 1024
                    ) {

                        overlay
                            ?.classList
                            .add(
                                'hidden'
                            );

                    } else {

                        body
                            .classList
                            .remove(
                                'courier-sidebar-collapsed'
                            );

                        sidebar
                            ?.classList
                            .add(
                                '-translate-x-full'
                            );

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Reveal animation
            |--------------------------------------------------------------------------
            */

            if (
                'IntersectionObserver'
                in window
            ) {

                const observer =
                    new IntersectionObserver(
                        (entries) => {

                            entries
                                .forEach(
                                    (entry) => {

                                        if (
                                            ! entry
                                                .isIntersecting
                                        ) {
                                            return;
                                        }


                                        entry
                                            .target
                                            .classList
                                            .add(
                                                'show'
                                            );


                                        observer
                                            .unobserve(
                                                entry
                                                    .target
                                            );

                                    }
                                );

                        },
                        {
                            threshold: .06,
                        }
                    );


                document
                    .querySelectorAll(
                        '.reveal'
                    )
                    .forEach(
                        (
                            element,
                            index
                        ) => {

                            element
                                .style
                                .transitionDelay =
                                    `${
                                        Math.min(
                                            index * 45,
                                            220
                                        )
                                    }ms`;


                            observer
                                .observe(
                                    element
                                );

                        }
                    );

            } else {

                document
                    .querySelectorAll(
                        '.reveal'
                    )
                    .forEach(
                        (element) => {
                            element
                                .classList
                                .add(
                                    'show'
                                );
                        }
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Toast helper
            |--------------------------------------------------------------------------
            */

            window.SariCourierToast = (
                title,
                message
            ) => {

                const toast =
                    document.getElementById(
                        'courierToast'
                    );

                const bell =
                    document.getElementById(
                        'notificationBell'
                    );


                if (! toast) {
                    return;
                }


                const titleElement =
                    document.getElementById(
                        'courierToastTitle'
                    );

                const messageElement =
                    document.getElementById(
                        'courierToastMessage'
                    );


                if (titleElement) {
                    titleElement.textContent =
                        title;
                }


                if (messageElement) {
                    messageElement.textContent =
                        message;
                }


                toast
                    .classList
                    .remove(
                        'hidden'
                    );


                bell
                    ?.classList
                    .remove(
                        'bell-ring'
                    );


                if (bell) {
                    void bell.offsetWidth;
                }


                bell
                    ?.classList
                    .add(
                        'bell-ring'
                    );


                clearTimeout(
                    window
                        .__sariCourierToastTimer
                );


                window.__sariCourierToastTimer =
                    setTimeout(
                        () => {

                            toast
                                .classList
                                .add(
                                    'hidden'
                                );

                        },
                        3500
                    );

            };

        })();
    </script>


    @if(session('courier_success_title'))

        <script>
            document.addEventListener(
                'DOMContentLoaded',
                () => {

                    window
                        .SariCourierToast
                        ?.(
                            @json(
                                session(
                                    'courier_success_title'
                                )
                            ),

                            @json(
                                session(
                                    'courier_success_message'
                                )
                            )
                        );

                }
            );
        </script>

    @endif


    <div id="sariCourierNoticeModal" hidden aria-live="off">
        <div class="scn-backdrop" data-scn-backdrop aria-hidden="true"></div>
        <section class="scn-dialog" role="dialog" aria-modal="true" aria-labelledby="scnModalTitle" aria-describedby="scnModalDescription">
            <div class="scn-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg></div>
            <p class="scn-kicker">Rider operations</p>
            <h2 id="scnModalTitle" class="scn-title" data-scn-title>New assignment</h2>
            <p id="scnModalDescription" class="scn-message" data-scn-message>A new delivery has been assigned.</p>
            <div class="scn-actions">
                <button class="scn-button scn-dismiss" type="button" data-scn-dismiss>Dismiss</button>
                <a class="scn-button scn-primary" data-scn-link href="{{ route('courier.requests') }}">View assignment</a>
            </div>
        </section>
    </div>
    <script src="{{ asset('js/courier/sari-toast.js') }}?v=20261101-custom-notification-v6" defer
        data-sounds-base="{{ asset('sounds/courier') }}"
        data-owner="{{ $courierHeaderAccount?->id ?? 'rider' }}"></script>
    <script src="{{ asset('js/courier/header-notifications.js') }}?v=20261101-rider-sound-v5" defer data-courier-notification-script
        data-summary-url="{{ (\Illuminate\Support\Facades\Route::has('courier.notifications.summary') ? route('courier.notifications.summary') : '') }}"
        data-read-url="{{ (\Illuminate\Support\Facades\Route::has('courier.notifications.read') ? route('courier.notifications.read', ['notification' => '__notification__']) : '') }}"
        data-read-all-url="{{ (\Illuminate\Support\Facades\Route::has('courier.notifications.read-all') ? route('courier.notifications.read-all') : '') }}"></script>

    @stack('scripts')

    {{-- Authentication feedback only; displayed when an auth flash/error is present. --}}
    @include('components.auth.login-feedback-modal')

    @include('components.auth.logout-history-protection')
</body>

</html>