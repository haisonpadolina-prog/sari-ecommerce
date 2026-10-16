@php
    $logisticsSidebarId = (int) session('logistics_account_id', 0);

    $logisticsSidebarAccount = $logisticsSidebarId > 0
        ? \App\Models\Accounts\LogisticsAccount::query()->find($logisticsSidebarId)
        : null;

    $logisticsSidebarName = $logisticsSidebarAccount?->displayName()
        ?: session('logistics_name', 'SARI Logistics');

    $logisticsInitials = strtoupper(
        mb_substr(
            trim($logisticsSidebarName),
            0,
            2
        )
    ) ?: 'SL';

    $items = [
        [
            'route' => 'logistics.dashboard',
            'label' => 'Dashboard Overview',
            'match' => 'logistics.dashboard',
            'icon' => 'dashboard',
        ],

        [
            'route' => 'logistics.rider-applications',
            'label' => 'Rider Applications',
            'match' => 'logistics.rider-applications*',
            'icon' => 'user-plus',
        ],

        [
            'route' => 'logistics.rider-management',
            'label' => 'Rider Management',
            'match' => 'logistics.rider-management*',
            'icon' => 'users',
        ],

        [
            'route' => 'logistics.facilities.index',
            'label' => 'Sorting Facilities',
            'match' => 'logistics.facilities.*',
            'icon' => 'warehouse',
        ],

        [
            'route' => 'logistics.pickup-requests',
            'label' => 'Pickup Requests',
            'match' => 'logistics.pickup-requests*',
            'icon' => 'clipboard',
        ],

        [
            'route' => 'logistics.delivery-assignment',
            'label' => 'Pickup Assignment',
            'match' => 'logistics.delivery-assignment*',
            'icon' => 'assignment',
        ],

        [
            'route' => 'logistics.incoming-parcels',
            'label' => 'Incoming Parcels',
            'match' => 'logistics.incoming-parcels*',
            'icon' => 'box',
        ],

        [
            'route' => 'logistics.parcel-sorting',
            'label' => 'Parcel Sorting',
            'match' => 'logistics.parcel-sorting*',
            'icon' => 'sort',
        ],

        [
            'route' => 'logistics.hub-transfers',
            'label' => 'Hub Transfers',
            'match' => 'logistics.hub-transfers*',
            'icon' => 'transfer',
        ],

        [
            'route' => 'logistics.last-mile-assignment',
            'label' => 'Last Mile Assignment',
            'match' => 'logistics.last-mile-assignment*',
            'icon' => 'route',
        ],

        [
            'route' => 'logistics.delivery-monitoring',
            'label' => 'Delivery Monitoring',
            'match' => 'logistics.delivery-monitoring*',
            'icon' => 'truck',
        ],

        [
            'route' => 'logistics.reports',
            'label' => 'Reports',
            'match' => 'logistics.reports*',
            'icon' => 'chart',
        ],

        [
            'route' => 'logistics.messages',
            'label' => 'Chat / Messaging',
            'match' => 'logistics.messages*',
            'icon' => 'message',
        ],

        [
            'route' => 'logistics.account-management',
            'label' => 'Account Management',
            'match' => 'logistics.account-management*',
            'icon' => 'account',
        ],
    ];
@endphp

<aside
    id="logisticsSidebar"
    class="
        fixed inset-y-0 left-0 z-50
        flex w-[244px] max-w-[86vw]
        -translate-x-full flex-col
        border-r border-[#eee4d3]
        bg-[#fffdf8]
        transition-all duration-300 ease-out
        lg:translate-x-0
    "
>
    {{-- =====================================================
        BRAND
    ====================================================== --}}
    <div
        id="logisticsSidebarBrand"
        class="
            relative flex min-h-[74px]
            items-center justify-center
            border-b border-[#eee4d3]
            px-3
        "
    >
        <a
            id="logisticsSidebarLogo"
            href="{{ route('logistics.dashboard') }}"
            class="relative flex items-center justify-center"
            aria-label="SARI Logistics Dashboard"
            title="SARI Logistics Dashboard"
        >
            <span
                id="logisticsSidebarLogoStage"
                class="relative block h-[46px] overflow-visible"
            >
                <img
                    id="logisticsSidebarLogoFull"
                    src="{{ asset('images/sari-logo.png') }}"
                    alt="SARI"
                    draggable="false"
                    class="
                        absolute left-1/2 top-1/2
                        h-auto w-[104px] max-w-none
                        -translate-x-1/2 -translate-y-1/2
                        object-contain brightness-0
                        select-none
                    "
                >

                <img
                    id="logisticsSidebarLogoCompact"
                    src="{{ asset('images/sari-main-logo.png') }}"
                    alt="SARI"
                    draggable="false"
                    class="
                        pointer-events-none
                        absolute left-1/2 top-1/2
                        h-[40px] w-[40px] max-w-none
                        -translate-x-1/2 -translate-y-1/2
                        object-contain opacity-0
                        select-none
                    "
                >
            </span>
        </a>

        <button
            id="logisticsSidebarClose"
            type="button"
            class="
                absolute right-3 top-1/2
                grid h-8 w-8
                -translate-y-1/2
                place-items-center rounded-xl
                text-[#6e6558]
                transition-colors duration-150
                hover:bg-[#f7eedf]
                hover:text-[#b97805]
                lg:hidden
            "
            aria-label="Close logistics sidebar"
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[17px] w-[17px]"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>
        </button>
    </div>

    {{-- =====================================================
        NAVIGATION
    ====================================================== --}}
    <nav
        id="logisticsSidebarNav"
        class="flex-1 overflow-y-auto px-2.5 py-3"
    >

        <p
            class="
                logistics-sidebar-section-label
                mb-1 px-2.5 pt-1
                text-[7.5px] font-bold
                uppercase tracking-[.14em]
                text-[#a29787]
            "
        >
            Main Menu
        </p>

        @foreach($items as $index => $item)

            @if($index === 1)
                <div class="my-2 border-t border-[#eee4d3]"></div>

                <p
                    class="
                        logistics-sidebar-section-label
                        mb-1 px-2.5
                        text-[7.5px] font-bold
                        uppercase tracking-[.14em]
                        text-[#a29787]
                    "
                >
                    Riders & Network
                </p>
            @endif

            @if($index === 4)
                <div class="my-2 border-t border-[#eee4d3]"></div>

                <p
                    class="
                        logistics-sidebar-section-label
                        mb-1 px-2.5
                        text-[7.5px] font-bold
                        uppercase tracking-[.14em]
                        text-[#a29787]
                    "
                >
                    Parcel Operations
                </p>
            @endif

            @if($index === 11)
                <div class="my-2 border-t border-[#eee4d3]"></div>

                <p
                    class="
                        logistics-sidebar-section-label
                        mb-1 px-2.5
                        text-[7.5px] font-bold
                        uppercase tracking-[.14em]
                        text-[#a29787]
                    "
                >
                    Management
                </p>
            @endif

            <a
                href="{{ route($item['route']) }}"
                title="{{ $item['label'] }}"
                data-logistics-sidebar-item
                class="
                    logistics-sidebar-item
                    mb-[2px]
                    flex min-h-[38px]
                    items-center gap-2.5
                    rounded-[9px]
                    px-2.5 py-[7px]
                    text-[10.5px]
                    transition-all duration-150

                    {{
                        request()->routeIs($item['match'])
                            ? 'bg-[#d9930a] font-semibold text-white shadow-[0_5px_12px_rgba(217,147,10,0.14)]'
                            : 'font-medium text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]'
                    }}
                "
            >
                <span
                    class="
                        grid h-[15px] w-[15px]
                        shrink-0 place-items-center
                    "
                >
                    @switch($item['icon'])

                        @case('dashboard')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 11.5 12 4l9 7.5"></path>
                                <path d="M5.5 10v10h13V10"></path>
                                <path d="M9.5 20v-6h5v6"></path>
                            </svg>
                            @break

                        @case('user-plus')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="9" cy="8" r="3"></circle>
                                <path d="M3 20c.6-4 2.8-6 6-6"></path>
                                <path d="M17 8h4"></path>
                                <path d="M19 6v4"></path>
                            </svg>
                            @break

                        @case('users')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="8" cy="8" r="3"></circle>
                                <circle cx="17" cy="9" r="2.5"></circle>
                                <path d="M2.5 20c.5-4 2.5-6 5.5-6"></path>
                                <path d="M13 15c3.2 0 5.2 1.7 5.7 5"></path>
                            </svg>
                            @break

                        @case('warehouse')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 21h18"></path>
                                <path d="M5 21V8l7-4 7 4v13"></path>
                                <path d="M8 12h3"></path>
                                <path d="M13 12h3"></path>
                                <path d="M8 16h8"></path>
                            </svg>
                            @break

                        @case('clipboard')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="5" y="4" width="14" height="17" rx="2"></rect>
                                <path d="M9 4V2h6v2"></path>
                                <path d="M8 10h8"></path>
                                <path d="M8 14h6"></path>
                            </svg>
                            @break

                        @case('assignment')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 6h9"></path>
                                <path d="M4 12h7"></path>
                                <path d="M4 18h5"></path>
                                <circle cx="17" cy="14" r="3"></circle>
                                <path d="m19.5 16.5 2 2"></path>
                            </svg>
                            @break

                        @case('box')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m4 7 8-4 8 4-8 4-8-4Z"></path>
                                <path d="M4 7v10l8 4 8-4V7"></path>
                                <path d="M12 11v10"></path>
                            </svg>
                            @break

                        @case('sort')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7h10"></path>
                                <path d="M4 12h7"></path>
                                <path d="M4 17h4"></path>
                                <path d="m17 6 3 3-3 3"></path>
                                <path d="M20 9h-6"></path>
                            </svg>
                            @break

                        @case('transfer')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 7h12"></path>
                                <path d="m13 4 3 3-3 3"></path>
                                <path d="M20 17H8"></path>
                                <path d="m11 14-3 3 3 3"></path>
                            </svg>
                            @break

                        @case('route')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="6" cy="18" r="2"></circle>
                                <circle cx="18" cy="6" r="2"></circle>
                                <path d="M8 18h3a3 3 0 0 0 3-3V9a3 3 0 0 1 3-3"></path>
                            </svg>
                            @break

                        @case('truck')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 6h11v10H3z"></path>
                                <path d="M14 10h4l3 3v3h-7"></path>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="18" cy="18" r="2"></circle>
                            </svg>
                            @break

                        @case('chart')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 20V10"></path>
                                <path d="M10 20V4"></path>
                                <path d="M16 20v-7"></path>
                                <path d="M22 20H2"></path>
                            </svg>
                            @break

                        @case('message')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                                <path d="M8 10h8"></path>
                                <path d="M8 14h5"></path>
                            </svg>
                            @break

                        @case('account')
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="8" r="3.5"></circle>
                                <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
                            </svg>
                            @break

                    @endswitch
                </span>

                <span
                    class="
                        logistics-sidebar-label
                        whitespace-nowrap
                    "
                >
                    {{ $item['label'] }}
                </span>
            </a>

        @endforeach

        <div class="my-2 border-t border-[#eee4d3]"></div>

        <button
            id="logisticsLogoutButton"
            type="button"
            title="Logout"
            data-logistics-sidebar-item
            class="
                logistics-sidebar-item
                flex min-h-[38px] w-full
                items-center gap-2.5
                rounded-[9px]
                px-2.5 py-[7px]
                text-[10.5px] font-medium
                text-[#514b42]
                transition-all duration-150
                hover:bg-red-50
                hover:text-red-600
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[15px] w-[15px] shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M10 17l5-5-5-5"></path>
                <path d="M15 12H3"></path>
                <path d="M14 3h7v18h-7"></path>
            </svg>

            <span class="logistics-sidebar-label whitespace-nowrap">
                Logout
            </span>
        </button>

    </nav>

    {{-- =====================================================
        PROFILE
    ====================================================== --}}
    <div
        id="logisticsSidebarProfile"
        class="
            border-t border-[#eee4d3]
            bg-[#fffdf8]
            px-2.5 py-2.5
        "
    >
        <a
            href="{{ route('logistics.account-management') }}"
            title="{{ $logisticsSidebarName }}"
            data-logistics-sidebar-item
            class="
                flex w-full items-center gap-2
                rounded-[10px]
                border border-[#eadfca]
                bg-white p-2.5
                transition
                hover:border-[#dbc396]
                hover:bg-[#fffaf5]
            "
        >
            <div
                class="
                    grid h-8 w-8 shrink-0
                    place-items-center
                    rounded-full
                    bg-[#d9930a]
                    text-[9.5px]
                    font-semibold text-white
                "
            >
                {{ $logisticsInitials }}
            </div>

            <div
                id="logisticsSidebarProfileText"
                class="logistics-sidebar-label min-w-0 flex-1"
            >
                <p
                    class="
                        truncate
                        text-[10px]
                        font-semibold
                        text-[#211d17]
                    "
                >
                    {{ $logisticsSidebarName }}
                </p>

                <p
                    class="
                        mt-0.5 truncate
                        text-[8px]
                        text-[#8d8272]
                    "
                >
                    Logistics Manager
                </p>
            </div>

            <svg
                viewBox="0 0 24 24"
                class="
                    logistics-sidebar-extra
                    h-[13px] w-[13px]
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