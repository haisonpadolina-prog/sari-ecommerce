@php
    /*
    |--------------------------------------------------------------------------
    | REAL ADMIN MESSAGE UNREAD COUNT
    |--------------------------------------------------------------------------
    |
    | Counts only Seller -> Admin messages that have not been read yet.
    |
    */

    $adminSidebarUnreadMessages = 0;

    if (session('is_admin')) {
        $adminSidebarUnreadMessages = \App\Models\Messaging\ChatMessage::query()
            ->where('sender_role', 'seller')
            ->whereNull('read_by_admin_at')
            ->count();
    }
@endphp

<style>
    /* ============================================================
       SARI ADMIN SIDEBAR — MATCH SELLER SIDEBAR VISUAL SYSTEM
       Layout only. Existing routes, unread state and logout behavior stay intact.
       ============================================================ */

    #adminSidebar {
        width: var(--admin-sidebar-width, 238px) !important;
        overflow-x: hidden !important;
        border-right-color: #ece5da !important;
        background: #ffffff !important;
        contain: layout paint;
        transition:
            width 130ms var(--admin-sidebar-ease, cubic-bezier(.22, 1, .36, 1)),
            transform 300ms ease-out !important;
    }

    #adminSidebarBrand {
        min-height: 74px !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        border-bottom-color: #eee7dc !important;
        background: #ffffff !important;
        transition:
            min-height 220ms var(--admin-sidebar-ease, cubic-bezier(.22, 1, .36, 1)),
            padding 220ms var(--admin-sidebar-ease, cubic-bezier(.22, 1, .36, 1));
    }

    #adminSidebarLogo {
        height: 44px !important;
    }

    #adminSidebarFullLogo,
    #adminSidebarCompactLogo {
        object-fit: contain;
    }

    #adminSidebarFullLogo {
        width: 102px !important;
        height: auto !important;
    }

    #adminSidebarCompactLogo {
        width: 38px !important;
        height: 38px !important;
    }

    #adminSidebarClose {
        right: 12px !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 10px !important;
    }

    #adminSidebarClose svg {
        width: 17px !important;
        height: 17px !important;
    }

    #adminSidebarNav {
        padding: 14px 10px 16px !important;
        scrollbar-width: thin;
        scrollbar-color: #ddd4c7 transparent;
    }

    #adminSidebarNav::-webkit-scrollbar {
        width: 4px;
    }

    #adminSidebarNav::-webkit-scrollbar-track {
        background: transparent;
    }

    #adminSidebarNav::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #ddd4c7;
    }

    .admin-sidebar-section-label {
        margin: 10px 8px 7px !important;
        color: #9a7840 !important;
        font-size: 7.5px !important;
        line-height: 1 !important;
        font-weight: 700 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
        user-select: none !important;
    }

    .admin-sidebar-section-label:first-child {
        margin-top: 4px !important;
    }

    .admin-sidebar-section-divider {
        height: 1px !important;
        margin: 11px 6px 9px !important;
        background: #eee5d7 !important;
    }

    #adminSidebarNav > a[data-sidebar-item],
    #adminSidebarNav > form > button[data-sidebar-item] {
        min-height: 39px !important;
        width: 100% !important;
        gap: 10px !important;
        border: 1px solid transparent !important;
        border-radius: 10px !important;
        padding: 8px 10px !important;
        font-size: 10.5px !important;
        line-height: 1.15 !important;
        box-shadow: none;
        transform: none !important;
        transition:
            background-color .12s ease,
            border-color .12s ease,
            color .12s ease,
            box-shadow .12s ease,
            padding 190ms var(--admin-sidebar-ease, cubic-bezier(.22, 1, .36, 1)),
            gap 190ms var(--admin-sidebar-ease, cubic-bezier(.22, 1, .36, 1)) !important;
    }

    #adminSidebarNav > a[data-sidebar-item] + a[data-sidebar-item],
    #adminSidebarNav > form + a[data-sidebar-item],
    #adminSidebarNav > a[data-sidebar-item] + form {
        margin-top: 2px !important;
    }

    #adminSidebarNav [data-sidebar-item] svg {
        width: 16px !important;
        height: 16px !important;
        stroke-width: 1.75 !important;
    }

    #adminSidebarNav [data-sidebar-item] > span:first-child {
        gap: 10px !important;
    }

    #adminSidebarNav > a[data-sidebar-item]:not([data-sidebar-current="true"]):hover,
    #adminSidebarNav > form > button[data-sidebar-item]:hover,
    #adminSidebar [data-sidebar-item][data-admin-nav-active="false"]:hover {
        border-color: #eadfcf !important;
        background-color: #fff9ef !important;
        color: #94620a !important;
        box-shadow: none !important;
    }

    #adminSidebarNav > a[data-sidebar-item][data-sidebar-current="true"]:not([data-admin-nav-active]),
    #adminSidebarNav > a[data-sidebar-item][data-admin-nav-active="true"] {
        border-color: #d9950b !important;
        background-color: #d9950b !important;
        color: #ffffff !important;
        box-shadow: 0 5px 12px rgba(194, 132, 10, .12) !important;
    }

    #adminSidebarNav > a[data-sidebar-item][data-sidebar-current="true"]:not([data-admin-nav-active]):hover,
    #adminSidebarNav > a[data-sidebar-item][data-admin-nav-active="true"]:hover {
        border-color: #cf8d08 !important;
        background-color: #cf8d08 !important;
        color: #ffffff !important;
    }

    #adminSidebarMessageBadge {
        min-width: 18px !important;
        height: 18px !important;
        padding-inline: 5px !important;
        font-size: 7.5px !important;
    }

    #adminSidebarProfile {
        border-top-color: #eee7dc !important;
        background: #ffffff !important;
        padding: 9px 10px !important;
        transition: padding 190ms var(--admin-sidebar-ease, cubic-bezier(.22, 1, .36, 1));
    }

    #adminSidebarProfile > a {
        min-height: 54px !important;
        gap: 8px !important;
        border-color: #e9e0d3 !important;
        border-radius: 11px !important;
        background-color: #ffffff !important;
        padding: 8px 9px !important;
        box-shadow: 0 3px 10px rgba(40, 31, 21, .02) !important;
    }

    #adminSidebarProfile > a:hover {
        border-color: #dcc9a7 !important;
        background-color: #fffdf9 !important;
        box-shadow: 0 4px 12px rgba(40, 31, 21, .03) !important;
    }

    /* The profile shortcut stays a neutral Seller-style card on /admin/account. */
    #adminSidebar #adminSidebarProfile > a[data-admin-nav-active="true"] {
        border-color: #e9e0d3 !important;
        background-color: #ffffff !important;
        color: inherit !important;
        box-shadow: 0 3px 10px rgba(40, 31, 21, .02) !important;
    }

    #adminSidebar #adminSidebarProfile > a[data-admin-nav-active="true"]:hover {
        border-color: #dcc9a7 !important;
        background-color: #fffdf9 !important;
    }

    #adminSidebarProfile > a > div:first-child {
        width: 32px !important;
        height: 32px !important;
        background-color: #d9950b !important;
        font-size: 9.5px !important;
    }

    #adminSidebarProfile .sidebar-label p:first-child {
        font-size: 9.5px !important;
        font-weight: 650 !important;
    }

    #adminSidebarProfile .sidebar-label p:last-child {
        font-size: 7.5px !important;
    }

    #adminSidebarProfile .sidebar-extra {
        width: 13px !important;
        height: 13px !important;
    }

    #adminSidebar,
    #adminSidebar * {
        background-image: none !important;
    }

    @media (min-width: 1024px) {
        html.admin-sidebar-collapsed #adminSidebarBrand {
            min-height: 74px !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        html.admin-sidebar-collapsed .admin-sidebar-section-label,
        html.admin-sidebar-collapsed .admin-sidebar-section-divider {
            display: none !important;
        }

        html.admin-sidebar-collapsed #adminSidebarNav {
            padding: 12px 9px !important;
        }

        html.admin-sidebar-collapsed #adminSidebarNav > a[data-sidebar-item],
        html.admin-sidebar-collapsed #adminSidebarNav > form > button[data-sidebar-item] {
            min-height: 40px !important;
            justify-content: center !important;
            gap: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        html.admin-sidebar-collapsed #adminSidebarNav [data-sidebar-inner] {
            justify-content: center !important;
            gap: 0 !important;
        }

        html.admin-sidebar-collapsed #adminSidebarProfile {
            padding: 9px !important;
        }

        html.admin-sidebar-collapsed #adminSidebarProfile > a {
            justify-content: center !important;
            gap: 0 !important;
            padding: 8px !important;
        }
    }

    @media (max-height: 820px) and (min-width: 1024px) {
        #adminSidebarNav {
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }

        .admin-sidebar-section-label {
            margin-top: 8px !important;
            margin-bottom: 6px !important;
        }

        .admin-sidebar-section-divider {
            margin-top: 8px !important;
            margin-bottom: 7px !important;
        }

        #adminSidebarNav > a[data-sidebar-item],
        #adminSidebarNav > form > button[data-sidebar-item] {
            min-height: 36px !important;
            padding-top: 7px !important;
            padding-bottom: 7px !important;
            font-size: 10px !important;
        }
    }

    @media (max-width: 1023px) {
        #adminSidebar {
            width: 238px !important;
        }

        #adminSidebarBrand {
            min-height: 74px !important;
        }

        #adminSidebarFullLogo {
            display: block !important;
        }

        #adminSidebarCompactLogo {
            display: none !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #adminSidebar,
        #adminSidebarBrand,
        #adminSidebarNav,
        #adminSidebarProfile,
        #adminSidebar .sidebar-label,
        #adminSidebar .sidebar-extra,
        #adminSidebar [data-sidebar-item] {
            transition-duration: 1ms !important;
        }
    }
</style>

<aside
    id="adminSidebar"
    class="
        fixed inset-y-0 left-0 z-50
        flex w-[238px] -translate-x-full flex-col
        border-r border-[#eee4d3]
        bg-white
        transition-all duration-300 ease-out
        lg:translate-x-0
    "
>
    {{-- BRAND / LOGO --}}
    <div
        id="adminSidebarBrand"
        class="relative flex min-h-[74px] items-center justify-center border-b border-[#eee7dc] px-3"
    >
        <a
            id="adminSidebarLogo"
            href="{{ route('admin.dashboard') }}"
            class="relative flex h-[44px] w-full items-center justify-center"
            aria-label="SARI Admin Dashboard"
        >
            <img
                id="adminSidebarFullLogo"
                src="{{ asset('images/sari-logo.png') }}"
                alt="SARI"
                width="102"
                height="44"
                loading="eager"
                decoding="async"
                fetchpriority="high"
                class="h-auto w-[102px] object-contain brightness-0"
                style="width:102px;height:auto;object-fit:contain;filter:brightness(0);"
            >

            <img
                id="adminSidebarCompactLogo"
                src="{{ asset('images/sari-main-logo.png') }}"
                alt="SARI"
                width="38"
                height="38"
                loading="eager"
                decoding="async"
                class="h-[38px] w-[38px] object-contain"
                style="width:38px;height:38px;object-fit:contain;"
            >
        </a>

        <button
            id="adminSidebarClose"
            type="button"
            class="absolute right-3 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-[10px] text-[#6e6558] transition hover:bg-[#f7eedf] hover:text-[#b97805] lg:hidden"
            aria-label="Close admin sidebar"
        >
            <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M6 6l12 12"></path>
                <path d="M18 6 6 18"></path>
            </svg>
        </button>
    </div>

    {{-- NAVIGATION --}}
    <nav id="adminSidebarNav" class="flex-1 overflow-y-auto">
        <p class="admin-sidebar-section-label">MAIN MENU</p>

        {{-- Dashboard --}}
        <a
            href="{{ route('admin.dashboard') }}"
            title="Dashboard Overview"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.dashboard') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M3 11.5 12 4l9 7.5"></path>
                <path d="M5.5 10v10h13V10"></path>
                <path d="M9.5 20v-6h5v6"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Dashboard Overview</span>
        </a>

        <div class="admin-sidebar-section-divider"></div>
        <p class="admin-sidebar-section-label">ACCESS &amp; USERS</p>

        {{-- Account Registrations --}}
        <a
            href="{{ route('admin.registrations') }}"
            title="Account Registrations"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.registrations') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.registrations') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="10" cy="8" r="3.5"></circle>
                <path d="M3.5 20c.5-4 3-6 6.5-6s6 2 6.5 6"></path>
                <path d="M17 8h4"></path>
                <path d="M19 6v4"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Account Registrations</span>
        </a>

        {{-- User Accounts --}}
        <a
            href="{{ route('admin.users') }}"
            title="User Accounts"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.users') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.users') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="9" cy="8" r="3"></circle>
                <circle cx="17" cy="10" r="2.5"></circle>
                <path d="M3 20c.4-3.8 2.6-6 6-6s5.6 2.2 6 6"></path>
                <path d="M15 15c3.4 0 5.4 1.8 6 5"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">User Accounts</span>
        </a>


        {{-- Platform Monitoring --}}
        <a
            href="{{ route('admin.monitoring') }}"
            title="Platform Monitoring"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.monitoring*') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.monitoring*') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M3 12s3.4-6 9-6 9 6 9 6-3.4 6-9 6-9-6-9-6Z"></path>
                <circle cx="12" cy="12" r="2.5"></circle>
                <path d="M12 3v2M12 19v2M3 12H1M23 12h-2"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Platform Monitoring</span>
        </a>

        <div class="admin-sidebar-section-divider"></div>
        <p class="admin-sidebar-section-label">TRUST &amp; SAFETY</p>

        {{-- Seller Compliance --}}
        <a
            href="{{ route('admin.seller-compliance') }}"
            title="Seller Compliance"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.seller-compliance') || request()->routeIs('admin.seller-accounts.control') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.seller-compliance') || request()->routeIs('admin.seller-accounts.control') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M5 8h14l-1-4H6L5 8Z"></path>
                <path d="M6 8v11h12V8"></path>
                <path d="m9 14 2 2 4-4"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Seller Compliance</span>
        </a>

        {{-- Complaints --}}
        <a
            href="{{ route('admin.complaints') }}"
            title="Complaints & Disputes"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.complaints') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.complaints') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="5" y="3" width="14" height="18" rx="2"></rect>
                <path d="M9 8h6"></path>
                <path d="M9 12h6"></path>
                <path d="M9 16h3"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Complaints &amp; Disputes</span>
        </a>

        <div class="admin-sidebar-section-divider"></div>
        <p class="admin-sidebar-section-label">FINANCE &amp; INSIGHTS</p>

        {{-- Commission --}}
        <a
            href="{{ route('admin.commissions') }}"
            title="Commission Management"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.commissions') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.commissions') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M9 9h5a2 2 0 1 1 0 4h-4a2 2 0 1 0 0 4h5"></path>
                <path d="M12 6v12"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Commission Management</span>
        </a>

        {{-- Reports --}}
        <a
            href="{{ route('admin.reports') }}"
            title="Reports"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.reports') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.reports') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 20h16"></path>
                <path d="M7 17v-5"></path>
                <path d="M12 17V8"></path>
                <path d="M17 17V4"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Reports</span>
        </a>

        <div class="admin-sidebar-section-divider"></div>
        <p class="admin-sidebar-section-label">MARKETING</p>

        {{-- Events & Promotions --}}
        <a
            href="{{ route('admin.platform-campaigns') }}"
            title="Events & Promotions"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.platform-campaigns*') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.platform-campaigns*') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 8h16v11H4z"></path>
                <path d="M8 8V5h8v3"></path>
                <path d="M8 13h8M10 16h4"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Events &amp; Promotions</span>
        </a>

        <div class="admin-sidebar-section-divider"></div>
        <p class="admin-sidebar-section-label">PLATFORM</p>

        {{-- Platform Settings --}}
        <a
            href="{{ route('admin.platform-settings') }}"
            title="Platform Settings"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.platform-settings') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.platform-settings') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1A7 7 0 0 0 14.8 6L14.5 3h-5L9.2 6a7 7 0 0 0-1.7 1.1l-2.4-1-2 3.4L5.1 11a7 7 0 0 0 0 2l-2 1.5 2 3.4 2.4-1A7 7 0 0 0 9.2 18l.3 3h5l.3-3a7 7 0 0 0 1.7-1.1l2.4 1 2-3.4-2-1.5c.1-.3.1-.7.1-1Z"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Platform Settings</span>
        </a>

        <div class="admin-sidebar-section-divider"></div>
        <p class="admin-sidebar-section-label">SUPPORT</p>

        {{-- Chat / Messaging --}}
        <a
            href="{{ route('admin.messages') }}"
            title="Chat / Messaging"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.messages') ? 'true' : 'false' }}"
            class="flex items-center justify-between gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.messages') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <span class="flex items-center gap-3" data-sidebar-inner>
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                    <path d="M8 10h8"></path>
                    <path d="M8 14h5"></path>
                </svg>
                <span class="sidebar-label whitespace-nowrap">Chat / Messaging</span>
            </span>

            <span
                id="adminSidebarMessageBadge"
                data-admin-unread-badge
                data-has-unread="{{ $adminSidebarUnreadMessages > 0 ? 'true' : 'false' }}"
                class="sidebar-extra {{ $adminSidebarUnreadMessages > 0 ? 'grid' : 'hidden' }} h-5 min-w-[20px] place-items-center rounded-full px-1.5 text-[9px] font-bold {{ request()->routeIs('admin.messages') ? 'bg-white text-[#c48413]' : 'bg-[#d9930a] text-white' }}"
            >
                {{ $adminSidebarUnreadMessages > 99 ? '99+' : $adminSidebarUnreadMessages }}
            </span>
        </a>

        <div class="admin-sidebar-section-divider"></div>
        <p class="admin-sidebar-section-label">ACCOUNT</p>

        {{-- User Activity Log --}}
        <a
            href="{{ route('admin.user-activity') }}"
            title="User Activity Log"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.user-activity') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.user-activity') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M5 4h14v16H5z"></path>
                <path d="M8 8h8M8 12h8M8 16h5"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">User Activity Log</span>
        </a>

        {{-- Admin Recent Activity --}}
        <a
            href="{{ route('admin.recent-activity') }}"
            title="Admin Recent Activity"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.recent-activity') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.recent-activity') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="8"></circle>
                <path d="M12 8v4l3 2"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Admin Recent Activity</span>
        </a>

        {{-- Account Management --}}
        <a
            href="{{ route('admin.account') }}"
            title="Account Management"
            data-sidebar-item
            data-sidebar-current="{{ request()->routeIs('admin.account') ? 'true' : 'false' }}"
            class="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium transition-all duration-200 {{ request()->routeIs('admin.account') ? 'bg-[#d9930a] text-white shadow-[0_10px_25px_rgba(217,147,10,0.18)]' : 'text-[#514b42] hover:bg-[#f9f1e3] hover:text-[#a96e05]' }}"
        >
            <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="8" r="3.5"></circle>
                <path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path>
            </svg>
            <span class="sidebar-label whitespace-nowrap">Account Management</span>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button
                type="submit"
                title="Logout"
                data-sidebar-item
                class="flex w-full items-center gap-3 rounded-xl px-4 py-3.5 text-[13px] font-medium text-[#514b42] transition-all duration-200 hover:bg-red-50 hover:text-red-600"
            >
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M14 3h7v18h-7"></path>
                </svg>
                <span class="sidebar-label whitespace-nowrap">Logout</span>
            </button>
        </form>
    </nav>

    {{-- ADMIN PROFILE --}}
    <div id="adminSidebarProfile" class="border-t border-[#eee7dc] bg-white p-4 transition-all duration-300">
        <a
            href="{{ route('admin.account') }}"
            title="Admin User"
            data-sidebar-item
            class="flex w-full items-center gap-3 rounded-2xl border border-[#eadfca] bg-white p-3.5 text-left transition-all duration-200 hover:border-[#dbc396] hover:shadow-sm"
        >
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#d9930a] text-[13px] font-semibold text-white">AD</div>

            <div class="sidebar-label min-w-0 flex-1">
                <p class="truncate text-[13px] font-semibold text-[#211d17]">Admin User</p>
                <p class="mt-0.5 truncate text-[10px] text-[#8d8272]">Super Administrator</p>
            </div>

            <svg viewBox="0 0 24 24" class="sidebar-extra h-4 w-4 shrink-0 text-[#82796b]" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </a>
    </div>
</aside>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebarMessageBadge =
        document.getElementById('adminSidebarMessageBadge');

    if (!sidebarMessageBadge) {
        return;
    }

    function setSidebarUnreadCount(count) {

        const unread =
            Math.max(0, Number(count) || 0);

        sidebarMessageBadge.textContent =
            unread > 99
                ? '99+'
                : String(unread);

        sidebarMessageBadge.dataset.hasUnread =
            unread > 0
                ? 'true'
                : 'false';

        if (unread > 0) {
            sidebarMessageBadge.classList.remove('hidden');
            sidebarMessageBadge.classList.add('grid');
        } else {
            sidebarMessageBadge.classList.remove('grid');
            sidebarMessageBadge.classList.add('hidden');
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Sync with Admin navbar notification system
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'sari:admin-unread-count-changed',
        function (event) {
            setSidebarUnreadCount(
                event.detail?.count || 0
            );
        }
    );

    window.addEventListener(
        'sari:admin-all-messages-read',
        function () {
            setSidebarUnreadCount(0);
        }
    );

});
</script>
@endpush
