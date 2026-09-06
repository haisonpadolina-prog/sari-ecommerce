@props([
    'buyerEmail' => '',
])

{{-- ============================================================
     BUYER ACCOUNT — PAGE DATA
============================================================

     Purpose:
     Pass Laravel / Blade account data safely to buyer-account.js.

     Keeps:
     - Blade / PHP values inside Blade
     - JavaScript logic inside resources/js
     - Buyer login email available to external JavaScript

============================================================ --}}

@php

    $buyerAccountPageData = [

        'buyerEmail' => $buyerEmail,

        'storage' => [
            'account' => 'sariBuyerAccount',
        ],

    ];

@endphp


<script
    id="buyerAccountPageData"
    type="application/json"
>
@json($buyerAccountPageData)
</script>