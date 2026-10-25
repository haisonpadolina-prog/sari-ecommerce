
@php
    $isHubOperator = (bool) session('is_logistics_hub_operator', false);

    $providerId = (int) session('logistics_account_id', 0);

    $provider = $providerId > 0
        ? \App\Models\Accounts\LogisticsAccount::query()->find($providerId)
        : null;

    $profileName = $isHubOperator
        ? session('logistics_name', 'Hub Operator')
        : (
            $provider?->displayName()
            ?: session('logistics_name', 'SARI Logistics')
        );

    $operatingFacility = \App\Support\CurrentLogisticsHub::operatingFacility(request());

    $profileSubtitle = $isHubOperator
        ? session('logistics_hub_name', 'Assigned Logistics Hub')
        : ($operatingFacility?->name ?: 'Logistics Manager');

    $profileRoute = $isHubOperator
        ? 'logistics.dashboard'
        : 'logistics.account-management';

    $initials = mb_strtoupper(
        mb_substr(trim((string) $profileName), 0, 2)
    ) ?: 'SL';

    /*
    |--------------------------------------------------------------------------
    | Main Logistics Navigation
    |--------------------------------------------------------------------------
    */

    $managerSections = [
        [
            'label' => 'Main Menu',
            'items' => [
                [
                    'logistics.dashboard',
                    'Dashboard Overview',
                    'logistics.dashboard',
                    'dashboard',
                ],

                [
                    'logistics.branch.select',
                    'Switch Operating Branch',
                    'logistics.branch.*',
                    'warehouse',
                ],
            ],
        ],

        [
            'label' => 'Riders & Network',
            'items' => [
                [
                    'logistics.rider-applications',
                    'Rider Applications',
                    'logistics.rider-applications*',
                    'user-plus',
                ],

                [
                    'logistics.rider-management',
                    'Rider Management',
                    'logistics.rider-management*',
                    'users',
                ],

                [
                    'logistics.facilities.index',
                    'Sorting Facilities',
                    'logistics.facilities.*',
                    'warehouse',
                ],
            ],
        ],

        [
            'label' => 'Parcel Operations',
            'items' => [
                [
                    'logistics.parcel-scanner',
                    'Parcel QR Scanner',
                    'logistics.parcel-scanner*',
                    'scan',
                ],

                [
                    'logistics.pickup-requests',
                    'Pickup Requests',
                    'logistics.pickup-requests*',
                    'clipboard',
                ],

                [
                    'logistics.delivery-assignment',
                    'Pickup Assignment',
                    'logistics.delivery-assignment*',
                    'assignment',
                ],

                [
                    'logistics.incoming-parcels',
                    'Incoming Parcels',
                    'logistics.incoming-parcels*',
                    'box',
                ],

                [
                    'logistics.parcel-sorting',
                    'Parcel Sorting',
                    'logistics.parcel-sorting*',
                    'sort',
                ],

                [
                    'logistics.hub-transfers',
                    'Hub Transfers',
                    'logistics.hub-transfers*',
                    'transfer',
                ],

                [
                    'logistics.remittances.index',
                    'Rider COD Cash Intake',
                    'logistics.remittances*',
                    'cash',
                ],

                [
                    'logistics.branch-treasury.index',
                    'Branch Treasury',
                    'logistics.branch-treasury*',
                    'treasury',
                ],

                [
                    'logistics.last-mile-assignment',
                    'Last Mile Assignment',
                    'logistics.last-mile-assignment*',
                    'route',
                ],

                [
                    'logistics.delivery-monitoring',
                    'Delivery Monitoring',
                    'logistics.delivery-monitoring*',
                    'truck',
                ],
            ],
        ],

        [
            'label' => 'Management',
            'items' => [
                [
                    'logistics.reports',
                    'Reports',
                    'logistics.reports*',
                    'chart',
                ],

                [
                    'logistics.messages',
                    'Chat / Messaging',
                    'logistics.messages*',
                    'message',
                ],

                [
                    'logistics.account-management',
                    'Account Management',
                    'logistics.account-management*',
                    'account',
                ],

                [
                    'logistics.activity-logs',
                    'Activity Logs',
                    'logistics.activity-logs*',
                    'history',
                ],
            ],
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Hub Operator Navigation
    |--------------------------------------------------------------------------
    |
    | Hub Operators can operate their assigned municipal hub.
    | Network administration remains Manager-only.
    |
    */

    $hubOperatorSections = [
        [
            'label' => 'Main Menu',
            'items' => [
                [
                    'logistics.dashboard',
                    'Hub Dashboard',
                    'logistics.dashboard',
                    'dashboard',
                ],
            ],
        ],

        [
            'label' => 'Local Riders',
            'items' => [
                [
                    'logistics.rider-management',
                    'Rider Management',
                    'logistics.rider-management*',
                    'users',
                ],
            ],
        ],

        [
            'label' => 'Origin Hub Operations',
            'items' => [
                [
                    'logistics.parcel-scanner',
                    'Parcel QR Scanner',
                    'logistics.parcel-scanner*',
                    'scan',
                ],

                [
                    'logistics.pickup-requests',
                    'Pickup Requests',
                    'logistics.pickup-requests*',
                    'clipboard',
                ],

                [
                    'logistics.delivery-assignment',
                    'Pickup Assignment',
                    'logistics.delivery-assignment*',
                    'assignment',
                ],

                [
                    'logistics.incoming-parcels',
                    'Incoming Parcels',
                    'logistics.incoming-parcels*',
                    'box',
                ],

                [
                    'logistics.parcel-sorting',
                    'Parcel Sorting',
                    'logistics.parcel-sorting*',
                    'sort',
                ],
            ],
        ],

        [
            'label' => 'Transfers & Last Mile',
            'items' => [
                [
                    'logistics.hub-transfers',
                    'Hub Transfers',
                    'logistics.hub-transfers*',
                    'transfer',
                ],

                [
                    'logistics.last-mile-assignment',
                    'Last Mile Assignment',
                    'logistics.last-mile-assignment*',
                    'route',
                ],

                [
                    'logistics.delivery-monitoring',
                    'Delivery Monitoring',
                    'logistics.delivery-monitoring*',
                    'truck',
                ],
            ],
        ],

        [
            'label' => 'Hub Insights',
            'items' => [
                [
                    'logistics.reports',
                    'Reports',
                    'logistics.reports*',
                    'chart',
                ],

                [
                    'logistics.activity-logs',
                    'Hub Activity',
                    'logistics.activity-logs*',
                    'history',
                ],
            ],
        ],
    ];

    $sections = $isHubOperator
        ? $hubOperatorSections
        : $managerSections;

    /*
    |--------------------------------------------------------------------------
    | Static SVG Icons
    |--------------------------------------------------------------------------
    |
    | These icon fragments are defined here, not taken from user input.
    |
    */

    $icons = [
        'dashboard' =>
            '<path d="M3 11.5 12 4l9 7.5"/>'
            .'<path d="M5.5 10v10h13V10"/>'
            .'<path d="M9.5 20v-6h5v6"/>',

        'user-plus' =>
            '<circle cx="9" cy="8" r="3"/>'
            .'<path d="M3 20c.6-4 2.8-6 6-6M17 8h4m-2-2v4"/>',

        'users' =>
            '<circle cx="8" cy="8" r="3"/>'
            .'<circle cx="17" cy="9" r="2.5"/>'
            .'<path d="M2.5 20c.5-4 2.5-6 5.5-6M13 15c3.2 0 5.2 1.7 5.7 5"/>',

        'warehouse' =>
            '<path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3m2 0h3M8 16h8"/>',

        'clipboard' =>
            '<rect x="5" y="4" width="14" height="17" rx="2"/>'
            .'<path d="M9 4V2h6v2M8 10h8m-8 4h6"/>',

        'assignment' =>
            '<path d="M4 6h9M4 12h7M4 18h5"/>'
            .'<circle cx="17" cy="14" r="3"/>'
            .'<path d="m19.5 16.5 2 2"/>',

        'box' =>
            '<path d="m4 7 8-4 8 4-8 4-8-4ZM4 7v10l8 4 8-4V7M12 11v10"/>',

        'sort' =>
            '<path d="M4 7h10M4 12h7M4 17h4m13-11 3 3-3 3M20 9h-6"/>',

        'scan' =>
            '<path d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M20 16v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3"/>'
            .'<path d="M8 8h3v3H8zM13 8h3v3h-3zM8 13h3v3H8zM13 13h3v3h-3z"/>',

        'cash' =>
            '<rect x="3" y="6" width="18" height="12" rx="2"/>'
            .'<path d="M7 10h10M7 14h5"/>',

        'treasury' =>
            '<path d="M3 9h18M5 9v9m4-9v9m6-9v9m4-9v9M3 18h18M12 3l9 4H3l9-4Z"/>',

        'transfer' =>
            '<path d="M4 7h12m-3-3 3 3-3 3M20 17H8m3-3-3 3 3 3"/>',

        'route' =>
            '<circle cx="6" cy="18" r="2"/>'
            .'<circle cx="18" cy="6" r="2"/>'
            .'<path d="M8 18h3a3 3 0 0 0 3-3V9a3 3 0 0 1 3-3"/>',

        'truck' =>
            '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/>'
            .'<circle cx="7" cy="18" r="2"/>'
            .'<circle cx="18" cy="18" r="2"/>',

        'chart' =>
            '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',

        'message' =>
            '<path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7ZM8 10h8m-8 4h5"/>',

        'account' =>
            '<circle cx="12" cy="8" r="3.5"/>'
            .'<path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"/>',

        'history' =>
            '<path d="M3 12a9 9 0 1 0 2.6-6.4"/>'
            .'<path d="M3 4v5h5M12 7v5l3 2"/>',
    ];
@endphp

<aside
    id="logisticsSidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-[244px] max-w-[86vw] -translate-x-full flex-col border-r border-[#eee4d3] bg-[#fffdf8] transition-all duration-300 ease-out lg:translate-x-0"
>
    {{-- BRAND --}}

    <div
        id="logisticsSidebarBrand"
        class="relative flex min-h-[74px] items-center justify-center border-b border-[#eee4d3] px-3"
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
                    class="absolute left-1/2 top-1/2 h-auto w-[104px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain brightness-0 select-none"
                >

                <img
                    id="logisticsSidebarLogoCompact"
                    src="{{ asset('images/sari-main-logo.png') }}"
                    alt="SARI"
                    draggable="false"
                    class="pointer-events-none absolute left-1/2 top-1/2 h-[40px] w-[40px] max-w-none -translate-x-1/2 -translate-y-1/2 object-contain opacity-0 select-none"
                >
            </span>
        </a>

        <button
            id="logisticsSidebarClose"
            type="button"
            class="absolute right-3 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-xl text-[#6e6558] transition-colors duration-150 hover:bg-[#f7eedf] hover:text-[#b97805] lg:hidden"
            aria-label="Close logistics sidebar"
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[17px] w-[17px]"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M6 6l12 12M18 6 6 18"/>
            </svg>
        </button>
    </div>

    {{-- NAVIGATION --}}

    <nav
        id="logisticsSidebarNav"
        class="flex-1 overflow-y-auto px-2.5 py-3"
    >
        @foreach($sections as $index => $section)

            @if($index > 0)
                <div class="my-2 border-t border-[#eee4d3]"></div>
            @endif

            <p class="logistics-sidebar-section-label mb-1 px-2.5 pt-1 text-[7.5px] font-bold uppercase tracking-[.14em] text-[#a29787]">
                {{ $section['label'] }}
            </p>

            @foreach($section['items'] as [$routeName, $label, $match, $icon])

                <a
                    href="{{ route($routeName) }}"
                    title="{{ $label }}"
                    data-logistics-sidebar-item
                    @if(request()->routeIs($match))
                        aria-current="page"
                    @endif
                    class="logistics-sidebar-item mb-[2px] flex min-h-[38px] items-center gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] transition-all duration-150 {{ request()->routeIs($match) ? 'bg-[#d9930a] font-semibold text-white shadow-[0_5px_12px_rgba(217,147,10,0.14)]' : 'font-medium text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
                >
                    <span
                        class="grid h-[15px] w-[15px] shrink-0 place-items-center"
                        aria-hidden="true"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-[15px] w-[15px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            {!! $icons[$icon] ?? $icons['dashboard'] !!}
                        </svg>
                    </span>

                    <span class="logistics-sidebar-label whitespace-nowrap">
                        {{ $label }}
                    </span>
                </a>

            @endforeach

        @endforeach

        <div class="my-2 border-t border-[#eee4d3]"></div>

        {{-- LOGOUT --}}

        <button
            id="logisticsLogoutButton"
            type="button"
            title="Logout"
            data-logistics-sidebar-item
            class="logistics-sidebar-item flex min-h-[38px] w-full items-center gap-2.5 rounded-[9px] px-2.5 py-[7px] text-[10.5px] font-medium text-[#514b42] transition-all duration-150 hover:bg-red-50 hover:text-red-600"
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[15px] w-[15px] shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M10 17l5-5-5-5M15 12H3M14 3h7v18h-7"/>
            </svg>

            <span class="logistics-sidebar-label whitespace-nowrap">
                Logout
            </span>
        </button>
    </nav>

    {{-- PROFILE --}}

    <div
        id="logisticsSidebarProfile"
        class="border-t border-[#eee4d3] bg-[#fffdf8] px-2.5 py-2.5"
    >
        <a
            href="{{ route($profileRoute) }}"
            title="{{ $profileName }}"
            data-logistics-sidebar-item
            class="flex w-full items-center gap-2 rounded-[10px] border border-[#eadfca] bg-white p-2.5 transition hover:border-[#dbc396] hover:bg-[#fffaf5]"
        >
            <div
                class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[9.5px] font-semibold text-white"
            >
                {{ $initials }}
            </div>

            <div
                id="logisticsSidebarProfileText"
                class="logistics-sidebar-label min-w-0 flex-1"
            >
                <p class="truncate text-[10px] font-semibold text-[#211d17]">
                    {{ $profileName }}
                </p>

                <p class="mt-0.5 truncate text-[8px] text-[#8d8272]">
                    {{ $profileSubtitle }}
                </p>
            </div>

            <svg
                viewBox="0 0 24 24"
                class="logistics-sidebar-extra h-[13px] w-[13px] shrink-0 text-[#82796b]"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="m9 18 6-6-6-6"/>
            </svg>
        </a>
    </div>
</aside>
