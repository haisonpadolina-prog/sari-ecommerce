<header id="logisticsHeader" class="sticky top-0 z-40 border-b border-[#eee4d3] bg-[#fffdf9]/95 backdrop-blur-md">
    <div class="flex min-h-[86px] w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="flex min-w-0 items-center gap-3 sm:gap-4">
            <button id="logisticsMenuButton" type="button" class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-[#e8dfd0] bg-white text-[#4f473c] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805] lg:hidden" aria-label="Open logistics menu" aria-expanded="false">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>
            </button>

            <button id="logisticsSidebarToggle" type="button" class="hidden h-11 w-11 shrink-0 place-items-center rounded-xl border border-[#e8dfd0] bg-white text-[#4f473c] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805] lg:grid" aria-label="Toggle logistics sidebar">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-[18px] font-bold leading-tight tracking-[-0.025em] text-[#17140e] sm:text-[20px] xl:text-[22px]">
                    @yield('page-title', 'Dashboard Overview')
                </h1>
                <div class="mt-1 hidden items-center gap-1.5 text-[11px] leading-4 text-[#978d7d] sm:flex">
                    <a href="{{ route('logistics.dashboard') }}" class="transition hover:text-[#b97805]">Logistics</a>
                    <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                    <span class="truncate font-medium text-[#5c5448]">@yield('page-title', 'Dashboard')</span>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2 sm:gap-3">
            <div class="hidden rounded-xl border border-[#e8dfd0] bg-white px-4 py-2.5 text-[10px] font-medium text-[#8b8174] md:block">
                Live database operations
            </div>

            <div class="relative">
                <button id="logisticsNotificationButton" type="button" class="grid h-11 w-11 place-items-center rounded-xl border border-[#e8dfd0] bg-white text-[#443d33] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]" aria-label="Open notifications" aria-expanded="false">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg>
                </button>

                <div id="logisticsNotificationPanel" class="absolute right-0 top-[calc(100%+10px)] hidden w-[300px] overflow-hidden rounded-2xl border border-[#e9dfd0] bg-white shadow-[0_18px_50px_rgba(76,58,34,0.14)] sm:w-[340px]">
                    <div class="border-b border-[#eee4d3] bg-[#fffdf8] px-4 py-3.5">
                        <p class="text-[13px] font-bold text-[#302a22]">Notifications</p>
                        <p class="mt-0.5 text-[10px] text-[#978d7d]">Current items that need Logistics attention.</p>
                    </div>
                    @php
                        $logisticsHeaderAlerts = [
                            ['Pending Rider Applications', \App\Models\RegistrationApplication::query()->where('role','rider')->where('status','pending')->count(), 'logistics.rider-applications'],
                            ['Pickup Requests', \App\Models\MarketplaceOrder::query()->where('status','ready_for_pickup')->whereNull('courier_email')->count(), 'logistics.pickup-requests'],
                            ['Parcels to Sort', \App\Models\LogisticsParcel::query()->where('status','received')->count(), 'logistics.parcel-sorting'],
                            ['Unread Rider Messages', \App\Models\LogisticsMessage::query()->where('sender_role','rider')->whereNull('read_at')->count(), 'logistics.messages'],
                        ];
                    @endphp
                    <div class="divide-y divide-[#eee4d3]">
                        @foreach($logisticsHeaderAlerts as [$label,$count,$routeName])
                            <a href="{{ route($routeName) }}" class="flex items-center justify-between gap-3 px-4 py-3 text-[9px] transition hover:bg-[#fff9ef]">
                                <span class="text-[#51483d]">{{ $label }}</span>
                                <strong class="rounded-full bg-[#f9f1e3] px-2 py-1 text-[8px] text-[#a96e05]">{{ $count }}</strong>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <a href="{{ route('logistics.account-management') }}" class="hidden items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-[#fff7e9] xl:flex">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[11px] font-bold text-white">SL</div>
                <div class="min-w-0">
                    <p class="truncate text-[11px] font-semibold text-[#28231c]">SARI Logistics</p>
                    <p class="mt-0.5 text-[10px] text-[#908779]">Logistics Manager</p>
                </div>
            </a>
        </div>
    </div>
</header>
