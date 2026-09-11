@extends('layouts.buyer')

@section('title', 'Checkout — SARI')
@section('page-title', 'Checkout')

@section('content')
@include('components.buyer.header')

<div class="mx-auto w-full max-w-[1500px] p-4 sm:p-6 lg:p-8">
    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[11px] text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <form id="buyerCheckoutForm" method="POST" action="{{ route('buyer.checkout.store') }}" class="grid gap-5 xl:grid-cols-[1fr_400px]">
        @csrf
        @if (!empty($buyNowItemId))
            <input type="hidden" name="buy_now_item_id" value="{{ (int) $buyNowItemId }}">
        @elseif (!empty($selectedItemIds))
            @foreach ($selectedItemIds as $selectedItemId)
                <input type="hidden" name="checkout_item_ids[]" value="{{ (int) $selectedItemId }}">
            @endforeach
        @endif
        <div class="space-y-5">
            <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
                <p class="text-[8px] font-semibold uppercase tracking-[.14em] text-[#a8731f]">Delivery Information</p>
                <h1 class="mt-2 text-[20px] font-bold text-[#302a24]">Where should we deliver?</h1>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Full Name</label>
                        <input name="buyer_name" value="{{ old('buyer_name', $profile['name']) }}" required class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px] outline-none focus:border-[#c99128]">
                    </div>
                    <div>
                        <label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Email</label>
                        <input name="buyer_email" type="email" value="{{ old('buyer_email', $profile['email']) }}" required class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px] outline-none focus:border-[#c99128]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Contact Number</label>
                        <input name="buyer_phone" value="{{ old('buyer_phone', $profile['phone']) }}" required placeholder="09xxxxxxxxx" class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px] outline-none focus:border-[#c99128]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Complete Delivery Address</label>
                        <textarea name="buyer_address" required rows="4" class="w-full resize-none rounded-xl border border-[#e4ddd3] px-4 py-3 text-[9px] leading-5 outline-none focus:border-[#c99128]" placeholder="House/Unit, Street, Barangay, City/Municipality, Province">{{ old('buyer_address', $profile['address']) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
                <p class="text-[8px] font-semibold uppercase tracking-[.14em] text-[#a8731f]">Payment</p>
                <h2 class="mt-2 text-[16px] font-bold text-[#302a24]">Payment Method</h2>
                <label class="mt-4 flex cursor-pointer items-center gap-3 rounded-2xl border border-[#eadfc9] bg-[#fffaf2] p-4">
                    <input type="radio" name="payment_method" value="COD" checked class="accent-[#c99128]">
                    <div>
                        <p class="text-[10px] font-semibold text-[#514a42]">Cash on Delivery</p>
                        <p class="mt-1 text-[8px] text-[#91887d]">Pay the courier after the package reaches you.</p>
                    </div>
                </label>
            </section>

            <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
                <h2 class="text-[16px] font-bold text-[#302a24]">Products by Seller</h2>
                <div class="mt-4 space-y-4">
                    @foreach ($groups as $sellerId => $sellerItems)
                        <div class="rounded-2xl border border-[#eee7dc] bg-[#fcfbf8] p-4">
                            <p class="text-[9px] font-semibold text-[#a8731f]">{{ $sellerItems->first()->product->seller?->store_name ?: 'SARI Seller' }}</p>
                            <div class="mt-3 space-y-2">
                                @foreach ($sellerItems as $item)
                                    <div class="flex items-start justify-between gap-3 text-[9px]">
                                        <div class="min-w-0"><p class="font-semibold text-[#514a42]">{{ $item->product->name }} × {{ $item->quantity }}</p><p class="mt-1 text-[7px] text-[#91887d]">{{ $cart->variantLabel($item) }}</p></div>
                                        <span class="shrink-0 font-semibold text-[#302a24]">₱{{ number_format($cart->lineTotal($item), 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="h-fit rounded-[22px] border border-[#ebe4da] bg-white p-5 xl:sticky xl:top-24">
            <h2 class="text-[15px] font-bold text-[#302a24]">Checkout Summary</h2>
            <div class="mt-5 space-y-3 text-[9px]">
                <div class="flex justify-between"><span class="text-[#8d8479]">Products</span><span class="font-semibold">₱{{ number_format($subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-[#8d8479]">Delivery</span><span class="font-semibold">₱{{ number_format($deliveryFee, 2) }}</span></div>
                <div class="border-t border-[#eee8df] pt-3"><div class="flex items-end justify-between"><span class="font-semibold">Total</span><span class="text-[21px] font-bold text-[#b97805]">₱{{ number_format($grandTotal, 2) }}</span></div></div>
            </div>
            <button id="buyerPlaceOrderButton" class="mt-5 h-12 w-full rounded-xl bg-[#c99128] text-[10px] font-semibold text-white hover:bg-[#b47e1e]">Place Order</button>
            <p class="mt-3 text-[7px] leading-4 text-[#9b9287]">Stock deduction and order creation happen in one database transaction. If any item becomes unavailable, the checkout is rolled back.</p>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
    @vite('resources/js/buyer-checkout.js')
@endpush
