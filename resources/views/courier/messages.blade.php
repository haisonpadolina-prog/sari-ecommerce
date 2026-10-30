@extends('layouts.courier')

@section('title', 'Chat / Messaging — SARI Rider')
@section('header-title', 'Chat / Messaging')
@section('header-subtitle', 'SARI Logistics')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/messages.css') }}?v=20261030-2"
    >
@endpush

@section('content')
@php
    $hasProvider = (int) ($account->logistics_account_id ?? 0) > 0;

    $riderName = trim(
        ($account->first_name ?? '').' '.($account->last_name ?? '')
    ) ?: 'Rider';

    $riderInitials = mb_strtoupper(
        mb_substr((string) ($account->first_name ?? ''), 0, 1).
        mb_substr((string) ($account->last_name ?? ''), 0, 1)
    ) ?: 'R';

    $isOnline = $account->availability_status === 'online';

    $accountStatus = $account->account_status
        ? ucfirst(str_replace('_', ' ', $account->account_status))
        : 'Not available';

    $latestMessage = $messages->last();
    $messageCount = $messages->count();

    $conversationPreview = $latestMessage
        ? \Illuminate\Support\Str::limit(
            ($latestMessage->sender_role === 'rider' ? 'You: ' : '').
            (string) $latestMessage->body,
            100
        )
        : 'Start a conversation with Logistics.';
@endphp

<div id="riderMessenger" class="courier-messages-page">
    @if(session('success'))
        <div class="rm-alert rm-alert-success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rm-alert rm-alert-error" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rm-workspace">
        {{-- LEFT: CONVERSATION AND SEARCH --}}
        <aside
            class="rm-conversations"
            aria-label="Conversations and message search"
        >
            <div class="rm-pane-heading">
                <h2 class="rm-pane-title">Conversations</h2>

                <p class="rm-pane-description">
                    Your Logistics coordination channel
                </p>

                <div class="rm-search">
                    <label for="riderMessageSearch" class="rm-sr-only">
                        Search messages in this conversation
                    </label>

                    <svg
                        class="rm-search-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        aria-hidden="true"
                    >
                        <circle cx="10.5" cy="10.5" r="6.5"/>
                        <path d="m16 16 4.5 4.5"/>
                    </svg>

                    <input
                        id="riderMessageSearch"
                        type="search"
                        class="rm-search-input"
                        placeholder="Search messages..."
                        autocomplete="off"
                        aria-controls="riderMessageList"
                    >
                </div>

                <p
                    id="riderSearchResults"
                    class="rm-search-result"
                    role="status"
                    aria-live="polite"
                    hidden
                ></p>
            </div>

            <nav class="rm-conversation-list" aria-label="Your conversation">
                <a
                    id="riderConversationLink"
                    href="#riderMessageThread"
                    class="rm-conversation"
                    aria-current="true"
                >
                    <span class="rm-brand" aria-hidden="true">
                        <img
                            src="{{ asset('images/sari-main-logo.png') }}"
                            alt=""
                        >
                    </span>

                    <div class="rm-conversation-copy">
                        <strong class="rm-conversation-name">
                            SARI Logistics
                        </strong>

                        <p class="rm-conversation-role">
                            Dispatch and delivery support
                        </p>

                        <p class="rm-preview">
                            {{ $conversationPreview }}
                        </p>
                    </div>
                </a>
            </nav>
        </aside>

        {{-- CENTER: CHAT --}}
        <section class="rm-center" aria-labelledby="riderChatTitle">
            <header class="rm-chat-header">
                <div class="rm-contact">
                    <span class="rm-brand" aria-hidden="true">
                        <img
                            src="{{ asset('images/sari-main-logo.png') }}"
                            alt=""
                        >
                    </span>

                    <div class="rm-contact-copy">
                        <h2 id="riderChatTitle" class="rm-contact-name">
                            SARI Logistics
                        </h2>

                        <p class="rm-contact-description">
                            Your Logistics coordination channel
                        </p>
                    </div>
                </div>

                <a
                    href="{{ url()->current() }}"
                    class="rm-icon-button"
                    aria-label="Refresh messages"
                    title="Refresh messages"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M3 10a9 9 0 1 1 2.6 8.4"/>
                        <path d="M3 4v6h6"/>
                    </svg>
                </a>
            </header>

            @unless($hasProvider)
                <div class="rm-notice" role="status">
                    Your Rider account is not connected to a Logistics provider.
                    Messaging becomes available once a provider is assigned.
                </div>
            @endunless

            <div
                id="riderMessageThread"
                class="rm-thread"
                role="region"
                aria-label="Messages with SARI Logistics"
                tabindex="0"
            >
                <ol id="riderMessageList" class="rm-message-list">
                    @foreach($messages as $message)
                        @php
                            $isOwnMessage = $message->sender_role === 'rider';
                        @endphp

                        <li
                            class="rm-message {{ $isOwnMessage ? 'rm-message-own' : '' }}"
                            data-message-row
                        >
                            @unless($isOwnMessage)
                                <span
                                    class="rm-brand rm-message-avatar"
                                    aria-hidden="true"
                                >
                                    <img
                                        src="{{ asset('images/sari-main-logo.png') }}"
                                        alt=""
                                    >
                                </span>
                            @endunless

                            <div class="rm-message-content">
                                <p class="rm-sender">
                                    {{ $isOwnMessage ? 'You' : 'SARI Logistics' }}
                                </p>

                                <p class="rm-bubble" data-message-body>{{ $message->body }}</p>

                                @if($message->created_at)
                                    <time
                                        class="rm-time"
                                        datetime="{{ $message->created_at->toIso8601String() }}"
                                    >
                                        {{ $message->created_at->format('M d, h:i A') }}
                                    </time>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>

                <div
                    id="riderMessageEmpty"
                    class="rm-empty"
                    @if($messageCount > 0) hidden @endif
                >
                    <div class="rm-empty-icon" aria-hidden="true">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.6"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"/>
                            <path d="M7 8h10M7 12h6"/>
                        </svg>
                    </div>

                    <strong id="riderMessageEmptyTitle">
                        No messages yet
                    </strong>

                    <p id="riderMessageEmptyDescription">
                        {{ $hasProvider
                            ? 'Start a conversation about your pickups or deliveries.'
                            : 'Your conversation will appear here once your account is connected.' }}
                    </p>
                </div>
            </div>

            <form
                id="riderMessageForm"
                method="POST"
                action="{{ route('courier.messages.send') }}"
                class="rm-composer"
            >
                @csrf

                <label for="courierMessageBody" class="rm-sr-only">
                    Message to SARI Logistics
                </label>

                <div class="rm-composer-box">
                    <textarea
                        id="courierMessageBody"
                        name="body"
                        class="rm-textarea"
                        rows="1"
                        maxlength="3000"
                        required
                        placeholder="Write a message to Logistics..."
                        aria-describedby="riderMessageHelp{{ $errors->has('body') ? ' riderMessageError' : '' }}"
                        aria-invalid="{{ $errors->has('body') ? 'true' : 'false' }}"
                        @disabled(!$hasProvider)
                    >{{ old('body') }}</textarea>

                    <button
                        type="submit"
                        class="rm-icon-button rm-send"
                        aria-label="Send message"
                        title="Send message"
                        @disabled(!$hasProvider)
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="m22 2-7 20-4-9-9-4 20-7Z"/>
                            <path d="M22 2 11 13"/>
                        </svg>
                    </button>
                </div>

                @error('body')
                    <p id="riderMessageError" class="rm-field-error">
                        {{ $message }}
                    </p>
                @enderror

                <div class="rm-composer-meta">
                    <span id="riderMessageHelp">
                        Include the order number. Maximum 3,000 characters.
                    </span>

                    <span
                        id="riderMessageCount"
                        aria-hidden="true"
                        hidden
                    ></span>
                </div>
            </form>
        </section>

        {{-- RIGHT: EXISTING RIDER ACCOUNT DATA --}}
        <aside class="rm-details" aria-labelledby="riderChatDetailsTitle">
            <div class="rm-pane-heading">
                <h2 id="riderChatDetailsTitle" class="rm-pane-title">
                    Account Details
                </h2>

                <p class="rm-pane-description">
                    Your Rider account
                </p>
            </div>

            <div class="rm-profile">
                <div class="rm-profile-avatar" aria-hidden="true">
                    {{ $riderInitials }}
                </div>

                <strong class="rm-profile-name">
                    {{ $riderName }}
                </strong>

                <p class="rm-profile-email">
                    {{ $account->email }}
                </p>

                <span class="rm-role">Rider</span>
            </div>

            <dl class="rm-detail-list">
                <div class="rm-detail-card">
                    <dt>Account Status</dt>
                    <dd>{{ $accountStatus }}</dd>
                </div>

                <div class="rm-detail-card">
                    <dt>Your Availability</dt>
                    <dd>
                        <span class="rm-status {{ $isOnline ? 'rm-status-online' : '' }}">
                            {{ $isOnline ? 'Online' : 'Offline' }}
                        </span>
                    </dd>
                </div>

                <div class="rm-detail-card">
                    <dt>Conversation</dt>
                    <dd>
                        {{ $messageCount }}
                        {{ $messageCount === 1 ? 'total message' : 'total messages' }}
                    </dd>
                </div>

                <div class="rm-detail-card">
                    <dt>Logistics Provider</dt>
                    <dd>
                        {{ $hasProvider ? 'Assigned' : 'Not assigned' }}
                    </dd>
                </div>
            </dl>

            <p class="rm-details-note">
                Use this conversation for pickup coordination, delivery updates,
                and concerns requiring Logistics assistance.
            </p>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('riderMessenger');

    if (!root) return;

    const thread = root.querySelector('#riderMessageThread');
    const textarea = root.querySelector('#courierMessageBody');
    const counter = root.querySelector('#riderMessageCount');

    const search = root.querySelector('#riderMessageSearch');
    const searchResults = root.querySelector('#riderSearchResults');
    const conversationLink = root.querySelector('#riderConversationLink');

    const empty = root.querySelector('#riderMessageEmpty');
    const emptyTitle = root.querySelector('#riderMessageEmptyTitle');
    const emptyDescription = root.querySelector('#riderMessageEmptyDescription');

    const initialEmptyTitle = emptyTitle?.textContent.trim() || '';
    const initialEmptyDescription =
        emptyDescription?.textContent.trim() || '';

    const messages = Array.from(
        root.querySelectorAll('[data-message-row]')
    ).map((row) => ({
        row,
        text: (
            row.querySelector('[data-message-body]')?.textContent || ''
        ).toLocaleLowerCase(),
    }));

    const updateComposer = () => {
        if (!textarea) return;

        textarea.style.height = 'auto';
        textarea.style.height = `${Math.min(textarea.scrollHeight, 120)}px`;

        if (counter) {
            counter.hidden = false;
            counter.textContent =
                `${textarea.value.length.toLocaleString('en-US')} / 3,000`;
        }
    };

    const filterMessages = () => {
        const query = (search?.value || '').trim().toLocaleLowerCase();
        let visibleCount = 0;

        messages.forEach(({ row, text }) => {
            const matches = !query || text.includes(query);

            row.hidden = !matches;

            if (matches) {
                visibleCount += 1;
            }
        });

        if (searchResults) {
            searchResults.hidden = !query;
            searchResults.textContent =
                `${visibleCount} matching ` +
                `${visibleCount === 1 ? 'message' : 'messages'}`;
        }

        if (empty) {
            empty.hidden = visibleCount > 0;
        }

        if (emptyTitle) {
            emptyTitle.textContent = query
                ? 'No matching messages'
                : initialEmptyTitle;
        }

        if (emptyDescription) {
            emptyDescription.textContent = query
                ? 'Try another keyword or clear the search.'
                : initialEmptyDescription;
        }

        if (thread) {
            thread.scrollTop = query ? 0 : thread.scrollHeight;
        }
    };

    search?.addEventListener('input', filterMessages);
    textarea?.addEventListener('input', updateComposer);

    conversationLink?.addEventListener('click', (event) => {
        event.preventDefault();

        if (search) {
            search.value = '';
        }

        filterMessages();
        thread?.focus({ preventScroll: true });
    });

    window.addEventListener('pageshow', updateComposer);

    updateComposer();
    filterMessages();
});
</script>
@endpush