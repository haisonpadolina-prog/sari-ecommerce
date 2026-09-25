@extends('layouts.courier')

@section('title', 'Messages')
@section('header-title', 'Messages')
@section('header-subtitle', 'SARI Logistics')

@push('styles')
<style>
    .messages-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }

    .messages-pattern {
        background-color: #fcfaf6;
        background-image:
            radial-gradient(
                rgba(217, 147, 10, .055) 1px,
                transparent 1px
            );
        background-size: 22px 22px;
    }

    .messages-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .messages-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .messages-scrollbar::-webkit-scrollbar-thumb {
        background: #ded3c1;
        border-radius: 999px;
    }

    .messages-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cdbb9e;
    }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-[1350px]">

    {{-- =========================================================
         SUCCESS MESSAGE
    ========================================================== --}}
    @if (session('success'))
        <div
            class="
                mb-5 flex items-start gap-3
                rounded-2xl
                border border-emerald-200
                bg-emerald-50
                px-4 py-3.5
                text-emerald-700
            "
        >
            <div
                class="
                    mt-0.5 grid h-6 w-6
                    shrink-0 place-items-center
                    rounded-full
                    bg-emerald-600
                    text-white
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-3.5 w-3.5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m5 12 4 4L19 6"></path>
                </svg>
            </div>

            <p class="text-[9px] font-semibold leading-5">
                {{ session('success') }}
            </p>
        </div>
    @endif


    {{-- =========================================================
         PAGE INTRO
    ========================================================== --}}
    <section
        class="
            reveal
            flex flex-col justify-between gap-5
            border-b border-[#eee4d3]
            pb-5
            lg:flex-row lg:items-end
        "
    >
        <div class="max-w-2xl">

            <div class="flex flex-wrap items-center gap-2.5">

                <span
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        border border-[#eadfc9]
                        bg-[#fffaf1]
                        px-3 py-1.5
                        text-[8px] font-bold uppercase
                        tracking-[.11em]
                        text-[#a66d08]
                    "
                >
                    <span
                        class="
                            h-1.5 w-1.5
                            rounded-full
                            bg-[#d9930a]
                        "
                    ></span>

                    Rider Support
                </span>


                <span
                    class="
                        inline-flex items-center gap-2
                        rounded-full
                        border border-[#e4ded4]
                        bg-white
                        px-3 py-1.5
                        text-[8px] font-semibold
                        text-[#817769]
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="h-3 w-3"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            d="
                                M21 14
                                a4 4 0 0 1-4 4
                                H8
                                l-5 3
                                V7
                                a4 4 0 0 1 4-4
                                h10
                                a4 4 0 0 1 4 4
                                v7Z
                            "
                        ></path>
                    </svg>

                    {{ $messages->count() }}
                    {{ $messages->count() === 1 ? 'message' : 'messages' }}
                </span>

            </div>


            <h2
                class="
                    mt-3
                    text-[18px] font-bold
                    tracking-[-.025em]
                    text-[#211d17]
                    sm:text-[20px]
                "
            >
                SARI Logistics conversation
            </h2>


            <p
                class="
                    mt-1.5
                    max-w-xl
                    text-[9px] leading-5
                    text-[#817769]
                    sm:text-[10px]
                "
            >
                Communicate directly with the Logistics team regarding
                assignments, pickups, active deliveries, and rider support.
            </p>

        </div>


        <a
            href="{{ route('courier.dashboard') }}"
            class="
                inline-flex min-h-10
                items-center justify-center gap-2
                self-start
                rounded-xl
                border border-[#e6dccb]
                bg-white
                px-4 py-2.5
                text-[9px] font-semibold
                text-[#51483d]
                shadow-sm
                transition
                hover:border-[#d9be8c]
                hover:bg-[#fffaf1]
                hover:text-[#a66d08]
                lg:self-auto
            "
        >
            <svg
                viewBox="0 0 24 24"
                class="h-3.5 w-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M3 12h18"></path>
                <path d="M8 7l-5 5 5 5"></path>
            </svg>

            Back to Dashboard
        </a>

    </section>


    {{-- =========================================================
         CHAT CONTAINER
    ========================================================== --}}
    <section
        class="
            reveal messages-card
            mt-5 overflow-hidden
            rounded-[20px]
        "
    >

        {{-- =====================================================
             CONVERSATION HEADER
        ====================================================== --}}
        <div
            class="
                flex items-center justify-between gap-4
                border-b border-[#eee4d3]
                bg-[#fffdf9]
                px-4 py-4
                sm:px-5
            "
        >

            <div class="flex min-w-0 items-center gap-3">

                <div class="relative shrink-0">

                    <div
                        class="
                            grid h-11 w-11
                            place-items-center
                            rounded-xl
                            bg-[#fff2d8]
                            text-[#b77900]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 7h16v11H4z"></path>
                            <path d="m4 7 3-3h10l3 3"></path>
                            <path d="M8 11h8"></path>
                            <path d="M8 15h5"></path>
                        </svg>
                    </div>


                    <span
                        class="
                            absolute
                            -bottom-1 -right-1
                            h-3.5 w-3.5
                            rounded-full
                            border-[3px] border-[#fffdf9]
                            bg-[#4F7D63]
                        "
                    ></span>

                </div>


                <div class="min-w-0">

                    <div class="flex items-center gap-2">

                        <h3
                            class="
                                truncate
                                text-[12px] font-bold
                                text-[#211d17]
                            "
                        >
                            SARI Logistics
                        </h3>


                        <span
                            class="
                                hidden
                                rounded-full
                                border border-[#e5eee8]
                                bg-[#f3f8f5]
                                px-2 py-1
                                text-[6px] font-bold uppercase
                                tracking-[.08em]
                                text-[#4F7D63]
                                sm:inline-flex
                            "
                        >
                            Support
                        </span>

                    </div>


                    <p
                        class="
                            mt-1
                            flex items-center gap-1.5
                            text-[7px]
                            text-[#918677]
                        "
                    >
                        <span
                            class="
                                h-1.5 w-1.5
                                rounded-full
                                bg-[#4F7D63]
                            "
                        ></span>

                        Logistics dispatch and rider support
                    </p>

                </div>

            </div>


            <div
                class="
                    hidden
                    items-center gap-2
                    rounded-xl
                    bg-[#fbf7ef]
                    px-3 py-2
                    text-[7px]
                    text-[#817769]
                    sm:flex
                "
            >
                <svg
                    viewBox="0 0 24 24"
                    class="h-3 w-3 text-[#a66d08]"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M12 8v4"></path>
                    <path d="M12 16h.01"></path>
                    <circle cx="12" cy="12" r="9"></circle>
                </svg>

                Delivery-related messages only
            </div>

        </div>


        {{-- =====================================================
             MESSAGE AREA
        ====================================================== --}}
        <div
            id="riderMessageArea"
            class="
                messages-pattern
                messages-scrollbar
                min-h-[430px]
                max-h-[560px]
                overflow-y-auto
                px-4 py-5
                sm:px-6
            "
        >

            @forelse ($messages as $message)

                @php
                    $isRider =
                        $message->sender_role === 'rider';
                @endphp


                <div
                    class="
                        mb-4 flex
                        {{ $isRider
                            ? 'justify-end'
                            : 'justify-start'
                        }}
                    "
                >

                    <div
                        class="
                            flex max-w-[88%]
                            items-end gap-2
                            sm:max-w-[74%]

                            {{ $isRider
                                ? 'flex-row-reverse'
                                : ''
                            }}
                        "
                    >

                        {{-- =========================================
                             AVATAR
                        ========================================== --}}
                        <div
                            class="
                                hidden h-8 w-8
                                shrink-0
                                place-items-center
                                rounded-full
                                text-[8px] font-bold
                                sm:grid

                                {{ $isRider
                                    ? 'bg-[#d9930a] text-white'
                                    : 'border border-[#eadfc9] bg-[#fffdf9] text-[#b77900]'
                                }}
                            "
                        >
                            @if ($isRider)
                                {{ strtoupper(
                                    substr(
                                        $courier['name'] ?? 'R',
                                        0,
                                        1
                                    )
                                ) }}
                            @else
                                L
                            @endif
                        </div>


                        {{-- =========================================
                             MESSAGE BUBBLE
                        ========================================== --}}
                        <div class="min-w-0">

                            <div
                                class="
                                    px-4 py-3
                                    shadow-[0_4px_14px_rgba(75,59,30,.035)]

                                    {{ $isRider
                                        ? '
                                            rounded-[18px]
                                            rounded-br-[6px]
                                            bg-[#d9930a]
                                            text-white
                                        '
                                        : '
                                            rounded-[18px]
                                            rounded-bl-[6px]
                                            border border-[#eadfc9]
                                            bg-[#fffdf9]
                                            text-[#51493f]
                                        '
                                    }}
                                "
                            >

                                <p
                                    class="
                                        whitespace-pre-wrap
                                        break-words
                                        text-[9px]
                                        leading-[1.75]
                                    "
                                >
                                    {{ $message->body }}
                                </p>

                            </div>


                            <div
                                class="
                                    mt-1.5
                                    flex items-center gap-1.5

                                    {{ $isRider
                                        ? 'justify-end pr-1'
                                        : 'justify-start pl-1'
                                    }}
                                "
                            >

                                <span
                                    class="
                                        text-[6px]
                                        font-medium
                                        text-[#a09587]
                                    "
                                >
                                    {{ $message->created_at?->format('M d, h:i A') }}
                                </span>


                                @if ($isRider)

                                    <svg
                                        viewBox="0 0 24 24"
                                        class="
                                            h-3 w-3
                                            text-[#c29032]
                                        "
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="m4 12 3 3 5-6"></path>
                                        <path d="m10 12 3 3 7-8"></path>
                                    </svg>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


            @empty

                {{-- =================================================
                     EMPTY CONVERSATION
                ================================================== --}}
                <div
                    class="
                        flex min-h-[390px]
                        items-center justify-center
                    "
                >

                    <div
                        class="
                            mx-auto
                            max-w-[410px]
                            px-5
                            text-center
                        "
                    >

                        <div
                            class="
                                relative
                                mx-auto
                                grid h-16 w-16
                                place-items-center
                                rounded-[20px]
                                border border-[#eadfc9]
                                bg-[#fffdf9]
                                text-[#b47a11]
                                shadow-[0_10px_28px_rgba(75,59,30,.06)]
                            "
                        >

                            <svg
                                viewBox="0 0 24 24"
                                class="h-7 w-7"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                            >
                                <path
                                    d="
                                        M21 14
                                        a4 4 0 0 1-4 4
                                        H8
                                        l-5 3
                                        V7
                                        a4 4 0 0 1 4-4
                                        h10
                                        a4 4 0 0 1 4 4
                                        v7Z
                                    "
                                ></path>

                                <path d="M8 10h8"></path>
                                <path d="M8 14h5"></path>
                            </svg>


                            <span
                                class="
                                    absolute
                                    -right-1 -top-1
                                    h-4 w-4
                                    rounded-full
                                    border-[3px] border-[#fffdf9]
                                    bg-[#d9930a]
                                "
                            ></span>

                        </div>


                        <h3
                            class="
                                mt-4
                                text-[13px] font-bold
                                tracking-[-.02em]
                                text-[#413a31]
                            "
                        >
                            No messages yet
                        </h3>


                        <p
                            class="
                                mt-2
                                text-[8px] leading-5
                                text-[#918677]
                            "
                        >
                            Start a conversation with SARI Logistics if
                            you need assistance with an assignment,
                            pickup, or active delivery.
                        </p>


                        <div
                            class="
                                mt-5
                                inline-flex
                                items-center gap-2
                                rounded-xl
                                border border-[#eee4d3]
                                bg-[#fffdf9]
                                px-4 py-3
                            "
                        >
                            <span
                                class="
                                    h-2 w-2
                                    rounded-full
                                    bg-[#4F7D63]
                                "
                            ></span>

                            <p
                                class="
                                    text-[7px] font-semibold
                                    text-[#817769]
                                "
                            >
                                SARI Logistics support is available here
                            </p>
                        </div>

                    </div>

                </div>

            @endforelse

        </div>


        {{-- =====================================================
             MESSAGE COMPOSER
        ====================================================== --}}
        <form
            method="POST"
            action="{{ route('courier.messages.send') }}"
            class="
                border-t border-[#eee4d3]
                bg-[#fffdf9]
                p-4
                sm:p-5
            "
        >
            @csrf

            <div
                class="
                    rounded-2xl
                    border border-[#e6dccb]
                    bg-white
                    p-2
                    transition
                    focus-within:border-[#d9930a]
                    focus-within:ring-4
                    focus-within:ring-[#d9930a]/[.07]
                "
            >

                <textarea
                    id="riderMessageInput"
                    name="body"
                    required
                    rows="2"
                    maxlength="2000"
                    placeholder="Write a message to SARI Logistics..."
                    class="
                        block
                        min-h-[68px]
                        w-full
                        resize-none
                        border-0
                        bg-transparent
                        px-3 py-2
                        text-[9px]
                        leading-5
                        text-[#51483d]
                        outline-none
                        placeholder:text-[#aaa093]
                        focus:ring-0
                    "
                >{{ old('body') }}</textarea>


                <div
                    class="
                        flex flex-col gap-2
                        border-t border-[#f0e8db]
                        px-2 pt-2
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    "
                >

                    <div
                        class="
                            flex items-center gap-2
                            px-1
                            text-[7px]
                            text-[#9a9082]
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 10v6"></path>
                            <path d="M12 7h.01"></path>
                        </svg>

                        <span>
                            Keep messages related to rider operations.
                        </span>
                    </div>


                    <button
                        type="submit"
                        class="
                            inline-flex min-h-10
                            items-center justify-center gap-2
                            rounded-xl
                            bg-[#d9930a]
                            px-5 py-2.5
                            text-[9px] font-semibold
                            text-white
                            shadow-[0_8px_18px_rgba(217,147,10,.15)]
                            transition
                            hover:bg-[#c98505]
                            active:scale-[.98]
                        "
                    >
                        Send Message

                        <svg
                            viewBox="0 0 24 24"
                            class="h-3.5 w-3.5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                        >
                            <path d="m4 4 17 8-17 8 3-8-3-8Z"></path>
                            <path d="M7 12h14"></path>
                        </svg>
                    </button>

                </div>

            </div>

        </form>

    </section>

</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const messageArea =
        document.getElementById(
            'riderMessageArea'
        );

    const messageInput =
        document.getElementById(
            'riderMessageInput'
        );


    /*
    |--------------------------------------------------------------------------
    | Keep the latest messages visible
    |--------------------------------------------------------------------------
    */

    if (messageArea) {
        messageArea.scrollTop =
            messageArea.scrollHeight;
    }


    /*
    |--------------------------------------------------------------------------
    | Ctrl + Enter / Cmd + Enter to submit
    |--------------------------------------------------------------------------
    */

    messageInput?.addEventListener(
        'keydown',
        event => {

            if (
                event.key === 'Enter' &&
                (event.ctrlKey || event.metaKey)
            ) {
                event.preventDefault();

                messageInput
                    .closest('form')
                    ?.requestSubmit();
            }
        }
    );

});
</script>
@endpush