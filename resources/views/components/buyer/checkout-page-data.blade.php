@props([
    'promotionCatalog' => [],
    'currentAddress' => [],
])

{{-- ============================================================
     BUYER CHECKOUT — PAGE DATA
============================================================

     Purpose:
     Pass Laravel / Blade checkout data safely to
     buyer-checkout.js.

     This keeps:
     - PHP / Blade data inside Blade
     - JavaScript logic inside resources/js
     - Laravel-generated routes
     - Promotion catalog available to JavaScript
     - Current delivery address available to JavaScript

============================================================ --}}

@php
    $buyerCheckoutPageData = [
        'promotionCatalog' => $promotionCatalog,

        'currentAddress' => [
            'name' => $currentAddress['name'] ?? '',
            'phone' => $currentAddress['phone'] ?? '',
            'address' => $currentAddress['address'] ?? '',
        ],

        'routes' => [
            'cart' => route('buyer.cart'),

            'orders' => route('buyer.orders'),

            'products' => route('buyer.products'),
        ],
    ];
@endphp


<script
    id="buyerCheckoutPageData"
    type="application/json"
>
@json($buyerCheckoutPageData)
</script>