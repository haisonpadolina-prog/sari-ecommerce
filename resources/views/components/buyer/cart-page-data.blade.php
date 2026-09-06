@props([])

{{-- ============================================================
     BUYER CART — PAGE DATA
============================================================

     Purpose:
     Pass Laravel-generated routes safely to buyer-cart.js.

     This keeps:
     - Blade / PHP inside Blade
     - JavaScript logic inside resources/js
     - Laravel routes out of external JavaScript
     - Cart page easier to maintain

============================================================ --}}

@php
    $buyerCartPageData = [
        'routes' => [
            'products' => route('buyer.products'),

            'checkout' => route('buyer.checkout'),
        ],
    ];
@endphp


<script
    id="buyerCartPageData"
    type="application/json"
>
@json($buyerCartPageData)
</script>