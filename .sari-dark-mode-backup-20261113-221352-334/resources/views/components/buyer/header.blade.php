@php
    // Read the authenticated buyer identity, not placeholder browser/session values.
    $buyerHeaderAccountId = (int) session('buyer_account_id', 0);
    $buyerHeaderSocialId = (int) session('buyer_social_account_id', 0);
    $buyerHeaderEmail = '';
    $buyerHeaderName = '';
    $buyerHeaderPhotoUrl = null;

    if ((bool) session('is_buyer') && $buyerHeaderAccountId > 0) {
        // Reuse the authenticated account loaded by BuyerPageController, when
        // available for this request. All other controllers keep the old query.
        $buyerHeaderAccount = request()->attributes->get('sari.buyer.active_account');
        if (!($buyerHeaderAccount instanceof \App\Models\Accounts\BuyerAccount)
            || (int) $buyerHeaderAccount->getKey() !== $buyerHeaderAccountId) {
            $buyerHeaderAccount = \App\Models\Accounts\BuyerAccount::query()
                ->select(['id', 'first_name', 'last_name', 'email', 'profile_image_path'])
                ->find($buyerHeaderAccountId);
        }

        if ($buyerHeaderAccount) {
            $buyerHeaderName = trim((string) $buyerHeaderAccount->first_name . ' ' . (string) $buyerHeaderAccount->last_name);
            $buyerHeaderEmail = trim((string) $buyerHeaderAccount->email);
            $buyerHeaderImagePath = trim((string) ($buyerHeaderAccount->profile_image_path ?? ''));
            if ($buyerHeaderImagePath !== '') {
                $buyerHeaderPhotoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($buyerHeaderImagePath);
            }
        }
    } elseif ((bool) session('is_buyer') && $buyerHeaderSocialId > 0) {
        $buyerHeaderSocial = \App\Models\Accounts\SocialAccount::query()
            ->select(['id', 'name', 'email', 'avatar_url'])
            ->find($buyerHeaderSocialId);

        if ($buyerHeaderSocial) {
            $buyerHeaderName = trim((string) $buyerHeaderSocial->name);
            $buyerHeaderEmail = trim((string) $buyerHeaderSocial->email);
            $buyerHeaderSocialAvatar = trim((string) ($buyerHeaderSocial->avatar_url ?? ''));
            if (str_starts_with($buyerHeaderSocialAvatar, 'https://')) {
                $buyerHeaderPhotoUrl = $buyerHeaderSocialAvatar;
            }
        }
    }

    // Session is a fallback for pages that haven't loaded an account record yet.
    if ($buyerHeaderName === '') {
        $buyerHeaderName = trim((string) session('buyer_name', ''));
    }
    if ($buyerHeaderEmail === '') {
        $buyerHeaderEmail = trim((string) session('buyer_email', ''));
    }
    if ($buyerHeaderName === '' && $buyerHeaderEmail !== '') {
        $buyerHeaderHandle = trim((string) \Illuminate\Support\Str::before($buyerHeaderEmail, '@'));
        $buyerHeaderName = \Illuminate\Support\Str::of($buyerHeaderHandle)
            ->replace(['.', '_', '-'], ' ')->title()->toString();
    }
    $buyerHeaderName = $buyerHeaderName ?: 'Buyer Account';
    $buyerHeaderInitials = mb_strtoupper(
        mb_substr(preg_replace('/[^\pL\pN]/u', '', $buyerHeaderName) ?: 'BU', 0, 2)
    );
@endphp

<header id="buyerHeader" class="sticky top-0 z-40 border-b border-[#E9EDF2] bg-white">
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
                <h1 class="truncate font-bold tracking-[-0.035em] text-[#17212F]">
                    @yield('page-title', 'Buyer Home')
                </h1>

                <div class="buyer-header-breadcrumb mt-1 flex min-w-0 items-center gap-2 text-[#758092]">
                    <a
                        id="buyerHeaderBreadcrumbName"
                        href="{{ route('buyer.home') }}"
                        wire:navigate.hover
                        class="max-w-[150px] truncate font-medium transition hover:text-[#C99128]"
                    >
                        Buyer
                    </a>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                    <span class="truncate font-semibold text-[#475467]">@yield('page-title', 'Home')</span>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="flex min-w-0 items-center gap-1.5 sm:gap-2">
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

            <div class="mx-1 hidden h-8 w-px bg-[#E5EAF0] xl:block"></div>

            <a
                id="buyerProfileButton"
                href="{{ route('buyer.account') }}"
                wire:navigate.hover
                class="buyer-header-profile flex shrink-0 items-center border border-transparent transition-colors duration-150 hover:border-[#E5EAF0] hover:bg-[#F6F8FB] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C99128]"
                aria-label="Open account management for {{ $buyerHeaderName }}"
                title="{{ $buyerHeaderName }} — Account Management"
            >
                <span class="buyer-shell-avatar relative grid shrink-0 place-items-center overflow-hidden rounded-full bg-[#d9950b] font-bold text-white">
                    @if($buyerHeaderPhotoUrl)
                        <img id="buyerHeaderProfileImage" src="{{ $buyerHeaderPhotoUrl }}" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer" class="h-full w-full object-cover">
                        <span id="buyerHeaderProfileInitials" class="sr-only">{{ $buyerHeaderInitials ?: 'BU' }}</span>
                    @else
                        <span id="buyerHeaderProfileInitials">{{ $buyerHeaderInitials ?: 'BU' }}</span>
                    @endif
                </span>

                <span class="buyer-header-profile-details hidden min-w-0 xl:block">
                    <span id="buyerHeaderProfileName" class="block max-w-[165px] truncate font-semibold text-[#202B3A]">{{ $buyerHeaderName }}</span>
                    <span
                        id="buyerHeaderProfileEmail"
                        data-buyer-login-email="{{ $buyerHeaderEmail }}"
                        class="mt-0.5 block max-w-[165px] truncate text-[#667085]"
                    >{{ $buyerHeaderEmail ?: 'View profile & settings' }}</span>
                </span>
            </a>
        </div>
    </div>


    {{-- Scoped overrides for the new professional header; shell.css uses !important rules. --}}
    <style>
        #buyerHeader {
            border-bottom-color: #E9EDF2 !important;
            background: #FFFFFF !important;
        }
        #buyerHeader .buyer-header-main {
            min-height: 82px !important;
            gap: 16px !important;
        }
        #buyerHeader h1 {
            font-size: clamp(21px, 1.85vw, 27px) !important;
            font-weight: 700 !important;
            line-height: 1.22 !important;
            letter-spacing: -.035em !important;
        }
        #buyerHeader .buyer-header-breadcrumb {
            gap: 8px !important;
            font-size: 11.5px !important;
            line-height: 1.4 !important;
            color: #758092 !important;
        }
        #buyerHeader .buyer-header-breadcrumb svg {
            width: 13px !important;
            height: 13px !important;
            flex: 0 0 13px !important;
            color: #B5BDC8;
        }
        #buyerHeader .buyer-header-control {
            border-color: #E5EAF0 !important;
            color: #344054 !important;
            box-shadow: none !important;
        }
        #buyerHeader .buyer-header-control:hover {
            background: #F4F6F9 !important;
            border-color: #D4DBE4 !important;
        }
        #buyerHeader .buyer-header-icon-button:hover {
            color: #C99128 !important;
        }
        #buyerHeader .buyer-header-profile {
            gap: 11px !important;
            border-radius: 13px !important;
            padding: 6px 9px !important;
            text-decoration: none !important;
        }
        #buyerHeader .buyer-header-profile .buyer-shell-avatar {
            width: 39px !important;
            height: 39px !important;
            font-size: 12px !important;
            box-shadow: 0 0 0 2px #FFFFFF, 0 0 0 3px #F0E5CD;
        }
        #buyerHeaderProfileName {
            font-size: 12px !important;
            line-height: 1.3 !important;
            font-weight: 650 !important;
        }
        #buyerHeaderProfileEmail {
            font-size: 10px !important;
            line-height: 1.4 !important;
        }
        @media (max-width: 1279px) {
            #buyerHeader .buyer-header-profile { padding: 5px !important; }
        }
        @media (max-width: 767px) {
            #buyerHeader .buyer-header-main {
                min-height: 74px !important;
                padding-inline: 12px !important;
                gap: 10px !important;
            }
            #buyerHeader h1 { font-size: 21px !important; }
            #buyerHeader .buyer-header-breadcrumb { font-size: 10px !important; }
            #buyerHeader .buyer-header-profile .buyer-shell-avatar {
                width: 35px !important;
                height: 35px !important;
            }
        }
        @media (max-width: 380px) {
            #buyerHeader .buyer-header-main { gap: 5px !important; }
            #buyerHeader .buyer-header-icon-button { width: 34px !important; }
        }
        @media (prefers-reduced-motion: reduce) {
            #buyerHeader .buyer-header-profile { transition: none !important; }
        }
    </style>

</header>
