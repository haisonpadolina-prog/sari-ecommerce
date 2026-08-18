<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SARI Admin')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --admin-sidebar-width: 275px;
        }

        @media (min-width: 1024px) {
            #adminSidebar {
                width: var(--admin-sidebar-width);
            }

            #adminContent {
                padding-left: var(--admin-sidebar-width);
            }
        }
    </style>
</head>

<body class="m-0 min-h-screen bg-[#faf9f6] font-poppins text-[#1f1b16] antialiased">

    <div class="min-h-screen">

        @include('components.admin.sidebar')

        <div
            id="adminContent"
            class="min-h-screen transition-all duration-300 ease-out"
        >
            @include('components.admin.header')

            <main
                class="
                    w-full
                    px-4 py-5
                    sm:px-6 sm:py-6
                    lg:px-8 lg:py-7
                    xl:px-10
                "
            >
                @yield('content')
            </main>
        </div>

        <div
            id="adminSidebarOverlay"
            class="
                fixed inset-0 z-40
                hidden
                bg-black/30
                backdrop-blur-[1px]
                lg:hidden
            "
        ></div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminSidebarOverlay');

            const mobileMenuButton = document.getElementById('adminMenuButton');
            const mobileCloseButton = document.getElementById('adminSidebarClose');

            // This button is now inside the HEADER / NAVBAR.
            const desktopToggleButton = document.getElementById('adminSidebarToggle');

            const sidebarLogo = document.getElementById('adminSidebarLogo');
            const sidebarNav = document.getElementById('adminSidebarNav');
            const sidebarProfile = document.getElementById('adminSidebarProfile');

            const labels = document.querySelectorAll('.sidebar-label');
            const extras = document.querySelectorAll('.sidebar-extra');
            const sidebarItems = document.querySelectorAll('[data-sidebar-item]');
            const innerItems = document.querySelectorAll('[data-sidebar-inner]');

            let isCollapsed = false;


            /*
            |--------------------------------------------------------------------------
            | MOBILE SIDEBAR
            |--------------------------------------------------------------------------
            */

            function openSidebar() {
                sidebar?.classList.remove('-translate-x-full');
                overlay?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar?.classList.add('-translate-x-full');
                overlay?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            mobileMenuButton?.addEventListener('click', openSidebar);
            mobileCloseButton?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);


            /*
            |--------------------------------------------------------------------------
            | DESKTOP COLLAPSE / EXPAND
            |--------------------------------------------------------------------------
            */

            function setSidebarCollapsed(collapsed) {

                isCollapsed = collapsed;

                document.documentElement.style.setProperty(
                    '--admin-sidebar-width',
                    collapsed ? '88px' : '275px'
                );


                // Hide the full SARI logo when sidebar is icon-only.
                if (sidebarLogo) {
                    sidebarLogo.classList.toggle('hidden', collapsed);
                }


                // Hide text labels and badges.
                labels.forEach(function (label) {
                    label.classList.toggle('hidden', collapsed);
                });

                extras.forEach(function (extra) {
                    extra.classList.toggle('hidden', collapsed);
                });


                // Center sidebar icons when collapsed.
                sidebarItems.forEach(function (item) {

                    if (collapsed) {
                        item.style.justifyContent = 'center';
                        item.style.paddingLeft = '0';
                        item.style.paddingRight = '0';
                        item.style.columnGap = '0';
                    } else {
                        item.style.justifyContent = '';
                        item.style.paddingLeft = '';
                        item.style.paddingRight = '';
                        item.style.columnGap = '';
                    }

                });


                // Chat item has an inner wrapper.
                innerItems.forEach(function (item) {

                    if (collapsed) {
                        item.style.justifyContent = 'center';
                        item.style.columnGap = '0';
                    } else {
                        item.style.justifyContent = '';
                        item.style.columnGap = '';
                    }

                });


                if (sidebarNav) {
                    sidebarNav.style.paddingLeft = collapsed ? '12px' : '';
                    sidebarNav.style.paddingRight = collapsed ? '12px' : '';
                }


                if (sidebarProfile) {
                    sidebarProfile.style.padding = collapsed ? '12px' : '';
                }


                // Header burger stays in the navbar.
                // Only update accessibility state.
                if (desktopToggleButton) {

                    desktopToggleButton.setAttribute(
                        'aria-expanded',
                        collapsed ? 'false' : 'true'
                    );

                    desktopToggleButton.setAttribute(
                        'aria-label',
                        collapsed
                            ? 'Expand sidebar'
                            : 'Collapse sidebar'
                    );

                }

            }


            desktopToggleButton?.addEventListener('click', function () {

                if (window.innerWidth < 1024) {
                    return;
                }

                setSidebarCollapsed(!isCollapsed);

            });


            /*
            |--------------------------------------------------------------------------
            | RESPONSIVE RESET
            |--------------------------------------------------------------------------
            */

            window.addEventListener('resize', function () {

                if (window.innerWidth < 1024 && isCollapsed) {
                    setSidebarCollapsed(false);
                }

                if (window.innerWidth >= 1024) {
                    overlay?.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }

            });

        });
    </script>

    @stack('scripts')

</body>
</html>