@php
    /*
    |--------------------------------------------------------------------------
    | ZERO-REQUEST BUYER SIDEBAR LOGO
    |--------------------------------------------------------------------------
    | The Admin looks stable mainly because its sidebar stays mounted.
    | Buyer now does the same AND embeds the logo directly so the browser
    | never needs a separate logo HTTP request on the first Buyer paint.
    */
    $buyerSidebarLogoPath =
        public_path('images/sari-logo.png');

    $buyerSidebarLogoSrc =
        asset('images/sari-logo.png');

    if (is_file($buyerSidebarLogoPath)) {
        try {
            $buyerSidebarLogoMime =
                function_exists('mime_content_type')
                    ? mime_content_type($buyerSidebarLogoPath)
                    : 'image/png';

            $buyerSidebarLogoMime =
                $buyerSidebarLogoMime ?: 'image/png';

            $buyerSidebarLogoSrc =
                'data:'
                . $buyerSidebarLogoMime
                . ';base64,'
                . base64_encode(
                    file_get_contents(
                        $buyerSidebarLogoPath
                    )
                );
        } catch (\Throwable $e) {
            $buyerSidebarLogoSrc =
                asset('images/sari-logo.png');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ZERO-REQUEST COMPACT BUYER LOGO
    |--------------------------------------------------------------------------
    */
    $buyerSidebarCompactLogoPath =
        public_path('images/sari-main-logo.png');

    $buyerSidebarCompactLogoSrc =
        asset('images/sari-main-logo.png');

    if (is_file($buyerSidebarCompactLogoPath)) {
        try {
            $buyerSidebarCompactLogoMime =
                function_exists('mime_content_type')
                    ? mime_content_type($buyerSidebarCompactLogoPath)
                    : 'image/png';

            $buyerSidebarCompactLogoMime =
                $buyerSidebarCompactLogoMime ?: 'image/png';

            $buyerSidebarCompactLogoSrc =
                'data:'
                . $buyerSidebarCompactLogoMime
                . ';base64,'
                . base64_encode(
                    file_get_contents(
                        $buyerSidebarCompactLogoPath
                    )
                );
        } catch (\Throwable $e) {
            $buyerSidebarCompactLogoSrc =
                asset('images/sari-main-logo.png');
        }
    }

@endphp

<aside
    id="buyerSidebar"
    class="
        fixed
        inset-y-0
        left-0
        z-50

        flex
        -translate-x-full
        flex-col

        border-r
        border-[#eee4d3]

        bg-[#fffdf8]

        transition-transform
        duration-200
        ease-out

        lg:translate-x-0
    "
>

    {{-- ============================================================
         SIDEBAR HEADER
    ============================================================ --}}
    <div
        class="
            relative

            flex
            min-h-[110px]
            items-center
            justify-center

            border-b
            border-[#eee4d3]

            px-4
        "
    >

        {{-- ========================================================
             LOGO — INLINE DATA URI, ZERO SEPARATE IMAGE REQUEST
        ========================================================= --}}
        <a
            id="buyerSidebarLogo"
            href="{{ route('buyer.home') }}"
            wire:navigate.hover
            class="
                relative
                flex
                items-center
                justify-center
            "
            aria-label="SARI Buyer Home"
            title="SARI Buyer Home"
        >
            <span
                id="buyerSidebarLogoStage"
                class="
                    relative
                    block
                    h-[64px]
                    overflow-visible
                "
            >
                {{-- Full wordmark — expanded sidebar --}}
                <img
                    id="buyerSidebarLogoFull"
                    src="{{ $buyerSidebarLogoSrc }}"
                    alt="SARI"
                    loading="eager"
                    decoding="sync"
                    draggable="false"
                    class="
                        absolute
                        left-1/2
                        top-1/2
                        h-auto
                        w-[145px]
                        max-w-none
                        -translate-x-1/2
                        -translate-y-1/2
                        object-contain
                        brightness-0
                        select-none
                    "
                    style="
                        opacity:1;
                        visibility:visible;
                    "
                >

                {{-- Compact icon — collapsed sidebar --}}
                <img
                    id="buyerSidebarLogoCompact"
                    src="{{ $buyerSidebarCompactLogoSrc }}"
                    alt="SARI"
                    loading="eager"
                    decoding="sync"
                    draggable="false"
                    class="
                        pointer-events-none
                        absolute
                        left-1/2
                        top-1/2
                        h-[52px]
                        w-[52px]
                        max-w-none
                        -translate-x-1/2
                        -translate-y-1/2
                        object-contain
                        opacity-0
                        select-none
                    "
                    style="visibility:hidden;"
                >
            </span>
        </a>


        {{-- ========================================================
             MOBILE CLOSE
        ========================================================= --}}
        <button
            id="buyerSidebarClose"
            type="button"
            class="
                absolute
                right-4
                top-1/2

                grid
                h-10
                w-10

                -translate-y-1/2

                place-items-center

                rounded-xl

                text-[#6e6558]

                transition-colors
                duration-150

                hover:bg-[#f7eedf]
                hover:text-[#b97805]

                focus:outline-none
                focus:ring-4
                focus:ring-[#d89a25]/10

                lg:hidden
            "
            aria-label="Close buyer sidebar"
        >
            <svg
                viewBox="0 0 24 24"
                class="h-5 w-5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>
        </button>

    </div>


    {{-- ============================================================
         NAVIGATION
    ============================================================ --}}
    <nav
        id="buyerSidebarNav"
        class="
            flex-1
            space-y-1.5
            overflow-y-auto
            px-4
            py-6
        "
        data-buyer-scroll
    >

        {{-- HOME --}}
        <a
            href="{{ route('buyer.home') }}"
            wire:navigate.hover
            title="Home"
            data-buyer-sidebar-item
            class="buyer-sidebar-item 
                flex
                items-center
                gap-3

                rounded-xl

                px-4
                py-3.5

                text-[13px]

                transition-colors
                duration-150

                {{ request()->routeIs('buyer.home')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)] font-semibold'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05] font-medium' }}
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[19px] w-[19px] shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path d="M3 11.5 12 4l9 7.5"></path>
                <path d="M5.5 10v10h13V10"></path>
                <path d="M9.5 20v-6h5v6"></path>
            </svg>

            <span class="buyer-sidebar-label whitespace-nowrap">
                Home
            </span>
        </a>


        {{-- SHOP PRODUCTS --}}
        <a
            href="{{ route('buyer.products') }}"
            wire:navigate.hover
            title="Shop Products"
            data-buyer-sidebar-item
            class="buyer-sidebar-item 
                flex
                items-center
                gap-3

                rounded-xl

                px-4
                py-3.5

                text-[13px]

                transition-colors
                duration-150

                {{ request()->routeIs('buyer.products') || request()->routeIs('buyer.product.details')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)] font-semibold'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05] font-medium' }}
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[19px] w-[19px] shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path d="M5 7h14l-1 13H6L5 7Z"></path>
                <path d="M9 7a3 3 0 0 1 6 0"></path>
                <path d="M8 12h8"></path>
            </svg>

            <span class="buyer-sidebar-label whitespace-nowrap">
                Shop Products
            </span>
        </a>


        {{-- SHOPPING CART --}}
        <a
            href="{{ route('buyer.cart') }}"
            wire:navigate.hover
            title="Shopping Cart"
            data-buyer-sidebar-item
            class="buyer-sidebar-item 
                flex
                items-center
                justify-between
                gap-3

                rounded-xl

                px-4
                py-3.5

                text-[13px]

                transition-colors
                duration-150

                {{ request()->routeIs('buyer.cart')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)] font-semibold'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05] font-medium' }}
            "
        >
            <span class="buyer-sidebar-nav-main flex items-center gap-3" data-buyer-sidebar-inner>
                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M3 4h2l2 12h10l2-8H7"></path>
                    <circle cx="9" cy="20" r="1.5"></circle>
                    <circle cx="17" cy="20" r="1.5"></circle>
                </svg>

                <span class="buyer-sidebar-label whitespace-nowrap">
                    Shopping Cart
                </span>
            </span>

            <span
                id="buyerSidebarCartCount"
                class="
                    buyer-sidebar-extra
                    hidden
                    min-w-[20px]
                    place-items-center
                    rounded-full
                    bg-[#d9930a]
                    px-1.5
                    py-0.5
                    text-[8px]
                    font-bold
                    text-white
                "
                aria-label="Cart item count"
            >
                0
            </span>
        </a>


        {{-- REWARDS / VOUCHERS --}}
        <a
            href="{{ route('buyer.rewards') }}"
            wire:navigate.hover
            title="Rewards & Vouchers"
            data-buyer-sidebar-item
            class="buyer-sidebar-item 
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3.5
                text-[13px]
                transition-colors
                duration-150

                {{ request()->routeIs('buyer.rewards')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)] font-semibold'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05] font-medium' }}
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[19px] w-[19px] shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path d="M20 12a2 2 0 0 0 0-4h-2.2a3.8 3.8 0 0 0-5.8-3 3.8 3.8 0 0 0-5.8 3H4a2 2 0 0 0 0 4h16Z"></path>
                <path d="M12 5v15"></path>
                <path d="M4 12h16v7H4z"></path>
            </svg>

            <span class="buyer-sidebar-label whitespace-nowrap">
                Rewards / Vouchers
            </span>
        </a>


        {{-- MY ORDERS --}}
        <a
            href="{{ route('buyer.orders') }}"
            wire:navigate.hover
            title="My Orders"
            data-buyer-sidebar-item
            class="buyer-sidebar-item 
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3.5
                text-[13px]
                transition-colors
                duration-150

                {{ request()->routeIs('buyer.orders')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)] font-semibold'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05] font-medium' }}
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[19px] w-[19px] shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path d="M5 7h14l-1 13H6L5 7Z"></path>
                <path d="M9 7a3 3 0 0 1 6 0"></path>
            </svg>

            <span class="buyer-sidebar-label whitespace-nowrap">
                My Orders
            </span>
        </a>


        {{-- CHAT / MESSAGING --}}
        <a
            href="{{ route('buyer.messages') }}"
            wire:navigate.hover
            title="Chat / Messaging"
            data-buyer-sidebar-item
            class="buyer-sidebar-item 
                flex
                items-center
                justify-between
                gap-3
                rounded-xl
                px-4
                py-3.5
                text-[13px]
                transition-colors
                duration-150

                {{ request()->routeIs('buyer.messages')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)] font-semibold'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05] font-medium' }}
            "
        >
            <span class="buyer-sidebar-nav-main flex items-center gap-3" data-buyer-sidebar-inner>
                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                    <path d="M8 10h8"></path>
                </svg>

                <span class="buyer-sidebar-label whitespace-nowrap">
                    Chat / Messaging
                </span>
            </span>

            <span
                id="buyerSidebarMessageCount"
                class="
                    buyer-sidebar-extra
                    hidden
                    min-w-[20px]
                    place-items-center
                    rounded-full
                    bg-[#d9930a]
                    px-1.5
                    py-0.5
                    text-[8px]
                    font-bold
                    text-white
                "
                aria-label="Unread message count"
            >
                0
            </span>
        </a>


        {{-- ACCOUNT MANAGEMENT --}}
        <a
            href="{{ route('buyer.account') }}"
            wire:navigate.hover
            title="Account Management"
            data-buyer-sidebar-item
            class="buyer-sidebar-item 
                flex
                items-center
                gap-3
                rounded-xl
                px-4
                py-3.5
                text-[13px]
                transition-colors
                duration-150

                {{ request()->routeIs('buyer.account')
                    ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)] font-semibold'
                    : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05] font-medium' }}
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-[19px] w-[19px] shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <circle cx="12" cy="8" r="3.5"></circle>
                <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
            </svg>

            <span class="buyer-sidebar-label whitespace-nowrap">
                Account Management
            </span>
        </a>

    </nav>


    {{-- ============================================================
         BOTTOM AREA — LOGOUT
    ============================================================ --}}
    <div
        id="buyerSidebarProfile"
        class="
            border-t
            border-[#eee4d3]

            bg-[#fffdf8]

            p-4

            transition-colors
            duration-150
        "
    >
        <form
            id="buyerLogoutForm"
            method="POST"
            action="{{ route('buyer.logout') }}"
        >
            @csrf

            <button
                type="submit"
                title="Logout"
                data-buyer-sidebar-item
                class="buyer-sidebar-item 
                    flex
                    w-full
                    items-center
                    gap-3

                    rounded-xl

                    px-4
                    py-3.5

                    text-left
                    text-[13px]
                    font-medium
                    text-[#514b42]

                    transition-colors
                    duration-150

                    hover:bg-red-50
                    hover:text-red-600

                    focus:outline-none
                    focus:ring-4
                    focus:ring-red-500/10
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-[19px] w-[19px] shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M14 3h7v18h-7"></path>
                </svg>

                <span class="buyer-sidebar-label whitespace-nowrap">
                    Logout
                </span>
            </button>
        </form>
    </div>

</aside>
