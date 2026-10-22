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

                @if (!empty($savedAddresses) && $savedAddresses->isNotEmpty())
                    <div class="mt-4 rounded-xl border border-[#e3e7eb] bg-[#fafbfc] p-3.5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-[9px] font-semibold text-[#38414b]">Saved delivery address</p>
                                <p class="mt-0.5 text-[7.5px] text-[#8a939d]">Your active address is preselected. Choose another saved address or edit the fields below for this order only.</p>
                            </div>

                            <select
                                id="checkoutSavedAddress"
                                class="h-10 min-w-[220px] rounded-xl border border-[#dfe4e8] bg-white px-3 text-[9px] font-medium text-[#4d5762] outline-none focus:border-[#c99128]"
                            >
                                <option
                                    value=""
                                    data-recipient="{{ $registeredProfile['name'] ?? $profile['name'] }}"
                                    data-phone="{{ $registeredProfile['phone'] ?? $profile['phone'] }}"
                                    data-address="{{ $registeredProfile['address'] ?? $profile['address'] }}"
                                    @selected(empty($selectedAddressId))
                                >
                                    Registered address{{ empty($selectedAddressId) ? ' · Active' : '' }}
                                </option>
                                @foreach ($savedAddresses as $address)
                                    <option
                                        value="{{ $address->id }}"
                                        data-recipient="{{ $address->recipient_name }}"
                                        data-phone="{{ $address->contact_no }}"
                                        data-address="{{ $address->fullAddress() }}"
                                        @selected((int) ($selectedAddressId ?? 0) === (int) $address->id)
                                    >
                                        {{ $address->label }}{{ $address->is_default ? ' · Default' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Full Name</label>
                        <input id="checkoutBuyerName" name="buyer_name" value="{{ old('buyer_name', $profile['name']) }}" required class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px] outline-none focus:border-[#c99128]">
                    </div>
                    <div>
                        <label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Email</label>
                        <input name="buyer_email" type="email" value="{{ old('buyer_email', $profile['email']) }}" required class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px] outline-none focus:border-[#c99128]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Contact Number</label>
                        <input id="checkoutBuyerPhone" name="buyer_phone" value="{{ old('buyer_phone', $profile['phone']) }}" required placeholder="09xxxxxxxxx" class="h-11 w-full rounded-xl border border-[#e4ddd3] px-4 text-[9px] outline-none focus:border-[#c99128]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-[8px] font-semibold text-[#62594e]">Complete Delivery Address</label>
                        <textarea id="checkoutBuyerAddress" name="buyer_address" required rows="4" class="w-full resize-none rounded-xl border border-[#e4ddd3] px-4 py-3 text-[9px] leading-5 outline-none focus:border-[#c99128]" placeholder="House/Unit, Street, Barangay, City/Municipality, Province">{{ old('buyer_address', $profile['address']) }}</textarea>
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

                            @php
                                $sellerShipping = $shippingBySeller[(int) $sellerId] ?? null;
                                $shippingOffer = $sellerShipping['promotion'] ?? null;
                            @endphp

                            @if($shippingOffer && ($shippingOffer['active'] ?? false))
                                <div class="mt-3 rounded-xl border border-[#d9e8de] bg-[#f4faf6] px-3 py-2.5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-[8px] font-semibold text-[#46775a]">Free Shipping Promotion</p>
                                            <p class="mt-1 text-[7px] leading-4 text-[#708078]">
                                                Minimum spend ₱{{ number_format((float) ($shippingOffer['minimum_order_amount'] ?? 0), 2) }} · up to ₱{{ number_format((float) ($shippingOffer['maximum_shipping_discount'] ?? 0), 2) }} shipping off
                                            </p>
                                        </div>
                                        <span class="shrink-0 rounded-full px-2 py-1 text-[6.5px] font-semibold {{ ($shippingOffer['eligible'] ?? false) ? 'bg-[#e5f5eb] text-[#397451]' : 'border border-[#d5e3da] bg-white text-[#748178]' }}">
                                            @if($shippingOffer['eligible'] ?? false)
                                                Applied · save ₱{{ number_format((float) ($shippingOffer['shipping_discount'] ?? 0), 2) }}
                                            @else
                                                Add ₱{{ number_format((float) ($shippingOffer['remaining_to_qualify'] ?? 0), 2) }} more
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif

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

            <div class="mt-5 rounded-2xl border border-[#eee7dc] bg-[#fcfbf8] p-4">
                <label for="checkoutVoucherCode" class="block text-[8px] font-semibold uppercase tracking-[.1em] text-[#8d8479]">Voucher code</label>
                <div class="mt-2 flex gap-2">
                    <input id="checkoutVoucherCode" name="voucher_code" value="{{ old('voucher_code', $appliedVoucherCode ?? '') }}" placeholder="Seller or SARI event voucher" class="h-10 min-w-0 flex-1 rounded-xl border border-[#e4ddd3] bg-white px-3 text-[9px] uppercase outline-none focus:border-[#c99128]">
                    <button id="applyCheckoutVoucherButton" type="button" class="h-10 shrink-0 rounded-xl border border-[#d5a94f] bg-[#fff9ec] px-4 text-[8px] font-bold text-[#956619] transition hover:bg-[#fff1ce]">Apply</button>
                </div>
                <p class="mt-2 text-[7px] leading-4 text-[#9b9287]">Apply a SARI event voucher to preview the discount now. Every voucher is revalidated securely again when the order is placed.</p>

                @if(($platformVoucherDiscount ?? 0) > 0)
                    <div class="mt-3 rounded-xl border border-[#d9e8de] bg-[#f4faf6] px-3 py-2.5">
                        <p class="text-[8px] font-semibold text-[#46775a]">SARI voucher applied · {{ $appliedVoucherCode }}</p>
                        <p class="mt-1 text-[7px] text-[#708078]">You save ₱{{ number_format((float) $platformVoucherDiscount, 2) }} on eligible products.</p>
                    </div>
                @elseif(!empty($voucherPreviewError))
                    <div class="mt-3 rounded-xl border border-[#ead7d2] bg-[#fff7f5] px-3 py-2.5 text-[7.5px] leading-4 text-[#9b5f55]">{{ $voucherPreviewError }}</div>
                @endif

                @if(!empty($platformVoucherOffers))
                    <div class="mt-3 border-t border-[#eee7dc] pt-3">
                        <p class="text-[7.5px] font-semibold text-[#6e665d]">Available SARI event vouchers</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($platformVoucherOffers as $offer)
                                <button type="button" data-platform-voucher-code="{{ $offer['code'] }}" class="rounded-lg border border-[#e6cf9b] bg-[#fff9ec] px-2.5 py-2 text-left hover:bg-[#fff4d9]">
                                    <span class="block text-[8px] font-bold text-[#93651b]">{{ $offer['code'] }}</span>
                                    <span class="mt-0.5 block text-[6.8px] text-[#8d7b60]">{{ $offer['value'] }}@if(($offer['minimum'] ?? 0)>0) · Min ₱{{ number_format((float)$offer['minimum'],2) }}@endif</span>
                                    @if(!empty($offer['ends_at_pht']))<span class="mt-0.5 block text-[6.4px] text-[#9b9287]">Until {{ $offer['ends_at_pht'] }} PHT</span>@endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-5 space-y-3 text-[9px]">
                @if(($platformSaleDiscount ?? 0) > 0)
                    <div class="flex justify-between"><span class="text-[#8d8479]">Products before SARI event</span><span class="font-semibold">₱{{ number_format($sellerSubtotal, 2) }}</span></div>
                    <div class="flex justify-between text-[#9a6a1a]"><span>SARI event discount</span><span class="font-semibold">-₱{{ number_format($platformSaleDiscount, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-[#8d8479]">Products payable</span><span class="font-semibold">₱{{ number_format($subtotal, 2) }}</span></div>
                @else
                    <div class="flex justify-between"><span class="text-[#8d8479]">Products</span><span class="font-semibold">₱{{ number_format($subtotal, 2) }}</span></div>
                @endif
                @if(($platformVoucherDiscount ?? 0) > 0)
                    <div class="flex justify-between text-[#4f8065]"><span>SARI voucher · {{ $appliedVoucherCode }}</span><span class="font-semibold">-₱{{ number_format((float) $platformVoucherDiscount, 2) }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-[#8d8479]">Courier delivery fee</span><span class="font-semibold">₱{{ number_format($originalDeliveryFee, 2) }}</span></div>
                @if(($shippingDiscount ?? 0) > 0)
                    <div class="flex justify-between text-[#4f8065]"><span>Free Shipping discount</span><span class="font-semibold">-₱{{ number_format($shippingDiscount, 2) }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-[#8d8479]">Delivery payable</span><span class="font-semibold">₱{{ number_format($deliveryFee, 2) }}</span></div>
                <div class="border-t border-[#eee8df] pt-3"><div class="flex items-end justify-between"><span class="font-semibold">Total</span><span class="text-[21px] font-bold text-[#b97805]">₱{{ number_format($grandTotal, 2) }}</span></div></div>
            </div>
            <button id="buyerPlaceOrderButton" class="mt-5 h-12 w-full rounded-xl bg-[#c99128] text-[10px] font-semibold text-white hover:bg-[#b47e1e]">Place Order</button>
            <p class="mt-3 text-[7px] leading-4 text-[#9b9287]">Stock, vouchers, and Free Shipping eligibility are revalidated on the server when you place the order. Admin and Buyer promotion schedules use one clock: Philippine Standard Time (PHT · UTC+8).</p>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
    @vite('resources/js/buyer-checkout.js')
    <script>
        (() => {
            const input = document.getElementById('checkoutVoucherCode');
            const applyButton = document.getElementById('applyCheckoutVoucherButton');
            const savedAddress = document.getElementById('checkoutSavedAddress');
            const buyerName = document.getElementById('checkoutBuyerName');
            const buyerPhone = document.getElementById('checkoutBuyerPhone');
            const buyerAddress = document.getElementById('checkoutBuyerAddress');

            function applySavedAddress() {
                const option = savedAddress?.selectedOptions?.[0];
                if (!option) return;

                if (buyerName) buyerName.value = option.dataset.recipient || buyerName.value;
                if (buyerPhone) buyerPhone.value = option.dataset.phone || buyerPhone.value;
                if (buyerAddress) buyerAddress.value = option.dataset.address || buyerAddress.value;
            }

            savedAddress?.addEventListener('change', applySavedAddress);

            function openVoucherPreview(code) {
                const value = String(code || '').trim().toUpperCase();
                const url = new URL(@json(route('buyer.checkout')), window.location.origin);

                if (value) {
                    url.searchParams.set('voucher_code', value);
                }

                @if (!empty($buyNowItemId))
                    url.searchParams.set('buy_now', @json((int) $buyNowItemId));
                @else
                    url.searchParams.set('selection', '1');
                    @foreach ($selectedItemIds as $selectedItemId)
                        url.searchParams.append('items[]', @json((int) $selectedItemId));
                    @endforeach
                @endif

                window.location.assign(url.toString());
            }

            applyButton?.addEventListener('click', () => openVoucherPreview(input?.value));

            input?.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter') return;
                event.preventDefault();
                openVoucherPreview(input.value);
            });

            document.addEventListener('click', function (event) {
                const button = event.target.closest('[data-platform-voucher-code]');
                if (!button) return;
                if (input) input.value = button.dataset.platformVoucherCode || '';
                openVoucherPreview(button.dataset.platformVoucherCode || '');
            });
        })();
    </script>
@endpush
