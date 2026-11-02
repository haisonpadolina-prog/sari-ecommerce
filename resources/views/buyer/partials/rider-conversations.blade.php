{{-- Buyer inbound Rider conversations: replies only, reuses universal authenticated API. --}}
<link rel="stylesheet" href="{{ asset('css/buyer/sari-rider-conversations.css') }}?v=20261101-r2">
<div id="sariBuyerRiderInbox" class="sari-brm" data-api-base="{{ url('/messaging/api') }}" data-csrf="{{ csrf_token() }}">
  <header class="sari-brm-page-header"><div><p>Buyer communications</p><h1>Rider <span>Messages</span></h1></div><a href="{{ route('buyer.messages') }}">Store conversations ›</a></header>
  <section class="sari-brm-shell">
    <aside class="sari-brm-side"><div class="sari-brm-heading"><h2>Rider conversations</h2><p>Messages from riders coordinating your deliveries</p><label><span class="sari-brm-sr">Search Rider messages</span><input id="sariBrmSearch" type="search" placeholder="Search conversations..." autocomplete="off"></label></div><div id="sariBrmThreads" class="sari-brm-threads"><p>Loading Rider conversations…</p></div></aside>
    <section class="sari-brm-chat" aria-label="Rider messages"><header id="sariBrmHeader" hidden><span class="sari-brm-avatar" id="sariBrmAvatar">R</span><div><h2 id="sariBrmName">Rider</h2><p>Private delivery coordination</p></div></header><div id="sariBrmMessages" class="sari-brm-messages" role="log" aria-live="polite"><div class="sari-brm-empty"><h3>No Rider conversation selected</h3><p>Select a Rider who has messaged you to reply.</p></div></div><p id="sariBrmError" class="sari-brm-error" role="alert" hidden></p><form id="sariBrmReply" hidden><label for="sariBrmBody" class="sari-brm-sr">Reply to Rider</label><textarea id="sariBrmBody" maxlength="3000" rows="1" placeholder="Write a message to the Rider..."></textarea><button type="submit" id="sariBrmSend" aria-label="Send reply">Send</button></form></section>
  </section>
</div>
<script src="{{ asset('js/buyer/sari-rider-conversations.js') }}?v=20261101-r2" defer></script>
