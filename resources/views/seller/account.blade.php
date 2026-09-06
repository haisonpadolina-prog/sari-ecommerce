@extends('layouts.seller')

@section('title', 'Account Management — SARI Seller')
@section('page-title', 'Account Management')

@section('content')

<style>
    .seller-account-control {
        color: #302a24 !important;
        -webkit-text-fill-color: #302a24 !important;
        background: #fff !important;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .seller-account-control::placeholder {
        color: #aaa196 !important;
        -webkit-text-fill-color: #aaa196 !important;
    }

    .seller-account-control:focus {
        outline: none;
        border-color: #d49a2d !important;
        box-shadow: 0 0 0 4px rgba(212, 154, 45, .08);
    }

    .seller-account-section {
        scroll-margin-top: 118px;
    }

    .seller-account-nav-link[data-active="true"] {
        border-color: #ead8b4;
        background: #fffaf1;
        color: #9a6817;
    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE ACCOUNT TYPOGRAPHY
    |--------------------------------------------------------------------------
    | The original page used many 6px–11px labels. These overrides preserve
    | the current layout and functionality while keeping text readable on
    | laptop, desktop, zoomed-out, and smaller screens.
    */
    .seller-account-page {
        --account-font-xs: clamp(0.76rem, 0.72rem + 0.10vw, 0.84rem);
        --account-font-sm: clamp(0.82rem, 0.77rem + 0.12vw, 0.91rem);
        --account-font-md: clamp(0.88rem, 0.82rem + 0.15vw, 0.98rem);
        --account-font-lg: clamp(1rem, 0.94rem + 0.18vw, 1.12rem);
    }

    /* Very small captions / chips / helper text */
    .seller-account-page [class*="text-[6px]"],
    .seller-account-page [class*="text-[6.5px]"],
    .seller-account-page [class*="text-[7px]"],
    .seller-account-page [class*="text-[7.5px]"] {
        font-size: var(--account-font-xs) !important;
        line-height: 1.45 !important;
    }

    /* Common labels / sidebar / controls */
    .seller-account-page [class*="text-[8px]"],
    .seller-account-page [class*="text-[8.5px]"] {
        font-size: var(--account-font-sm) !important;
        line-height: 1.45 !important;
    }

    /* Normal body text */
    .seller-account-page [class*="text-[9px]"],
    .seller-account-page [class*="text-[9.5px]"],
    .seller-account-page [class*="text-[10px]"] {
        font-size: var(--account-font-md) !important;
        line-height: 1.5 !important;
    }

    /* Section headings */
    .seller-account-page [class*="text-[11px]"] {
        font-size: var(--account-font-lg) !important;
        line-height: 1.35 !important;
    }

    .seller-account-page .seller-account-control {
        min-height: 44px;
        font-size: var(--account-font-sm) !important;
        line-height: 1.4 !important;
    }

    .seller-account-page textarea.seller-account-control {
        min-height: 120px;
        padding-top: 0.8rem !important;
        padding-bottom: 0.8rem !important;
    }

    .seller-account-page label {
        line-height: 1.4;
    }

    .seller-account-page button,
    .seller-account-page a {
        line-height: 1.35;
    }

    .seller-account-page .seller-account-nav-link {
        min-height: 46px;
        font-size: var(--account-font-sm) !important;
    }

    /* Give the larger navigation labels enough room on wide screens. */
    @media (min-width: 1280px) {
        .seller-account-page .seller-account-workspace {
            grid-template-columns: 290px minmax(0, 1fr) !important;
        }
    }

    @media (max-width: 640px) {
        .seller-account-page {
            --account-font-xs: 0.78rem;
            --account-font-sm: 0.84rem;
            --account-font-md: 0.90rem;
            --account-font-lg: 1rem;
        }

        .seller-account-page .seller-account-control {
            min-height: 46px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        html {
            scroll-behavior: auto !important;
        }
    }
</style>

<div class="seller-account-page mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        ACCOUNT OVERVIEW
    ========================================================== --}}
    <section class="overflow-hidden rounded-[20px] border border-[#e9e3da] bg-white">
        <div class="flex flex-col gap-5 px-5 py-5 sm:px-6 sm:py-6 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex items-start gap-4">
                <div class="relative shrink-0">
                    <div class="grid h-14 w-14 place-items-center rounded-[16px] bg-[#d9930a] text-[15px] font-bold text-white shadow-[0_8px_20px_rgba(217,147,10,.18)]">
                        SS
                    </div>

                    <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-[3px] border-white bg-[#67a17c]"></span>
                </div>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-[#d6e5dc] bg-[#f4f8f5] px-2.5 py-1 text-[7px] font-bold uppercase tracking-[.11em] text-[#56816a]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#67a17c]"></span>
                            Verified Seller
                        </span>

                        <span class="text-[7px] font-semibold text-[#93897d]">
                            Seller ID: SLR-2026-001
                        </span>
                    </div>

                    <h2 class="mt-2 text-[21px] font-bold tracking-[-.035em] text-[#211c16] sm:text-[24px]">
                        SARI Seller Store
                    </h2>

                    <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-[clamp(.82rem,.78rem+.12vw,.92rem)] text-[#81786c]">
                        <span>seller@gmail.com</span>
                        <span class="hidden h-1 w-1 rounded-full bg-[#d5cdc2] sm:inline-block"></span>
                        <span>General Merchandise</span>
                        <span class="hidden h-1 w-1 rounded-full bg-[#d5cdc2] sm:inline-block"></span>
                        <span>Member since Aug 2026</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button
                    id="openSellerStatusSummary"
                    type="button"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#e3dcd2] bg-white px-4 text-[8px] font-bold text-[#62594e] transition hover:border-[#d8bc89] hover:bg-[#fffaf2] hover:text-[#9a6817]"
                >
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                    Seller Status
                </button>

                <button
                    type="button"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#d48f08] px-4 text-[8px] font-bold text-white shadow-[0_8px_18px_rgba(212,143,8,.14)] transition hover:bg-[#bd7d05]"
                >
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="m6 12 4 4 8-8"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>

        {{-- STATUS STRIP --}}
        <div class="grid grid-cols-2 border-t border-[#eee8df] bg-[#fcfbf9] lg:grid-cols-4">
            <div class="border-b border-r border-[#eee8df] px-4 py-3.5 lg:border-b-0 sm:px-5">
                <p class="text-[6.5px] font-semibold uppercase tracking-[.08em] text-[#9b9287]">Account</p>
                <div class="mt-1.5 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-[#67a17c]"></span>
                    <p class="text-[10px] font-bold text-[#3f493f]">Active</p>
                </div>
            </div>

            <div class="border-b border-[#eee8df] px-4 py-3.5 lg:border-b-0 lg:border-r sm:px-5">
                <p class="text-[6.5px] font-semibold uppercase tracking-[.08em] text-[#9b9287]">Verification</p>
                <p class="mt-1.5 text-[10px] font-bold text-[#a8731f]">Approved</p>
            </div>

            <div class="border-r border-[#eee8df] px-4 py-3.5 sm:px-5">
                <p class="text-[6.5px] font-semibold uppercase tracking-[.08em] text-[#9b9287]">Store Rating</p>
                <div class="mt-1.5 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" class="h-3 w-3 text-[#d49312]" fill="currentColor">
                        <path d="m12 3 2.6 5.3 5.8.8-4.2 4.1 1 5.8-5.2-2.7L6.8 19l1-5.8-4.2-4.1 5.8-.8L12 3Z"></path>
                    </svg>
                    <p class="text-[10px] font-bold text-[#302a24]">4.8 / 5</p>
                </div>
            </div>

            <div class="px-4 py-3.5 sm:px-5">
                <p class="text-[6.5px] font-semibold uppercase tracking-[.08em] text-[#9b9287]">Last Login</p>
                <p class="mt-1.5 text-[10px] font-bold text-[#514a42]">Today, 10:42 AM</p>
            </div>
        </div>
    </section>


    {{-- =========================================================
        ACCOUNT WORKSPACE
    ========================================================== --}}
    <section class="seller-account-workspace mt-4 grid grid-cols-1 items-start gap-4 xl:grid-cols-[290px_minmax(0,1fr)]">

        {{-- LEFT NAVIGATION --}}
        <aside class="self-start xl:sticky xl:top-[112px]">
            <div class="rounded-[18px] border border-[#e9e3da] bg-white p-3">
                <div class="px-2 pb-3 pt-1">
                    <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#9a9185]">
                        Account Settings
                    </p>
                    <p class="mt-1 text-[7px] leading-4 text-[#aaa196]">
                        Manage your seller profile and security.
                    </p>
                </div>

                <nav class="space-y-1">
                    @php
                        $accountNav = [
                            ['id' => 'personal-information', 'label' => 'Personal Information', 'icon' => 'user'],
                            ['id' => 'business-information', 'label' => 'Business Profile', 'icon' => 'store'],
                            ['id' => 'business-address', 'label' => 'Business Address', 'icon' => 'pin'],
                            ['id' => 'verification-documents', 'label' => 'Verification', 'icon' => 'shield'],
                            ['id' => 'password-security', 'label' => 'Password & Security', 'icon' => 'lock'],
                            ['id' => 'seller-notifications', 'label' => 'Notifications', 'icon' => 'bell'],
                            ['id' => 'account-actions', 'label' => 'Account Actions', 'icon' => 'settings'],
                        ];
                    @endphp

                    @foreach ($accountNav as $index => $item)
                        <button
                            type="button"
                            data-account-nav="{{ $item['id'] }}"
                            data-active="{{ $index === 0 ? 'true' : 'false' }}"
                            class="seller-account-nav-link flex w-full items-center gap-3 rounded-xl border border-transparent px-3 py-2.5 text-left text-[8px] font-semibold text-[#625a50] transition hover:bg-[#fcfaf7] hover:text-[#9a6817]"
                        >
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg border border-[#eee7de] bg-[#fcfbf9] text-[#8b8175]">
                                @if ($item['icon'] === 'user')
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path></svg>
                                @elseif ($item['icon'] === 'store')
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h16"></path><path d="M5 10v9h14v-9"></path><path d="M7 10 9 5h6l2 5"></path></svg>
                                @elseif ($item['icon'] === 'pin')
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"></path><circle cx="12" cy="9" r="2"></circle></svg>
                                @elseif ($item['icon'] === 'shield')
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                                @elseif ($item['icon'] === 'lock')
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
                                @elseif ($item['icon'] === 'bell')
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg>
                                @else
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4v-.2a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"></path></svg>
                                @endif
                            </span>

                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>

                            <svg viewBox="0 0 24 24" class="h-3 w-3 shrink-0 text-[#b5ada3]" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                        </button>
                    @endforeach
                </nav>

                <div class="mt-3 border-t border-[#eee8df] pt-3">
                    <button
                        type="button"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition hover:bg-[#fcfaf7]"
                    >
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#d9930a] text-[8px] font-bold text-white">
                            SS
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-[8px] font-bold text-[#403930]">SARI Seller</span>
                            <span class="mt-0.5 block truncate text-[7px] text-[#9b9287]">Public Store Profile</span>
                        </span>
                        <svg viewBox="0 0 24 24" class="h-3 w-3 text-[#aaa196]" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                    </button>
                </div>
            </div>
        </aside>


        {{-- RIGHT CONTENT --}}
        <div class="min-w-0 space-y-4">

            {{-- =====================================================
                PERSONAL INFORMATION
            ====================================================== --}}
            <section id="personal-information" data-account-section class="seller-account-section overflow-hidden rounded-[18px] border border-[#e9e3da] bg-white">
                <div class="flex flex-col gap-3 border-b border-[#eee8df] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#fff6e6] text-[#b87a12]">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20c.5-4.2 3-6.5 7-6.5s6.5 2.3 7 6.5"></path></svg>
                            </span>
                            <div>
                                <h3 class="text-[11px] font-bold text-[#302a24]">Personal Information</h3>
                                <p class="mt-0.5 text-[7.5px] text-[#91887d]">Your identity and seller contact information.</p>
                            </div>
                        </div>
                    </div>

                    <span class="w-fit rounded-full border border-[#e6e0d7] bg-[#fcfbf9] px-2.5 py-1 text-[6.5px] font-bold uppercase tracking-[.08em] text-[#8d8479]">
                        Registration Details
                    </span>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">First Name</label>
                            <input type="text" value="SARI" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Last Name</label>
                            <input type="text" value="Seller" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Middle Initial</label>
                            <input type="text" value="A." maxlength="5" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Sex</label>
                            <div class="relative">
                                <select class="seller-account-control h-10 w-full appearance-none rounded-xl border border-[#e3ddd4] pl-3.5 pr-9 text-[8.5px]">
                                    <option selected>Male</option>
                                    <option>Female</option>
                                    <option>Prefer not to say</option>
                                </select>
                                <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3 top-1/2 h-3 w-3 -translate-y-1/2 text-[#968b7d]" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Email Address</label>
                            <input type="email" value="seller@gmail.com" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Contact Number</label>
                            <input type="text" value="+63 912 345 6789" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Birthday</label>
                            <input type="date" value="2000-05-12" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Age</label>
                            <input type="text" value="26" readonly class="h-10 w-full rounded-xl border border-[#e7e2dc] bg-[#f6f4f1] px-3.5 text-[8.5px] font-medium text-[#8e8579] outline-none">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Seller ID</label>
                            <input type="text" value="SLR-2026-001" readonly class="h-10 w-full rounded-xl border border-[#e7e2dc] bg-[#f6f4f1] px-3.5 text-[8.5px] font-medium text-[#8e8579] outline-none">
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end border-t border-[#eee8df] pt-4">
                        <button type="button" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg bg-[#d48f08] px-4 text-[7.5px] font-bold text-white transition hover:bg-[#bd7d05]">
                            Save Personal Information
                        </button>
                    </div>
                </div>
            </section>


            {{-- =====================================================
                BUSINESS INFORMATION
            ====================================================== --}}
            <section id="business-information" data-account-section class="seller-account-section overflow-hidden rounded-[18px] border border-[#e9e3da] bg-white">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#fff6e6] text-[#b87a12]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h16"></path><path d="M5 10v9h14v-9"></path><path d="M7 10 9 5h6l2 5"></path></svg>
                        </span>
                        <div>
                            <h3 class="text-[11px] font-bold text-[#302a24]">Business Profile</h3>
                            <p class="mt-0.5 text-[7.5px] text-[#91887d]">Store details visible across the marketplace.</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Business Name</label>
                            <input type="text" value="SARI Seller Store" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Line of Business</label>
                            <div class="relative">
                                <select class="seller-account-control h-10 w-full appearance-none rounded-xl border border-[#e3ddd4] pl-3.5 pr-9 text-[8.5px]">
                                    <option selected>General Merchandise</option>
                                    <option>Electronics</option>
                                    <option>Fashion</option>
                                    <option>Home & Living</option>
                                    <option>Beauty</option>
                                    <option>Books</option>
                                    <option>Food & Beverage</option>
                                </select>
                                <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3 top-1/2 h-3 w-3 -translate-y-1/2 text-[#968b7d]" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Store Status</label>
                            <div class="flex h-10 items-center gap-2 rounded-xl border border-[#d8e6dd] bg-[#f5f9f6] px-3.5">
                                <span class="h-2 w-2 rounded-full bg-[#67a17c]"></span>
                                <span class="text-[8.5px] font-bold text-[#56816a]">Active / Verified</span>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Store Description</label>
                            <textarea rows="4" class="seller-account-control w-full resize-none rounded-xl border border-[#e3ddd4] px-3.5 py-3 text-[8.5px] leading-5">A verified SARI marketplace store offering quality everyday products with reliable order fulfillment and customer service.</textarea>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end border-t border-[#eee8df] pt-4">
                        <button type="button" class="inline-flex h-9 items-center justify-center rounded-lg bg-[#d48f08] px-4 text-[7.5px] font-bold text-white transition hover:bg-[#bd7d05]">
                            Save Business Information
                        </button>
                    </div>
                </div>
            </section>


            {{-- =====================================================
                BUSINESS ADDRESS
            ====================================================== --}}
            <section id="business-address" data-account-section class="seller-account-section overflow-hidden rounded-[18px] border border-[#e9e3da] bg-white">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#fff6e6] text-[#b87a12]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"></path><circle cx="12" cy="9" r="2"></circle></svg>
                        </span>
                        <div>
                            <h3 class="text-[11px] font-bold text-[#302a24]">Business Address</h3>
                            <p class="mt-0.5 text-[7.5px] text-[#91887d]">Pickup and operational address for your seller account.</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Province</label>
                            <div class="relative">
                                <select class="seller-account-control h-10 w-full appearance-none rounded-xl border border-[#e3ddd4] pl-3.5 pr-9 text-[8.5px]">
                                    <option selected>Laguna</option>
                                    <option>Metro Manila</option>
                                    <option>Cavite</option>
                                    <option>Rizal</option>
                                </select>
                                <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3 top-1/2 h-3 w-3 -translate-y-1/2 text-[#968b7d]" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Municipality / City</label>
                            <div class="relative">
                                <select class="seller-account-control h-10 w-full appearance-none rounded-xl border border-[#e3ddd4] pl-3.5 pr-9 text-[8.5px]">
                                    <option selected>Santa Rosa City</option>
                                    <option>Calamba City</option>
                                    <option>Biñan City</option>
                                </select>
                                <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3 top-1/2 h-3 w-3 -translate-y-1/2 text-[#968b7d]" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Barangay</label>
                            <div class="relative">
                                <select class="seller-account-control h-10 w-full appearance-none rounded-xl border border-[#e3ddd4] pl-3.5 pr-9 text-[8.5px]">
                                    <option selected>Balibago</option>
                                    <option>Tagapo</option>
                                    <option>Macabling</option>
                                </select>
                                <svg viewBox="0 0 24 24" class="pointer-events-none absolute right-3 top-1/2 h-3 w-3 -translate-y-1/2 text-[#968b7d]" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">ZIP Code</label>
                            <input type="text" value="4026" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Street / House No. / Building</label>
                            <input type="text" value="123 Marketplace Street" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>
                    </div>

                    <div class="mt-4 flex items-start gap-3 rounded-[13px] border border-[#dbe5ec] bg-[#f7fafc] p-3.5">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-[#647f97] shadow-sm">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"></path><circle cx="12" cy="9" r="2"></circle></svg>
                        </span>
                        <div>
                            <p class="text-[7.5px] font-bold text-[#53636f]">Address service</p>
                            <p class="mt-1 text-[7px] leading-4 text-[#7f8f9a]">Province, municipality, and barangay selections can remain connected to your Philippine address dataset or API.</p>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end border-t border-[#eee8df] pt-4">
                        <button type="button" class="inline-flex h-9 items-center justify-center rounded-lg bg-[#d48f08] px-4 text-[7.5px] font-bold text-white transition hover:bg-[#bd7d05]">
                            Save Address
                        </button>
                    </div>
                </div>
            </section>


            {{-- =====================================================
                VERIFICATION DOCUMENTS
            ====================================================== --}}
            <section id="verification-documents" data-account-section class="seller-account-section overflow-hidden rounded-[18px] border border-[#e9e3da] bg-white">
                <div class="flex flex-col gap-3 border-b border-[#eee8df] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#fff6e6] text-[#b87a12]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                        </span>
                        <div>
                            <h3 class="text-[11px] font-bold text-[#302a24]">Verification Documents</h3>
                            <p class="mt-0.5 text-[7.5px] text-[#91887d]">Government ID and business permit used for seller verification.</p>
                        </div>
                    </div>

                    <span class="w-fit rounded-full border border-[#d6e5dc] bg-[#f4f8f5] px-2.5 py-1 text-[6.5px] font-bold text-[#56816a]">
                        All documents verified
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-3 p-5 sm:p-6 lg:grid-cols-2">
                    <div class="rounded-[14px] border border-[#e9e3da] bg-[#fcfbf9] p-4">
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#e5eaf0] bg-white text-[#647f97]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="8" cy="11" r="2"></circle><path d="M13 10h5"></path><path d="M13 14h4"></path></svg>
                            </span>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-[8.5px] font-bold text-[#3d3730]">Government Valid ID</p>
                                    <span class="rounded-full bg-[#eef6f1] px-2 py-0.5 text-[6px] font-bold text-[#56816a]">Verified</span>
                                </div>

                                <p class="mt-1 truncate text-[7px] text-[#958c80]">seller-valid-id.jpg</p>
                                <p class="mt-0.5 text-[6.5px] text-[#aaa196]">Uploaded Aug 10, 2026</p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button type="button" class="rounded-lg border border-[#ded7cd] bg-white px-3 py-1.5 text-[7px] font-bold text-[#625a50] transition hover:bg-[#fffaf2]">View Document</button>
                                    <button type="button" class="rounded-lg border border-[#ded7cd] bg-white px-3 py-1.5 text-[7px] font-bold text-[#625a50] transition hover:bg-[#fffaf2]">Replace</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[14px] border border-[#e9e3da] bg-[#fcfbf9] p-4">
                        <div class="flex items-start gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#eadfc9] bg-white text-[#ad791f]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 3h10l4 4v14H5z"></path><path d="M15 3v5h5"></path><path d="M9 12h6"></path><path d="M9 16h4"></path></svg>
                            </span>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-[8.5px] font-bold text-[#3d3730]">Business Permit</p>
                                    <span class="rounded-full bg-[#eef6f1] px-2 py-0.5 text-[6px] font-bold text-[#56816a]">Verified</span>
                                </div>

                                <p class="mt-1 truncate text-[7px] text-[#958c80]">business-permit.pdf</p>
                                <p class="mt-0.5 text-[6.5px] text-[#aaa196]">Uploaded Aug 10, 2026</p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button type="button" class="rounded-lg border border-[#ded7cd] bg-white px-3 py-1.5 text-[7px] font-bold text-[#625a50] transition hover:bg-[#fffaf2]">View Document</button>
                                    <button type="button" class="rounded-lg border border-[#ded7cd] bg-white px-3 py-1.5 text-[7px] font-bold text-[#625a50] transition hover:bg-[#fffaf2]">Replace</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 flex items-start gap-3 rounded-[13px] border border-[#eadfc9] bg-[#fffaf3] p-3.5">
                        <svg viewBox="0 0 24 24" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                        <p class="text-[7px] leading-4 text-[#8e7c61]">Replacing a verified document may require another administrator review before the new document becomes active.</p>
                    </div>
                </div>
            </section>


            {{-- =====================================================
                PASSWORD & SECURITY
            ====================================================== --}}
            <section id="password-security" data-account-section class="seller-account-section overflow-hidden rounded-[18px] border border-[#e9e3da] bg-white">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#fff6e6] text-[#b87a12]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path></svg>
                        </span>
                        <div>
                            <h3 class="text-[11px] font-bold text-[#302a24]">Password & Security</h3>
                            <p class="mt-0.5 text-[7.5px] text-[#91887d]">Protect access to your seller account.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 lg:grid-cols-[1fr_280px]">
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Current Password</label>
                            <input type="password" placeholder="Enter current password" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">New Password</label>
                            <input type="password" placeholder="Enter new password" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[8px] font-semibold text-[#5c544a]">Confirm New Password</label>
                            <input type="password" placeholder="Confirm new password" class="seller-account-control h-10 w-full rounded-xl border border-[#e3ddd4] px-3.5 text-[8.5px]">
                        </div>

                        <button type="button" class="inline-flex h-9 items-center justify-center rounded-lg bg-[#d48f08] px-4 text-[7.5px] font-bold text-white transition hover:bg-[#bd7d05]">
                            Update Password
                        </button>
                    </div>

                    <div class="self-start rounded-[14px] border border-[#e9e3da] bg-[#fcfbf9] p-4">
                        <div class="flex items-center gap-2">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-white text-[#8f7952] shadow-sm">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path></svg>
                            </span>
                            <p class="text-[8px] font-bold text-[#514a42]">Password recommendation</p>
                        </div>

                        <ul class="mt-3 space-y-2 text-[7px] leading-4 text-[#8e8579]">
                            <li class="flex gap-2"><span class="text-[#c48a21]">•</span><span>Use at least 8 characters.</span></li>
                            <li class="flex gap-2"><span class="text-[#c48a21]">•</span><span>Include uppercase and lowercase letters.</span></li>
                            <li class="flex gap-2"><span class="text-[#c48a21]">•</span><span>Add a number and symbol.</span></li>
                            <li class="flex gap-2"><span class="text-[#c48a21]">•</span><span>Do not reuse passwords from other accounts.</span></li>
                        </ul>
                    </div>
                </div>
            </section>


            {{-- =====================================================
                NOTIFICATIONS
            ====================================================== --}}
            <section id="seller-notifications" data-account-section class="seller-account-section overflow-hidden rounded-[18px] border border-[#e9e3da] bg-white">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#fff6e6] text-[#b87a12]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg>
                        </span>
                        <div>
                            <h3 class="text-[11px] font-bold text-[#302a24]">Seller Notifications</h3>
                            <p class="mt-0.5 text-[7.5px] text-[#91887d]">Choose the seller activities you want to hear about.</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-[#eee8df]">
                    @php
                        $notifications = [
                            ['title' => 'New Order Notifications', 'desc' => 'Notify me whenever a new customer order arrives.', 'checked' => true],
                            ['title' => 'Low Stock Alerts', 'desc' => 'Notify me when an active product or variant is running low.', 'checked' => true],
                            ['title' => 'Delivery Updates', 'desc' => 'Receive courier pickup and delivery status updates.', 'checked' => true],
                            ['title' => 'Customer Feedback', 'desc' => 'Notify me when buyers leave a new review.', 'checked' => true],
                            ['title' => 'Marketing & Promotions', 'desc' => 'Receive seller campaign and promotion updates.', 'checked' => false],
                        ];
                    @endphp

                    @foreach ($notifications as $notification)
                        <label class="flex cursor-pointer items-center justify-between gap-4 px-5 py-4 transition hover:bg-[#fcfbf9] sm:px-6">
                            <div class="min-w-0">
                                <p class="text-[8.5px] font-bold text-[#3d3730]">{{ $notification['title'] }}</p>
                                <p class="mt-1 text-[7px] leading-4 text-[#958c80]">{{ $notification['desc'] }}</p>
                            </div>

                            <span class="relative inline-flex h-6 w-11 shrink-0 items-center">
                                <input
                                    type="checkbox"
                                    class="peer sr-only"
                                    @checked($notification['checked'])
                                >
                                <span class="absolute inset-0 rounded-full bg-[#d9d5cf] transition peer-checked:bg-[#d48f08]"></span>
                                <span class="absolute left-1 h-4 w-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-5"></span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end border-t border-[#eee8df] px-5 py-4 sm:px-6">
                    <button type="button" class="inline-flex h-9 items-center justify-center rounded-lg bg-[#d48f08] px-4 text-[7.5px] font-bold text-white transition hover:bg-[#bd7d05]">
                        Save Preferences
                    </button>
                </div>
            </section>


            {{-- =====================================================
                ACCOUNT ACTIONS
            ====================================================== --}}
            <section id="account-actions" data-account-section class="seller-account-section overflow-hidden rounded-[18px] border border-[#e9e3da] bg-white">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-[#fff6e6] text-[#b87a12]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4v-.2a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"></path></svg>
                        </span>
                        <div>
                            <h3 class="text-[11px] font-bold text-[#302a24]">Account Actions</h3>
                            <p class="mt-0.5 text-[7.5px] text-[#91887d]">Security and account-level controls.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 p-5 sm:p-6 lg:grid-cols-2">
                    <div class="rounded-[14px] border border-[#e9e3da] bg-[#fcfbf9] p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[8.5px] font-bold text-[#3d3730]">Sign Out Other Sessions</p>
                                <p class="mt-1 text-[7px] leading-4 text-[#958c80]">Log out your seller account from other browsers or devices.</p>
                            </div>

                            <button type="button" class="shrink-0 rounded-lg border border-[#ddd5ca] bg-white px-3 py-1.5 text-[7px] font-bold text-[#675f55] transition hover:bg-[#fffaf2]">
                                Sign Out
                            </button>
                        </div>
                    </div>

                    <div class="rounded-[14px] border border-[#ead2d2] bg-[#fff8f8] p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[8.5px] font-bold text-[#865858]">Request Account Deactivation</p>
                                <p class="mt-1 text-[7px] leading-4 text-[#9b7777]">Request temporary deactivation of your seller account.</p>
                            </div>

                            <button type="button" class="shrink-0 rounded-lg border border-[#dfbbbb] bg-white px-3 py-1.5 text-[7px] font-bold text-[#a65f5f] transition hover:bg-[#fff3f3]">
                                Request
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-2 flex items-start gap-3 rounded-[13px] border border-[#eadfc9] bg-[#fffaf3] p-3.5">
                        <svg viewBox="0 0 24 24" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                        <p class="text-[7px] leading-4 text-[#8e7c61]">Changes to your business name, address, valid ID, or business permit may require administrator verification before they take effect.</p>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <div class="h-5"></div>
</div>


{{-- =============================================================
    SELLER STATUS MODAL
============================================================== --}}
<div id="sellerStatusSummaryModal" class="fixed inset-0 z-[180] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[3px]">
    <div class="w-full max-w-[520px] overflow-hidden rounded-[22px] border border-[#e8dfd3] bg-white shadow-[0_30px_90px_rgba(37,29,19,.24)]">
        <div class="flex items-start justify-between gap-4 border-b border-[#eee8df] px-5 py-4 sm:px-6">
            <div>
                <p class="text-[7px] font-bold uppercase tracking-[.12em] text-[#a8731f]">Seller Account</p>
                <h3 class="mt-1 text-[17px] font-bold tracking-[-.03em] text-[#302a24]">Account Status</h3>
                <p class="mt-1 text-[7.5px] leading-4 text-[#91887d]">Current verification and marketplace standing.</p>
            </div>

            <button id="closeSellerStatusSummary" type="button" class="grid h-9 w-9 place-items-center rounded-xl border border-[#e4ddd3] bg-white text-[#756d63] transition hover:bg-[#fffaf2]" aria-label="Close">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg>
            </button>
        </div>

        <div class="p-5 sm:p-6">
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-[13px] border border-[#d9e7df] bg-[#f6faf7] p-3.5">
                    <p class="text-[6.5px] font-semibold uppercase tracking-[.08em] text-[#7d9184]">Account</p>
                    <p class="mt-1.5 text-[10px] font-bold text-[#56816a]">Active</p>
                </div>

                <div class="rounded-[13px] border border-[#eadfc9] bg-[#fffaf3] p-3.5">
                    <p class="text-[6.5px] font-semibold uppercase tracking-[.08em] text-[#9a8a6d]">Verification</p>
                    <p class="mt-1.5 text-[10px] font-bold text-[#a8731f]">Approved</p>
                </div>

                <div class="rounded-[13px] border border-[#e9e3da] bg-[#fcfbf9] p-3.5">
                    <p class="text-[6.5px] font-semibold uppercase tracking-[.08em] text-[#9b9287]">Warnings</p>
                    <p class="mt-1.5 text-[10px] font-bold text-[#514a42]">0 / 3</p>
                </div>

                <div class="rounded-[13px] border border-[#e9e3da] bg-[#fcfbf9] p-3.5">
                    <p class="text-[6.5px] font-semibold uppercase tracking-[.08em] text-[#9b9287]">Store Rating</p>
                    <p class="mt-1.5 text-[10px] font-bold text-[#514a42]">4.8 / 5</p>
                </div>
            </div>

            <div class="mt-4 rounded-[13px] border border-[#e9e3da] bg-white p-4">
                <div class="flex items-start gap-3">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#f4f8f5] text-[#56816a]">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                    </span>

                    <div>
                        <p class="text-[8px] font-bold text-[#403930]">Seller account is in good standing</p>
                        <p class="mt-1 text-[7px] leading-4 text-[#8f867a]">Your store is verified and currently has full access to seller marketplace tools.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('livewire:navigated', function () {
    const navButtons = Array.from(
        document.querySelectorAll('[data-account-nav]')
    );

    const sections = Array.from(
        document.querySelectorAll('[data-account-section]')
    );

    function setActiveNav(id) {
        navButtons.forEach(function (button) {
            button.dataset.active =
                button.dataset.accountNav === id
                    ? 'true'
                    : 'false';
        });
    }

    navButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const section =
                document.getElementById(
                    this.dataset.accountNav
                );

            if (!section) {
                return;
            }

            setActiveNav(this.dataset.accountNav);

            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });

    if ('IntersectionObserver' in window && sections.length) {
        const observer =
            new IntersectionObserver(
                function (entries) {
                    const visible =
                        entries
                            .filter(entry => entry.isIntersecting)
                            .sort(
                                (a, b) =>
                                    b.intersectionRatio -
                                    a.intersectionRatio
                            );

                    if (
                        visible.length &&
                        visible[0].target.id
                    ) {
                        setActiveNav(
                            visible[0].target.id
                        );
                    }
                },
                {
                    root: null,
                    rootMargin: '-120px 0px -55% 0px',
                    threshold: [0.15, 0.35, 0.6]
                }
            );

        sections.forEach(function (section) {
            observer.observe(section);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Seller Status Modal
    |--------------------------------------------------------------------------
    */
    const statusModal =
        document.getElementById(
            'sellerStatusSummaryModal'
        );

    const openStatus =
        document.getElementById(
            'openSellerStatusSummary'
        );

    const closeStatus =
        document.getElementById(
            'closeSellerStatusSummary'
        );

    function openStatusModal() {
        statusModal?.classList.remove('hidden');
        statusModal?.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeStatusModal() {
        statusModal?.classList.add('hidden');
        statusModal?.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    openStatus?.addEventListener(
        'click',
        openStatusModal
    );

    closeStatus?.addEventListener(
        'click',
        closeStatusModal
    );

    statusModal?.addEventListener(
        'click',
        function (event) {
            if (event.target === statusModal) {
                closeStatusModal();
            }
        }
    );

    document.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key === 'Escape' &&
                statusModal &&
                !statusModal.classList.contains('hidden')
            ) {
                closeStatusModal();
            }
        }
    );
}, { once: true });
</script>
@endpush