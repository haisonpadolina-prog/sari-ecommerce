@extends('layouts.seller')

@section('title', 'Seller Dashboard — SARI')
@section('page-title', 'Dashboard Overview')

@section('content')


<div class="mx-auto w-full max-w-[1800px]">

    {{-- =========================================================
        FLASH / COMPLIANCE NOTICES
    ========================================================== --}}

    @if (session('success'))
        <div class="mb-5 flex items-start gap-3 rounded-[18px] border border-[#d5e5dc] bg-[#f3f8f5] px-5 py-4 text-[#56816a] shadow-[0_10px_28px_rgba(77,120,91,0.06)]">
            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path d="m5 12 4 4L19 6"></path>
                </svg>
            </div>

            <div>
                <p class="text-[11px] font-bold">Product submitted successfully</p>
                <p class="mt-1 text-[10px] leading-5 text-[#6f8877]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-5 flex items-start gap-3 rounded-[18px] border border-[#eadfc9] bg-[#fcf8ef] px-5 py-4 text-[#9c6c1d] shadow-[0_10px_28px_rgba(165,116,35,0.06)]">
            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 3 3 20h18L12 3Z"></path>
                    <path d="M12 9v5"></path>
                    <path d="M12 17h.01"></path>
                </svg>
            </div>

            <div>
                <p class="text-[11px] font-bold">Product requires administrator review</p>
                <p class="mt-1 text-[10px] leading-5 text-[#8f7953]">{{ session('warning') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->has('product'))
        <div class="mb-5 rounded-[18px] border border-[#ead7d7] bg-[#fdf5f5] px-5 py-4">
            <p class="text-[11px] font-bold text-[#a45f5f]">Selling action unavailable</p>
            <p class="mt-1 text-[10px] leading-5 text-[#987070]">{{ $errors->first('product') }}</p>
        </div>
    @endif

    @if ($sellerAccount->isSuspended())
        <section class="mb-5 overflow-hidden rounded-[22px] border border-[#e5caca] bg-white shadow-[0_12px_36px_rgba(130,69,69,0.07)]">
            <div class="border-l-[4px] border-[#b8685f] bg-[#fdf8f7] p-5 sm:p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl border border-[#ead3d0] bg-white text-[#b8685f]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="8"></circle>
                                <path d="m8 8 8 8"></path>
                            </svg>
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-[16px] font-bold text-[#553c39]">
                                    Selling Privileges Suspended
                                </h3>

                                <span class="rounded-full border border-[#e7caca] bg-white px-2.5 py-1 text-[9px] font-bold text-[#a65d57]">
                                    {{ $sellerAccount->warning_count }} / 3 warnings
                                </span>
                            </div>

                            <p class="mt-2 max-w-[760px] text-[11px] leading-6 text-[#8c6f6b]">
                                You cannot upload new products while your account is suspended.
                                You can still review your dashboard and contact the administrator.
                            </p>

                            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-[10px]">
                                <span class="text-[#8f7772]">
                                    <strong class="text-[#5e4541]">Suspended until:</strong>
                                    {{ $sellerAccount->suspended_until?->format('M d, Y h:i A') }}
                                </span>

                                @if ($sellerAccount->suspension_reason)
                                    <span class="text-[#8f7772]">
                                        <strong class="text-[#5e4541]">Reason:</strong>
                                        {{ $sellerAccount->suspension_reason }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('seller.compliance.message') }}" class="w-full rounded-[16px] border border-[#ead8d6] bg-white p-4 xl:max-w-[420px]">
                        @csrf

                        <label class="text-[10px] font-bold text-[#594540]">
                            Message Administrator
                        </label>

                        <textarea
                            name="message"
                            rows="2"
                            required
                            placeholder="Explain your concern or request a review..."
                            class="mt-2 w-full resize-none rounded-xl border border-[#e6d8d5] bg-[#fdfafa] px-3 py-2.5 text-[10px] leading-5 outline-none focus:border-[#bc8178] focus:ring-4 focus:ring-[#bc8178]/10"
                        ></textarea>

                        <button
                            type="submit"
                            class="mt-2 inline-flex h-10 w-full items-center justify-center rounded-xl bg-[#8f5e58] px-4 text-[10px] font-semibold text-white transition hover:bg-[#794e49]"
                        >
                            Send to Admin
                        </button>
                    </form>
                </div>
            </div>
        </section>
    @elseif ($sellerAccount->warning_count > 0)
        <div class="mb-5 flex flex-col gap-3 rounded-[18px] border border-[#eadfc9] bg-[#fffaf2] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[11px] font-bold text-[#8b641f]">
                    Compliance Warning: {{ $sellerAccount->warning_count }} / 3
                </p>
                <p class="mt-1 text-[10px] text-[#927d59]">
                    Three active warnings will temporarily suspend selling privileges for 30 days.
                </p>
            </div>

            <a href="{{ route('seller.messages') }}" class="text-[10px] font-semibold text-[#a8731f] hover:text-[#805513]">
                Contact Administrator →
            </a>
        </div>
    @endif


    {{-- =========================================================
        TOP SELLER ACTION / WELCOME
    ========================================================== --}}
    <section
        class="
            rounded-[22px]
            border border-[#ebe4da]
            bg-white
            p-5

            sm:p-6
            lg:p-7
        "
    >
        <div
            class="
                flex flex-col gap-5
                lg:flex-row
                lg:items-center
                lg:justify-between
            "
        >

            <div>
                <div
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        border border-[#d9e6dd]
                        bg-[#f3f8f5]
                        px-3 py-1.5
                        text-[9px] font-semibold
                        uppercase tracking-[0.14em]
                        text-[#56816a]
                    "
                >
                    <span class="h-2 w-2 rounded-full {{ $sellerAccount->isSuspended() ? 'bg-[#b8685f]' : 'bg-[#68a07b]' }}"></span>
                    {{ $sellerAccount->isSuspended() ? 'Store Suspended' : 'Store Online' }}
                </div>

                <h2
                    class="
                        mt-3
                        text-[22px] font-bold
                        tracking-[-0.03em]
                        text-[#211c16]

                        sm:text-[24px]
                    "
                >
                    Good morning, Seller
                </h2>

                <p
                    class="
                        mt-2
                        max-w-[720px]
                        text-[12px] leading-6
                        text-[#81786c]

                        sm:text-[13px]
                    "
                >
                    Manage your products, review incoming orders, monitor inventory,
                    track shipments, and view your store performance.
                </p>
            </div>


            {{-- ADD PRODUCT PRIMARY ACTION --}}
            <div class="flex flex-wrap items-center gap-3">

                <button
                    id="openAddProductModal"
                    type="button"
                    @if ($sellerAccount->isSuspended()) disabled @endif
                    class="
                        inline-flex h-12
                        items-center gap-2
                        rounded-xl
                        bg-[#c99128]
                        px-5
                        text-[11px] font-semibold
                        text-white
                        shadow-[0_10px_24px_rgba(201,145,40,0.18)]
                        transition

                        hover:-translate-y-0.5
                        hover:bg-[#b47e1e]

                        active:translate-y-0
                        disabled:cursor-not-allowed
                        disabled:opacity-45
                        disabled:hover:translate-y-0
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-[18px] w-[18px]"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>

                    Add New Product
                </button>


                <a
                    href="{{ route('seller.orders') }}"
                    class="
                        inline-flex h-12
                        items-center gap-2
                        rounded-xl
                        border border-[#e6dfd4]
                        bg-white
                        px-4
                        text-[11px] font-semibold
                        text-[#62594e]
                        transition

                        hover:border-[#d4c29f]
                        hover:bg-[#fcf9f3]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                    </svg>

                    View Orders
                </a>

            </div>

        </div>
    </section>


    {{-- =========================================================
        SUMMARY CARDS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-4

            sm:grid-cols-2
            xl:grid-cols-4
        "
    >

        {{-- PRODUCTS --}}
        <div class="rounded-[18px] border border-[#dce5ed] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Uploaded Products
                    </p>

                    <h3
                        id="sellerProductCount"
                        class="
                            mt-2
                            text-[27px] font-bold
                            tracking-[-0.04em]
                            text-[#211d18]
                        "
                    >
                        {{ $products->count() }}
                    </h3>

                    <p class="mt-2 text-[10px] text-[#8f877d]">
                        {{ $products->where('moderation_status', 'approved')->count() }} approved listings
                    </p>
                </div>

                <div
                    class="
                        grid h-11 w-11
                        place-items-center
                        rounded-xl
                        border border-[#d9e3ec]
                        bg-[#f3f7fa]
                        text-[#627f99]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <path d="M5 7h14l-1 13H6L5 7Z"></path>
                        <path d="M9 7a3 3 0 0 1 6 0"></path>
                    </svg>
                </div>
            </div>
        </div>


        {{-- ORDERS --}}
        <div class="rounded-[18px] border border-[#eadfc9] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Pending Orders
                    </p>

                    <h3 class="mt-2 text-[27px] font-bold tracking-[-0.04em] text-[#211d18]">
                        24
                    </h3>

                    <p class="mt-2 text-[10px] text-[#ad781c]">
                        8 new today
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#eadfc8] bg-[#fbf6ec] text-[#b98020]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="12" cy="12" r="8"></circle>
                        <path d="M12 8v4l2 2"></path>
                    </svg>
                </div>
            </div>
        </div>


        {{-- SALES --}}
        <div class="rounded-[18px] border border-[#d8e6dd] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Monthly Sales
                    </p>

                    <h3 class="mt-2 text-[27px] font-bold tracking-[-0.04em] text-[#211d18]">
                        ₱186,420
                    </h3>

                    <p class="mt-2 text-[10px] font-medium text-[#56816a]">
                        ▲ 18.3% this month
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#d5e5dc] bg-[#f1f7f3] text-[#56816a]">
                    <span class="text-[17px] font-semibold">₱</span>
                </div>
            </div>
        </div>


        {{-- LOW STOCK --}}
        <div class="rounded-[18px] border border-[#ead9d9] bg-white p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-medium text-[#797168]">
                        Low Stock Items
                    </p>

                    <h3 class="mt-2 text-[27px] font-bold tracking-[-0.04em] text-[#211d18]">
                        7
                    </h3>

                    <p class="mt-2 text-[10px] text-[#a47777]">
                        Needs inventory update
                    </p>
                </div>

                <div class="grid h-11 w-11 place-items-center rounded-xl border border-[#ead7d7] bg-[#faf1f1] text-[#ad6767]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M12 3 3 20h18L12 3Z"></path>
                        <path d="M12 9v5"></path>
                        <path d="M12 17h.01"></path>
                    </svg>
                </div>
            </div>
        </div>

    </section>


    {{-- =========================================================
        MY PRODUCTS / UPLOADED PRODUCTS
    ========================================================== --}}
    <section
        class="
            mt-5
            overflow-hidden
            rounded-[22px]
            border border-[#ebe4da]
            bg-white
        "
    >

        <div
            class="
                flex flex-col gap-4
                border-b border-[#eee8df]
                p-5

                lg:flex-row
                lg:items-center
                lg:justify-between
            "
        >

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-[16px] font-bold text-[#28221b]">
                        Your Products
                    </h3>

                    <span
                        class="
                            rounded-full
                            border border-[#e6dfd4]
                            bg-[#fcfaf7]
                            px-2.5 py-1
                            text-[8px] font-semibold
                            text-[#83796c]
                        "
                    >
                        Uploaded Products
                    </span>
                </div>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    View and manage products currently listed in your SARI store.
                </p>
            </div>


            <div
                class="
                    flex flex-col gap-2
                    sm:flex-row
                "
            >

                <div class="relative">
                    <svg
                        viewBox="0 0 24 24"
                        class="
                            pointer-events-none
                            absolute left-3.5 top-1/2
                            h-4 w-4
                            -translate-y-1/2
                            text-[#9e968b]
                        "
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>

                    <input
                        id="sellerProductSearch"
                        type="search"
                        placeholder="Search products..."
                        class="
                            h-10 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            pl-10 pr-4
                            text-[10px]
                            outline-none

                            focus:border-[#c99a3d]
                            focus:ring-4
                            focus:ring-[#c99a3d]/10

                            sm:w-[220px]
                        "
                    >
                </div>


                <select
                    id="sellerProductStatus"
                    class="
                        h-10
                        rounded-xl
                        border border-[#e6dfd5]
                        bg-white
                        px-3
                        text-[10px]
                        text-[#625a50]
                        outline-none
                    "
                >
                    <option value="">All Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending Review</option>
                    <option value="flagged">Flagged</option>
                    <option value="rejected">Rejected</option>
                    <option value="low stock">Low Stock</option>
                </select>


                <a
                    href="{{ route('seller.products.archive') }}"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[#e6dfd5] bg-white px-4 text-[10px] font-semibold text-[#625a50] transition hover:border-[#d4c29f] hover:bg-[#fcf9f3]"
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16"></path>
                        <path d="M6 7v12h12V7"></path>
                        <path d="M9 11h6"></path>
                    </svg>
                    Archive
                </a>

                <button
                    id="openAddProductModalSecondary"
                    type="button"
                    @if ($sellerAccount->isSuspended()) disabled @endif
                    class="
                        inline-flex h-10
                        items-center justify-center gap-2
                        rounded-xl
                        bg-[#c99128]
                        px-4
                        text-[10px] font-semibold
                        text-white
                        transition

                        hover:bg-[#b47e1e]
                    "
                >
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>

                    Add Product
                </button>

            </div>

        </div>


        {{-- PRODUCT CARDS --}}
        <div
            id="sellerProductGrid"
            class="
                grid grid-cols-1
                gap-4
                p-5

                md:grid-cols-2
                xl:grid-cols-3
                2xl:grid-cols-4
            "
        >

            @forelse ($products as $product)
                @php
                    $statusClass = match ($product->moderation_status) {
                        'approved' => 'bg-[#eef6f1] text-[#56816a] border-[#d7e8dd]',
                        'pending' => 'bg-[#fbf5e9] text-[#a8731f] border-[#eadfc9]',
                        'flagged' => 'bg-[#faf0ed] text-[#ad6257] border-[#ead5d1]',
                        'rejected' => 'bg-[#f8eeee] text-[#9f5a5a] border-[#e8d3d3]',
                        'removed' => 'bg-[#f3f1ee] text-[#756d64] border-[#dfdad3]',
                        default => 'bg-[#f4f2ee] text-[#756d64] border-[#e2ddd5]',
                    };

                    $statusLabel = match ($product->moderation_status) {
                        'approved' => 'Approved',
                        'pending' => 'Pending Review',
                        'flagged' => 'Flagged',
                        'rejected' => 'Rejected',
                        'removed' => 'Removed',
                        default => ucfirst($product->moderation_status),
                    };

                    $stockIsLow = $product->stock <= 5;

                    $imageBg = match ($loop->index % 4) {
                        0 => 'bg-[#f3f6f8]',
                        1 => 'bg-[#fbf5eb]',
                        2 => 'bg-[#f1f7f3]',
                        default => 'bg-[#f6f2f8]',
                    };

                    $imageText = match ($loop->index % 4) {
                        0 => 'text-[#748b9e]',
                        1 => 'text-[#aa7a2b]',
                        2 => 'text-[#63836e]',
                        default => 'text-[#7d6b8c]',
                    };
                @endphp

                <article
                    data-product-card
                    data-product-name="{{ strtolower($product->name) }}"
                    data-product-status="{{ $product->moderation_status }} {{ $stockIsLow ? 'low stock' : '' }}"
                    class="
                        overflow-hidden
                        rounded-[18px]
                        border border-[#e9e3da]
                        bg-white
                        transition duration-300

                        hover:-translate-y-1
                        hover:border-[#d9c8a8]
                        hover:shadow-[0_14px_34px_rgba(68,52,28,0.08)]
                    "
                >
                    <div class="flex h-[150px] items-center justify-center overflow-hidden {{ $imageBg }}">
                        @if ($product->image_path)
                            <img
                                src="{{ route('seller.products.image', $product) }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover transition duration-500 hover:scale-[1.03]"
                            >
                        @else
                            <div class="text-center {{ $imageText }}">
                                <svg viewBox="0 0 24 24" class="mx-auto h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                                    <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                                </svg>

                                <p class="mt-2 text-[8px] font-medium uppercase tracking-[0.1em]">
                                    Product Image
                                </p>
                            </div>
                        @endif
                    </div>


                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-[11px] font-bold text-[#312b25]">
                                    {{ $product->name }}
                                </p>

                                <p class="mt-1 text-[8px] text-[#978e83]">
                                    {{ $product->category }} • {{ $product->sku }}
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-col items-end gap-1.5">
                                <span class="rounded-full border px-2 py-1 text-[8px] font-semibold {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                                @if ($product->requires_re_review)
                                    <span class="rounded-full bg-[#fbf2df] px-2 py-1 text-[7px] font-semibold text-[#a8731f]">EDITED • RE-REVIEW</span>
                                @endif
                            </div>
                        </div>


                        @if ($product->moderation_status === 'flagged')
                            <div class="mt-3 rounded-xl border border-[#ead8d2] bg-[#fdf7f5] px-3 py-2.5">
                                <div class="flex items-start gap-2">
                                    <svg viewBox="0 0 24 24" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-[#ad6257]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 3 3 20h18L12 3Z"></path>
                                        <path d="M12 9v5"></path>
                                    </svg>

                                    <div>
                                        <p class="text-[8px] font-bold text-[#995b52]">
                                            Flagged for Admin Review
                                        </p>

                                        @if ($product->screening_reason)
                                            <p class="mt-1 line-clamp-2 text-[8px] leading-4 text-[#98756f]">
                                                {{ $product->screening_reason }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif ($product->moderation_status === 'pending')
                            <div class="mt-3 rounded-xl border border-[#eadfc9] bg-[#fcf8ef] px-3 py-2.5">
                                <p class="text-[8px] font-semibold text-[#997127]">
                                    {{ $product->requires_re_review ? 'Listing changed after review and is waiting for administrator re-approval.' : 'Waiting for administrator approval before public listing.' }}
                                </p>
                            </div>
                        @elseif ($product->moderation_status === 'rejected' && $product->admin_review_note)
                            <div class="mt-3 rounded-xl border border-[#ead7d7] bg-[#fdf5f5] px-3 py-2.5">
                                <p class="text-[8px] font-semibold text-[#a15f5f]">
                                    Admin note: {{ $product->admin_review_note }}
                                </p>
                            </div>
                        @endif


                        <div class="mt-4 flex items-end justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-[15px] font-bold text-[#a8731f]">
                                        ₱{{ number_format((float) $product->price, 2) }}
                                    </p>

                                    @if ((float) $product->discount > 0)
                                        <span class="rounded-full bg-[#faf0ed] px-2 py-1 text-[7px] font-bold text-[#ad6257]">
                                            {{ rtrim(rtrim(number_format((float) $product->discount, 2), '0'), '.') }}% OFF
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-1 text-[8px] text-[#958c80]">
                                    Uploaded {{ $product->created_at?->diffForHumans() }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-[9px] font-semibold {{ $stockIsLow ? 'text-[#ad5f5f]' : 'text-[#56816a]' }}">
                                    {{ $product->stock }} {{ $product->stock == 1 ? 'stock' : 'stocks' }} {{ $stockIsLow ? 'left' : '' }}
                                </p>

                                <p class="mt-1 text-[8px] text-[#958c80]">
                                    {{ $stockIsLow ? 'Low stock' : 'Healthy stock' }}
                                </p>
                            </div>
                        </div>


                        <div class="mt-4 grid grid-cols-2 gap-2 border-t border-[#eee8df] pt-4 sm:grid-cols-4">
                            <button
                                type="button"
                                data-view-product
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-category="{{ $product->category }}"
                                data-product-sku="{{ $product->sku }}"
                                data-product-price="{{ $product->price }}"
                                data-product-stock="{{ $product->stock }}"
                                data-product-discount="{{ $product->discount }}"
                                data-product-voucher="{{ $product->voucher_code }}"
                                data-product-description="{{ $product->description }}"
                                data-product-status="{{ $statusLabel }}"
                                data-product-image="{{ $product->image_path ? route('seller.products.image', $product) : '' }}"
                                class="rounded-lg border border-[#e5ded4] px-2 py-2 text-[8px] font-semibold text-[#675f55] transition hover:-translate-y-0.5 hover:bg-[#fcf8f1]"
                            >
                                View
                            </button>

                            <button
                                type="button"
                                data-edit-product
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-category="{{ $product->category }}"
                                data-product-sku="{{ $product->sku }}"
                                data-product-price="{{ $product->price }}"
                                data-product-stock="{{ $product->stock }}"
                                data-product-discount="{{ $product->discount }}"
                                data-product-voucher="{{ $product->voucher_code }}"
                                data-product-description="{{ $product->description }}"
                                data-product-image="{{ $product->image_path ? route('seller.products.image', $product) : '' }}"
                                @if ($sellerAccount->isSuspended()) disabled @endif
                                class="rounded-lg border border-[#e5ded4] px-2 py-2 text-[8px] font-semibold text-[#675f55] transition hover:-translate-y-0.5 hover:bg-[#fcf8f1] disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                data-product-action="archive"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                class="rounded-lg border border-[#eadfc9] bg-[#fffaf2] px-2 py-2 text-[8px] font-semibold text-[#a8731f] transition hover:-translate-y-0.5 hover:bg-[#fbf4e7]"
                            >
                                Archive
                            </button>

                            <button
                                type="button"
                                data-product-action="delete"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                class="rounded-lg border border-[#ead8d8] bg-[#fcf5f5] px-2 py-2 text-[8px] font-semibold text-[#a96565] transition hover:-translate-y-0.5 hover:bg-[#faeeee]"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </article>

            @empty
                <div class="col-span-full rounded-[18px] border border-dashed border-[#dfd6c8] bg-[#fcfaf7] px-6 py-14 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#fbf5e9] text-[#a8731f]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>
                        </svg>
                    </div>

                    <p class="mt-4 text-[11px] font-bold text-[#4f473e]">
                        No products uploaded yet
                    </p>

                    <p class="mt-1 text-[9px] text-[#958c80]">
                        Add your first product and it will appear here for admin review.
                    </p>
                </div>
            @endforelse

        </div>


        <div
            id="sellerProductEmpty"
            class="
                hidden
                border-t border-[#eee8df]
                p-8
                text-center
            "
        >
            <p class="text-[11px] font-semibold text-[#5d554c]">
                No matching products found.
            </p>

            <p class="mt-1 text-[9px] text-[#948b7f]">
                Try changing your search or filter.
            </p>
        </div>


        <div
            class="
                flex items-center justify-between
                border-t border-[#eee8df]
                bg-[#fcfaf7]
                px-5 py-4
            "
        >
            <p class="text-[9px] text-[#91887d]">
                Showing recent products from your store
            </p>

            <button class="text-[9px] font-semibold text-[#a8731f] hover:text-[#805513]">
                View All Products →
            </button>
        </div>

    </section>


    {{-- =========================================================
        SELLER WORKFLOW
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-[1.3fr_.7fr]
        "
    >

        {{-- ORDER PIPELINE --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Order Management
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Current fulfillment status based on your seller workflow.
                </p>
            </div>


            <div
                class="
                    mt-5
                    grid grid-cols-2
                    gap-3

                    md:grid-cols-3
                "
            >

                @php
                    $orderFlow = [
                        ['title' => 'New Orders', 'count' => '8', 'sub' => 'Review order details', 'bg' => 'bg-[#fbf5e9]', 'text' => 'text-[#ad791f]'],
                        ['title' => 'Preparing', 'count' => '9', 'sub' => 'Pack & print waybill', 'bg' => 'bg-[#f3f7fa]', 'text' => 'text-[#647f97]'],
                        ['title' => 'Ready Pickup', 'count' => '6', 'sub' => 'For courier handoff', 'bg' => 'bg-[#f1f7f3]', 'text' => 'text-[#56816a]'],
                        ['title' => 'In Transit', 'count' => '12', 'sub' => 'Track shipment', 'bg' => 'bg-[#f6f2f8]', 'text' => 'text-[#7c6a8c]'],
                        ['title' => 'Delivered', 'count' => '31', 'sub' => 'Customer received', 'bg' => 'bg-[#eef6f1]', 'text' => 'text-[#56816a]'],
                        ['title' => 'Feedback', 'count' => '14', 'sub' => 'Reviews to check', 'bg' => 'bg-[#fcfaf6]', 'text' => 'text-[#917549]'],
                    ];
                @endphp

                @foreach ($orderFlow as $item)
                    <a
                        href="{{ route('seller.orders') }}"
                        class="
                            rounded-[16px]
                            border border-[#ece6dd]
                            bg-[#fcfbf8]
                            p-4
                            transition

                            hover:border-[#d9c9ad]
                            hover:bg-[#fcf8f1]
                        "
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold text-[#453e36]">
                                    {{ $item['title'] }}
                                </p>

                                <p class="mt-1 text-[8px] leading-4 text-[#958c80]">
                                    {{ $item['sub'] }}
                                </p>
                            </div>

                            <span class="grid h-9 min-w-[36px] place-items-center rounded-xl {{ $item['bg'] }} px-2 text-[11px] font-bold {{ $item['text'] }}">
                                {{ $item['count'] }}
                            </span>
                        </div>
                    </a>
                @endforeach

            </div>
        </div>


        {{-- INVENTORY ALERTS --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-[16px] font-bold text-[#28221b]">
                        Inventory Alerts
                    </h3>

                    <p class="mt-1 text-[10px] text-[#91887d]">
                        Products that need attention.
                    </p>
                </div>

                <span class="rounded-full bg-[#faeeee] px-2.5 py-1 text-[9px] font-semibold text-[#ad5f5f]">
                    7 low
                </span>
            </div>


            <div class="mt-5 space-y-3">

                <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-semibold text-[#3d3730]">
                                Wireless Earbuds Pro
                            </p>

                            <p class="mt-1 text-[8px] text-[#958c80]">
                                SKU: EL-10028
                            </p>
                        </div>

                        <span class="text-[10px] font-bold text-[#ad5f5f]">
                            2 left
                        </span>
                    </div>
                </div>


                <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-semibold text-[#3d3730]">
                                Canvas Tote Bag
                            </p>

                            <p class="mt-1 text-[8px] text-[#958c80]">
                                SKU: FS-10431
                            </p>
                        </div>

                        <span class="text-[10px] font-bold text-[#ad791f]">
                            4 left
                        </span>
                    </div>
                </div>


                <div class="rounded-[14px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-semibold text-[#3d3730]">
                                Linen Storage Basket
                            </p>

                            <p class="mt-1 text-[8px] text-[#958c80]">
                                SKU: HM-10375
                            </p>
                        </div>

                        <span class="text-[10px] font-bold text-[#ad791f]">
                            5 left
                        </span>
                    </div>
                </div>

            </div>


            <button
                id="openAddProductModalInventory"
                type="button"
                    @if ($sellerAccount->isSuspended()) disabled @endif
                class="
                    mt-4 flex h-10
                    w-full items-center justify-center
                    rounded-xl
                    border border-[#dfd5c7]
                    bg-white
                    text-[9px] font-semibold
                    text-[#62594e]
                    transition

                    hover:bg-[#fcf8f1]
                "
            >
                Add / Restock Product
            </button>

        </div>

    </section>


    {{-- =========================================================
        SALES + REPORTS
    ========================================================== --}}
    <section
        class="
            mt-5
            grid grid-cols-1
            gap-5

            xl:grid-cols-[1.4fr_.6fr]
        "
    >

        {{-- SALES GRAPH --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-[16px] font-bold text-[#28221b]">
                        Sales Performance
                    </h3>

                    <p class="mt-1 text-[10px] text-[#91887d]">
                        Sales and performance tracking.
                    </p>
                </div>

                <select class="h-10 rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[10px] text-[#625a50] outline-none">
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>This Year</option>
                </select>
            </div>


            <div class="mt-5 overflow-hidden rounded-[18px] border border-[#efebe4] bg-[#fcfbf8] p-4">
                <div class="overflow-x-auto">

                    <svg
                        viewBox="0 0 820 280"
                        class="h-[270px] min-w-[680px] w-full"
                        fill="none"
                    >
                        <line x1="65" y1="35" x2="790" y2="35" stroke="#EAE5DE"/>
                        <line x1="65" y1="85" x2="790" y2="85" stroke="#EAE5DE"/>
                        <line x1="65" y1="135" x2="790" y2="135" stroke="#EAE5DE"/>
                        <line x1="65" y1="185" x2="790" y2="185" stroke="#EAE5DE"/>
                        <line x1="65" y1="235" x2="790" y2="235" stroke="#E4DED6"/>

                        <text x="10" y="39" fill="#A1988D" font-size="10">₱60K</text>
                        <text x="10" y="89" fill="#A1988D" font-size="10">₱45K</text>
                        <text x="10" y="139" fill="#A1988D" font-size="10">₱30K</text>
                        <text x="10" y="189" fill="#A1988D" font-size="10">₱15K</text>
                        <text x="10" y="239" fill="#A1988D" font-size="10">₱0</text>

                        <path
                            d="M70 220
                               C130 210,150 188,205 194
                               S300 157,350 166
                               S440 130,495 141
                               S580 103,640 112
                               S730 75,785 84"
                            stroke="#C99128"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <circle cx="70" cy="220" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="205" cy="194" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="350" cy="166" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="495" cy="141" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="640" cy="112" r="4" fill="#FCFBF8" stroke="#C99128" stroke-width="2"/>
                        <circle cx="785" cy="84" r="5" fill="#C99128"/>

                        <text x="55" y="267" fill="#9B9388" font-size="10">Week 1</text>
                        <text x="190" y="267" fill="#9B9388" font-size="10">Week 2</text>
                        <text x="335" y="267" fill="#9B9388" font-size="10">Week 3</text>
                        <text x="480" y="267" fill="#9B9388" font-size="10">Week 4</text>
                        <text x="625" y="267" fill="#9B9388" font-size="10">Current</text>
                    </svg>

                </div>
            </div>
        </div>


        {{-- REPORT CARD --}}
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Financial Snapshot
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Quick summary before generating a full report.
                </p>
            </div>


            <div class="mt-5 space-y-3">

                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] text-[#958c80]">
                        Gross Sales
                    </p>

                    <p class="mt-2 text-[19px] font-bold text-[#302a24]">
                        ₱186,420
                    </p>
                </div>


                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] text-[#958c80]">
                        Estimated Platform Commission
                    </p>

                    <p class="mt-2 text-[19px] font-bold text-[#a8731f]">
                        ₱18,642
                    </p>
                </div>


                <div class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                    <p class="text-[9px] text-[#958c80]">
                        Estimated Net Revenue
                    </p>

                    <p class="mt-2 text-[19px] font-bold text-[#56816a]">
                        ₱167,778
                    </p>
                </div>

            </div>


            <a
                href="{{ route('seller.reports') }}"
                class="
                    mt-4 flex h-11
                    w-full items-center justify-center
                    rounded-xl
                    bg-[#c99128]
                    text-[10px] font-semibold
                    text-white
                    transition

                    hover:bg-[#b47e1e]
                "
            >
                Generate Full Report
            </a>
        </div>

    </section>


    {{-- =========================================================
        QUICK ACTIONS
    ========================================================== --}}
    <section class="mt-5">
        <div class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">

            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">
                    Seller Quick Actions
                </h3>

                <p class="mt-1 text-[10px] text-[#91887d]">
                    Shortcuts to the most-used seller tools.
                </p>
            </div>


            <div
                class="
                    mt-5
                    grid grid-cols-2
                    gap-3

                    md:grid-cols-3
                    xl:grid-cols-6
                "
            >

                <button
                    id="openAddProductModalQuick"
                    type="button"
                    @if ($sellerAccount->isSuspended()) disabled @endif
                    class="
                        rounded-[15px]
                        border border-[#ece6dd]
                        bg-[#fcfbf8]
                        p-4
                        text-center
                        transition

                        hover:border-[#d8c8ad]
                        hover:bg-[#fcf8f1]
                    "
                >
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-[#fbf5e9] text-[#ad791f]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[9px] font-semibold text-[#4f473e]">
                        Add Product
                    </p>
                </button>


                <a href="{{ route('seller.orders') }}" class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4 text-center transition hover:border-[#d8c8ad] hover:bg-[#fcf8f1]">
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-[#f3f7fa] text-[#647f97]">
                        <span class="text-[11px] font-bold">24</span>
                    </div>

                    <p class="mt-3 text-[9px] font-semibold text-[#4f473e]">
                        New Orders
                    </p>
                </a>


                <a href="{{ route('seller.orders') }}" class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4 text-center transition hover:border-[#d8c8ad] hover:bg-[#fcf8f1]">
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-[#f1f7f3] text-[#56816a]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 3h10l4 4v14H5z"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[9px] font-semibold text-[#4f473e]">
                        Print Waybill
                    </p>
                </a>


                <a href="{{ route('seller.orders') }}" class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4 text-center transition hover:border-[#d8c8ad] hover:bg-[#fcf8f1]">
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-[#f6f2f8] text-[#7c6a8c]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 7h11v10H3z"></path>
                            <path d="M14 10h4l3 3v4h-7z"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[9px] font-semibold text-[#4f473e]">
                        Track Shipment
                    </p>
                </a>


                <a href="{{ route('seller.reports') }}" class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4 text-center transition hover:border-[#d8c8ad] hover:bg-[#fcf8f1]">
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-[#f3f7fa] text-[#647f97]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 20h16"></path>
                            <path d="M7 17v-5"></path>
                            <path d="M12 17V8"></path>
                            <path d="M17 17V4"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[9px] font-semibold text-[#4f473e]">
                        Sales Report
                    </p>
                </a>


                <a href="{{ route('seller.messages') }}" class="rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4 text-center transition hover:border-[#d8c8ad] hover:bg-[#fcf8f1]">
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-[#f1f7f3] text-[#56816a]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                        </svg>
                    </div>

                    <p class="mt-3 text-[9px] font-semibold text-[#4f473e]">
                        Open Chat
                    </p>
                </a>

            </div>

        </div>
    </section>


    <div class="h-5"></div>

</div>


{{-- =============================================================
    ADD PRODUCT MODAL
============================================================== --}}
<div
    id="sellerAddProductModal"
    class="
        fixed inset-0 z-[100]
        hidden
        items-center justify-center
        bg-black/35
        p-4
        backdrop-blur-[2px]
    "
>

    <div
        class="
            max-h-[92vh]
            w-full max-w-[720px]
            overflow-y-auto
            rounded-[24px]
            border border-[#e9dfcf]
            bg-white
            shadow-[0_30px_90px_rgba(38,30,18,0.22)]
        "
    >

        {{-- Modal Header --}}
        <div
            class="
                sticky top-0 z-10
                flex items-start justify-between gap-4
                border-b border-[#eee7dc]
                bg-white
                p-5

                sm:p-6
            "
        >
            <div>
                <div
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        bg-[#fbf5e9]
                        px-2.5 py-1
                        text-[8px] font-semibold
                        uppercase tracking-[0.1em]
                        text-[#a8731f]
                    "
                >
                    Product Inventory
                </div>

                <h3 class="mt-2 text-[19px] font-bold tracking-[-0.03em] text-[#28221b]">
                    Add New Product
                </h3>

                <p class="mt-1 text-[9px] text-[#91887d]">
                    Upload a product to your inventory. New listings are screened and sent to the administrator for review.
                </p>
            </div>


            <button
                id="closeAddProductModal"
                type="button"
                class="
                    grid h-10 w-10 shrink-0
                    place-items-center
                    rounded-xl
                    border border-[#e6dfd5]
                    text-[#756d63]
                    transition

                    hover:bg-[#fcf7ee]
                    hover:text-[#a8731f]
                "
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6 6 18"></path>
                </svg>
            </button>

        </div>


        <form
            id="sellerAddProductForm"
            method="POST"
            action="{{ route('seller.products.store') }}"
            enctype="multipart/form-data"
            class="p-5 sm:p-6"
        >
            @csrf

            <div
                class="
                    grid grid-cols-1
                    gap-4

                    md:grid-cols-2
                "
            >

                {{-- Product Name --}}
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        Product Name *
                    </label>

                    <input
                        id="sellerNewProductName"
                        name="name"
                        type="text"
                        required
                        value="{{ old('name') }}"
                        placeholder="Example: Wireless Earbuds Pro"
                        class="
                            h-11 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            px-4
                            text-[10px]
                            outline-none

                            focus:border-[#c99a3d]
                            focus:ring-4
                            focus:ring-[#c99a3d]/10
                        "
                    >
                </div>


                {{-- Category --}}
                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        Category *
                    </label>

                    <select
                        id="sellerNewProductCategory"
                        name="category"
                        required
                        class="
                            h-11 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            px-3
                            text-[10px]
                            outline-none
                        "
                    >
                        <option value="">Select Category</option>
                        <option>Electronics</option>
                        <option>Fashion</option>
                        <option>Home & Living</option>
                        <option>Beauty</option>
                        <option>Books</option>
                        <option>Food & Beverage</option>
                        <option>Others</option>
                    </select>
                </div>


                {{-- SKU --}}
                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        SKU / Product Code
                    </label>

                    <input
                        id="sellerNewProductSku"
                        name="sku"
                        type="text"
                        value="{{ old('sku') }}"
                        placeholder="Example: EL-10029"
                        class="
                            h-11 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            px-4
                            text-[10px]
                            outline-none
                        "
                    >
                </div>


                {{-- Price --}}
                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        Price *
                    </label>

                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-semibold text-[#a8731f]">
                            ₱
                        </span>

                        <input
                            id="sellerNewProductPrice"
                        name="price"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            placeholder="0.00"
                            class="
                                h-11 w-full
                                rounded-xl
                                border border-[#e6dfd5]
                                bg-[#fcfbf9]
                                pl-8 pr-4
                                text-[10px]
                                outline-none
                            "
                        >
                    </div>
                </div>


                {{-- Stock --}}
                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        Stock Quantity *
                    </label>

                    <input
                        id="sellerNewProductStock"
                        name="stock"
                        type="number"
                        min="0"
                        required
                        placeholder="0"
                        class="
                            h-11 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            px-4
                            text-[10px]
                            outline-none
                        "
                    >
                </div>


                {{-- Discount --}}
                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        Discount %
                    </label>

                    <input
                        id="sellerNewProductDiscount"
                        name="discount"
                        type="number"
                        min="0"
                        max="100"
                        value="0"
                        class="
                            h-11 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            px-4
                            text-[10px]
                            outline-none
                        "
                    >
                </div>


                {{-- Voucher --}}
                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        Voucher Code
                    </label>

                    <input
                        type="text"
                        name="voucher_code"
                        placeholder="Optional"
                        class="
                            h-11 w-full
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            px-4
                            text-[10px]
                            outline-none
                        "
                    >
                </div>


                {{-- Product Image --}}
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        Product Image
                    </label>

                    <label
                        class="
                            flex cursor-pointer
                            items-center gap-4
                            rounded-[16px]
                            border border-dashed border-[#d9c9ae]
                            bg-[#fcfaf6]
                            p-4
                            transition

                            hover:border-[#c99a3d]
                            hover:bg-[#fbf6ec]
                        "
                    >
                        <input
                            id="sellerNewProductImage"
                        name="image"
                            type="file"
                            accept="image/*"
                            class="hidden"
                        >

                        <div
                            id="sellerImagePreviewBox"
                            class="
                                grid h-14 w-14
                                shrink-0 place-items-center
                                overflow-hidden
                                rounded-xl
                                bg-white
                                text-[#a8731f]
                            "
                        >
                            <svg
                                id="sellerImagePlaceholder"
                                viewBox="0 0 24 24"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                <circle cx="8" cy="9" r="1.5"></circle>
                                <path d="m4 17 5-5 4 4 2-2 5 4"></path>
                            </svg>

                            <img
                                id="sellerImagePreview"
                                src=""
                                alt=""
                                class="hidden h-full w-full object-cover"
                            >
                        </div>

                        <div>
                            <p class="text-[10px] font-semibold text-[#4f473e]">
                                Upload product image
                            </p>

                            <p class="mt-1 text-[8px] text-[#958c80]">
                                PNG, JPG, WEBP — up to 4 MB
                            </p>
                        </div>
                    </label>
                </div>


                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">
                        Product Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        placeholder="Describe the product..."
                        class="
                            w-full resize-none
                            rounded-xl
                            border border-[#e6dfd5]
                            bg-[#fcfbf9]
                            px-4 py-3
                            text-[10px]
                            leading-5
                            outline-none
                        "
                    ></textarea>
                </div>

            </div>


            <div
                class="
                    mt-6
                    flex flex-col-reverse gap-2
                    border-t border-[#eee8df]
                    pt-5

                    sm:flex-row
                    sm:justify-end
                "
            >

                <button
                    id="cancelAddProductModal"
                    type="button"
                    class="
                        h-11
                        rounded-xl
                        border border-[#e0d7ca]
                        bg-white
                        px-5
                        text-[10px] font-semibold
                        text-[#62594e]
                    "
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    @if ($sellerAccount->isSuspended()) disabled @endif
                    class="
                        h-11
                        rounded-xl
                        bg-[#c99128]
                        px-5
                        text-[10px] font-semibold
                        text-white
                        transition

                        hover:bg-[#b47e1e]
                        disabled:cursor-not-allowed
                        disabled:opacity-45
                    "
                >
                    {{ $sellerAccount->isSuspended() ? 'Selling Suspended' : 'Add Product' }}
                </button>

            </div>

        </form>

    </div>

</div>


{{-- =============================================================
    VIEW PRODUCT MODAL
============================================================== --}}
<div id="sellerViewProductModal" class="fixed inset-0 z-[130] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[2px]">
    <div class="max-h-[92vh] w-full max-w-[650px] overflow-y-auto rounded-[24px] border border-[#e9dfcf] bg-white shadow-[0_30px_90px_rgba(38,30,18,0.22)]">
        <div class="flex items-center justify-between border-b border-[#eee7dc] p-5 sm:p-6">
            <div>
                <p class="text-[9px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">Product Details</p>
                <h3 id="viewProductName" class="mt-1 text-[19px] font-bold text-[#28221b]">Product</h3>
            </div>
            <button type="button" data-close-view class="grid h-10 w-10 place-items-center rounded-xl border border-[#e6dfd5] text-[#756d63] hover:bg-[#fcf7ee]">×</button>
        </div>

        <div class="p-5 sm:p-6">
            <div id="viewProductImageWrap" class="hidden overflow-hidden rounded-[18px] border border-[#ece5dc] bg-[#fcfaf7]">
                <img id="viewProductImage" src="" alt="" class="h-[260px] w-full object-cover">
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl bg-[#fcfaf7] p-3"><p class="text-[8px] text-[#958c80]">Category</p><p id="viewProductCategory" class="mt-1 text-[10px] font-semibold text-[#403a33]"></p></div>
                <div class="rounded-xl bg-[#fcfaf7] p-3"><p class="text-[8px] text-[#958c80]">SKU</p><p id="viewProductSku" class="mt-1 text-[10px] font-semibold text-[#403a33]"></p></div>
                <div class="rounded-xl bg-[#fcfaf7] p-3"><p class="text-[8px] text-[#958c80]">Price</p><p id="viewProductPrice" class="mt-1 text-[10px] font-bold text-[#a8731f]"></p></div>
                <div class="rounded-xl bg-[#fcfaf7] p-3"><p class="text-[8px] text-[#958c80]">Stock</p><p id="viewProductStock" class="mt-1 text-[10px] font-semibold text-[#403a33]"></p></div>
            </div>

            <div class="mt-3 rounded-[15px] border border-[#ece6dd] bg-[#fcfbf8] p-4">
                <p class="text-[9px] font-semibold text-[#514a41]">Moderation Status</p>
                <p id="viewProductStatus" class="mt-1 text-[11px] font-bold text-[#a8731f]"></p>
            </div>

            <div class="mt-3 rounded-[15px] border border-[#ece6dd] bg-white p-4">
                <p class="text-[9px] font-semibold text-[#514a41]">Description</p>
                <p id="viewProductDescription" class="mt-2 whitespace-pre-line text-[10px] leading-5 text-[#756d63]"></p>
            </div>
        </div>
    </div>
</div>


{{-- =============================================================
    EDIT PRODUCT MODAL
============================================================== --}}
<div id="sellerEditProductModal" class="fixed inset-0 z-[130] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[2px]">
    <div class="max-h-[92vh] w-full max-w-[760px] overflow-y-auto rounded-[24px] border border-[#e9dfcf] bg-white shadow-[0_30px_90px_rgba(38,30,18,0.22)]">
        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-[#eee7dc] bg-white p-5 sm:p-6">
            <div>
                <p class="text-[9px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">Product Management</p>
                <h3 class="mt-1 text-[19px] font-bold text-[#28221b]">Edit Product</h3>
                <p class="mt-1 text-[9px] text-[#91887d]">Changing the name, category, description, or image automatically sends the listing back for admin review.</p>
            </div>
            <button type="button" data-close-edit class="grid h-10 w-10 place-items-center rounded-xl border border-[#e6dfd5] text-[#756d63] hover:bg-[#fcf7ee]">×</button>
        </div>

        <form id="sellerEditProductForm" method="POST" action="" enctype="multipart/form-data" class="p-5 sm:p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Product Name *</label>
                    <input id="editProductName" name="name" required class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none focus:border-[#c99a3d] focus:ring-4 focus:ring-[#c99a3d]/10">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Category *</label>
                    <select id="editProductCategory" name="category" required class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 text-[10px] outline-none">
                        <option>Electronics</option><option>Fashion</option><option>Home & Living</option><option>Beauty</option><option>Books</option><option>Food & Beverage</option><option>Others</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">SKU</label>
                    <input id="editProductSku" name="sku" class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Price *</label>
                    <input id="editProductPrice" name="price" type="number" min="0" step="0.01" required class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Stock *</label>
                    <input id="editProductStock" name="stock" type="number" min="0" required class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Discount %</label>
                    <input id="editProductDiscount" name="discount" type="number" min="0" max="100" class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Voucher Code</label>
                    <input id="editProductVoucher" name="voucher_code" class="h-11 w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 text-[10px] outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Replace Product Image</label>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-[120px_1fr]">
                        <div id="editCurrentImageWrap" class="hidden h-[100px] overflow-hidden rounded-xl border border-[#e6dfd5] bg-[#fcfaf7]">
                            <img id="editCurrentImage" src="" alt="Current image" class="h-full w-full object-cover">
                        </div>
                        <input name="image" type="file" accept="image/*" class="block w-full rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-3 py-3 text-[9px]">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-[10px] font-semibold text-[#514a41]">Description</label>
                    <textarea id="editProductDescription" name="description" rows="4" class="w-full resize-none rounded-xl border border-[#e6dfd5] bg-[#fcfbf9] px-4 py-3 text-[10px] leading-5 outline-none"></textarea>
                </div>
            </div>

            <div class="mt-5 rounded-[14px] border border-[#eadfc9] bg-[#fffaf2] p-4 text-[9px] leading-5 text-[#8f7953]">
                <strong>Sensitive edits:</strong> name, category, description, and product image. If any of these change, previous approval is removed and the product is re-screened before admin re-review.
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t border-[#eee8df] pt-5">
                <button type="button" data-close-edit class="h-11 rounded-xl border border-[#e0d7ca] bg-white px-5 text-[10px] font-semibold text-[#62594e]">Cancel</button>
                <button type="submit" class="h-11 rounded-xl bg-[#c99128] px-5 text-[10px] font-semibold text-white hover:bg-[#b47e1e]">Save Changes</button>
            </div>
        </form>
    </div>
</div>


{{-- =============================================================
    ARCHIVE / DELETE CONFIRMATION
============================================================== --}}
<div id="sellerProductActionModal" class="fixed inset-0 z-[140] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[2px]">
    <div class="w-full max-w-[440px] rounded-[22px] border border-[#e8dfd3] bg-white p-6 shadow-[0_30px_80px_rgba(46,29,25,.22)]">
        <div class="grid h-11 w-11 place-items-center rounded-2xl bg-[#fbf5e9] text-[#a8731f]">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M6 7v12h12V7"></path></svg>
        </div>
        <h3 id="productActionTitle" class="mt-4 text-[18px] font-bold text-[#28221b]">Archive Product?</h3>
        <p id="productActionText" class="mt-2 text-[10px] leading-5 text-[#81786c]"></p>

        <form id="productActionForm" method="POST" action="" class="mt-5 flex justify-end gap-2">
            @csrf
            <button type="button" data-close-product-action class="h-11 rounded-xl border border-[#e0d7ca] bg-white px-5 text-[10px] font-semibold text-[#62594e]">Cancel</button>
            <button id="productActionSubmit" type="submit" class="h-11 rounded-xl bg-[#c99128] px-5 text-[10px] font-semibold text-white">Confirm</button>
        </form>
    </div>
</div>


{{-- =============================================================
    REAL-TIME ADMIN COMPLIANCE POPUP
============================================================== --}}
<div id="sellerRealtimeAlert" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/45 p-4 backdrop-blur-[3px]">
    <div id="sellerRealtimeAlertCard" class="w-full max-w-[520px] scale-95 rounded-[26px] border border-[#ead8d1] bg-white p-6 opacity-0 shadow-[0_35px_100px_rgba(38,30,18,.30)] transition-all duration-300 sm:p-7">
        <div class="flex items-start gap-4">
            <div id="realtimeAlertIcon" class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-[#fff2ef] text-[#b8685f]">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3 3 20h18L12 3Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[9px] font-semibold uppercase tracking-[0.14em] text-[#a8731f]">SARI Compliance Center</p>
                <h3 id="realtimeAlertTitle" class="mt-2 text-[20px] font-bold tracking-[-0.03em] text-[#28221b]">Compliance Update</h3>
                <p id="realtimeAlertProduct" class="mt-1 hidden text-[10px] font-semibold text-[#756d63]"></p>
            </div>
        </div>

        <div class="mt-5 rounded-[16px] border border-[#eee4da] bg-[#fcfaf7] p-4">
            <p id="realtimeAlertMessage" class="text-[11px] leading-6 text-[#6f675e]"></p>
            <div id="realtimeWarningBadge" class="mt-3 hidden w-fit rounded-full bg-[#faeeee] px-3 py-1.5 text-[9px] font-bold text-[#ad5f5f]"></div>
            <p id="realtimeSuspendedUntil" class="mt-3 hidden text-[10px] font-semibold text-[#8c605b]"></p>
        </div>

        <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <a href="{{ route('seller.messages') }}" id="realtimeMessageAdmin" class="hidden h-11 items-center justify-center rounded-xl border border-[#e0d7ca] bg-white px-5 text-[10px] font-semibold text-[#62594e]">Message Admin</a>
            <button id="closeRealtimeAlert" type="button" class="h-11 rounded-xl bg-[#c99128] px-6 text-[10px] font-semibold text-white hover:bg-[#b47e1e]">Got It</button>
        </div>
    </div>
</div>


@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('sellerAddProductModal');

    const openButtons = [
        document.getElementById('openAddProductModal'),
        document.getElementById('openAddProductModalSecondary'),
        document.getElementById('openAddProductModalInventory'),
        document.getElementById('openAddProductModalQuick')
    ].filter(Boolean);

    const closeButton = document.getElementById('closeAddProductModal');
    const cancelButton = document.getElementById('cancelAddProductModal');

    const searchInput = document.getElementById('sellerProductSearch');
    const statusFilter = document.getElementById('sellerProductStatus');
    const emptyState = document.getElementById('sellerProductEmpty');

    const imageInput = document.getElementById('sellerNewProductImage');
    const imagePreview = document.getElementById('sellerImagePreview');
    const imagePlaceholder = document.getElementById('sellerImagePlaceholder');


    function openModal() {
        modal?.classList.remove('hidden');
        modal?.classList.add('flex');

        document.body.classList.add('overflow-hidden');
    }


    function closeModal() {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');

        document.body.classList.remove('overflow-hidden');
    }


    openButtons.forEach(function (button) {
        button.addEventListener('click', openModal);
    });


    closeButton?.addEventListener('click', closeModal);
    cancelButton?.addEventListener('click', closeModal);


    modal?.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });


    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });


    /*
    |--------------------------------------------------------------------------
    | IMAGE PREVIEW
    |--------------------------------------------------------------------------
    */

    imageInput?.addEventListener('change', function () {

        const file = this.files?.[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {

            if (!imagePreview) {
                return;
            }

            imagePreview.src = event.target.result;

            imagePreview.classList.remove('hidden');
            imagePlaceholder?.classList.add('hidden');

        };

        reader.readAsDataURL(file);

    });


    /*
    |--------------------------------------------------------------------------
    | PRODUCT FILTER
    |--------------------------------------------------------------------------
    */

    function filterProducts() {

        const query =
            searchInput?.value.trim().toLowerCase() || '';

        const status =
            statusFilter?.value.toLowerCase() || '';

        const cards =
            Array.from(
                document.querySelectorAll('[data-product-card]')
            );


        let visible = 0;


        cards.forEach(function (card) {

            const productName =
                (card.dataset.productName || '').toLowerCase();

            const productStatus =
                (card.dataset.productStatus || '').toLowerCase();


            const searchMatches =
                query === '' ||
                productName.includes(query);


            const statusMatches =
                status === '' ||
                productStatus.includes(status);


            const matches =
                searchMatches &&
                statusMatches;


            card.classList.toggle(
                'hidden',
                !matches
            );


            if (matches) {
                visible++;
            }

        });


        emptyState?.classList.toggle(
            'hidden',
            visible !== 0
        );

    }


    searchInput?.addEventListener(
        'input',
        filterProducts
    );


    statusFilter?.addEventListener(
        'change',
        filterProducts
    );


    /*
    |--------------------------------------------------------------------------
    | SERVER VALIDATION
    |--------------------------------------------------------------------------
    */

    @if ($errors->any() && !$errors->has('product'))
        openModal();
    @endif


    /*
    |--------------------------------------------------------------------------
    | VIEW PRODUCT
    |--------------------------------------------------------------------------
    */
    const viewModal = document.getElementById('sellerViewProductModal');
    const editModal = document.getElementById('sellerEditProductModal');
    const actionModal = document.getElementById('sellerProductActionModal');

    function showFlexModal(element) {
        element?.classList.remove('hidden');
        element?.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function hideFlexModal(element) {
        element?.classList.add('hidden');
        element?.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-view-product]').forEach(function (button) {
        button.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('viewProductName').textContent = d.productName || 'Product';
            document.getElementById('viewProductCategory').textContent = d.productCategory || '—';
            document.getElementById('viewProductSku').textContent = d.productSku || '—';
            document.getElementById('viewProductPrice').textContent = '₱' + Number(d.productPrice || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('viewProductStock').textContent = (d.productStock || '0') + ' in stock';
            document.getElementById('viewProductStatus').textContent = d.productStatus || '—';
            document.getElementById('viewProductDescription').textContent = d.productDescription || 'No description provided.';

            const image = document.getElementById('viewProductImage');
            const imageWrap = document.getElementById('viewProductImageWrap');
            if (d.productImage) {
                image.src = d.productImage;
                image.alt = d.productName || 'Product image';
                imageWrap.classList.remove('hidden');
            } else {
                image.src = '';
                imageWrap.classList.add('hidden');
            }

            showFlexModal(viewModal);
        });
    });

    document.querySelectorAll('[data-close-view]').forEach(function (button) {
        button.addEventListener('click', function () { hideFlexModal(viewModal); });
    });


    /*
    |--------------------------------------------------------------------------
    | EDIT PRODUCT
    |--------------------------------------------------------------------------
    */
    const editForm = document.getElementById('sellerEditProductForm');

    document.querySelectorAll('[data-edit-product]').forEach(function (button) {
        button.addEventListener('click', function () {
            const d = this.dataset;
            editForm.action = '{{ url('/seller/products') }}/' + d.productId;
            document.getElementById('editProductName').value = d.productName || '';
            document.getElementById('editProductCategory').value = d.productCategory || 'Others';
            document.getElementById('editProductSku').value = d.productSku || '';
            document.getElementById('editProductPrice').value = d.productPrice || 0;
            document.getElementById('editProductStock').value = d.productStock || 0;
            document.getElementById('editProductDiscount').value = d.productDiscount || 0;
            document.getElementById('editProductVoucher').value = d.productVoucher || '';
            document.getElementById('editProductDescription').value = d.productDescription || '';

            const image = document.getElementById('editCurrentImage');
            const imageWrap = document.getElementById('editCurrentImageWrap');
            if (d.productImage) {
                image.src = d.productImage;
                imageWrap.classList.remove('hidden');
            } else {
                image.src = '';
                imageWrap.classList.add('hidden');
            }

            showFlexModal(editModal);
        });
    });

    document.querySelectorAll('[data-close-edit]').forEach(function (button) {
        button.addEventListener('click', function () { hideFlexModal(editModal); });
    });


    /*
    |--------------------------------------------------------------------------
    | ARCHIVE / DELETE -> RECOVERABLE ARCHIVE
    |--------------------------------------------------------------------------
    */
    const actionForm = document.getElementById('productActionForm');
    const actionTitle = document.getElementById('productActionTitle');
    const actionText = document.getElementById('productActionText');
    const actionSubmit = document.getElementById('productActionSubmit');

    document.querySelectorAll('[data-product-action]').forEach(function (button) {
        button.addEventListener('click', function () {
            const action = this.dataset.productAction;
            const id = this.dataset.productId;
            const name = this.dataset.productName;

            actionForm.action = '{{ url('/seller/products') }}/' + id + '/' + action;

            if (action === 'delete') {
                actionTitle.textContent = 'Delete Product?';
                actionText.textContent = '“' + name + '” will disappear from your active dashboard and move to Archived Products. It is not permanently erased, so compliance history stays intact.';
                actionSubmit.textContent = 'Move to Archive';
                actionSubmit.className = 'h-11 rounded-xl bg-[#a96565] px-5 text-[10px] font-semibold text-white hover:bg-[#955757]';
            } else {
                actionTitle.textContent = 'Archive Product?';
                actionText.textContent = '“' + name + '” will move to Archived Products. You can restore it later.';
                actionSubmit.textContent = 'Archive Product';
                actionSubmit.className = 'h-11 rounded-xl bg-[#c99128] px-5 text-[10px] font-semibold text-white hover:bg-[#b47e1e]';
            }

            showFlexModal(actionModal);
        });
    });

    document.querySelectorAll('[data-close-product-action]').forEach(function (button) {
        button.addEventListener('click', function () { hideFlexModal(actionModal); });
    });

    [viewModal, editModal, actionModal].forEach(function (element) {
        element?.addEventListener('click', function (event) {
            if (event.target === element) hideFlexModal(element);
        });
    });


    /*
    |--------------------------------------------------------------------------
    | REAL-TIME ADMIN COMPLIANCE ALERTS (LARAVEL REVERB / ECHO)
    |--------------------------------------------------------------------------
    */
    const realtimeToken = @json($sellerAccount->realtime_token);
    const realtimeModal = document.getElementById('sellerRealtimeAlert');
    const realtimeCard = document.getElementById('sellerRealtimeAlertCard');

    function openRealtimeAlert(payload) {
        document.getElementById('realtimeAlertTitle').textContent = payload.title || 'Compliance Update';
        document.getElementById('realtimeAlertMessage').textContent = payload.message || 'Your seller account has a new compliance update.';

        const productLine = document.getElementById('realtimeAlertProduct');
        if (payload.product_name) {
            productLine.textContent = 'Product: ' + payload.product_name;
            productLine.classList.remove('hidden');
        } else {
            productLine.classList.add('hidden');
        }

        const warningBadge = document.getElementById('realtimeWarningBadge');
        if (payload.warning_number) {
            warningBadge.textContent = 'Warning ' + payload.warning_number + ' / ' + (payload.max_warnings || 3);
            warningBadge.classList.remove('hidden');
        } else {
            warningBadge.classList.add('hidden');
        }

        const suspendedUntil = document.getElementById('realtimeSuspendedUntil');
        const messageAdmin = document.getElementById('realtimeMessageAdmin');

        if (payload.suspended_until) {
            const date = new Date(payload.suspended_until);
            suspendedUntil.textContent = 'Suspended until: ' + date.toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });
            suspendedUntil.classList.remove('hidden');
            messageAdmin.classList.remove('hidden');
            messageAdmin.classList.add('flex');
        } else {
            suspendedUntil.classList.add('hidden');
            messageAdmin.classList.add('hidden');
            messageAdmin.classList.remove('flex');
        }

        realtimeModal.classList.remove('hidden');
        realtimeModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(function () {
            realtimeCard.classList.remove('scale-95', 'opacity-0');
            realtimeCard.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeRealtimeAlert() {
        realtimeCard.classList.add('scale-95', 'opacity-0');
        realtimeCard.classList.remove('scale-100', 'opacity-100');
        window.setTimeout(function () {
            realtimeModal.classList.add('hidden');
            realtimeModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }, 220);
    }

    document.getElementById('closeRealtimeAlert')?.addEventListener('click', closeRealtimeAlert);

    if (window.Echo && realtimeToken) {
        window.Echo
            .channel('sari.seller.' + realtimeToken)
            .listen('.seller.compliance.alert', function (event) {
                openRealtimeAlert(event);

                // Refresh after the seller acknowledges the popup so statuses/counts reflect DB updates.
                document.getElementById('closeRealtimeAlert').onclick = function () {
                    closeRealtimeAlert();
                    window.setTimeout(function () { window.location.reload(); }, 260);
                };
            });
    } else {
        console.info('SARI realtime: Laravel Echo is not connected. Run Reverb and Vite to receive instant admin alerts.');
    }

});
</script>

@endpush