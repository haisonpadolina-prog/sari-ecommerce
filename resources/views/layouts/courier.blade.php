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
        .courier-sidebar-section,
        #courierSidebarProfile {
            transition: all .2s ease;
        }

        body.courier-sidebar-collapsed #courierSidebar {
            width: 92px;
        }

        body.courier-sidebar-collapsed #courierMain {
            padding-left: 92px;
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

        body.courier-sidebar-collapsed #courierSidebarProfile > a {
            justify-content: center;
            padding: 10px;
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
    @endphp


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
            flex w-[275px]
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
                class="flex items-center justify-center"
                aria-label="SARI Rider Dashboard"
            >

                <img
                    src="{{ asset('images/sari-logo.png') }}"
                    alt="SARI"
                    class="
                        h-auto
                        w-[145px]
                        object-contain
                        brightness-0
                    "
                >

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


            <div class="space-y-1.5">

                {{-- Dashboard --}}
                <a
                    href="{{ route('courier.dashboard') }}"
                    title="Dashboard Overview"
                    data-courier-sidebar-item
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


            <div class="space-y-1.5">

                {{-- Pickup Assignments --}}
                <a
                    href="{{ route('courier.requests') }}"
                    title="Pickup Assignments"
                    data-courier-sidebar-item
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


                {{-- Active Deliveries --}}
                <a
                    href="{{ route('courier.deliveries') }}"
                    title="Active Deliveries"
                    data-courier-sidebar-item
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


            <div class="space-y-1.5">

                {{-- Earnings --}}
                <a
                    href="{{ route('courier.earnings') }}"
                    title="Earnings"
                    data-courier-sidebar-item
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

                </a>


                {{-- Profile --}}
                <a
                    href="{{ route('courier.profile') }}"
                    title="Account Management"
                    data-courier-sidebar-item
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


        {{-- =====================================================
            SIDEBAR PROFILE
        ====================================================== --}}

        <div
            id="courierSidebarProfile"
            class="
                border-t border-[#eee4d3]
                bg-[#fffdf8]
                p-4
                transition-all duration-300
            "
        >

            <a
                href="{{ route('courier.profile') }}"
                title="{{ $courierName }}"
                data-courier-sidebar-item
                class="
                    flex w-full
                    items-center
                    gap-3
                    rounded-2xl
                    border border-[#eadfca]
                    bg-white
                    p-3.5
                    transition
                    hover:border-[#dbc396]
                    hover:shadow-sm
                "
            >

                <div
                    class="
                        grid h-11 w-11
                        shrink-0
                        place-items-center
                        rounded-full
                        bg-[#3C6E91]
                        text-[12px]
                        font-semibold
                        text-white
                    "
                >
                    {{ $courierInitial ?: 'R' }}
                </div>


                <div class="courier-sidebar-label min-w-0 flex-1">

                    <p
                        class="
                            truncate
                            text-[12px]
                            font-semibold
                            text-[#211d17]
                        "
                    >
                        {{ $courierName }}
                    </p>

                    <p
                        class="
                            mt-0.5
                            truncate
                            text-[9px]
                            text-[#8d8272]
                        "
                    >
                        {{ $courierVehicle }}
                        · Verified Rider
                    </p>

                </div>


                <svg
                    viewBox="0 0 24 24"
                    class="
                        courier-sidebar-extra
                        h-4 w-4
                        shrink-0
                        text-[#82796b]
                    "
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="m9 18 6-6-6-6"></path>
                </svg>

            </a>

        </div>

    </aside>


    {{-- =========================================================
        MAIN CONTENT
    ========================================================== --}}

    <div
        id="courierMain"
        class="
            min-h-screen
            lg:pl-[275px]
        "
    >

        {{-- =====================================================
            HEADER
        ====================================================== --}}

        <header
            class="
                sticky top-0 z-40
                border-b border-[#eee4d3]
                bg-[#fffdf9]/95
                backdrop-blur-md
            "
        >

            <div
                class="
                    flex min-h-[86px]
                    w-full
                    items-center
                    justify-between
                    gap-3
                    px-4
                    sm:px-6
                    lg:px-8
                    xl:px-10
                "
            >

                <div
                    class="
                        flex
                        min-w-0
                        items-center
                        gap-3
                        sm:gap-4
                    "
                >

                    {{-- Mobile menu --}}
                    <button
                        id="courierMenuButton"
                        type="button"
                        class="
                            grid h-11 w-11
                            shrink-0
                            place-items-center
                            rounded-xl
                            border border-[#e8dfd0]
                            bg-white
                            text-[#4f473c]
                            shadow-sm
                            transition
                            hover:border-[#d9be8c]
                            hover:bg-[#fff9ef]
                            hover:text-[#b97805]
                            lg:hidden
                        "
                        aria-label="Open navigation"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 7h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 17h16"></path>
                        </svg>

                    </button>


                    {{-- Desktop collapse --}}
                    <button
                        id="courierSidebarToggle"
                        type="button"
                        class="
                            hidden h-11 w-11
                            shrink-0
                            place-items-center
                            rounded-xl
                            border border-[#e8dfd0]
                            bg-white
                            text-[#4f473c]
                            shadow-sm
                            transition-all duration-200
                            hover:border-[#d9be8c]
                            hover:bg-[#fff9ef]
                            hover:text-[#b97805]
                            active:scale-95
                            lg:grid
                        "
                        aria-label="Collapse sidebar"
                        aria-expanded="true"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 7h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 17h16"></path>
                        </svg>

                    </button>


                    <div class="min-w-0">

                        <h1
                            class="
                                truncate
                                text-[18px]
                                font-bold
                                tracking-[-0.025em]
                                text-[#17140e]
                                sm:text-[20px]
                                xl:text-[22px]
                            "
                        >
                            @yield(
                                'header-title',
                                'Rider Dashboard'
                            )
                        </h1>


                        <div
                            class="
                                mt-1
                                hidden
                                items-center
                                gap-1.5
                                text-[11px]
                                text-[#978d7d]
                                sm:flex
                            "
                        >

                            <a
                                href="{{ route('courier.dashboard') }}"
                                class="
                                    transition
                                    hover:text-[#b97805]
                                "
                            >
                                Rider
                            </a>


                            <svg
                                viewBox="0 0 24 24"
                                class="h-3 w-3"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>


                            <span
                                class="
                                    font-medium
                                    text-[#5c5448]
                                "
                            >
                                @yield(
                                    'header-subtitle',
                                    'Dashboard'
                                )
                            </span>

                        </div>

                    </div>

                </div>


                <div
                    class="
                        flex
                        items-center
                        gap-2
                        sm:gap-3
                    "
                >

                    {{-- Search --}}
                    <div class="relative hidden md:block">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                pointer-events-none
                                absolute
                                left-4 top-1/2
                                h-[18px] w-[18px]
                                -translate-y-1/2
                                text-[#9a9184]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>


                        <input
                            type="search"
                            placeholder="Search deliveries, orders..."
                            class="
                                h-11 w-[220px]
                                rounded-[14px]
                                border border-[#e6ddcf]
                                bg-white
                                pl-11 pr-4
                                text-[11px]
                                outline-none
                                placeholder:text-[#a89f92]
                                focus:border-[#d89a25]
                                focus:ring-4
                                focus:ring-[#d89a25]/10
                                xl:w-[300px]
                            "
                        >

                    </div>


                    {{-- Notification control --}}
                    <button
                        id="notificationBell"
                        type="button"
                        class="
                            relative
                            grid h-11 w-11
                            place-items-center
                            rounded-xl
                            border border-[#e8dfd0]
                            bg-white
                            text-[#443d33]
                            shadow-sm
                            transition
                            hover:border-[#d9be8c]
                            hover:bg-[#fff9ef]
                            hover:text-[#b97805]
                        "
                        aria-label="Notifications"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>

                    </button>


                    <div
                        class="
                            hidden
                            h-9 w-px
                            bg-[#eee4d5]
                            xl:block
                        "
                    ></div>


                    {{-- Header profile --}}
                    <a
                        href="{{ route('courier.profile') }}"
                        class="
                            hidden
                            items-center
                            gap-2.5
                            rounded-xl
                            px-2 py-1.5
                            transition
                            hover:bg-[#fff7e9]
                            xl:flex
                        "
                    >

                        <div
                            class="
                                grid h-10 w-10
                                place-items-center
                                rounded-full
                                bg-[#3C6E91]
                                text-[11px]
                                font-bold
                                text-white
                            "
                        >
                            {{ $courierInitial ?: 'R' }}
                        </div>


                        <div>

                            <p
                                class="
                                    text-[11px]
                                    font-semibold
                                    text-[#28231c]
                                "
                            >
                                {{ $courierName }}
                            </p>

                            <p
                                class="
                                    mt-0.5
                                    text-[9px]
                                    text-[#908779]
                                "
                            >
                                Verified Rider
                            </p>

                        </div>

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


    @stack('scripts')

</body>

</html>