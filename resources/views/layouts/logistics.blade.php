<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SARI Logistics')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                },
            },
        };
    </script>

    <style>
        :root {
            --logistics-sidebar-width: 275px;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        #logisticsSidebar,
        #logisticsContent {
            transition: all .25s ease;
        }

        @media (min-width: 1024px) {
            #logisticsSidebar {
                width: var(--logistics-sidebar-width);
            }

            #logisticsContent {
                padding-left: var(--logistics-sidebar-width);
            }

            body.logistics-sidebar-collapsed {
                --logistics-sidebar-width: 86px;
            }

            body.logistics-sidebar-collapsed .logistics-sidebar-label,
            body.logistics-sidebar-collapsed .logistics-sidebar-extra,
            body.logistics-sidebar-collapsed #logisticsSidebarProfileText {
                display: none;
            }

            body.logistics-sidebar-collapsed [data-logistics-sidebar-item] {
                justify-content: center;
            }

            body.logistics-sidebar-collapsed #logisticsSidebarLogo img {
                width: 48px;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-[#faf9f6] text-[#1f1b16] antialiased">
    <div class="min-h-screen">
        @include('components.logistics.sidebar')

        <div id="logisticsContent" class="min-h-screen">
            @include('components.logistics.header')

            <main class="w-full px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-7 xl:px-10">
                @yield('content')
            </main>
        </div>

        <button
            id="logisticsSidebarOverlay"
            type="button"
            class="fixed inset-0 z-40 hidden bg-black/30 backdrop-blur-[1px] lg:hidden"
            aria-label="Close logistics menu"
        ></button>
    </div>

    <div id="logisticsLogoutModal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[2px]">
        <div id="logisticsLogoutModalCard" class="w-full max-w-[420px] rounded-[22px] border border-[#e8dfd3] bg-white p-6 shadow-[0_30px_80px_rgba(46,29,25,.22)]">
            <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#fbf5e9] text-[#a8731f]">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M14 3h7v18h-7"></path>
                </svg>
            </div>

            <h3 class="mt-4 text-[18px] font-bold text-[#28221b]">Log out of SARI Logistics?</h3>
            <p class="mt-2 text-[11px] leading-5 text-[#81786c]">
                Your Logistics session will be securely ended and you will return to the SARI login page.
            </p>

            <div class="mt-6 flex justify-end gap-2">
                <button id="logisticsCancelLogout" type="button" class="h-11 rounded-xl border border-[#e0d7ca] bg-white px-5 text-[11px] font-semibold text-[#62594e] transition hover:bg-[#fcf8f1]">
                    Cancel
                </button>
                <form method="POST" action="{{ route('logistics.logout') }}">
                    @csrf
                    <button id="logisticsConfirmLogout" type="submit" class="h-11 rounded-xl bg-[#c99128] px-5 text-[11px] font-semibold text-white shadow-[0_10px_24px_rgba(201,145,40,0.18)] transition hover:bg-[#b47e1e]">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            const sidebar = document.getElementById('logisticsSidebar');
            const overlay = document.getElementById('logisticsSidebarOverlay');
            const menuButton = document.getElementById('logisticsMenuButton');
            const closeButton = document.getElementById('logisticsSidebarClose');
            const desktopToggle = document.getElementById('logisticsSidebarToggle');
            const notificationButton = document.getElementById('logisticsNotificationButton');
            const notificationPanel = document.getElementById('logisticsNotificationPanel');
            const logoutButton = document.getElementById('logisticsLogoutButton');
            const logoutModal = document.getElementById('logisticsLogoutModal');
            const cancelLogout = document.getElementById('logisticsCancelLogout');

            function openSidebar() {
                sidebar?.classList.remove('-translate-x-full');
                sidebar?.classList.add('translate-x-0');
                overlay?.classList.remove('hidden');
                menuButton?.setAttribute('aria-expanded', 'true');
            }

            function closeSidebar() {
                sidebar?.classList.remove('translate-x-0');
                sidebar?.classList.add('-translate-x-full');
                overlay?.classList.add('hidden');
                menuButton?.setAttribute('aria-expanded', 'false');
            }

            function closeNotifications() {
                notificationPanel?.classList.add('hidden');
                notificationButton?.setAttribute('aria-expanded', 'false');
            }

            menuButton?.addEventListener('click', openSidebar);
            closeButton?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);

            desktopToggle?.addEventListener('click', function () {
                body.classList.toggle('logistics-sidebar-collapsed');
            });

            document.querySelectorAll('[data-logistics-sidebar-item]').forEach(function (item) {
                item.addEventListener('click', function () {
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                });
            });

            notificationButton?.addEventListener('click', function (event) {
                event.stopPropagation();
                notificationPanel?.classList.toggle('hidden');
                notificationButton.setAttribute(
                    'aria-expanded',
                    notificationPanel?.classList.contains('hidden') ? 'false' : 'true'
                );
            });

            notificationPanel?.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            document.addEventListener('click', closeNotifications);

            logoutButton?.addEventListener('click', function () {
                logoutModal?.classList.remove('hidden');
                logoutModal?.classList.add('flex');
            });

            cancelLogout?.addEventListener('click', function () {
                logoutModal?.classList.add('hidden');
                logoutModal?.classList.remove('flex');
            });

            logoutModal?.addEventListener('click', function (event) {
                if (event.target === logoutModal) {
                    logoutModal.classList.add('hidden');
                    logoutModal.classList.remove('flex');
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1024) {
                    sidebar?.classList.remove('-translate-x-full', 'translate-x-0');
                    overlay?.classList.add('hidden');
                } else if (!sidebar?.classList.contains('translate-x-0')) {
                    sidebar?.classList.add('-translate-x-full');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
