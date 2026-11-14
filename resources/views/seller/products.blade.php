@extends('layouts.seller')

@section('title', 'Products — SARI')
@section('page-title', 'Product Management')

{{-- Products CSS stays in a Blade partial, not in public/css. --}}
@push('styles')
    @include('seller.products.partials.styles')
@endpush

@section('content')
{{-- SARI_STRUCTURED_SIZE_CHART_LINK_V1 --}}
<div style="display:flex;justify-content:flex-end;margin:0 auto 14px;max-width:1520px;font-family:Poppins,ui-sans-serif,system-ui,sans-serif">
    <a href="{{ route('seller.products.size-charts') }}" style="font-size:12px;font-weight:650;color:var(--sari-dark-a63aa16d7c, #59636e);text-decoration:none;padding:8px 0">Product Size Charts <span aria-hidden="true">›</span></a>
</div>




@php
    /*
    | Reuse the seller account already passed to this page.
    | No database query is added here.
    */
    $warningCount = (int) ($sellerAccount->warning_count ?? 0);
    $activeSuspension = $sellerAccount->suspended_until
        ? now()->lt(\Illuminate\Support\Carbon::parse($sellerAccount->suspended_until))
        : false;
    $sellerLocked = $warningCount >= 3 || $activeSuspension;
@endphp

@php
    /*
     * First-screen products arrive from SellerProductController@index.
     * This removes the skeleton -> AJAX -> render wait from initial navigation.
     */
    $initialProductLibrary = $initialProductLibrary ?? [
        'success' => true,
        'server_now' => now()->toIso8601String(),
        'count' => 0,
        'initial_count' => 0,
        'products' => [],
    ];

    $initialProducts = collect($initialProductLibrary['products'] ?? []);
@endphp

{{-- Early server-rendered library bootstrap (kept before the catalog DOM). --}}
@include('seller.products.partials.bootstrap')

{{-- Product workspace + server-rendered initial cards, unchanged. --}}
@include('seller.products.partials.catalog')

{{-- Original Product CRUD dialogs: unique IDs and all JS hooks are preserved. --}}
@include('seller.products.partials.modals.view')
@include('seller.products.partials.modals.edit')
@include('seller.products.partials.modals.action')
@endsection

@push('scripts')
<script>
window.__SARI_PRODUCTS_CONFIG__ = {
    libraryUrl: @json(route('seller.products.library')),
    productsBaseUrl: @json(url('/seller/products')),
    productDraftUrl: @json(route('seller.products.draft')),
    productDraftSaveUrl: @json(route('seller.products.draft.save')),
    productDraftDeleteUrl: @json(route('seller.products.draft.delete')),
    requestedCategory: @json((string) request('category', '')),
    createUrl: @json(route('seller.products.create')),
};
</script>
<script src="{{ asset('js/seller-products.js') }}?v=20261107-title-ellipsis" data-navigate-once></script>














@endpush


