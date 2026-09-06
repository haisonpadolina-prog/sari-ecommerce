@props([
    'products' => [],
    'shop' => [],
])

{{-- ============================================================
     BUYER SHOP — PAGE DATA
============================================================

     Purpose:
     Pass Laravel / Blade data safely to buyer-shop.js.

     This keeps:
     - PHP / Blade data inside Blade
     - JavaScript logic inside resources/js
     - Laravel-generated routes
     - Shop products available to JavaScript

============================================================ --}}

@php
    $buyerShopPageData = [
        'products' => $products,

        'shop' => $shop,

        'routes' => [
            'products' => route('buyer.products'),

            'productsBase' => url('/buyer/products'),

            'cart' => route('buyer.cart'),

            'messages' => route('buyer.messages'),
        ],
    ];
@endphp


<script
    id="buyerShopPageData"
    type="application/json"
>
@json($buyerShopPageData)
</script>