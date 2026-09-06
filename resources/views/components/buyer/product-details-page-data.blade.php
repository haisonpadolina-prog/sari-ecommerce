@props([
    'product' => [],
    'products' => [],
])

{{-- ============================================================
     BUYER PRODUCT DETAILS — PAGE DATA

     Data bridge between Laravel / Blade and
     resources/js/buyer-product-details.js
============================================================ --}}

@php
    $buyerProductDetailsPageData = [
        'product' => $product,

        // Used for "You may also like" / related products.
        'products' => $products,

        'routes' => [
            // Product pages
            'products' => route('buyer.products'),
            'productsBase' => url('/buyer/products'),

            // Cart
            'cart' => route('buyer.cart'),
            'cartStore' => route('buyer.cart.items.store'),

            // Checkout / Buy Now
            'checkout' => route('buyer.checkout'),
        ],
    ];
@endphp

<script
    id="buyerProductDetailsPageData"
    type="application/json"
>
@json($buyerProductDetailsPageData)
</script>