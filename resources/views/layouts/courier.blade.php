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

    <link rel="stylesheet" href="{{ asset('css/courier/layout.css') }}?v=20261030-shell-1">

    @stack('styles')

    @vite('resources/js/app.js')
</head>

<body class="courier-shell min-h-screen antialiased">
    <a href="#courierPageContent" class="courier-skip-link">Skip to page content</a>

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
        class="fixed inset-0 z-40 hidden bg-black/30 backdrop-blur-[1px] lg:hidden"
    ></div>

    {{-- =========================================================
        SIDEBAR
    ========================================================== --}}

    @include('components.courier.sidebar')

    {{-- =========================================================
        MAIN CONTENT
    ========================================================== --}}

    <div
        id="courierMain"
        class="min-h-screen lg:pl-[275px]"
    >

        {{-- =====================================================
            HEADER
        ====================================================== --}}

        @include('components.courier.header')

        {{-- PAGE CONTENT --}}
        <main
            id="courierPageContent"
            tabindex="-1"
            class="px-4 py-5 sm:px-6 lg:px-8 xl:px-10"
        >
            @yield('content')
        </main>

    </div>

    {{-- =========================================================
        TOAST
    ========================================================== --}}

    <div
        id="courierToast"
        role="status"
        aria-live="polite"
        aria-atomic="true"
        class="fixed right-4 top-24 z-[70] hidden w-[calc(100%-2rem)] max-w-sm rounded-2xl border border-[#eadfca] bg-white p-4 shadow-[0_18px_55px_rgba(66,52,25,.15)] sm:right-6"
    >

        <div class="flex items-start gap-3">

            <div
                class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-50 text-emerald-600"
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
                    class="text-[12px] font-semibold text-[#201c16]"
                >
                    Updated
                </p>

                <p
                    id="courierToastMessage"
                    class="mt-1 text-[13px] leading-5 text-[#7f7567]"
                ></p>

            </div>

        </div>

    </div>

    {{-- =========================================================
        SHELL JAVASCRIPT
    ========================================================== --}}

    <script src="{{ asset('js/courier-shell.js') }}?v=20261027"></script>

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
