@props([
    'defaultConversations' => [],
    'buyerEmail' => '',
])

{{-- ============================================================
     BUYER MESSAGES — PAGE DATA
============================================================

     Purpose:
     Pass Laravel / Blade data safely to buyer-messages.js.

     This keeps:
     - PHP / Blade data inside Blade
     - JavaScript logic inside resources/js
     - Default seller conversations available to JavaScript
     - Buyer session email available if needed later

============================================================ --}}

@php
    $buyerMessagesPageData = [
        'buyerEmail' => $buyerEmail,

        'defaultConversations' => $defaultConversations,

        'routes' => [
            'products' => route('buyer.products'),
            'messages' => route('buyer.messages'),
        ],

        'storage' => [
            'conversations' => 'sariBuyerConversations',
        ],
    ];
@endphp


<script
    id="buyerMessagesPageData"
    type="application/json"
>
@json($buyerMessagesPageData)
</script>