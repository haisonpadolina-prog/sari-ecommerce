@php
    $items = [
        ['route' => 'logistics.dashboard', 'label' => 'Dashboard Overview', 'match' => 'logistics.dashboard', 'icon' => 'dashboard'],
        ['route' => 'logistics.rider-applications', 'label' => 'Rider Applications', 'match' => 'logistics.rider-applications', 'icon' => 'user-plus'],
        ['route' => 'logistics.rider-management', 'label' => 'Rider Management', 'match' => 'logistics.rider-management', 'icon' => 'users'],
        ['route' => 'logistics.pickup-requests', 'label' => 'Pickup Requests', 'match' => 'logistics.pickup-requests', 'icon' => 'clipboard'],
        ['route' => 'logistics.incoming-parcels', 'label' => 'Incoming Parcels', 'match' => 'logistics.incoming-parcels', 'icon' => 'box'],
        ['route' => 'logistics.parcel-sorting', 'label' => 'Parcel Sorting', 'match' => 'logistics.parcel-sorting', 'icon' => 'sort'],
        ['route' => 'logistics.delivery-assignment', 'label' => 'Delivery Assignment', 'match' => 'logistics.delivery-assignment', 'icon' => 'assignment'],
        ['route' => 'logistics.delivery-monitoring', 'label' => 'Delivery Monitoring', 'match' => 'logistics.delivery-monitoring', 'icon' => 'truck'],
        ['route' => 'logistics.reports', 'label' => 'Reports', 'match' => 'logistics.reports', 'icon' => 'chart'],
        ['route' => 'logistics.messages', 'label' => 'Chat / Messaging', 'match' => 'logistics.messages', 'icon' => 'message'],
        ['route' => 'logistics.account-management', 'label' => 'Account Management', 'match' => 'logistics.account-management', 'icon' => 'settings'],
    ];
@endphp

<aside id="logisticsSidebar" class="fixed inset-y-0 left-0 z-50 flex w-[275px] -translate-x-full flex-col border-r border-[#eee4d3] bg-[#fffdf8] lg:translate-x-0">
    <div class="relative flex min-h-[104px] items-center justify-center border-b border-[#eee4d3] px-4">
        <a id="logisticsSidebarLogo" href="{{ route('logistics.dashboard') }}" class="flex items-center justify-center" aria-label="SARI Logistics Dashboard">
            <img src="{{ asset('images/sari-logo.png') }}" alt="SARI" class="h-auto w-[145px] object-contain brightness-0">
        </a>

        <button id="logisticsSidebarClose" type="button" class="absolute right-4 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-xl text-[#6e6558] transition hover:bg-[#f7eedf] hover:text-[#b97805] lg:hidden" aria-label="Close logistics menu">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1.5 overflow-y-auto px-4 py-6">
        @foreach ($items as $item)
            <a
                href="{{ route($item['route']) }}"
                data-logistics-sidebar-item
                title="{{ $item['label'] }}"
                class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[12px] font-semibold transition-all duration-200 {{ request()->routeIs($item['match']) ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
            >
                <span class="grid h-[20px] w-[20px] shrink-0 place-items-center">
                    @switch($item['icon'])
                        @case('dashboard')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 11.5 12 4l9 7.5"></path><path d="M5.5 10v10h13V10"></path><path d="M9.5 20v-6h5v6"></path></svg>
                            @break
                        @case('user-plus')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"></circle><path d="M3 20c.6-4 2.8-6 6-6"></path><path d="M17 8h4"></path><path d="M19 6v4"></path></svg>
                            @break
                        @case('users')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="3"></circle><circle cx="17" cy="9" r="2.5"></circle><path d="M2.5 20c.5-4 2.5-6 5.5-6"></path><path d="M13 15c3.2 0 5.2 1.7 5.7 5"></path></svg>
                            @break
                        @case('clipboard')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V2h6v2"></path><path d="M8 10h8"></path><path d="M8 14h6"></path></svg>
                            @break
                        @case('box')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 7 8-4 8 4-8 4-8-4Z"></path><path d="M4 7v10l8 4 8-4V7"></path><path d="M12 11v10"></path></svg>
                            @break
                        @case('sort')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h10"></path><path d="M4 12h7"></path><path d="M4 17h4"></path><path d="m17 6 3 3-3 3"></path><path d="M20 9h-6"></path></svg>
                            @break
                        @case('assignment')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h9"></path><path d="M4 12h7"></path><path d="M4 18h5"></path><circle cx="17" cy="14" r="3"></circle><path d="m19.5 16.5 2 2"></path></svg>
                            @break
                        @case('truck')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h11v10H3z"></path><path d="M14 10h4l3 3v3h-7"></path><circle cx="7" cy="18" r="2"></circle><circle cx="18" cy="18" r="2"></circle></svg>
                            @break
                        @case('chart')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V10"></path><path d="M10 20V4"></path><path d="M16 20v-7"></path><path d="M22 20H2"></path></svg>
                            @break
                        @case('message')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path><path d="M8 10h8"></path><path d="M8 14h5"></path></svg>
                            @break
                        @default
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"></path></svg>
                    @endswitch
                </span>

                <span class="logistics-sidebar-label whitespace-nowrap">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-[#eee4d3] p-4">
        <div id="logisticsSidebarProfile" class="rounded-2xl border border-[#eee4d3] bg-white p-3 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[11px] font-bold text-white">SL</div>
                <div id="logisticsSidebarProfileText" class="min-w-0 flex-1">
                    <p class="truncate text-[11px] font-semibold text-[#28231c]">SARI Logistics</p>
                    <p class="mt-0.5 truncate text-[10px] text-[#908779]">Logistics Manager</p>
                </div>
            </div>

            <button id="logisticsLogoutButton" type="button" class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-[#eadfce] px-3 py-2.5 text-[10px] font-semibold text-[#776b5c] transition hover:bg-[#fff7e9] hover:text-[#a96e05]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M14 3h7v18h-7"></path></svg>
                <span class="logistics-sidebar-label">Logout</span>
            </button>
        </div>
    </div>
</aside>
