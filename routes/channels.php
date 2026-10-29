<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private buyer–seller chat channels use the platform's role-specific session
// identities, not Laravel's unrelated default `users` authentication guard.
Broadcast::channel('sari.buyer-seller.seller.{sellerId}', static function ($user, string $sellerId): bool {
    return $user->getAuthIdentifier() === 'seller-' . $sellerId;
}, ['guards' => ['sari-chat-session']]);

Broadcast::channel('sari.buyer-seller.buyer.{type}.{buyerId}', static function ($user, string $type, string $buyerId): bool {
    return in_array($type, ['account', 'social'], true)
        && $user->getAuthIdentifier() === 'buyer-' . $type . '-' . $buyerId;
}, ['guards' => ['sari-chat-session']]);
