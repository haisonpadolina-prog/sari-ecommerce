@extends('layouts.buyer')

@section('title', ($shop['store_name'] ?? 'Shop') . ' — SARI')
@section('page-title', 'Seller Shop')

@section('content')
@include('components.buyer.header')

<div class="mx-auto w-full max-w-[1500px] p-4 sm:p-6 lg:p-8">
    <section class="rounded-[24px] border border-[#ebe4da] bg-white p-5 sm:p-7">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="grid h-16 w-16 place-items-center rounded-[18px] border border-[#eadfc9] bg-[#fffaf2] text-[18px] font-bold text-[#b97805]">{{ strtoupper(substr($shop['store_name'], 0, 1)) }}</div>
                <div>
                    <p class="text-[8px] font-semibold uppercase tracking-[.14em] text-[#a8731f]">Verified SARI Seller</p>
                    <h1 class="mt-1 text-[24px] font-bold tracking-[-.03em] text-[#28221b]">{{ $shop['store_name'] }}</h1>
                    <p class="mt-1 text-[9px] text-[#8c8378]">{{ $shop['products_count'] }} active products • ★ {{ number_format((float) $shop['rating'], 1) }} from {{ $shop['rating_count'] }} reviews</p>
                </div>
            </div>
            <a href="{{ route('buyer.messages', ['seller' => $shop['seller_account_id']]) }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#c99128] px-5 text-[9px] font-semibold text-white hover:bg-[#b47e1e]">Message Seller</a>
        </div>
    </section>

    <section class="mt-5 rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[16px] font-bold text-[#2f2923]">Products</h2>
                <p class="mt-1 text-[9px] text-[#91887d]">Only approved and active listings are visible here.</p>
            </div>
            <input id="shopProductSearch" type="search" placeholder="Search this shop..." class="h-10 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px] outline-none focus:border-[#c99128] sm:w-[280px]">
        </div>

        <div id="shopProductGrid" class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <x-buyer.product-card :product="$product" :interactive="true" :show-promotions="true" :show-stock="true" :show-quick-add="true" />
            @endforeach
        </div>

        <div id="shopProductsEmpty" class="mt-5 hidden rounded-2xl border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-10 text-center text-[10px] text-[#7e756a]">No products match your search.</div>
    </section>
</div>
@endsection

@push('scripts')
    @vite('resources/js/buyer-shop.js')
@endpush
