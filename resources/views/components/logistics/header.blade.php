@php
    $logisticsHeaderId = (int) session('logistics_account_id', 0);

    $logisticsHeaderAccount = $logisticsHeaderId > 0
        ? \App\Models\Accounts\LogisticsAccount::query()->find($logisticsHeaderId)
        : null;

    $logisticsHeaderName = $logisticsHeaderAccount?->displayName()
        ?: session('logistics_name', 'SARI Logistics');

    $logisticsHeaderInitials = strtoupper(
        mb_substr(
            trim($logisticsHeaderName),
            0,
            2
        )
    ) ?: 'SL';
@endphp

<header
    id="logisticsHeader"
    class="
        sticky top-0 z-40
        border-b border-[#eee4d3]
        bg-[#fffdf9]/95
        backdrop-blur-md
    "
>
    <div
        class="
            flex min-h-[72px] w-full
            items-center justify-between
            gap-[10px]
            px-[18px]
        "
    >
        {{-- LEFT --}}
        <div class="flex min-w-0 items-center gap-[10px]">

            <button
                id="logisticsMenuButton"
                type="button"
                class="
                    grid h-[38px] w-[38px]
                    shrink-0 place-items-center
                    rounded-[10px]
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
                aria-label="Open logistics menu"
                aria-expanded="false"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[17px] w-[17px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            <button
                id="logisticsSidebarToggle"
                type="button"
                class="
                    hidden h-[38px] w-[38px]
                    shrink-0 place-items-center
                    rounded-[10px]
                    border border-[#e8dfd0]
                    bg-white
                    text-[#4f473c]
                    shadow-sm
                    transition
                    hover:border-[#d9be8c]
                    hover:bg-[#fff9ef]
                    hover:text-[#b97805]
                    active:scale-[.97]
                    lg:grid
                "
                aria-label="Collapse sidebar"
                aria-expanded="true"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[17px] w-[17px]"
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
                        text-[17px]
                        font-bold
                        leading-[1.15]
                        tracking-[-0.025em]
                        text-[#17140e]
                    "
                >
                    @yield('page-title', 'Dashboard Overview')
                </h1>

                <div
                    class="
                        mt-[3px] hidden
                        items-center gap-[5px]
                        text-[9px]
                        text-[#978d7d]
                        sm:flex
                    "
                >
                    <a
                        href="{{ route('logistics.dashboard') }}"
                        class="transition hover:text-[#b97805]"
                    >
                        Logistics
                    </a>

                    <svg
                        viewBox="0 0 24 24"
                        class="h-[10px] w-[10px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>

                    <span
                        class="
                            truncate font-medium
                            text-[#5c5448]
                        "
                    >
                        @yield('page-title', 'Dashboard')
                    </span>
                </div>

            </div>
        </div>

        {{-- RIGHT --}}
        <div class="flex shrink-0 items-center gap-2">

            <div class="relative hidden md:block">

                <svg
                    viewBox="0 0 24 24"
                    class="
                        pointer-events-none
                        absolute left-[13px] top-1/2
                        h-[15px] w-[15px]
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
                    placeholder="Search logistics..."
                    class="
                        h-[38px] w-[220px]
                        rounded-[11px]
                        border border-[#e6ddcf]
                        bg-white
                        pl-[38px] pr-3
                        text-[10px]
                        text-[#302a22]
                        outline-none
                        placeholder:text-[#a89f92]
                        focus:border-[#d89a25]
                        focus:ring-4
                        focus:ring-[#d89a25]/10
                        lg:w-[235px]
                        xl:w-[260px]
                    "
                >
            </div>

            <div class="relative">

                <button
                    id="logisticsNotificationButton"
                    type="button"
                    class="
                        relative grid
                        h-[38px] w-[38px]
                        place-items-center
                        rounded-[10px]
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
                    aria-expanded="false"
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[17px] w-[17px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                        <path d="M10 21h4"></path>
                    </svg>
                </button>

                @php
                    $logisticsHeaderAlerts = [
                        [
                            'Pending Rider Applications',
                            \App\Models\Registration\RegistrationApplication::query()
                                ->where('role', 'rider')
                                ->where('status', 'pending')
                                ->where(function ($query) use ($logisticsHeaderId) {
                                    $query
                                        ->where(
                                            'logistics_account_id',
                                            $logisticsHeaderId
                                        )
                                        ->orWhereNull(
                                            'logistics_account_id'
                                        );
                                })
                                ->count(),
                            'logistics.rider-applications',
                        ],

                        [
                            'Pickup Requests',
                            \App\Models\Orders\MarketplaceOrder::query()
                                ->where(
                                    'logistics_account_id',
                                    $logisticsHeaderId
                                )
                                ->where(
                                    'status',
                                    'ready_for_pickup'
                                )
                                ->whereNull(
                                    'courier_email'
                                )
                                ->count(),
                            'logistics.pickup-requests',
                        ],

                        [
                            'Parcels to Sort',
                            \App\Models\Delivery\LogisticsParcel::query()
                                ->where(
                                    'status',
                                    'received'
                                )
                                ->whereHas(
                                    'order',
                                    fn ($query) =>
                                        $query->where(
                                            'logistics_account_id',
                                            $logisticsHeaderId
                                        )
                                )
                                ->count(),
                            'logistics.parcel-sorting',
                        ],
                    ];

                    $logisticsHeaderAlertCount =
                        collect($logisticsHeaderAlerts)
                            ->sum(fn ($alert) => (int) $alert[1]);
                @endphp

                @if($logisticsHeaderAlertCount > 0)
                    <span
                        class="
                            pointer-events-none
                            absolute -right-[5px] -top-[5px]
                            grid h-[18px] min-w-[18px]
                            place-items-center
                            rounded-full
                            border-2 border-[#fffdf9]
                            bg-[#d9930a]
                            px-1
                            text-[8px]
                            font-bold text-white
                        "
                    >
                        {{ $logisticsHeaderAlertCount > 99
                            ? '99+'
                            : $logisticsHeaderAlertCount
                        }}
                    </span>
                @endif

                <div
                    id="logisticsNotificationPanel"
                    class="
                        absolute right-0
                        top-[calc(100%+10px)]
                        hidden w-[310px]
                        overflow-hidden
                        rounded-[16px]
                        border border-[#e9dfd0]
                        bg-white
                        shadow-[0_18px_50px_rgba(76,58,34,0.14)]
                    "
                >
                    <div
                        class="
                            border-b border-[#eee4d3]
                            bg-[#fffdf8]
                            px-4 py-3
                        "
                    >
                        <p class="text-[11px] font-bold text-[#302a22]">
                            Notifications
                        </p>

                        <p class="mt-0.5 text-[8px] text-[#978d7d]">
                            Items requiring Logistics attention.
                        </p>
                    </div>

                    <div class="divide-y divide-[#eee4d3]">

                        @foreach($logisticsHeaderAlerts as [$label, $count, $routeName])

                            <a
                                href="{{ route($routeName) }}"
                                class="
                                    flex items-center
                                    justify-between gap-3
                                    px-4 py-3
                                    text-[8.5px]
                                    transition
                                    hover:bg-[#fff9ef]
                                "
                            >
                                <span class="text-[#51483d]">
                                    {{ $label }}
                                </span>

                                <strong
                                    class="
                                        rounded-full
                                        bg-[#f9f1e3]
                                        px-2 py-1
                                        text-[8px]
                                        text-[#a96e05]
                                    "
                                >
                                    {{ $count }}
                                </strong>
                            </a>

                        @endforeach

                    </div>
                </div>

            </div>

            <div class="hidden h-7 w-px bg-[#eee4d5] xl:block"></div>

            <a
                href="{{ route('logistics.account-management') }}"
                class="
                    hidden items-center gap-2
                    rounded-[10px]
                    px-1.5 py-1
                    transition
                    hover:bg-[#fff7e9]
                    xl:flex
                "
            >
                <div
                    class="
                        grid h-[35px] w-[35px]
                        place-items-center
                        rounded-full
                        bg-[#d9930a]
                        text-[10px]
                        font-bold text-white
                    "
                >
                    {{ $logisticsHeaderInitials }}
                </div>

                <div>
                    <p
                        class="
                            max-w-[150px] truncate
                            text-[10.5px]
                            font-semibold
                            text-[#28231c]
                        "
                    >
                        {{ $logisticsHeaderName }}
                    </p>

                    <p class="mt-0.5 text-[8px] text-[#908779]">
                        Logistics Manager
                    </p>
                </div>
            </a>

        </div>
    </div>
</header>