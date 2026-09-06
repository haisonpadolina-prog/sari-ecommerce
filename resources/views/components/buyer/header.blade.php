<header
    class="
        sticky
        top-0
        z-40

        border-b
        border-[#eee4d3]

        bg-[#fffdf9]/95

        backdrop-blur-md
    "
>

    {{-- ============================================================
         HEADER MAIN ROW
    ============================================================ --}}
    <div
        class="
            flex
            min-h-[86px]
            w-full
            items-center
            justify-between
            gap-3

            px-4

            sm:px-6

            lg:px-8

            xl:px-10
        "
    >

        {{-- ========================================================
             LEFT SIDE
        ========================================================= --}}
        <div
            class="
                flex
                min-w-0
                items-center
                gap-3

                sm:gap-4
            "
        >

            {{-- ====================================================
                 MOBILE SIDEBAR BUTTON
            ===================================================== --}}
            <button
                id="buyerMobileMenuButton"
                type="button"
                class="
                    grid
                    h-11
                    w-11
                    shrink-0
                    place-items-center

                    rounded-xl

                    border
                    border-[#e8dfd0]

                    bg-white

                    text-[#4f473c]

                    shadow-sm

                    transition

                    hover:border-[#d9be8c]
                    hover:bg-[#fff9ef]
                    hover:text-[#b97805]

                    focus:outline-none
                    focus:ring-4
                    focus:ring-[#d89a25]/10

                    active:scale-95

                    lg:hidden
                "
                aria-label="Open buyer navigation"
                aria-controls="buyerSidebar"
                aria-expanded="false"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>


            {{-- ====================================================
                 DESKTOP SIDEBAR TOGGLE
            ===================================================== --}}
            <button
                id="buyerSidebarToggle"
                type="button"
                class="
                    hidden
                    h-11
                    w-11
                    shrink-0
                    place-items-center

                    rounded-xl

                    border
                    border-[#e8dfd0]

                    bg-white

                    text-[#4f473c]

                    shadow-sm

                    transition-all
                    duration-200

                    hover:border-[#d9be8c]
                    hover:bg-[#fff9ef]
                    hover:text-[#b97805]

                    focus:outline-none
                    focus:ring-4
                    focus:ring-[#d89a25]/10

                    active:scale-95

                    lg:grid
                "
                aria-label="Collapse sidebar"
                aria-controls="buyerSidebar"
                aria-expanded="true"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>


            {{-- ====================================================
                 PAGE TITLE + BREADCRUMB
            ===================================================== --}}
            <div class="min-w-0">

                <h1
                    class="
                        truncate

                        text-[18px]
                        font-bold
                        tracking-[-0.025em]
                        text-[#17140e]

                        sm:text-[20px]

                        xl:text-[22px]
                    "
                >
                    @yield('page-title', 'Buyer Home')
                </h1>


                <div
                    class="
                        mt-1

                        hidden
                        items-center
                        gap-1.5

                        text-[11px]
                        text-[#978d7d]

                        sm:flex
                    "
                >

                    <a
                        id="buyerHeaderBreadcrumbName"
                        href="{{ route('buyer.home') }}"
                        wire:navigate.hover
                        class="
                            max-w-[180px]
                            truncate
                            transition

                            hover:text-[#b97805]
                        "
                    >
                        Buyer
                    </a>


                    <svg
                        viewBox="0 0 24 24"
                        class="h-3 w-3"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>


                    <span
                        class="
                            font-medium
                            text-[#5c5448]
                        "
                    >
                        @yield('page-title', 'Home')
                    </span>

                </div>

            </div>

        </div>


        {{-- ========================================================
             RIGHT SIDE
        ========================================================= --}}
        <div
            class="
                flex
                min-w-0
                items-center
                gap-2

                sm:gap-3
            "
        >

            {{-- ====================================================
                 DESKTOP SEARCH
            ===================================================== --}}
            <div
                class="
                    relative

                    hidden

                    md:block
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        pointer-events-none

                        absolute
                        left-4
                        top-1/2

                        h-[18px]
                        w-[18px]

                        -translate-y-1/2

                        text-[#9a9184]
                    "
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <circle
                        cx="11"
                        cy="11"
                        r="7"
                    ></circle>

                    <path d="m20 20-4-4"></path>
                </svg>


                <input
                    id="buyerHeaderSearch"
                    type="search"
                    name="search"
                    placeholder="Search products, brands..."
                    autocomplete="off"
                    class="
                        h-11
                        w-[220px]

                        rounded-[14px]

                        border
                        border-[#e6ddcf]

                        bg-white

                        pl-11
                        pr-4

                        text-[10px]
                        text-[#28231c]

                        outline-none

                        transition

                        placeholder:text-[#a89f92]

                        focus:border-[#d89a25]
                        focus:ring-4
                        focus:ring-[#d89a25]/10

                        xl:w-[300px]
                    "
                >

            </div>


            {{-- ====================================================
                 CART
            ===================================================== --}}
            <a
                id="buyerCartButton"
                href="{{ route('buyer.cart') }}"
                wire:navigate.hover
                class="
                    relative

                    grid
                    h-11
                    w-11
                    shrink-0
                    place-items-center

                    rounded-xl

                    border
                    border-[#e8dfd0]

                    bg-white

                    text-[#443d33]

                    shadow-sm

                    transition

                    hover:border-[#d9be8c]
                    hover:bg-[#fff9ef]
                    hover:text-[#b97805]

                    focus:outline-none
                    focus:ring-4
                    focus:ring-[#d89a25]/10

                    active:scale-95
                "
                aria-label="View cart"
                title="Shopping Cart"
            >

                <svg
                    viewBox="0 0 24 24"
                    class="
                        h-[20px]
                        w-[20px]
                    "
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                >
                    <path d="M3 4h2l2 12h10l2-8H7"></path>

                    <circle
                        cx="9"
                        cy="20"
                        r="1.5"
                    ></circle>

                    <circle
                        cx="17"
                        cy="20"
                        r="1.5"
                    ></circle>
                </svg>


                {{-- =================================================
                     CART COUNT
                ================================================== --}}
                <span
                    id="buyerCartCount"
                    class="
                        absolute

                        -right-1.5
                        -top-1.5

                        hidden

                        h-[21px]
                        min-w-[21px]

                        place-items-center

                        rounded-full

                        border-2
                        border-[#fffdf9]

                        bg-[#d9930a]

                        px-1

                        text-[9px]
                        font-bold
                        text-white
                    "
                    aria-label="Cart item count"
                >
                    0
                </span>

            </a>


            {{-- ====================================================
                 DIVIDER
            ===================================================== --}}
            <div
                class="
                    hidden
                    h-9
                    w-px

                    bg-[#eee4d5]

                    xl:block
                "
            ></div>


            {{-- ====================================================
                 PROFILE
            ===================================================== --}}
            <a
                id="buyerProfileButton"
                href="{{ route('buyer.account') }}"
                wire:navigate.hover
                class="
                    hidden
                    items-center
                    gap-2.5

                    rounded-xl

                    px-2
                    py-1.5

                    transition

                    hover:bg-[#fff7e9]

                    focus:outline-none
                    focus:ring-4
                    focus:ring-[#d89a25]/10

                    xl:flex
                "
                aria-label="Open Account Management"
                title="Account Management"
            >

                <div
                    class="
                        relative
                        grid
                        h-10
                        w-10
                        shrink-0
                        place-items-center
                        overflow-hidden

                        rounded-full

                        bg-[#d9930a]

                        text-[11px]
                        font-bold
                        text-white
                    "
                >

                    <img
                        id="buyerHeaderProfileImage"
                        src=""
                        alt="Buyer profile photo"
                        class="
                            hidden
                            h-full
                            w-full
                            object-cover
                        "
                    >

                    <span id="buyerHeaderProfileInitials">
                        BU
                    </span>

                </div>


                <div class="min-w-0">

                    <p
                        id="buyerHeaderProfileName"
                        class="
                            max-w-[140px]

                            truncate

                            text-[11px]
                            font-semibold
                            text-[#28231c]
                        "
                    >
                        Buyer Account
                    </p>

                    <p
                        id="buyerHeaderProfileEmail"
                        data-buyer-login-email="{{ session('buyer_email', 'buyer@gmail.com') }}"
                        class="
                            mt-0.5
                            max-w-[140px]
                            truncate

                            text-[9px]
                            text-[#908779]
                        "
                    >
                        {{ session('buyer_email', 'buyer@gmail.com') }}
                    </p>

                </div>

            </a>

        </div>

    </div>


    {{-- ============================================================
         MOBILE SEARCH
    ============================================================ --}}
    <div
        class="
            border-t
            border-[#f0e8dc]

            px-4
            py-3

            md:hidden
        "
    >

        <div class="relative">

            <svg
                viewBox="0 0 24 24"
                class="
                    pointer-events-none

                    absolute
                    left-3.5
                    top-1/2

                    h-4
                    w-4

                    -translate-y-1/2

                    text-[#9a9184]
                "
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <circle
                    cx="11"
                    cy="11"
                    r="7"
                ></circle>

                <path d="m20 20-4-4"></path>
            </svg>


            <input
                id="buyerMobileSearch"
                type="search"
                name="mobile_search"
                placeholder="Search products..."
                autocomplete="off"
                class="
                    h-10
                    w-full

                    rounded-xl

                    border
                    border-[#e6dfd5]

                    bg-white

                    pl-10
                    pr-4

                    text-[10px]
                    text-[#28231c]

                    outline-none

                    transition

                    placeholder:text-[#a89f92]

                    focus:border-[#d89a25]
                    focus:ring-4
                    focus:ring-[#d89a25]/10
                "
            >

        </div>

    </div>

</header>