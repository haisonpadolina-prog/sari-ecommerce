@php
    $buyerHeaderEmail = (string) session('buyer_email', 'buyer@gmail.com');
    $buyerHeaderHandle = trim((string) \Illuminate\Support\Str::before($buyerHeaderEmail, '@'));
    $buyerHeaderName = trim((string) session('buyer_name', ''));

    if ($buyerHeaderName === '') {
        $buyerHeaderName = \Illuminate\Support\Str::of($buyerHeaderHandle ?: 'Buyer Account')
            ->replace(['.', '_', '-'], ' ')
            ->title()
            ->toString();
    }

    $buyerHeaderInitials = strtoupper(
        mb_substr(preg_replace('/[^A-Za-z0-9]/', '', $buyerHeaderName) ?: 'BU', 0, 2)
    );
@endphp

<header id="buyerHeader" class="sticky top-0 z-40 border-b border-[#eee7dc] bg-white">
    <div class="buyer-header-main flex w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-5 xl:px-6">
        {{-- LEFT --}}
        <div class="flex min-w-0 items-center gap-2.5">
            <button
                id="buyerMobileMenuButton"
                type="button"
                class="buyer-header-control grid shrink-0 place-items-center border border-[#e8dfd0] bg-white text-[#4f473c] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805] focus:outline-none focus:ring-4 focus:ring-[#d89a25]/10 lg:hidden"
                aria-label="Open buyer navigation"
                aria-controls="buyerSidebar"
                aria-expanded="false"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            <button
                id="buyerSidebarToggle"
                type="button"
                class="buyer-header-control hidden shrink-0 place-items-center border border-[#e8dfd0] bg-white text-[#4f473c] shadow-sm transition hover:border-[#d9be8c] hover:bg-[#fff9ef] hover:text-[#b97805] focus:outline-none focus:ring-4 focus:ring-[#d89a25]/10 lg:grid"
                aria-label="Collapse sidebar"
                aria-controls="buyerSidebar"
                aria-expanded="true"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            <div class="min-w-0">
                <h1 class="truncate font-bold tracking-[-0.025em] text-[#17140e]">
                    @yield('page-title', 'Buyer Home')
                </h1>

                <div class="buyer-header-breadcrumb mt-1 hidden items-center gap-1.5 text-[#978d7d] sm:flex">
                    <a
                        id="buyerHeaderBreadcrumbName"
                        href="{{ route('buyer.home') }}"
                        wire:navigate.hover
                        class="max-w-[150px] truncate transition hover:text-[#b97805]"
                    >
                        Buyer
                    </a>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                    <span class="truncate font-medium text-[#5c5448]">@yield('page-title', 'Home')</span>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="flex min-w-0 items-center gap-1.5 sm:gap-2">
            <div class="relative hidden md:block">
                <svg viewBox="0 0 24 24" class="buyer-header-search-icon pointer-events-none absolute top-1/2 -translate-y-1/2 text-[#9a9184]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-4-4"></path>
                </svg>
                <input
                    id="buyerHeaderSearch"
                    type="search"
                    name="search"
                    placeholder="Search products, stores..."
                    autocomplete="off"
                    class="buyer-header-search border border-[#e6ddcf] bg-white text-[#28231c] outline-none transition placeholder:text-[#a89f92] focus:border-[#d89a25] focus:ring-4 focus:ring-[#d89a25]/10"
                >
            </div>

            <a
                id="buyerNotificationButton"
                href="{{ route('buyer.notifications') }}"
                wire:navigate.hover
                class="buyer-header-icon-button relative grid shrink-0 place-items-center text-[#475467] transition-colors hover:text-[#c9890b] focus:outline-none focus:ring-2 focus:ring-[#d59617]/20"
                aria-label="View notifications"
                title="Notifications"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>
                <span id="buyerNotificationCount" class="buyer-header-badge absolute hidden place-items-center rounded-full border-2 border-white bg-[#d9950b] font-bold text-white" aria-label="Unread notification count">0</span>
            </a>

            <a
                id="buyerCartButton"
                href="{{ route('buyer.cart') }}"
                wire:navigate.hover
                class="buyer-header-icon-button relative grid shrink-0 place-items-center text-[#475467] transition-colors hover:text-[#c9890b] focus:outline-none focus:ring-2 focus:ring-[#d59617]/20"
                aria-label="View cart"
                title="Shopping Cart"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M3 4h2l2 12h10l2-8H7"></path>
                    <circle cx="9" cy="20" r="1.5"></circle>
                    <circle cx="17" cy="20" r="1.5"></circle>
                </svg>
                <span id="buyerCartCount" class="buyer-header-badge absolute hidden place-items-center rounded-full border-2 border-white bg-[#d9950b] font-bold text-white" aria-label="Cart item count">0</span>
            </a>

            <div class="mx-1 hidden h-8 w-px bg-[#eee4d5] xl:block"></div>

            <a
                id="buyerProfileButton"
                href="{{ route('buyer.account') }}"
                wire:navigate.hover
                class="buyer-header-profile hidden items-center transition hover:bg-[#fff9ef] focus:outline-none focus:ring-4 focus:ring-[#d89a25]/10 xl:flex"
                aria-label="Open Account Management"
                title="Account Management"
            >
                <span class="buyer-shell-avatar relative grid shrink-0 place-items-center overflow-hidden rounded-full bg-[#d9950b] font-bold text-white">
                    <img id="buyerHeaderProfileImage" src="" alt="Buyer profile photo" class="hidden h-full w-full object-cover">
                    <span id="buyerHeaderProfileInitials">{{ $buyerHeaderInitials ?: 'BU' }}</span>
                </span>

                <span class="min-w-0">
                    <span id="buyerHeaderProfileName" class="block max-w-[145px] truncate font-semibold text-[#28231c]">{{ $buyerHeaderName ?: 'Buyer Account' }}</span>
                    <span
                        id="buyerHeaderProfileEmail"
                        data-buyer-login-email="{{ $buyerHeaderEmail }}"
                        class="mt-0.5 block max-w-[145px] truncate text-[#908779]"
                    >{{ $buyerHeaderEmail }}</span>
                </span>
            </a>
        </div>
    </div>

    {{-- MOBILE SEARCH --}}
    <div class="buyer-mobile-search border-t border-[#f0e8dc] px-4 py-2.5 md:hidden">
        <div class="relative">
            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9a9184]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m20 20-4-4"></path>
            </svg>
            <input
                id="buyerMobileSearch"
                type="search"
                name="mobile_search"
                placeholder="Search products..."
                autocomplete="off"
                class="h-10 w-full rounded-[11px] border border-[#e6dfd5] bg-white pl-10 pr-4 text-[10px] text-[#28231c] outline-none transition placeholder:text-[#a89f92] focus:border-[#d89a25] focus:ring-4 focus:ring-[#d89a25]/10"
            >
        </div>
    </div>
</header>
