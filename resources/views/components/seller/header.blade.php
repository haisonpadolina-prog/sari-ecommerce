<header class="sticky top-0 z-40 border-b border-[#eee4d3] bg-[#fffdf9]/95 backdrop-blur-md">
    <div class="flex min-h-[86px] w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 xl:px-10">

        <div class="flex min-w-0 items-center gap-3 sm:gap-4">
            <button
                id="sellerMenuButton"
                type="button"
                class="
                    grid h-11 w-11 shrink-0 place-items-center
                    rounded-xl border border-[#e8dfd0] bg-white
                    text-[#4f473c] shadow-sm transition
                    hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]
                    lg:hidden
                "
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            <button
                id="sellerSidebarToggle"
                type="button"
                class="
                    hidden h-11 w-11 shrink-0 place-items-center
                    rounded-xl border border-[#e8dfd0] bg-white
                    text-[#4f473c] shadow-sm transition-all duration-200
                    hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]
                    active:scale-95 lg:grid
                "
                aria-label="Collapse sidebar"
                aria-expanded="true"
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            <div class="min-w-0">
                <h1 class="truncate text-[18px] font-bold tracking-[-0.025em] text-[#17140e] sm:text-[20px] xl:text-[22px]">
                    @yield('page-title', 'Seller Dashboard')
                </h1>

                <div class="mt-1 hidden items-center gap-1.5 text-[11px] text-[#978d7d] sm:flex">
                    <a href="{{ route('seller.dashboard') }}" class="transition hover:text-[#b97805]">Seller</a>
                    <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                    <span class="font-medium text-[#5c5448]">@yield('page-title', 'Dashboard')</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="relative hidden md:block">
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-[#9a9184]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-4-4"></path>
                </svg>

                <input
                    type="search"
                    placeholder="Search orders, products..."
                    class="
                        h-11 w-[220px] rounded-[14px]
                        border border-[#e6ddcf] bg-white
                        pl-11 pr-4 text-[11px] outline-none
                        placeholder:text-[#a89f92]
                        focus:border-[#d89a25] focus:ring-4 focus:ring-[#d89a25]/10
                        xl:w-[300px]
                    "
                >
            </div>

            <button
                type="button"
                class="
                    relative grid h-11 w-11 place-items-center
                    rounded-xl border border-[#e8dfd0] bg-white
                    text-[#443d33] shadow-sm transition
                    hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805]
                "
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>
                <span class="absolute -right-1.5 -top-1.5 grid h-[21px] min-w-[21px] place-items-center rounded-full border-2 border-[#fffdf9] bg-[#d9930a] px-1 text-[9px] font-bold text-white">
                    5
                </span>
            </button>

            <div class="hidden h-9 w-px bg-[#eee4d5] xl:block"></div>

            <a href="{{ route('seller.account') }}" class="hidden items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-[#fff7e9] xl:flex">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-[#d9930a] text-[11px] font-bold text-white">SS</div>
                <div>
                    <p class="text-[11px] font-semibold text-[#28231c]">SARI Seller</p>
                    <p class="mt-0.5 text-[9px] text-[#908779]">Verified Store</p>
                </div>
            </a>
        </div>
    </div>
</header>
