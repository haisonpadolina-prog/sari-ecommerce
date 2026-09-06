@props([
    'products' => [],
    'searchQuery' => '',
])

{{-- ============================================================
     BUYER PRODUCTS — PAGE DATA
============================================================

     Purpose:
     Pass Laravel / Blade data safely to buyer-products.js.

     This keeps:
     - PHP / Blade data inside Blade
     - JavaScript logic inside resources/js
     - Laravel-generated routes
     - Product data available to the Products page

============================================================ --}}

@php
    $buyerProductsPageData = [
        'products' => $products,

        'searchQuery' => $searchQuery,

        'routes' => [
            'products' => route('buyer.products'),

            'productsBase' => url('/buyer/products'),

            'cart' => route('buyer.cart'),
        ],
    ];
@endphp


<script
    id="buyerProductsPageData"
    type="application/json"
>
@json($buyerProductsPageData)
</script>