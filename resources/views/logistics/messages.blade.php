@extends('layouts.logistics')

@section('title', 'Messages | SARI Logistics')
@section('page-title', 'Rider Messages')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/logistics/messages.css') }}?v=20261015-2"
    >
@endpush


@section('content')

@php
    $selectedRiderName =
        $selectedRider
            ? trim(
                $selectedRider->first_name
                .' '
                .$selectedRider->last_name
            )
            : null;

    $totalUnread =
        (int) $unreadByRider->sum();

    $onlineCount =
        $riders
            ->where(
                'availability_status',
                'online'
            )
            ->count();
@endphp


<div
    id="logisticsMessagesPage"
    class="logistics-messages-page"
    data-logistics-messages
>

    @if(session('success'))

        <div class="logistics-chat-alert">
            {{ session('success') }}
        </div>

    @endif


    @if($errors->any())

        <div class="logistics-chat-alert is-error">
            {{ $errors->first() }}
        </div>

    @endif


    <section id="logisticsPlatformChat">

        <div class="logistics-chat-grid">


            {{-- =====================================================
                LEFT — RIDER CONVERSATIONS
            ====================================================== --}}

            <aside id="logisticsConversationPane">

                <div class="logistics-conversation-heading">

                    <h3>
                        Rider Messages
                    </h3>

                    <p>
                        {{ $riders->count() }}
                        active Riders
                        ·
                        {{ $totalUnread }}
                        unread
                    </p>


                    <div class="logistics-search-wrap">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>


                        <input
                            id="logisticsMessageSearch"
                            type="search"
                            placeholder="Search Riders"
                            data-rider-search
                        >

                    </div>

                </div>


                <div
                    class="logistics-conversation-list"
                    data-rider-list
                >

                    @forelse($riders as $rider)

                        @php
                            $riderName =
                                trim(
                                    $rider->first_name
                                    .' '
                                    .$rider->last_name
                                );

                            $isActive =
                                $selectedRider
                                &&
                                (int) $selectedRider->id
                                ===
                                (int) $rider->id;

                            $unread =
                                (int) (
                                    $unreadByRider[
                                        $rider->id
                                    ]
                                    ?? 0
                                );

                            $isOnline =
                                $rider->availability_status
                                === 'online';

                            $searchText =
                                implode(
                                    ' ',
                                    array_filter([
                                        $riderName,
                                        $rider->email,
                                        $rider->barangay_name,
                                        $rider->municipality_name,
                                        $rider->vehicle_type,
                                        $rider->plate_number,
                                    ])
                                );
                        @endphp


                        <a
                            href="{{ route(
                                'logistics.messages',
                                ['rider' => $rider->id]
                            ) }}"
                            class="
                                logistics-conversation-item
                                {{ $isActive ? 'is-active' : '' }}
                            "
                            data-rider-item
                            data-search="{{ $searchText }}"
                        >

                            <div class="logistics-rider-avatar-wrap">

                                <span class="logistics-rider-avatar">
                                    {{
                                        mb_strtoupper(
                                            mb_substr(
                                                $riderName,
                                                0,
                                                1
                                            )
                                        )
                                    }}
                                </span>


                                <span
                                    class="
                                        logistics-online-dot
                                        {{
                                            $isOnline
                                                ? 'is-online'
                                                : ''
                                        }}
                                    "
                                ></span>

                            </div>


                            <div class="logistics-conversation-copy">

                                <div class="logistics-conversation-name">

                                    <strong>
                                        {{ $riderName }}
                                    </strong>


                                    @if($unread > 0)

                                        <span class="logistics-conversation-unread">
                                            {{ $unread > 99 ? '99+' : $unread }}
                                        </span>

                                    @endif

                                </div>


                                <p>
                                    {{
                                        $rider->barangay_name
                                        ?: 'Service area not set'
                                    }}

                                    @if($rider->municipality_name)
                                        ·
                                        {{ $rider->municipality_name }}
                                    @endif
                                </p>


                                <span>
                                    {{
                                        $rider->vehicle_type
                                        ?: 'Rider'
                                    }}

                                    @if($rider->plate_number)
                                        ·
                                        {{ $rider->plate_number }}
                                    @endif
                                </span>

                            </div>

                        </a>

                    @empty

                        <div class="logistics-conversation-empty">

                            <strong>
                                No active Riders
                            </strong>

                            <p>
                                Approved active Riders will appear here.
                            </p>

                        </div>

                    @endforelse


                    <div
                        class="logistics-conversation-empty"
                        data-search-empty
                        hidden
                    >

                        <strong>
                            No Riders found
                        </strong>

                        <p>
                            Try a different name or service area.
                        </p>

                    </div>

                </div>

            </aside>


            {{-- =====================================================
                CENTER — ACTIVE THREAD
            ====================================================== --}}

            <main id="logisticsCenterPane">

                @if($selectedRider)

                    {{-- Thread Header --}}
                    <header class="logistics-thread-header">

                        <div class="logistics-thread-person">

                            <span class="logistics-thread-avatar">
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            $selectedRiderName,
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </span>


                            <div>

                                <strong>
                                    {{ $selectedRiderName }}
                                </strong>


                                <div class="logistics-thread-meta">

                                    <span
                                        class="
                                            logistics-platform-status
                                            {{
                                                $selectedRider
                                                    ->availability_status
                                                === 'online'
                                                    ? 'is-online'
                                                    : 'is-offline'
                                            }}
                                        "
                                    >
                                        {{
                                            $selectedRider
                                                ->availability_status
                                            === 'online'
                                                ? 'Online'
                                                : 'Offline'
                                        }}
                                    </span>


                                    <span>
                                        Logistics ↔ Rider
                                    </span>

                                </div>

                            </div>

                        </div>


                        <div class="logistics-thread-badge">
                            Rider
                        </div>

                    </header>


                    {{-- Messages --}}
                    <div
                        id="logisticsPlatformMessages"
                        data-message-thread
                    >

                        @forelse($messages as $message)

                            @php
                                $outgoing =
                                    $message->sender_role
                                    === 'logistics';
                            @endphp


                            <div
                                class="
                                    logistics-message-row
                                    {{
                                        $outgoing
                                            ? 'is-outgoing'
                                            : 'is-incoming'
                                    }}
                                "
                            >

                                <div class="logistics-message-content">

                                    @unless($outgoing)

                                        <div class="logistics-message-label">
                                            {{ $selectedRiderName }}
                                        </div>

                                    @endunless


                                    <div
                                        data-chat-bubble
                                        data-chat-side="{{
                                            $outgoing
                                                ? 'outgoing'
                                                : 'incoming'
                                        }}"
                                    >{{ $message->body }}</div>


                                    <div class="logistics-message-time">

                                        {{
                                            $message
                                                ->created_at
                                                ?->timezone(
                                                    'Asia/Manila'
                                                )
                                                ->format(
                                                    'M d, g:i A'
                                                )
                                        }}


                                        @if(
                                            $outgoing
                                            &&
                                            $message->read_at
                                        )
                                            · Read
                                        @endif

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="logistics-thread-empty">

                                <div class="logistics-thread-empty-icon">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                    >
                                        <path
                                            d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"
                                        ></path>
                                    </svg>

                                </div>


                                <strong>
                                    Start a conversation
                                </strong>


                                <p>
                                    Send a message to
                                    {{ $selectedRiderName }}
                                    for Logistics coordination.
                                </p>

                            </div>

                        @endforelse

                    </div>


                    {{-- Composer --}}
                    <footer class="logistics-platform-composer">

                        <form
                            id="logisticsMessageForm"
                            method="POST"
                            action="{{ route(
                                'logistics.messages.send',
                                $selectedRider
                            ) }}"
                            data-message-form
                        >

                            @csrf


                            <textarea
                                id="logisticsPlatformInput"
                                name="body"
                                required
                                maxlength="3000"
                                rows="1"
                                placeholder="Message {{ $selectedRider->first_name }}..."
                                data-message-input
                            >{{ old('body') }}</textarea>


                            <button
                                id="logisticsPlatformSend"
                                type="submit"
                                aria-label="Send message"
                                title="Send message"
                                data-message-submit
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
                                    <path d="M22 2 11 13"></path>
                                    <path d="m22 2-7 20-4-9-9-4Z"></path>
                                </svg>

                            </button>

                        </form>


                        <div class="logistics-composer-meta">

                            <span>
                                Ctrl + Enter to send
                            </span>

                            <span>
                                <b data-message-count>
                                    {{ mb_strlen(old('body', '')) }}
                                </b>
                                / 3000
                            </span>

                        </div>

                    </footer>

                @else

                    <div class="logistics-thread-empty">

                        <div class="logistics-thread-empty-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                            >
                                <path
                                    d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"
                                ></path>
                            </svg>

                        </div>


                        <strong>
                            Select a Rider
                        </strong>

                        <p>
                            Choose a Rider conversation to begin.
                        </p>

                    </div>

                @endif

            </main>


            {{-- =====================================================
                RIGHT — RIDER DETAILS
            ====================================================== --}}

            <aside id="logisticsDetailsPane">

                @if($selectedRider)

                    <div class="logistics-details-heading">

                        <strong>
                            Rider Details
                        </strong>

                        <p>
                            Logistics network profile
                        </p>

                    </div>


                    <div class="logistics-details-content">

                        <div class="logistics-details-profile">

                            <div class="logistics-details-avatar">
                                {{
                                    mb_strtoupper(
                                        mb_substr(
                                            $selectedRiderName,
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </div>


                            <strong>
                                {{ $selectedRiderName }}
                            </strong>


                            <p>
                                {{ $selectedRider->email }}
                            </p>

                        </div>


                        <div class="logistics-details-list">

                            <div>

                                <span>
                                    Availability
                                </span>

                                <strong>
                                    {{
                                        ucfirst(
                                            $selectedRider
                                                ->availability_status
                                            ?: 'Unknown'
                                        )
                                    }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Service Area
                                </span>

                                <strong>
                                    {{
                                        $selectedRider->barangay_name
                                        ?: 'Not assigned'
                                    }}
                                </strong>

                                <p>
                                    {{
                                        $selectedRider
                                            ->municipality_name
                                        ?: ''
                                    }}
                                </p>

                            </div>


                            <div>

                                <span>
                                    Vehicle
                                </span>

                                <strong>
                                    {{
                                        $selectedRider->vehicle_type
                                        ?: 'Not recorded'
                                    }}
                                </strong>

                                <p>
                                    {{
                                        $selectedRider->plate_number
                                        ?: ''
                                    }}
                                </p>

                            </div>


                            <div>

                                <span>
                                    Account
                                </span>

                                <strong>
                                    {{
                                        ucfirst(
                                            $selectedRider
                                                ->account_status
                                            ?: 'Active'
                                        )
                                    }}
                                </strong>

                            </div>

                        </div>

                    </div>

                @endif

            </aside>

        </div>

    </section>

</div>

@endsection


@push('scripts')

<script>
(() => {
    'use strict';


    const initializeLogisticsMessages = () => {

        const root =
            document.querySelector(
                '[data-logistics-messages]'
            );


        if (
            !root
            ||
            root.dataset.ready === '1'
        ) {
            return;
        }


        root.dataset.ready = '1';


        const normalize = (value) => {
            return String(value || '')
                .toLocaleLowerCase()
                .trim();
        };


        /*
        |--------------------------------------------------------------------------
        | Rider search
        |--------------------------------------------------------------------------
        */

        const search =
            root.querySelector(
                '[data-rider-search]'
            );


        const riders = [
            ...root.querySelectorAll(
                '[data-rider-item]'
            )
        ];


        const searchEmpty =
            root.querySelector(
                '[data-search-empty]'
            );


        const filterRiders = () => {

            const query =
                normalize(
                    search?.value
                );


            let visible =
                0;


            riders.forEach(
                (rider) => {

                    const show =
                        normalize(
                            rider.dataset.search
                        ).includes(query);


                    rider.hidden =
                        !show;


                    if (show) {
                        visible++;
                    }

                }
            );


            if (searchEmpty) {

                searchEmpty.hidden =
                    riders.length === 0
                    ||
                    visible > 0;
            }

        };


        search?.addEventListener(
            'input',
            filterRiders
        );


        /*
        |--------------------------------------------------------------------------
        | Thread scroll
        |--------------------------------------------------------------------------
        */

        const thread =
            root.querySelector(
                '[data-message-thread]'
            );


        if (thread) {

            requestAnimationFrame(
                () => {

                    thread.scrollTop =
                        thread.scrollHeight;

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Composer
        |--------------------------------------------------------------------------
        */

        const form =
            root.querySelector(
                '[data-message-form]'
            );


        const input =
            root.querySelector(
                '[data-message-input]'
            );


        const submit =
            root.querySelector(
                '[data-message-submit]'
            );


        const count =
            root.querySelector(
                '[data-message-count]'
            );


        const resizeInput = () => {

            if (!input) {
                return;
            }


            input.style.height =
                'auto';


            input.style.height =
                Math.min(
                    input.scrollHeight,
                    112
                )
                + 'px';


            if (count) {

                count.textContent =
                    input.value.length;
            }

        };


        input?.addEventListener(
            'input',
            resizeInput
        );


        input?.addEventListener(
            'keydown',
            (event) => {

                if (
                    event.key === 'Enter'
                    &&
                    event.ctrlKey
                ) {

                    event.preventDefault();

                    form?.requestSubmit();
                }

            }
        );


        form?.addEventListener(
            'submit',
            (event) => {

                if (
                    !String(
                        input?.value || ''
                    ).trim()
                ) {

                    event.preventDefault();

                    input?.focus();

                    return;
                }


                if (submit) {
                    submit.disabled = true;
                }

            }
        );


        resizeInput();
        filterRiders();

    };


    if (
        document.readyState
        === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initializeLogisticsMessages
        );

    } else {

        initializeLogisticsMessages();
    }

})();
</script>

@endpush