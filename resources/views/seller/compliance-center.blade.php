@extends('layouts.seller')

@section('title', 'Compliance Center — SARI Seller')
@section('page-title', 'Compliance Center')

@section('content')
@php
    $warningCount = (int) ($seller->warning_count ?? 0);
    $warningLimit = 3;
    $warningProgress = min(100, max(0, ($warningCount / max(1, $warningLimit)) * 100));
    $accountStatus = ucfirst((string) ($seller->account_status ?: 'active'));
    $isSuspended = !empty($seller->suspended_until);
@endphp

<style>
    .seller-compliance-enterprise {
        --ce-gold: #d59617;
        --ce-gold-dark: #9f6705;
        --ce-gold-soft: #fffaf0;
        --ce-ink: #202124;
        --ce-text: #475467;
        --ce-muted: #8a94a3;
        --ce-line: #e3e7ec;
        --ce-soft-line: #eef1f4;
        --ce-canvas: #f6f7f9;
        --ce-panel: #ffffff;
        --ce-success: #4f7d63;
        --ce-danger: #a65353;

        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
        color: var(--ce-ink);
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    }

    .seller-compliance-enterprise *,
    .seller-compliance-enterprise *::before,
    .seller-compliance-enterprise *::after {
        box-sizing: border-box;
    }

    .ce-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
        border: 1px solid;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 9px;
        font-weight: 600;
        line-height: 1.5;
    }

    .ce-alert--success {
        border-color: #cfe4d7;
        background: #f4faf6;
        color: #4f7d63;
    }

    .ce-alert--error {
        border-color: #efcece;
        background: #fff6f6;
        color: #a65353;
    }

    .ce-alert__icon {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        margin-top: 1px;
    }

    .ce-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--ce-line);
    }

    .ce-eyebrow {
        margin: 0;
        color: var(--ce-muted);
        font-size: 8px;
        font-weight: 800;
        letter-spacing: .15em;
        text-transform: uppercase;
    }

    .ce-title {
        margin: 5px 0 0;
        color: var(--ce-ink);
        font-size: clamp(28px, 2.2vw, 36px);
        font-weight: 700;
        letter-spacing: -.045em;
        line-height: 1.05;
    }

    .ce-title__accent {
        color: var(--ce-gold);
    }

    .ce-subtitle {
        max-width: 760px;
        margin: 7px 0 0;
        color: var(--ce-muted);
        font-size: 9.5px;
        line-height: 1.6;
    }

    .ce-header-status {
        display: inline-flex;
        height: 30px;
        align-items: center;
        gap: 7px;
        border: 1px solid {{ $isSuspended ? '#efcece' : '#d7e5dc' }};
        border-radius: 7px;
        background: {{ $isSuspended ? '#fff6f6' : '#f5faf7' }};
        padding: 0 10px;
        color: {{ $isSuspended ? '#a65353' : '#4f7d63' }};
        font-size: 8px;
        font-weight: 750;
        white-space: nowrap;
    }

    .ce-header-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
    }

    .ce-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-top: 14px;
        overflow: hidden;
        border: 1px solid var(--ce-line);
        border-radius: 9px;
        background: #fff;
    }

    .ce-summary__item {
        min-width: 0;
        min-height: 86px;
        padding: 14px 16px;
    }

    .ce-summary__item + .ce-summary__item {
        border-left: 1px solid var(--ce-soft-line);
    }

    .ce-summary__label {
        margin: 0;
        color: var(--ce-muted);
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .ce-summary__value {
        margin: 7px 0 0;
        color: #344054;
        font-size: 16px;
        font-weight: 750;
        letter-spacing: -.025em;
        line-height: 1.2;
    }

    .ce-summary__value--gold {
        color: var(--ce-gold-dark);
    }

    .ce-summary__value--green {
        color: var(--ce-success);
    }

    .ce-summary__meta {
        margin: 5px 0 0;
        color: #98a2b3;
        font-size: 7.6px;
        line-height: 1.45;
    }

    .ce-progress {
        width: 100%;
        height: 4px;
        margin-top: 9px;
        overflow: hidden;
        border-radius: 999px;
        background: #edf0f3;
    }

    .ce-progress__bar {
        height: 100%;
        border-radius: inherit;
        background: var(--ce-gold);
    }

    .ce-suspension {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 12px;
        border: 1px solid #efcece;
        border-radius: 8px;
        background: #fff8f8;
        padding: 9px 11px;
        color: #a65353;
        font-size: 8.2px;
        line-height: 1.55;
    }

    .ce-suspension svg {
        width: 13px;
        height: 13px;
        flex: 0 0 13px;
        margin-top: 1px;
    }

    .ce-workspace {
        display: grid;
        grid-template-columns: minmax(0, .92fr) minmax(0, 1.35fr);
        gap: 14px;
        margin-top: 14px;
        align-items: stretch;
    }

    .ce-panel {
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--ce-line);
        border-radius: 9px;
        background: #fff;
    }

    .ce-panel__header {
        display: flex;
        min-height: 54px;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid var(--ce-soft-line);
        background: #fff;
        padding: 11px 14px;
    }

    .ce-panel__heading {
        margin: 0;
        color: #344054;
        font-size: 10.5px;
        font-weight: 750;
        letter-spacing: -.01em;
    }

    .ce-panel__subheading {
        margin: 2px 0 0;
        color: #98a2b3;
        font-size: 7.8px;
        line-height: 1.5;
    }

    .ce-panel__count {
        display: inline-flex;
        min-height: 24px;
        align-items: center;
        border: 1px solid #e1e5ea;
        border-radius: 6px;
        background: #f8fafc;
        padding: 0 8px;
        color: #667085;
        font-size: 7.5px;
        font-weight: 750;
        white-space: nowrap;
    }

    .ce-warnings {
        min-height: 500px;
        background: #fff;
    }

    .ce-warning-row {
        display: grid;
        grid-template-columns: 32px minmax(0, 1fr) auto;
        gap: 10px;
        align-items: start;
        border-bottom: 1px solid var(--ce-soft-line);
        padding: 12px 14px;
    }

    .ce-warning-row:last-child {
        border-bottom: 0;
    }

    .ce-warning-icon {
        display: grid;
        width: 32px;
        height: 32px;
        place-items: center;
        border: 1px solid #ead7a8;
        border-radius: 7px;
        background: var(--ce-gold-soft);
        color: var(--ce-gold-dark);
    }

    .ce-warning-icon svg {
        width: 14px;
        height: 14px;
    }

    .ce-warning-title {
        margin: 0;
        color: #4b5563;
        font-size: 8.8px;
        font-weight: 750;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .ce-warning-meta {
        margin: 3px 0 0;
        color: #98a2b3;
        font-size: 7.5px;
        line-height: 1.45;
    }

    .ce-warning-date {
        color: #98a2b3;
        font-size: 7px;
        white-space: nowrap;
    }

    .ce-empty {
        display: grid;
        min-height: 500px;
        place-items: center;
        padding: 30px 20px;
        text-align: center;
    }

    .ce-empty__icon {
        display: grid;
        width: 42px;
        height: 42px;
        margin: 0 auto;
        place-items: center;
        border: 1px solid #dce7df;
        border-radius: 9px;
        background: #f6faf7;
        color: #5f8a6d;
    }

    .ce-empty__icon svg {
        width: 18px;
        height: 18px;
    }

    .ce-empty__title {
        margin: 9px 0 0;
        color: #475467;
        font-size: 9.5px;
        font-weight: 750;
    }

    .ce-empty__copy {
        margin: 4px 0 0;
        color: #98a2b3;
        font-size: 8px;
    }

    .ce-chat-panel {
        display: flex;
        min-height: 500px;
        flex-direction: column;
    }

    .ce-chat {
        min-height: 0;
        flex: 1 1 auto;
        overflow-y: auto;
        overscroll-behavior: contain;
        background: var(--ce-canvas);
        padding: 14px;
        scrollbar-width: thin;
        scrollbar-color: #cfd6df transparent;
    }

    .ce-chat::-webkit-scrollbar {
        width: 6px;
    }

    .ce-chat::-webkit-scrollbar-track {
        background: transparent;
    }

    .ce-chat::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #cfd6df;
    }

    .ce-message-row {
        display: flex;
        margin-top: 11px;
    }

    .ce-message-row:first-child {
        margin-top: 0;
    }

    .ce-message-row--seller {
        justify-content: flex-end;
    }

    .ce-message-row--admin {
        justify-content: flex-start;
    }

    .ce-message {
        max-width: min(72%, 700px);
    }

    .ce-message__sender {
        margin-bottom: 4px;
        color: #98a2b3;
        font-size: 7.2px;
        font-weight: 750;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .ce-message-row--seller .ce-message__sender {
        text-align: right;
    }

    .ce-message__bubble {
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 8.7px;
        line-height: 1.6;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .ce-message-row--seller .ce-message__bubble {
        border: 1px solid #d59617;
        background: #fff;
        color: #6f4b08;
    }

    .ce-message-row--admin .ce-message__bubble {
        border: 1px solid #dde2e7;
        background: #fff;
        color: #475467;
    }

    .ce-chat-empty {
        display: grid;
        min-height: 100%;
        place-items: center;
        text-align: center;
    }

    .ce-chat-empty__icon {
        display: grid;
        width: 44px;
        height: 44px;
        margin: 0 auto;
        place-items: center;
        border: 1px solid #e1e5ea;
        border-radius: 9px;
        background: #fff;
        color: #98a2b3;
    }

    .ce-chat-empty__icon svg {
        width: 18px;
        height: 18px;
    }

    .ce-chat-empty__title {
        margin: 9px 0 0;
        color: #475467;
        font-size: 9.5px;
        font-weight: 750;
    }

    .ce-chat-empty__copy {
        margin: 4px 0 0;
        color: #98a2b3;
        font-size: 8px;
        line-height: 1.55;
    }

    .ce-composer {
        position: relative;
        z-index: 4;
        flex: 0 0 auto;
        border-top: 1px solid var(--ce-line);
        background: #fff;
        padding: 10px 94px 10px 12px;
    }

    .ce-composer__form {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        border: 1px solid #d7dce3;
        border-radius: 9px;
        background: #fff;
        padding: 6px;
        box-shadow: 0 2px 8px rgba(15,23,42,.025);
        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    .ce-composer__form:focus-within {
        border-color: var(--ce-gold);
        box-shadow: 0 0 0 3px rgba(213,150,23,.07);
    }

    .ce-composer__input {
        min-width: 0;
        min-height: 38px;
        max-height: 110px;
        flex: 1 1 auto;
        resize: none;
        border: 0;
        background: transparent;
        padding: 8px 7px;
        color: #344054;
        font: inherit;
        font-size: 8.7px;
        line-height: 1.55;
        outline: 0;
    }

    .ce-composer__input::placeholder {
        color: #a0a8b4;
    }

    .ce-send {
        display: grid;
        width: 42px;
        height: 42px;
        min-width: 42px;
        place-items: center;
        border: 1px solid #c9870c;
        border-radius: 9px;
        background: var(--ce-gold);
        color: #fff;
        box-shadow: 0 5px 14px rgba(159,103,5,.18);
        cursor: pointer;
        transition:
            background .15s ease,
            border-color .15s ease,
            box-shadow .15s ease,
            transform .15s ease;
    }

    .ce-send:hover {
        border-color: #a96f06;
        background: #c9870c;
        box-shadow: 0 7px 18px rgba(159,103,5,.23);
        transform: translateY(-1px);
    }

    .ce-send:active {
        box-shadow: 0 3px 9px rgba(159,103,5,.16);
        transform: translateY(0);
    }

    .ce-send svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: #fff;
        stroke-width: 1.9;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ce-send:focus-visible {
        outline: 2px solid rgba(213,150,23,.34);
        outline-offset: 3px;
    }

    @media (max-width: 980px) {
        .ce-workspace {
            grid-template-columns: 1fr;
        }

        .ce-warnings,
        .ce-chat-panel {
            min-height: 430px;
        }
    }

    @media (max-width: 640px) {
        .ce-composer {
            padding: 8px 72px 8px 8px;
        }

        .ce-send {
            width: 40px;
            height: 40px;
            min-width: 40px;
        }

        .ce-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .ce-summary {
            grid-template-columns: 1fr;
        }

        .ce-summary__item + .ce-summary__item {
            border-left: 0;
            border-top: 1px solid var(--ce-soft-line);
        }

        .ce-warning-row {
            grid-template-columns: 32px minmax(0, 1fr);
        }

        .ce-warning-date {
            grid-column: 2;
        }

        .ce-message {
            max-width: 86%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ce-composer__form,
        .ce-send {
            transition: none !important;
        }
    }
</style>

<div class="seller-compliance-enterprise">
    @if(session('success'))
        <div class="ce-alert ce-alert--success" role="status">
            <svg class="ce-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="m7.5 12 3 3 6-6"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="ce-alert ce-alert--error" role="alert">
            <svg class="ce-alert__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v6"></path>
                <path d="M12 17h.01"></path>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <header class="ce-header">
        <div>
            <p class="ce-eyebrow">Seller Governance</p>
            <h1 class="ce-title">Compliance <span class="ce-title__accent">Center</span></h1>
            <p class="ce-subtitle">
                Review your account standing, compliance records, restrictions, and official communication with SARI Administration.
            </p>
        </div>

        <span class="ce-header-status">{{ $accountStatus }}</span>
    </header>

    <section class="ce-summary" aria-label="Compliance overview">
        <div class="ce-summary__item">
            <p class="ce-summary__label">Account standing</p>
            <p class="ce-summary__value {{ $isSuspended ? '' : 'ce-summary__value--green' }}">
                {{ $accountStatus }}
            </p>
            <p class="ce-summary__meta">
                {{ $isSuspended ? 'Your Seller account currently has an active restriction.' : 'Your Seller account is currently operational.' }}
            </p>
        </div>

        <div class="ce-summary__item">
            <p class="ce-summary__label">Compliance warnings</p>
            <p class="ce-summary__value ce-summary__value--gold">{{ $warningCount }} / {{ $warningLimit }}</p>
            <div class="ce-progress" aria-label="Compliance warning progress">
                <div class="ce-progress__bar" style="width: {{ $warningProgress }}%"></div>
            </div>
        </div>

        <div class="ce-summary__item">
            <p class="ce-summary__label">Restriction status</p>
            <p class="ce-summary__value">
                {{ $seller->suspended_until ? 'Restricted' : 'No active restriction' }}
            </p>
            <p class="ce-summary__meta">
                @if($seller->suspended_until)
                    Until {{ $seller->suspended_until->format('M d, Y h:i A') }}
                @else
                    No suspension period is currently recorded.
                @endif
            </p>
        </div>
    </section>

    @if($seller->suspended_until)
        <div class="ce-suspension">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 7v6"></path>
                <path d="M12 17h.01"></path>
            </svg>
            <span>
                This Seller account is suspended until {{ $seller->suspended_until->format('M d, Y h:i A') }}.
            </span>
        </div>
    @endif

    <div class="ce-workspace">
        <section class="ce-panel">
            <div class="ce-panel__header">
                <div>
                    <h2 class="ce-panel__heading">Compliance History</h2>
                    <p class="ce-panel__subheading">Warnings and governance records associated with this Seller account.</p>
                </div>

                <span class="ce-panel__count">{{ $warningCount }} warning{{ $warningCount === 1 ? '' : 's' }}</span>
            </div>

            <div class="ce-warnings">
                @forelse($warnings as $warning)
                    <article class="ce-warning-row">
                        <div class="ce-warning-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 4 3 20h18L12 4Z"></path>
                                <path d="M12 9v4"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="ce-warning-title">{{ $warning->reason ?? 'Compliance Warning' }}</p>
                            <p class="ce-warning-meta">Recorded by SARI Administration</p>
                        </div>

                        <time class="ce-warning-date">
                            {{ $warning->created_at?->format('M d, Y') }}
                        </time>
                    </article>
                @empty
                    <div class="ce-empty">
                        <div>
                            <div class="ce-empty__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m5 12 4 4L19 6"></path>
                                </svg>
                            </div>
                            <p class="ce-empty__title">No compliance warnings</p>
                            <p class="ce-empty__copy">No warning records are currently associated with this Seller account.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="ce-panel ce-chat-panel">
            <div class="ce-panel__header">
                <div>
                    <h2 class="ce-panel__heading">Administration Communication</h2>
                    <p class="ce-panel__subheading">Official compliance conversation with SARI Administration.</p>
                </div>

                <span class="ce-panel__count">Official channel</span>
            </div>

            <div class="ce-chat" id="sellerComplianceMessages">
                @forelse($messages as $message)
                    <div class="ce-message-row {{ $message->sender_role === 'seller' ? 'ce-message-row--seller' : 'ce-message-row--admin' }}">
                        <div class="ce-message">
                            <div class="ce-message__sender">
                                {{ $message->sender_role === 'seller' ? 'You' : 'SARI Admin' }}
                            </div>

                            <div class="ce-message__bubble">
                                {{ $message->message }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="ce-chat-empty">
                        <div>
                            <div class="ce-chat-empty__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                                </svg>
                            </div>
                            <p class="ce-chat-empty__title">No compliance messages</p>
                            <p class="ce-chat-empty__copy">
                                Use the message field below to contact SARI Administration about a compliance matter.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="ce-composer">
                <form method="POST" action="{{ route('seller.compliance.message') }}" class="ce-composer__form">
                    @csrf

                    <textarea
                        id="sellerComplianceInput"
                        name="message"
                        required
                        maxlength="3000"
                        rows="1"
                        class="ce-composer__input"
                        placeholder="Message SARI Administration..."
                        aria-label="Message SARI Administration"
                    ></textarea>

                    <button type="submit" class="ce-send" aria-label="Send compliance message" title="Send message">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="m4 4 17 8-17 8 3-8-3-8Z"></path>
                            <path d="M7 12h14"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function bootSellerComplianceCenter() {
        const input = document.getElementById('sellerComplianceInput');
        const messages = document.getElementById('sellerComplianceMessages');

        if (!input || input.dataset.initialized === '1') return;
        input.dataset.initialized = '1';

        const resize = () => {
            input.style.height = 'auto';
            input.style.height = `${Math.min(110, Math.max(38, input.scrollHeight))}px`;
        };

        input.addEventListener('input', resize);
        resize();

        requestAnimationFrame(() => {
            if (messages) {
                messages.scrollTop = messages.scrollHeight;
            }
        });

        document.addEventListener('livewire:navigating', () => {
            input.dataset.initialized = '0';
        }, { once: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootSellerComplianceCenter, { once: true });
    } else {
        bootSellerComplianceCenter();
    }
})();
</script>
@endpush
