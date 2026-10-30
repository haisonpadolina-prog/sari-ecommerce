<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'SARI Logistics')
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: [
                            'Poppins',
                            'sans-serif'
                        ],
                    },
                },
            },
        };
    </script>

    <style>
        :root {
            --logistics-sidebar-width: 244px;
        }

        html,
        body {
            min-height: 100%;
            background: #F4F5F7;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        #logisticsSidebar,
        #logisticsContent {
            transition:
                width .25s ease,
                padding .25s ease,
                transform .25s ease;
        }

        #logisticsContent {
            min-height: 100vh;
            background: #F4F5F7;
        }

        #logisticsSidebarNav {
            scrollbar-width: thin;
            scrollbar-color:
                #e7ddcc
                transparent;
        }

        #logisticsSidebarNav::-webkit-scrollbar {
            width: 4px;
        }

        #logisticsSidebarNav::-webkit-scrollbar-track {
            background: transparent;
        }

        #logisticsSidebarNav::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: #e7ddcc;
        }

        #logisticsSidebarLogoCompact {
            opacity: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Global Logistics action modal
        |--------------------------------------------------------------------------
        */

        #logisticsActionModal {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;

            transition:
                opacity .18s ease,
                visibility .18s ease;
        }

        #logisticsActionModal.is-open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        #logisticsActionModalCard {
            opacity: 0;
            transform:
                translateY(10px)
                scale(.985);

            transition:
                opacity .2s ease,
                transform .2s ease;
        }

        #logisticsActionModal.is-open
        #logisticsActionModalCard {
            opacity: 1;
            transform:
                translateY(0)
                scale(1);
        }

        #logisticsActionModalIcon {
            background: #fbf5e9;
            color: #a8731f;
        }

        #logisticsActionModalConfirm {
            background: #d9930a;
        }

        #logisticsActionModalConfirm:hover {
            background: #c48608;
        }

        #logisticsActionModalCard.is-danger
        #logisticsActionModalIcon {
            background: #fff0f0;
            color: #c24141;
        }

        #logisticsActionModalCard.is-danger
        #logisticsActionModalConfirm {
            background: #c94b4b;
        }

        #logisticsActionModalCard.is-danger
        #logisticsActionModalConfirm:hover {
            background: #b73e3e;
        }

        body.logistics-modal-open {
            overflow: hidden;
        }

        @media (min-width: 1024px) {

            #logisticsSidebar {
                width: var(
                    --logistics-sidebar-width
                );
            }

            #logisticsContent {
                padding-left: var(
                    --logistics-sidebar-width
                );
            }

            body.logistics-sidebar-collapsed {
                --logistics-sidebar-width: 68px;
            }

            body.logistics-sidebar-collapsed
            .logistics-sidebar-label,

            body.logistics-sidebar-collapsed
            .logistics-sidebar-extra,

            body.logistics-sidebar-collapsed
            .logistics-sidebar-section-label,

            body.logistics-sidebar-collapsed
            #logisticsSidebarProfileText {
                display: none;
            }

            body.logistics-sidebar-collapsed
            [data-logistics-sidebar-item] {
                justify-content: center;
            }

            body.logistics-sidebar-collapsed
            #logisticsSidebarNav {
                padding-left: 8px;
                padding-right: 8px;
            }

            body.logistics-sidebar-collapsed
            #logisticsSidebarLogoFull {
                opacity: 0;
            }

            body.logistics-sidebar-collapsed
            #logisticsSidebarLogoCompact {
                opacity: 1;
            }

            body.logistics-sidebar-collapsed
            #logisticsSidebarProfile > a {
                justify-content: center;
                padding-left: 6px;
                padding-right: 6px;
            }
        }

        @media (
            prefers-reduced-motion:
            reduce
        ) {
            #logisticsActionModal,
            #logisticsActionModalCard {
                transition: none;
            }
        }
    </style>

    <style>
/* =========================================================
   MERGED FROM public/css/logistics/seller-parity.css
   Kept page-scoped/shared exactly as the original stylesheet.
========================================================= */

/*
 * SARI Logistics — Seller-parity foundation
 * Visual layer only: no changes to routes, forms, permissions or JS.
 */

:root {
    --logistics-sidebar-width: 238px;
    --logistics-shell-bg: #f4f5f7;
    --logistics-surface: #fff;
    --logistics-ink: #24201b;
    --logistics-muted: #91877b;
    --logistics-border: #ece5da;
    --logistics-gold: #d9950b;
    --logistics-gold-dark: #c88708;
    --logistics-gold-pale: #fff9ef;
    --logistics-shadow: 0 6px 18px rgba(33,24,14,.035);
    --logistics-shadow-hover: 0 12px 27px rgba(43,33,21,.065);
    --logistics-shell-ease: cubic-bezier(.22,1,.36,1);
}

/* Main shell */

html,
body,
#logisticsContent {
    background: var(--logistics-shell-bg) !important;
    background-image: none !important;
}

body {
    color: var(--logistics-ink);
    font-family: Poppins, sans-serif;
}

#logisticsContent > main {
    width: 100%;
    min-height: calc(100vh - 72px);
    padding: 28px 40px 36px;
    background: transparent;
}

/* Sidebar */

#logisticsSidebar {
    width: min(238px, 86vw);
    background: var(--logistics-surface) !important;
    border-right: 1px solid var(--logistics-border) !important;
    box-shadow: none !important;

    transition:
        width 130ms var(--logistics-shell-ease),
        transform 180ms var(--logistics-shell-ease) !important;
}

#logisticsContent {
    transition:
        padding-left 130ms var(--logistics-shell-ease) !important;
}

#logisticsSidebarBrand {
    min-height: 74px !important;
    background: #fff !important;
    border-bottom-color: #eee7dc !important;
}

#logisticsSidebarLogoStage {
    height: 44px !important;
}

#logisticsSidebarLogoFull {
    width: 102px !important;
}

#logisticsSidebarNav {
    padding: 14px 10px 16px !important;
    scrollbar-color: #ddd4c7 transparent !important;
}

#logisticsSidebarNav::-webkit-scrollbar-thumb {
    background: #ddd4c7 !important;
}

#logisticsSidebarNav .logistics-sidebar-section-label {
    margin: 10px 8px 7px !important;
    padding: 0 !important;
    color: #9a7840 !important;
    font-size: 7.5px !important;
    font-weight: 700 !important;
    letter-spacing: .13em !important;
    line-height: 1.15 !important;
}

#logisticsSidebarNav > div.border-t {
    margin: 11px 6px 9px !important;
    border-color: #eee7dd !important;
}

#logisticsSidebarNav .logistics-sidebar-item {
    min-height: 39px !important;
    margin-bottom: 2px !important;
    padding: 8px 10px !important;
    gap: 10px !important;

    border: 1px solid transparent !important;
    border-radius: 10px !important;

    font-size: 10.5px !important;
    line-height: 1.15 !important;

    box-shadow: none;
    transform: none !important;

    transition:
        background-color 120ms ease,
        border-color 120ms ease,
        color 120ms ease,
        box-shadow 120ms ease !important;
}

#logisticsSidebarNav .logistics-sidebar-item svg {
    width: 16px !important;
    height: 16px !important;
    stroke-width: 1.75;
}

/* Inactive navigation hover */

#logisticsSidebarNav
.logistics-sidebar-item:not([class*="bg-[#d9930a]"]):hover {
    background: var(--logistics-gold-pale) !important;
    border-color: #eadfcf !important;
    color: #94620a !important;
    box-shadow: none !important;
}

/* Active navigation */

#logisticsSidebarNav
.logistics-sidebar-item[class*="bg-[#d9930a]"] {
    background: var(--logistics-gold) !important;
    border-color: var(--logistics-gold) !important;
    color: #fff !important;

    box-shadow:
        0 5px 12px rgba(194,132,10,.12) !important;
}

#logisticsSidebarNav
.logistics-sidebar-item[class*="bg-[#d9930a]"]:hover {
    background: var(--logistics-gold-dark) !important;
    border-color: var(--logistics-gold-dark) !important;
    color: #fff !important;
}

#logisticsSidebarNav
.logistics-sidebar-item[class*="bg-[#d9930a]"] svg {
    color: #fff !important;
}

/* Sidebar profile */

#logisticsSidebarProfile {
    padding: 9px 10px !important;
    background: #fff !important;
    border-top-color: #eee7dc !important;
}

#logisticsSidebarProfile > a {
    min-height: 54px !important;
    border: 1px solid #e9e0d3 !important;
    border-radius: 11px !important;
    background: #fff !important;
    padding: 8px 9px !important;

    box-shadow:
        0 3px 10px rgba(40,31,21,.02);

    transition:
        background-color 120ms ease,
        border-color 120ms ease,
        box-shadow 120ms ease !important;
}

#logisticsSidebarProfile > a:hover {
    background: #fffdf9 !important;
    border-color: #dcc9a7 !important;

    box-shadow:
        0 4px 12px rgba(40,31,21,.03);
}

/* Header */

#logisticsHeader {
    background: #fff !important;
    border-bottom: 1px solid #eee4d3 !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
}

#logisticsHeader > div {
    min-height: 72px !important;
    padding-inline: 18px !important;
}

#logisticsHeader h1 {
    color: #191712 !important;
    letter-spacing: -.025em;
}

#logisticsHeader input[type="search"] {
    background: #fff !important;
    border-color: #e3dcd1 !important;
    border-radius: 11px !important;

    transition:
        border-color 120ms ease,
        box-shadow 120ms ease !important;
}

#logisticsHeader input[type="search"]:focus {
    border-color: #d9950b !important;

    box-shadow:
        0 0 0 3px rgba(216,155,16,.09) !important;
}

/* Icon-only notification bell */

#logisticsNotificationButton {
    border-color: transparent !important;
    background: transparent !important;
    box-shadow: none !important;
    transform: none !important;
    color: #475467 !important;
}

#logisticsNotificationButton:hover,
#logisticsNotificationButton:focus-visible {
    background: transparent !important;
    border-color: transparent !important;
    color: #c9890b !important;
    box-shadow: none !important;
}

/* Menu buttons */

#logisticsMenuButton,
#logisticsSidebarToggle {
    border-radius: 10px !important;
    background: #fff !important;
    border-color: #e5ded2 !important;

    box-shadow:
        0 2px 5px rgba(40,31,21,.035) !important;

    transition:
        background-color 120ms ease,
        color 120ms ease,
        border-color 120ms ease !important;
}

#logisticsMenuButton:hover,
#logisticsSidebarToggle:hover {
    background: #fff9ef !important;
    color: #a96f09 !important;
    border-color: #dec99f !important;
}

/* Shared Logistics workspace colors */

:is(
    .pickup-page,
    .intake-page,
    .sorting-page,
    .transfer-page,
    .lastmile-page,
    .monitor-page,
    .reports-page,
    .account-page
) {
    color: var(--logistics-ink);
}

/* Uniform form focus states */

:is(
    .pickup-page,
    .intake-page,
    .sorting-page,
    .transfer-page,
    .lastmile-page,
    .monitor-page,
    .reports-page,
    .account-page
)
:is(
    input:not([type="checkbox"]):not([type="radio"]),
    select,
    textarea
) {
    transition:
        background-color 140ms ease,
        border-color 140ms ease,
        box-shadow 140ms ease !important;
}

:is(
    .pickup-page,
    .intake-page,
    .sorting-page,
    .transfer-page,
    .lastmile-page,
    .monitor-page,
    .reports-page,
    .account-page
)
:is(
    input:not([type="checkbox"]):not([type="radio"]),
    select,
    textarea
):focus-visible {
    outline: none;
    border-color: #d9950b !important;

    box-shadow:
        0 0 0 3px rgba(216,155,16,.09) !important;
}

/* Keyboard accessibility */

:is(
    .pickup-page,
    .intake-page,
    .sorting-page,
    .transfer-page,
    .lastmile-page,
    .monitor-page,
    .reports-page,
    .account-page
)
:is(
    button,
    a,
    summary,
    select,
    input,
    textarea
):focus-visible {
    outline: 2px solid rgba(216,155,16,.7);
    outline-offset: 2px;
}

/* Seller-style card elevation */

:is(
    .pickup-stat,
    .pickup-batch,
    .intake-stat,
    .sorting-stat,
    .transfer-stat,
    .transfer-manifest,
    .lastmile-stat,
    .lastmile-area-card,
    .monitor-stat,
    .monitor-order-card,
    .reports-stat,
    .reports-card,
    .account-card
) {
    box-shadow: var(--logistics-shadow);

    transition:
        transform 180ms ease,
        box-shadow 180ms ease,
        border-color 180ms ease !important;
}

@media (hover: hover) and (pointer: fine) {
    :is(
        .pickup-stat,
        .pickup-batch,
        .intake-stat,
        .sorting-stat,
        .transfer-stat,
        .transfer-manifest,
        .lastmile-stat,
        .lastmile-area-card,
        .monitor-stat,
        .monitor-order-card,
        .reports-stat,
        .reports-card,
        .account-card
    ):hover {
        transform: translateY(-1px);
        box-shadow: var(--logistics-shadow-hover);
    }
}

/* Desktop collapsed state */

@media (min-width: 1024px) {
    body.logistics-sidebar-collapsed {
        --logistics-sidebar-width: 76px;
    }

    #logisticsSidebar {
        width: var(--logistics-sidebar-width) !important;
    }

    #logisticsContent {
        padding-left: var(--logistics-sidebar-width) !important;
    }

    body.logistics-sidebar-collapsed #logisticsSidebarNav {
        padding: 12px 9px !important;
    }

    body.logistics-sidebar-collapsed
    #logisticsSidebarNav .logistics-sidebar-item {
        justify-content: center !important;
        padding-inline: 0 !important;
    }

    body.logistics-sidebar-collapsed #logisticsSidebarProfile {
        padding: 9px !important;
    }
}

/* Responsive content padding */

@media (max-width: 1199px) {
    #logisticsContent > main {
        padding: 24px 26px 32px;
    }
}

@media (max-width: 767px) {
    #logisticsContent > main {
        padding: 18px 16px 24px;
    }

    #logisticsHeader > div {
        padding-inline: 14px !important;
    }
}

/* Reduced motion */

@media (prefers-reduced-motion: reduce) {
    #logisticsSidebar,
    #logisticsContent,
    #logisticsSidebarNav *,
    #logisticsHeader *,
    :is(
        .pickup-stat,
        .pickup-batch,
        .intake-stat,
        .sorting-stat,
        .transfer-stat,
        .transfer-manifest,
        .lastmile-stat,
        .lastmile-area-card,
        .monitor-stat,
        .monitor-order-card,
        .reports-stat,
        .reports-card,
        .account-card
    ) {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
        transform: none !important;
    }
}
</style>

    @stack('styles')
    {{-- Buyer-inspired visual system: loaded last, presentation only. --}}
    <link rel="stylesheet" href="{{ asset('css/logistics/buyer-parity.css') }}?v=20261029-1">
</head>

<body
    class="
        min-h-screen
        bg-[#F4F5F7]
        font-poppins
        text-[#1f1b16]
        antialiased
    "
>

    <div class="min-h-screen">

        @include('components.logistics.sidebar')

        <div
            id="logisticsContent"
            class="min-h-screen"
        >

            @include('components.logistics.header')

            <main
                class="
                    w-full
                    px-4 py-4
                    sm:px-5 sm:py-5
                    lg:px-6
                    xl:px-7
                    2xl:px-8
                "
            >
                @yield('content')
            </main>

        </div>

        <button
            id="logisticsSidebarOverlay"
            type="button"
            class="
                fixed inset-0 z-40
                hidden
                bg-black/30
                backdrop-blur-[1px]
                lg:hidden
            "
            aria-label="Close logistics menu"
        ></button>

    </div>


    {{-- =====================================================
        LOGOUT MODAL
    ====================================================== --}}

    <div
        id="logisticsLogoutModal"
        class="
            fixed inset-0 z-[120]
            hidden items-center justify-center
            bg-black/40 p-4
            backdrop-blur-[2px]
        "
        role="dialog"
        aria-modal="true"
        aria-labelledby="logisticsLogoutModalTitle"
        aria-describedby="logisticsLogoutModalDescription"
    >

        <div
            id="logisticsLogoutModalCard"
            class="
                w-full max-w-[400px]
                rounded-[18px]
                border border-[#e8dfd3]
                bg-white
                p-5
                shadow-[0_30px_80px_rgba(46,29,25,.22)]
            "
        >

            <div
                class="
                    grid h-10 w-10
                    place-items-center
                    rounded-[12px]
                    bg-[#fbf5e9]
                    text-[#a8731f]
                "
            >

                <svg
                    viewBox="0 0 24 24"
                    class="h-[18px] w-[18px]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M14 3h7v18h-7"></path>
                </svg>

            </div>


            <h3
                id="logisticsLogoutModalTitle"
                class="
                    mt-4
                    text-[16px]
                    font-bold
                    tracking-[-0.02em]
                    text-[#28221b]
                "
            >
                Log out of SARI Logistics?
            </h3>


            <p
                id="logisticsLogoutModalDescription"
                class="
                    mt-2
                    text-[9px]
                    leading-5
                    text-[#81786c]
                "
            >
                Your Logistics session will be securely ended
                and you will return to the SARI login page.
            </p>


            <div
                class="
                    mt-5 flex
                    justify-end gap-2
                "
            >

                <button
                    id="logisticsCancelLogout"
                    type="button"
                    class="
                        h-10 rounded-[10px]
                        border border-[#e0d7ca]
                        bg-white
                        px-4
                        text-[9px]
                        font-semibold
                        text-[#62594e]
                        transition
                        hover:bg-[#fcf8f1]
                    "
                >
                    Cancel
                </button>


                <form
                    method="POST"
                    action="{{ route('logistics.logout') }}"
                >
                    @csrf

                    <button
                        id="logisticsConfirmLogout"
                        type="submit"
                        class="
                            h-10 rounded-[10px]
                            bg-[#d9930a]
                            px-4
                            text-[9px]
                            font-semibold
                            text-white
                            shadow-[0_6px_16px_rgba(217,147,10,.18)]
                            transition
                            hover:bg-[#c48608]
                        "
                    >
                        Log Out
                    </button>
                </form>

            </div>

        </div>

    </div>


    {{-- =====================================================
        GLOBAL OPERATION CONFIRMATION MODAL
    ====================================================== --}}

    <div
        id="logisticsActionModal"
        class="
            fixed inset-0 z-[150]
            flex items-center justify-center
            bg-black/40 p-4
            backdrop-blur-[2px]
        "
        role="dialog"
        aria-modal="true"
        aria-labelledby="logisticsActionModalTitle"
        aria-describedby="logisticsActionModalMessage"
        aria-hidden="true"
    >

        <div
            id="logisticsActionModalCard"
            class="
                w-full max-w-[430px]
                overflow-hidden
                rounded-[20px]
                border border-[#e8dfd3]
                bg-white
                shadow-[0_30px_80px_rgba(46,29,25,.24)]
            "
        >

            <div class="p-5 sm:p-6">

                <div
                    class="
                        flex
                        items-start
                        justify-between
                        gap-4
                    "
                >

                    <div
                        id="logisticsActionModalIcon"
                        class="
                            grid h-11 w-11
                            shrink-0
                            place-items-center
                            rounded-[13px]
                        "
                    >

                        <svg
                            id="logisticsActionModalDefaultIcon"
                            viewBox="0 0 24 24"
                            class="h-[19px] w-[19px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M12 3v12"></path>
                            <path d="m8 11 4 4 4-4"></path>
                            <path d="M5 21h14"></path>
                        </svg>


                        <svg
                            id="logisticsActionModalDangerIcon"
                            viewBox="0 0 24 24"
                            class="hidden h-[19px] w-[19px]"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 7h16"></path>
                            <path d="M9 7V4h6v3"></path>
                            <path d="m7 7 1 13h8l1-13"></path>
                            <path d="M10 11v5"></path>
                            <path d="M14 11v5"></path>
                        </svg>

                    </div>


                    <button
                        id="logisticsActionModalClose"
                        type="button"
                        class="
                            grid h-9 w-9
                            shrink-0
                            place-items-center
                            rounded-[10px]
                            text-[#8b8174]
                            transition
                            hover:bg-[#f8f4ed]
                            hover:text-[#453d33]
                        "
                        aria-label="Close confirmation"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M6 6l12 12"></path>
                            <path d="M18 6 6 18"></path>
                        </svg>

                    </button>

                </div>


                <p
                    class="
                        mt-4
                        text-[7px]
                        font-semibold
                        uppercase
                        tracking-[.14em]
                        text-[#a28f70]
                    "
                >
                    SARI Logistics · Operation Confirmation
                </p>


                <h3
                    id="logisticsActionModalTitle"
                    class="
                        mt-2
                        text-[16px]
                        font-bold
                        tracking-[-0.02em]
                        text-[#28221b]
                    "
                >
                    Confirm Action
                </h3>


                <p
                    id="logisticsActionModalMessage"
                    class="
                        mt-2
                        text-[9px]
                        leading-5
                        text-[#756c60]
                    "
                ></p>


                <div
                    class="
                        mt-4
                        rounded-[12px]
                        border border-[#eee5d7]
                        bg-[#fcfaf6]
                        px-4 py-3
                    "
                >

                    <div class="flex items-start gap-2.5">

                        <svg
                            viewBox="0 0 24 24"
                            class="
                                mt-[1px]
                                h-4 w-4
                                shrink-0
                                text-[#a8731f]
                            "
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 8v5"></path>
                            <path d="M12 16h.01"></path>
                        </svg>


                        <p
                            id="logisticsActionModalHint"
                            class="
                                text-[8px]
                                leading-4
                                text-[#8a7f71]
                            "
                        >
                            Review the operational details before
                            continuing. The backend will validate
                            the current parcel and Rider state again.
                        </p>

                    </div>

                </div>

            </div>


            <div
                class="
                    flex
                    flex-col-reverse
                    gap-2
                    border-t border-[#eee5d7]
                    bg-[#fffdf9]
                    px-5 py-4
                    sm:flex-row
                    sm:justify-end
                "
            >

                <button
                    id="logisticsActionModalCancel"
                    type="button"
                    class="
                        h-10
                        rounded-[10px]
                        border border-[#e0d7ca]
                        bg-white
                        px-5
                        text-[9px]
                        font-semibold
                        text-[#62594e]
                        transition
                        hover:bg-[#fcf8f1]
                    "
                >
                    Cancel
                </button>


                <button
                    id="logisticsActionModalConfirm"
                    type="button"
                    class="
                        h-10
                        rounded-[10px]
                        px-5
                        text-[9px]
                        font-semibold
                        text-white
                        shadow-[0_6px_16px_rgba(217,147,10,.18)]
                        transition
                        disabled:cursor-not-allowed
                        disabled:opacity-60
                    "
                >
                    Confirm
                </button>

            </div>

        </div>

    </div>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const body =
                    document.body;

                const sidebar =
                    document.getElementById(
                        'logisticsSidebar'
                    );

                const overlay =
                    document.getElementById(
                        'logisticsSidebarOverlay'
                    );

                const menuButton =
                    document.getElementById(
                        'logisticsMenuButton'
                    );

                const closeButton =
                    document.getElementById(
                        'logisticsSidebarClose'
                    );

                const desktopToggle =
                    document.getElementById(
                        'logisticsSidebarToggle'
                    );

                const notificationButton =
                    document.getElementById(
                        'logisticsNotificationButton'
                    );

                const notificationPanel =
                    document.getElementById(
                        'logisticsNotificationPanel'
                    );

                const logoutButton =
                    document.getElementById(
                        'logisticsLogoutButton'
                    );

                const logoutModal =
                    document.getElementById(
                        'logisticsLogoutModal'
                    );

                const cancelLogout =
                    document.getElementById(
                        'logisticsCancelLogout'
                    );

                /*
                |--------------------------------------------------------------------------
                | Global operation confirmation modal
                |--------------------------------------------------------------------------
                */

                const actionModal =
                    document.getElementById(
                        'logisticsActionModal'
                    );

                const actionModalCard =
                    document.getElementById(
                        'logisticsActionModalCard'
                    );

                const actionModalTitle =
                    document.getElementById(
                        'logisticsActionModalTitle'
                    );

                const actionModalMessage =
                    document.getElementById(
                        'logisticsActionModalMessage'
                    );

                const actionModalHint =
                    document.getElementById(
                        'logisticsActionModalHint'
                    );

                const actionModalCancel =
                    document.getElementById(
                        'logisticsActionModalCancel'
                    );

                const actionModalConfirm =
                    document.getElementById(
                        'logisticsActionModalConfirm'
                    );

                const actionModalClose =
                    document.getElementById(
                        'logisticsActionModalClose'
                    );

                const actionDefaultIcon =
                    document.getElementById(
                        'logisticsActionModalDefaultIcon'
                    );

                const actionDangerIcon =
                    document.getElementById(
                        'logisticsActionModalDangerIcon'
                    );

                /*
                |--------------------------------------------------------------------------
                | Keep the original browser confirm as a safety fallback
                |--------------------------------------------------------------------------
                */

                const nativeConfirm =
                    window.confirm.bind(
                        window
                    );

                let pendingConfirmation =
                    null;

                let lastFocusedElement =
                    null;

                let currentSubmitContext =
                    null;

                let modalBusy =
                    false;


                /*
                |--------------------------------------------------------------------------
                | Sidebar
                |--------------------------------------------------------------------------
                */

                function openSidebar() {

                    sidebar
                        ?.classList
                        .remove(
                            '-translate-x-full'
                        );

                    sidebar
                        ?.classList
                        .add(
                            'translate-x-0'
                        );

                    overlay
                        ?.classList
                        .remove(
                            'hidden'
                        );

                    menuButton
                        ?.setAttribute(
                            'aria-expanded',
                            'true'
                        );
                }


                function closeSidebar() {

                    sidebar
                        ?.classList
                        .remove(
                            'translate-x-0'
                        );

                    sidebar
                        ?.classList
                        .add(
                            '-translate-x-full'
                        );

                    overlay
                        ?.classList
                        .add(
                            'hidden'
                        );

                    menuButton
                        ?.setAttribute(
                            'aria-expanded',
                            'false'
                        );
                }


                function closeNotifications() {

                    notificationPanel
                        ?.classList
                        .add(
                            'hidden'
                        );

                    notificationButton
                        ?.setAttribute(
                            'aria-expanded',
                            'false'
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | Logout modal
                |--------------------------------------------------------------------------
                */

                function openLogoutModal() {

                    lastFocusedElement =
                        document.activeElement;

                    logoutModal
                        ?.classList
                        .remove(
                            'hidden'
                        );

                    logoutModal
                        ?.classList
                        .add(
                            'flex'
                        );

                    body
                        .classList
                        .add(
                            'logistics-modal-open'
                        );

                    window.setTimeout(
                        function () {
                            cancelLogout
                                ?.focus();
                        },
                        20
                    );
                }


                function closeLogoutModal() {

                    logoutModal
                        ?.classList
                        .add(
                            'hidden'
                        );

                    logoutModal
                        ?.classList
                        .remove(
                            'flex'
                        );

                    body
                        .classList
                        .remove(
                            'logistics-modal-open'
                        );

                    if (
                        lastFocusedElement
                        instanceof HTMLElement
                    ) {
                        lastFocusedElement
                            .focus();
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Confirmation modal copy
                |--------------------------------------------------------------------------
                */

                function confirmationPresentation(
                    message
                ) {

                    const text =
                        String(
                            message
                            ?? ''
                        )
                            .trim();

                    const lower =
                        text
                            .toLowerCase();


                    if (
                        lower.includes(
                            'delete this unused sorting facility'
                        )
                    ) {
                        return {
                            title:
                                'Delete Sorting Facility?',

                            confirmLabel:
                                'Delete Facility',

                            hint:
                                'This removes the unused facility from your Logistics network. This action should only continue when the facility is no longer referenced by active operations.',

                            danger:
                                true,
                        };
                    }


                    if (
                        lower.startsWith(
                            'assign '
                        )
                        &&
                        lower.includes(
                            'seller pickup'
                        )
                    ) {
                        return {
                            title:
                                'Assign Pickup Rider?',

                            confirmLabel:
                                'Assign Rider',

                            hint:
                                'The selected Rider will receive this Seller pickup batch. The backend will re-check Rider availability and parcel eligibility before assignment.',

                            danger:
                                false,
                        };
                    }


                    if (
                        lower.startsWith(
                            'assign '
                        )
                        &&
                        lower.includes(
                            ' to '
                        )
                    ) {
                        return {
                            title:
                                'Assign Last-Mile Rider?',

                            confirmLabel:
                                'Assign Rider',

                            hint:
                                'The selected local Rider will receive the destination-hub batch for final barangay delivery.',

                            danger:
                                false,
                        };
                    }


                    if (
                        lower.startsWith(
                            'verify '
                        )
                        ||
                        lower.includes(
                            'verify this seller pickup request'
                        )
                    ) {
                        return {
                            title:
                                'Verify Pickup Request?',

                            confirmLabel:
                                'Verify Request',

                            hint:
                                'Verified parcels become eligible for Pickup Rider assignment. The backend will validate the order and routing state before saving.',

                            danger:
                                false,
                        };
                    }


                    if (
                        lower.includes(
                            'physical receipt'
                        )
                        &&
                        lower.includes(
                            'origin sorting hub'
                        )
                    ) {
                        return {
                            title:
                                'Receive Parcel at Origin Hub?',

                            confirmLabel:
                                'Confirm Receipt',

                            hint:
                                'Confirm only after the parcel has been physically handed over by the Pickup Rider at the assigned origin hub.',

                            danger:
                                false,
                        };
                    }


                    if (
                        lower.startsWith(
                            'mark '
                        )
                        &&
                        lower.includes(
                            ' as sorted '
                        )
                    ) {
                        return {
                            title:
                                'Confirm Parcel Sorting?',

                            confirmLabel:
                                'Mark Sorted',

                            hint:
                                'The parcel will move from origin-hub intake into the selected sorting zone and become eligible for the next Logistics leg.',

                            danger:
                                false,
                        };
                    }


                    if (
                        lower.startsWith(
                            'create a hub transfer manifest'
                        )
                    ) {
                        return {
                            title:
                                'Create Hub Transfer Manifest?',

                            confirmLabel:
                                'Create Manifest',

                            hint:
                                'The selected sorted parcels will be grouped into one linehaul manifest using the vehicle and driver information entered on this page.',

                            danger:
                                false,
                        };
                    }


                    if (
                        lower.startsWith(
                            'dispatch '
                        )
                    ) {
                        return {
                            title:
                                'Dispatch Hub Transfer?',

                            confirmLabel:
                                'Dispatch Transfer',

                            hint:
                                'This confirms that the manifest has physically departed the origin hub and its parcels will move into linehaul transit.',

                            danger:
                                false,
                        };
                    }


                    if (
                        lower.includes(
                            'physical receipt'
                        )
                        &&
                        lower.includes(
                            'destination hub'
                        )
                    ) {
                        return {
                            title:
                                'Receive Transfer at Destination Hub?',

                            confirmLabel:
                                'Confirm Receipt',

                            hint:
                                'Confirm only after the transfer and its parcels have physically arrived at the destination sorting hub.',

                            danger:
                                false,
                        };
                    }


                    return {
                        title:
                            'Confirm Logistics Action?',

                        confirmLabel:
                            'Confirm',

                        hint:
                            'Review the operational details before continuing. The backend will validate the current parcel and Rider state again.',

                        danger:
                            false,
                    };
                }


                /*
                |--------------------------------------------------------------------------
                | Fix external form-associated order_ids[]
                |--------------------------------------------------------------------------
                |
                | Last-Mile hidden inputs use:
                |
                | form="lastmile-form-..."
                |
                | so they are valid form controls even though they are not
                | descendants of the <form>. form.querySelectorAll() therefore
                | returns zero. input.form gives us the correct association.
                |--------------------------------------------------------------------------
                */

                function normalizedConfirmationMessage(
                    message,
                    form
                ) {

                    let output =
                        String(
                            message
                            ?? ''
                        );


                    if (
                        ! form
                        ||
                        ! form.matches(
                            '[data-lastmile-form]'
                        )
                    ) {
                        return output;
                    }


                    const parcelCount =
                        Array
                            .from(
                                document
                                    .querySelectorAll(
                                        'input[name="order_ids[]"]'
                                    )
                            )
                            .filter(
                                function (input) {
                                    return input.form
                                        === form;
                                }
                            )
                            .length;


                    output =
                        output.replace(
                            /Assign\s+\d+\s+parcels?/i,
                            'Assign '
                            + parcelCount
                            + ' '
                            + (
                                parcelCount
                                === 1
                                    ? 'parcel'
                                    : 'parcels'
                            )
                        );


                    return output;
                }


                /*
                |--------------------------------------------------------------------------
                | Open / close action modal
                |--------------------------------------------------------------------------
                */

                function openActionModal(
                    message,
                    form,
                    submitter
                ) {

                    if (
                        ! actionModal
                        ||
                        ! actionModalCard
                    ) {
                        return;
                    }


                    const normalizedMessage =
                        normalizedConfirmationMessage(
                            message,
                            form
                        );

                    const presentation =
                        confirmationPresentation(
                            normalizedMessage
                        );


                    pendingConfirmation = {
                        form:
                            form,

                        submitter:
                            submitter,

                        message:
                            normalizedMessage,
                    };


                    lastFocusedElement =
                        document.activeElement;


                    actionModalTitle.textContent =
                        presentation.title;

                    actionModalMessage.textContent =
                        normalizedMessage;

                    actionModalHint.textContent =
                        presentation.hint;

                    actionModalConfirm.textContent =
                        presentation.confirmLabel;


                    actionModalCard
                        .classList
                        .toggle(
                            'is-danger',
                            presentation.danger
                        );


                    actionDefaultIcon
                        ?.classList
                        .toggle(
                            'hidden',
                            presentation.danger
                        );

                    actionDangerIcon
                        ?.classList
                        .toggle(
                            'hidden',
                            ! presentation.danger
                        );


                    actionModalConfirm.disabled =
                        false;

                    modalBusy =
                        false;


                    actionModal
                        .classList
                        .add(
                            'is-open'
                        );

                    actionModal
                        .setAttribute(
                            'aria-hidden',
                            'false'
                        );

                    body
                        .classList
                        .add(
                            'logistics-modal-open'
                        );


                    window.setTimeout(
                        function () {
                            actionModalCancel
                                ?.focus();
                        },
                        20
                    );
                }


                function closeActionModal(
                    restoreFocus = true
                ) {

                    if (
                        ! actionModal
                    ) {
                        return;
                    }


                    actionModal
                        .classList
                        .remove(
                            'is-open'
                        );

                    actionModal
                        .setAttribute(
                            'aria-hidden',
                            'true'
                        );

                    actionModalCard
                        ?.classList
                        .remove(
                            'is-danger'
                        );

                    actionModalConfirm.disabled =
                        false;

                    modalBusy =
                        false;

                    pendingConfirmation =
                        null;

                    body
                        .classList
                        .remove(
                            'logistics-modal-open'
                        );


                    if (
                        restoreFocus
                        &&
                        lastFocusedElement
                        instanceof HTMLElement
                    ) {
                        lastFocusedElement
                            .focus();
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Capture the form currently being submitted
                |--------------------------------------------------------------------------
                |
                | Existing Logistics page scripts call window.confirm()
                | inside their submit handlers.
                |
                | This capture listener runs first and lets the global
                | window.confirm override know which form initiated the action.
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'submit',
                    function (event) {

                        const form =
                            event.target;

                        if (
                            ! (
                                form
                                instanceof HTMLFormElement
                            )
                        ) {
                            return;
                        }


                        currentSubmitContext = {
                            form:
                                form,

                            submitter:
                                event.submitter
                                instanceof HTMLElement
                                    ? event.submitter
                                    : (
                                        document.activeElement
                                        instanceof HTMLElement
                                            ? document.activeElement
                                            : null
                                    ),
                        };


                        window.setTimeout(
                            function () {

                                if (
                                    currentSubmitContext
                                    ?.form
                                    === form
                                ) {
                                    currentSubmitContext =
                                        null;
                                }

                            },
                            0
                        );

                    },
                    true
                );


                /*
                |--------------------------------------------------------------------------
                | Replace audited native form confirmations
                |--------------------------------------------------------------------------
                |
                | Existing page handlers still call:
                |
                | window.confirm(message)
                |
                | The first call opens our branded modal and returns false,
                | so the page prevents the original submit.
                |
                | After the user confirms, requestSubmit() runs the form again
                | with an approval flag. On that second pass this function
                | returns true synchronously, allowing all existing handlers,
                | validation and backend routes to remain unchanged.
                |--------------------------------------------------------------------------
                */

                window.confirm =
                    function (
                        message
                    ) {

                        const context =
                            currentSubmitContext;


                        /*
                        | Safety fallback for a future confirm()
                        | that is not triggered by form submission.
                        */
                        if (
                            ! context
                            ||
                            ! (
                                context.form
                                instanceof HTMLFormElement
                            )
                        ) {
                            return nativeConfirm(
                                message
                            );
                        }


                        const form =
                            context.form;


                        if (
                            form.dataset
                                .sariLogisticsConfirmApproved
                            === '1'
                        ) {
                            delete form.dataset
                                .sariLogisticsConfirmApproved;

                            return true;
                        }


                        openActionModal(
                            message,
                            form,
                            context.submitter
                        );


                        return false;
                    };


                /*
                |--------------------------------------------------------------------------
                | Continue approved form submission
                |--------------------------------------------------------------------------
                */

                actionModalConfirm
                    ?.addEventListener(
                        'click',
                        function () {

                            if (
                                modalBusy
                                ||
                                ! pendingConfirmation
                            ) {
                                return;
                            }


                            const form =
                                pendingConfirmation
                                    .form;

                            const submitter =
                                pendingConfirmation
                                    .submitter;


                            if (
                                ! (
                                    form
                                    instanceof HTMLFormElement
                                )
                            ) {
                                closeActionModal();

                                return;
                            }


                            /*
                            | requestSubmit() normally performs
                            | constraint validation before firing
                            | another submit event.
                            */
                            if (
                                typeof form
                                    .reportValidity
                                === 'function'
                                &&
                                ! form.reportValidity()
                            ) {
                                closeActionModal(
                                    false
                                );

                                return;
                            }


                            modalBusy =
                                true;

                            actionModalConfirm.disabled =
                                true;


                            form.dataset
                                .sariLogisticsConfirmApproved =
                                    '1';


                            /*
                            | Close before resubmitting so the
                            | interface never appears frozen.
                            */
                            actionModal
                                ?.classList
                                .remove(
                                    'is-open'
                                );

                            actionModal
                                ?.setAttribute(
                                    'aria-hidden',
                                    'true'
                                );

                            body
                                .classList
                                .remove(
                                    'logistics-modal-open'
                                );


                            window.setTimeout(
                                function () {

                                    /*
                                    | If validation or another listener
                                    | prevented a submit event entirely,
                                    | do not leave stale approval behind.
                                    */
                                    delete form.dataset
                                        .sariLogisticsConfirmApproved;

                                },
                                100
                            );


                            try {

                                if (
                                    typeof form
                                        .requestSubmit
                                    === 'function'
                                ) {

                                    if (
                                        submitter
                                        instanceof HTMLElement
                                        &&
                                        submitter
                                            .isConnected
                                        &&
                                        submitter
                                            .form
                                        === form
                                    ) {
                                        form.requestSubmit(
                                            submitter
                                        );
                                    } else {
                                        form.requestSubmit();
                                    }

                                } else {

                                    HTMLFormElement
                                        .prototype
                                        .submit
                                        .call(
                                            form
                                        );
                                }

                            } catch (error) {

                                actionModalConfirm.disabled =
                                    false;

                                modalBusy =
                                    false;

                                delete form.dataset
                                    .sariLogisticsConfirmApproved;

                                actionModal
                                    ?.classList
                                    .add(
                                        'is-open'
                                    );

                                actionModal
                                    ?.setAttribute(
                                        'aria-hidden',
                                        'false'
                                    );

                                body
                                    .classList
                                    .add(
                                        'logistics-modal-open'
                                    );

                                console.error(
                                    'Unable to continue Logistics form submission.',
                                    error
                                );
                            }
                        }
                    );


                actionModalCancel
                    ?.addEventListener(
                        'click',
                        function () {
                            closeActionModal();
                        }
                    );


                actionModalClose
                    ?.addEventListener(
                        'click',
                        function () {
                            closeActionModal();
                        }
                    );


                actionModal
                    ?.addEventListener(
                        'click',
                        function (event) {

                            if (
                                event.target
                                === actionModal
                                &&
                                ! modalBusy
                            ) {
                                closeActionModal();
                            }
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | Shell interactions
                |--------------------------------------------------------------------------
                */

                menuButton
                    ?.addEventListener(
                        'click',
                        openSidebar
                    );

                closeButton
                    ?.addEventListener(
                        'click',
                        closeSidebar
                    );

                overlay
                    ?.addEventListener(
                        'click',
                        closeSidebar
                    );


                desktopToggle
                    ?.addEventListener(
                        'click',
                        function () {

                            body
                                .classList
                                .toggle(
                                    'logistics-sidebar-collapsed'
                                );

                            desktopToggle
                                .setAttribute(
                                    'aria-expanded',
                                    body
                                        .classList
                                        .contains(
                                            'logistics-sidebar-collapsed'
                                        )
                                        ? 'false'
                                        : 'true'
                                );
                        }
                    );


                document
                    .querySelectorAll(
                        '[data-logistics-sidebar-item]'
                    )
                    .forEach(
                        function (item) {

                            item
                                .addEventListener(
                                    'click',
                                    function () {

                                        if (
                                            window
                                                .innerWidth
                                            < 1024
                                        ) {
                                            closeSidebar();
                                        }
                                    }
                                );
                        }
                    );


                notificationButton
                    ?.addEventListener(
                        'click',
                        function (event) {

                            event
                                .stopPropagation();

                            notificationPanel
                                ?.classList
                                .toggle(
                                    'hidden'
                                );

                            notificationButton
                                .setAttribute(
                                    'aria-expanded',
                                    notificationPanel
                                        ?.classList
                                        .contains(
                                            'hidden'
                                        )
                                        ? 'false'
                                        : 'true'
                                );
                        }
                    );


                notificationPanel
                    ?.addEventListener(
                        'click',
                        function (event) {
                            event
                                .stopPropagation();
                        }
                    );


                document.addEventListener(
                    'click',
                    closeNotifications
                );


                logoutButton
                    ?.addEventListener(
                        'click',
                        openLogoutModal
                    );


                cancelLogout
                    ?.addEventListener(
                        'click',
                        closeLogoutModal
                    );


                logoutModal
                    ?.addEventListener(
                        'click',
                        function (event) {

                            if (
                                event.target
                                === logoutModal
                            ) {
                                closeLogoutModal();
                            }
                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | Keyboard behavior
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key
                            !== 'Escape'
                        ) {
                            return;
                        }


                        if (
                            actionModal
                                ?.classList
                                .contains(
                                    'is-open'
                                )
                        ) {
                            event
                                .preventDefault();

                            closeActionModal();

                            return;
                        }


                        if (
                            logoutModal
                            &&
                            ! logoutModal
                                .classList
                                .contains(
                                    'hidden'
                                )
                        ) {
                            event
                                .preventDefault();

                            closeLogoutModal();

                            return;
                        }


                        if (
                            window.innerWidth
                            < 1024
                            &&
                            sidebar
                                ?.classList
                                .contains(
                                    'translate-x-0'
                                )
                        ) {
                            closeSidebar();
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Basic focus trap for confirmation modal
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key
                            !== 'Tab'
                            ||
                            ! actionModal
                                ?.classList
                                .contains(
                                    'is-open'
                                )
                        ) {
                            return;
                        }


                        const focusable =
                            Array
                                .from(
                                    actionModal
                                        .querySelectorAll(
                                            'button:not([disabled]), '
                                            + '[href], '
                                            + 'input:not([disabled]), '
                                            + 'select:not([disabled]), '
                                            + 'textarea:not([disabled]), '
                                            + '[tabindex]:not([tabindex="-1"])'
                                        )
                                )
                                .filter(
                                    function (element) {
                                        return element
                                            .offsetParent
                                            !== null;
                                    }
                                );


                        if (
                            focusable.length
                            === 0
                        ) {
                            return;
                        }


                        const first =
                            focusable[0];

                        const last =
                            focusable[
                                focusable.length
                                - 1
                            ];


                        if (
                            event.shiftKey
                            &&
                            document
                                .activeElement
                            === first
                        ) {
                            event
                                .preventDefault();

                            last.focus();

                        } else if (
                            ! event.shiftKey
                            &&
                            document
                                .activeElement
                            === last
                        ) {
                            event
                                .preventDefault();

                            first.focus();
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Responsive sidebar
                |--------------------------------------------------------------------------
                */

                window.addEventListener(
                    'resize',
                    function () {

                        if (
                            window.innerWidth
                            >= 1024
                        ) {
                            sidebar
                                ?.classList
                                .remove(
                                    '-translate-x-full',
                                    'translate-x-0'
                                );

                            overlay
                                ?.classList
                                .add(
                                    'hidden'
                                );

                        } else if (
                            ! sidebar
                                ?.classList
                                .contains(
                                    'translate-x-0'
                                )
                        ) {
                            sidebar
                                ?.classList
                                .add(
                                    '-translate-x-full'
                                );
                        }
                    }
                );
            }
        );
    </script>


    @stack('scripts')

</body>

</html>