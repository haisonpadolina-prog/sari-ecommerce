@extends('layouts.buyer')

@section('title', 'Notifications — SARI')
@section('page-title', 'Notifications')

@section('content')

@include('components.buyer.header')

<main
    class="mx-auto w-full max-w-[1400px] px-4 py-5 sm:px-6 lg:px-8"
    style="font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;"
>
    <style>
        /* ============================================================
           BUYER NOTIFICATIONS — CART / ORDERS VISUAL SYSTEM
        ============================================================ */

        .sari-notifications-header {
            padding: 2px 2px 14px;
        }

        .sari-notifications-eyebrow {
            color: #a87316;
            font-size: 10.5px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sari-notifications-title {
            margin-top: 5px;
            color: #2e343b;
            font-size: clamp(27px, 2.25vw, 31px);
            font-weight: 760;
            line-height: 1.04;
            letter-spacing: -.035em;
        }

        /* Plain text action, matching Cart / My Orders / Shipping. */
        .sari-notifications-read-all {
            position: relative;
            top: -5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 0;
            background: transparent;
            padding: 0;
            color: #59636e;
            font-size: 11.5px;
            font-weight: 750;
            line-height: 1.35;
            text-decoration: none;
            cursor: pointer;
            transition: color .14s ease;
        }

        .sari-notifications-read-all:hover,
        .sari-notifications-read-all:focus-visible {
            color: #c88912;
            outline: none;
        }

        .sari-notifications-read-all-arrow {
            display: inline-block;
            color: currentColor;
            font-size: 18px;
            font-weight: 800;
            line-height: .8;
            transform: translateY(-1px);
        }

        .sari-notifications-success {
            margin-top: 16px;
            border: 1px solid #d8e8de;
            border-radius: 11px;
            background: #f7fbf8;
            padding: 11px 14px;
            color: #4f7d61;
            font-size: 10.5px;
            font-weight: 600;
        }

        .sari-notifications-list {
            margin-top: 20px;
            overflow: hidden;
            border: 1px solid #e3e7eb;
            border-radius: 16px;
            background: #ffffff;
            box-shadow:
                0 10px 28px rgba(31,41,55,.045),
                0 2px 7px rgba(31,41,55,.018);
        }

        .sari-notification-row {
            border-bottom: 1px solid #edf0f3;
        }

        .sari-notification-row:last-child {
            border-bottom: 0;
        }

        .sari-notification-button {
            display: flex;
            width: 100%;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border: 0;
            background: #ffffff;
            text-align: left;
            cursor: pointer;
            transition: background-color .14s ease;
        }

        .sari-notification-button.is-unread {
            background: #fffdf8;
        }

        .sari-notification-button:hover,
        .sari-notification-button:focus-visible {
            background: #f8f9fa;
            outline: none;
        }

        /* Icons are simple and clean — no cream containers. */
        .sari-notification-icon {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            margin-top: 2px;
            place-items: center;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: #7b8490;
        }

        .sari-notification-button.is-unread .sari-notification-icon {
            color: #c88912;
        }

        .sari-notification-icon svg {
            width: 22px;
            height: 22px;
            stroke: currentColor;
        }

        .sari-notification-title {
            color: #252d36;
            font-size: 12.5px;
            font-weight: 750;
            line-height: 1.4;
        }

        .sari-notification-unread-dot {
            width: 7px;
            height: 7px;
            flex: 0 0 7px;
            border-radius: 999px;
            background: #c88912;
        }

        .sari-notification-message {
            display: block;
            margin-top: 4px;
            color: #67727e;
            font-size: 10.5px;
            font-weight: 500;
            line-height: 1.6;
        }

        .sari-notification-meta {
            display: block;
            margin-top: 8px;
            color: #9aa2ab;
            font-size: 9.5px;
            font-weight: 500;
            line-height: 1.4;
        }

        .sari-notification-arrow {
            margin-top: 8px;
            color: #9aa3ad;
            font-size: 17px;
            line-height: 1;
        }

        .sari-notification-button:hover .sari-notification-arrow {
            color: #c88912;
        }

        /* Empty state mirrors Cart / My Orders / Shipping. */
        .sari-notifications-empty {
            padding: 72px 24px;
            text-align: center;
        }

        .sari-notifications-empty-icon {
            display: grid;
            width: 92px;
            height: 92px;
            margin-inline: auto;
            place-items: center;
            color: #b4bbc3;
        }

        .sari-notifications-empty-icon svg {
            width: 58px;
            height: 58px;
            color: #b4bbc3;
            stroke: currentColor;
            stroke-width: 1.45;
        }

        .sari-notifications-empty-title {
            margin-top: 18px;
            color: #9ca4ad;
            font-size: 22px;
            font-weight: 750;
            line-height: 1.2;
            letter-spacing: -.025em;
        }

        .sari-notifications-empty-copy {
            margin-top: 7px;
            color: #b0b7bf;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.55;
        }

        @media (max-width: 640px) {
            .sari-notifications-eyebrow {
                font-size: 9.5px;
            }

            .sari-notifications-title {
                font-size: 29px;
            }

            .sari-notifications-read-all {
                top: 0;
                font-size: 10.5px;
            }

            .sari-notifications-read-all-arrow {
                font-size: 16px;
            }

            .sari-notification-button {
                gap: 11px;
                padding: 14px;
            }

            .sari-notification-icon {
                width: 30px;
                height: 30px;
                flex-basis: 30px;
            }

            .sari-notification-icon svg {
                width: 20px;
                height: 20px;
            }

            .sari-notification-title {
                font-size: 11.5px;
            }

            .sari-notification-message {
                font-size: 10px;
            }

            .sari-notification-meta {
                font-size: 9px;
            }

            .sari-notifications-empty {
                padding: 56px 20px;
            }

            .sari-notifications-empty-icon {
                width: 64px;
                height: 64px;
            }

            .sari-notifications-empty-icon svg {
                width: 40px;
                height: 40px;
            }

            .sari-notifications-empty-title {
                font-size: 20px;
            }

            .sari-notifications-empty-copy {
                font-size: 11px;
            }
        }
    </style>

    {{-- PAGE HEADER --}}
    <section class="sari-notifications-header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="sari-notifications-eyebrow">Buyer updates</p>
                <h1 class="sari-notifications-title">Notifications</h1>
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('buyer.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="sari-notifications-read-all">
                        Mark all as read
                        <span class="sari-notifications-read-all-arrow" aria-hidden="true">&gt;</span>
                    </button>
                </form>
            @endif
        </div>
    </section>

    @if (session('success'))
        <div class="sari-notifications-success">
            {{ session('success') }}
        </div>
    @endif

    <section class="sari-notifications-list">
        @forelse ($notifications as $notification)
            <form
                method="POST"
                action="{{ route('buyer.notifications.read', ['event' => $notification->id]) }}"
                class="sari-notification-row"
            >
                @csrf

                <button
                    type="submit"
                    class="sari-notification-button {{ $notification->is_read ? '' : 'is-unread' }}"
                >
                    <span class="sari-notification-icon" aria-hidden="true">
                        @if (in_array($notification->status, ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer']))
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 7h11v9H3z"></path>
                                <path d="M14 10h4l3 3v3h-7z"></path>
                                <circle cx="7" cy="18" r="1.5"></circle>
                                <circle cx="18" cy="18" r="1.5"></circle>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M5 7h14l-1 13H6L5 7Z"></path>
                                <path d="M9 7a3 3 0 0 1 6 0"></path>
                            </svg>
                        @endif
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="flex flex-wrap items-center gap-2">
                            <strong class="sari-notification-title">
                                {{ $notification->title }}
                            </strong>

                            @unless ($notification->is_read)
                                <span
                                    class="sari-notification-unread-dot"
                                    aria-label="Unread"
                                ></span>
                            @endunless
                        </span>

                        <span class="sari-notification-message">
                            {{ $notification->message }}
                        </span>

                        <span class="sari-notification-meta">
                            {{ $notification->seller?->store_name ?: 'SARI Seller' }}

                            @if ($notification->order)
                                · {{ $notification->order->order_number }}
                            @endif

                            · {{ $notification->created_at?->diffForHumans() }}
                        </span>
                    </span>

                    <span class="sari-notification-arrow" aria-hidden="true">→</span>
                </button>
            </form>
        @empty
            <div class="sari-notifications-empty">
                <span class="sari-notifications-empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                        <path d="M10 21h4"></path>
                    </svg>
                </span>

                <h2 class="sari-notifications-empty-title">
                    No notifications yet
                </h2>

                <p class="sari-notifications-empty-copy">
                    Order and shipping updates will appear here.
                </p>
            </div>
        @endforelse
    </section>

    @if ($notifications->hasPages())
        <div class="mt-5">{{ $notifications->links() }}</div>
    @endif
</main>

@endsection
