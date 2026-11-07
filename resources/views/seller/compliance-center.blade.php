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
/* SARI Seller Compliance Center — consolidated UI design.
   Seller Orders / Reports / Notifications visual parity, 2026-11-06.
   All styles are local to this page; no backend or chat behavior changes. */
.seller-compliance-enterprise {
    --ce-gold: #C88912;
    --ce-gold-dark: #A56D0A;
    --ce-ink: #2F3742;
    --ce-text: #536170;
    --ce-muted: #8793A1;
    --ce-line: #E5E7EB;
    --ce-soft-line: #ECF0F3;
    --ce-canvas: #F8FAFC;
    --ce-panel: #FFFFFF;
    --ce-success: #4F7D63;
    --ce-danger: #A65353;
    /* Exact Buyer Shopping Cart strong floating-container elevation. */
    --ce-shadow:
        0 2px 5px rgba(15, 23, 42, .045),
        0 10px 24px rgba(15, 23, 42, .075),
        0 24px 54px rgba(15, 23, 42, .095),
        inset 0 1px 0 rgba(255, 255, 255, .92);
    width: 100%;
    max-width: 1520px;
    margin-inline: auto;
    padding-bottom: 28px;
    font-family: "Poppins", ui-sans-serif, system-ui, sans-serif;
    font-weight: 400;
    color: var(--ce-ink);
}
.seller-compliance-enterprise *,
.seller-compliance-enterprise *::before,
.seller-compliance-enterprise *::after { box-sizing: border-box; }

/* Open page header — consistent with Seller Orders / Notifications. */
.ce-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px 22px;
    margin-bottom: 20px;
    padding: 2px 2px 15px;
    border-bottom: 1px solid var(--ce-line);
}
.ce-eyebrow {
    margin: 0;
    color: #A87316;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .14em;
    line-height: 1.4;
    text-transform: uppercase;
}
.ce-title {
    margin: 6px 0 0;
    color: var(--ce-ink);
    font-size: clamp(27px, 2.3vw, 31px);
    font-weight: 760;
    line-height: 1.08;
    letter-spacing: -.04em;
}
.ce-title__accent { color: var(--ce-gold); }
.ce-subtitle {
    max-width: 790px;
    margin: 7px 0 0;
    color: var(--ce-muted);
    font-size: 11.5px;
    line-height: 1.65;
}
.ce-header-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 34px;
    border: 1px solid var(--ce-line);
    border-radius: 999px;
    background: #FFF;
    padding: 5px 13px;
    color: var(--ce-text);
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.ce-header-status::before {
    content: "";
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
}
.ce-header-status--active { color: var(--ce-success); border-color: #D2E3D7; }
.ce-header-status--restricted { color: var(--ce-danger); border-color: #ECD1D1; }

/* Only top-level overview cards float; their inner content stays flat. */
.ce-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin: 0 0 20px;
}
.ce-summary__item {
    min-width: 0;
    min-height: 126px;
    padding: 18px 20px;
    border: 1px solid var(--ce-line);
    border-radius: 20px;
    background: var(--ce-panel);
    box-shadow: var(--ce-shadow);
}
.ce-summary__label {
    margin: 0;
    color: #637081;
    font-size: 12.5px;
    font-weight: 650;
    line-height: 1.45;
}
.ce-summary__value {
    margin: 8px 0 0;
    color: #1F2937;
    font-size: clamp(21px, 1.8vw, 28px);
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -.025em;
    overflow-wrap: anywhere;
}
.ce-summary__value--gold { color: var(--ce-gold-dark); }
.ce-summary__value--green { color: var(--ce-success); }
.ce-summary__meta {
    margin: 7px 0 0;
    color: var(--ce-muted);
    font-size: 11px;
    line-height: 1.5;
}
.ce-progress {
    width: 100%;
    height: 6px;
    overflow: hidden;
    margin-top: 13px;
    border-radius: 999px;
    background: #EEF1F4;
}
.ce-progress__bar {
    height: 100%;
    border-radius: inherit;
    background: var(--ce-gold);
}

/* Feedback and restriction messages are accessible, flat status notices. */
.ce-alert,
.ce-suspension {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    border: 1px solid;
    border-radius: 12px;
    padding: 12px 15px;
    font-size: 12px;
    font-weight: 550;
    line-height: 1.55;
}
.ce-alert { margin-bottom: 16px; }
.ce-alert--success { border-color: #D2E3D7; background: #F6FAF7; color: var(--ce-success); }
.ce-alert--error,
.ce-suspension { border-color: #ECD1D1; background: #FFF7F7; color: var(--ce-danger); }
.ce-alert__icon,
.ce-suspension svg {
    width: 16px;
    height: 16px;
    flex: 0 0 16px;
    margin-top: 1px;
}
.ce-suspension { margin: -4px 0 20px; }

/* Compliance history (left) and official communications (right). */
.ce-workspace {
    display: grid;
    grid-template-columns: minmax(0, .88fr) minmax(0, 1.12fr);
    gap: 16px;
    align-items: stretch;
}
.ce-panel {
    min-width: 0;
    overflow: hidden;
    border: 1px solid var(--ce-line);
    border-radius: 20px;
    background: var(--ce-panel);
    box-shadow: var(--ce-shadow);
}
.ce-panel__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px 20px;
    min-height: 92px;
    border-bottom: 1px solid var(--ce-soft-line);
    padding: 20px 22px;
    background: #FFF;
}
.ce-panel__header > div { min-width: 0; }
.ce-panel__heading {
    margin: 0;
    color: var(--ce-ink);
    font-size: 17px;
    font-weight: 750;
    line-height: 1.35;
    letter-spacing: -.02em;
}
.ce-panel__subheading {
    margin: 5px 0 0;
    color: var(--ce-muted);
    font-size: 11.5px;
    line-height: 1.55;
}
.ce-panel__count {
    display: inline-flex;
    flex: 0 0 auto;
    align-items: center;
    min-height: 24px;
    color: #8793A1;
    font-size: 11px;
    font-weight: 650;
    white-space: nowrap;
}

/* Warning history: no extra icon boxes or nested card shadows. */
.ce-warnings { min-height: 425px; background: #FFF; }
.ce-warning-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: start;
    gap: 10px 16px;
    padding: 18px 22px;
    border-bottom: 1px solid var(--ce-soft-line);
    background: #FFF;
    transition: background-color .14s ease;
}
.ce-warning-row:last-child { border-bottom: 0; }
.ce-warning-row:hover { background: #F8FAFC; }
.ce-warning-title {
    margin: 0;
    color: #344054;
    font-size: 12.5px;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: anywhere;
}
.ce-warning-meta {
    margin: 5px 0 0;
    color: var(--ce-muted);
    font-size: 11px;
    line-height: 1.5;
}
.ce-warning-date {
    color: #98A2B3;
    font-size: 10.5px;
    line-height: 1.5;
    white-space: nowrap;
}
.ce-empty,
.ce-chat-empty {
    display: grid;
    place-items: center;
    min-height: 375px;
    padding: 36px 24px;
    text-align: center;
}
.ce-empty__title,
.ce-chat-empty__title {
    margin: 0;
    color: #485464;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -.01em;
    line-height: 1.4;
}
.ce-empty__copy,
.ce-chat-empty__copy {
    max-width: 370px;
    margin: 7px auto 0;
    color: var(--ce-muted);
    font-size: 11.5px;
    line-height: 1.65;
}

/* Chat deliberately scrolls within the communication panel; the warnings list does not. */
.ce-chat-panel { display: flex; flex-direction: column; min-height: 520px; }
.ce-chat {
    flex: 1 1 auto;
    min-height: 350px;
    max-height: 580px;
    overflow-y: auto;
    overscroll-behavior: contain;
    background: var(--ce-canvas);
    padding: 22px;
    scrollbar-width: thin;
    scrollbar-color: #C9D1DA transparent;
}
.ce-chat::-webkit-scrollbar { width: 7px; }
.ce-chat::-webkit-scrollbar-track { background: transparent; }
.ce-chat::-webkit-scrollbar-thumb { border-radius: 999px; background: #C9D1DA; }
.ce-message-row { display: flex; margin-top: 16px; }
.ce-message-row:first-child { margin-top: 0; }
.ce-message-row--seller { justify-content: flex-end; }
.ce-message-row--admin { justify-content: flex-start; }
.ce-message { min-width: 0; max-width: min(80%, 650px); }
.ce-message__sender {
    margin: 0 0 6px;
    color: #8793A1;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .03em;
}
.ce-message-row--seller .ce-message__sender { text-align: right; }
.ce-message__bubble {
    border: 1px solid #E2E7ED;
    border-radius: 13px;
    background: #FFF;
    padding: 12px 15px;
    color: #475467;
    font-size: 12px;
    line-height: 1.65;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    word-break: normal;
}
.ce-message-row--seller .ce-message__bubble {
    border-color: #D6AD57;
    background: #FFF;
    color: #3F4854;
    border-bottom-right-radius: 5px;
}
.ce-message-row--admin .ce-message__bubble { border-bottom-left-radius: 5px; }
.ce-chat-empty { min-height: 320px; }

/* Full-width composer; no legacy 94px right inset. */
.ce-composer {
    position: relative;
    z-index: 1;
    flex: 0 0 auto;
    border-top: 1px solid var(--ce-soft-line);
    background: #FFF;
    padding: 16px 18px;
}
.ce-composer__form {
    display: flex;
    min-width: 0;
    align-items: center;
    gap: 10px;
    border: 1px solid #DCE2E8;
    border-radius: 14px;
    background: #FFF;
    padding: 6px;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.ce-composer__form:focus-within {
    border-color: var(--ce-gold);
    box-shadow: 0 0 0 3px rgba(200,137,18,.10);
}
.ce-composer__input {
    min-width: 0;
    min-height: 40px;
    max-height: 110px;
    flex: 1 1 auto;
    resize: none;
    border: 0;
    border-radius: 8px;
    outline: 0;
    background: transparent;
    padding: 10px 12px;
    color: #344054;
    font: inherit;
    font-size: 12px;
    line-height: 1.55;
}
.ce-composer__input::placeholder { color: #98A2B3; }
.ce-send {
    display: grid;
    width: 44px;
    height: 44px;
    min-width: 44px;
    place-items: center;
    border: 1px solid var(--ce-gold);
    border-radius: 11px;
    background: var(--ce-gold);
    color: #FFF;
    cursor: pointer;
    transition: background-color .15s ease, border-color .15s ease;
}
.ce-send:hover { border-color: var(--ce-gold-dark); background: var(--ce-gold-dark); }
.ce-send:focus-visible { outline: 2px solid var(--ce-gold); outline-offset: 3px; }
.ce-send svg {
    width: 19px;
    height: 19px;
    fill: none;
    stroke: #FFF;
    stroke-width: 1.9;
    stroke-linecap: round;
    stroke-linejoin: round;
}

@media (max-width: 1100px) {
    .ce-workspace { grid-template-columns: minmax(0, 1fr); }
    .ce-warnings { min-height: 300px; }
    .ce-chat-panel { min-height: 480px; }
}
/* Match the rounded settings panels + lighter mobile Buyer Cart elevation. */
@media (max-width: 767px) {
    .seller-compliance-enterprise {
        --ce-shadow:
            0 1px 3px rgba(15, 23, 42, .035),
            0 6px 16px rgba(15, 23, 42, .055),
            0 13px 28px rgba(15, 23, 42, .065),
            inset 0 1px 0 rgba(255, 255, 255, .90);
    }
    .seller-compliance-enterprise .ce-summary__item,
    .seller-compliance-enterprise .ce-panel {
        border-radius: 16px;
    }
}

@media (max-width: 700px) {
    .ce-summary { grid-template-columns: minmax(0, 1fr); gap: 12px; }
    .ce-summary__item { min-height: 105px; padding: 17px 18px; }
    .ce-header { align-items: flex-start; }
    .ce-title { font-size: 27px; }
    .ce-panel__header { min-height: 0; padding: 17px 18px; }
    .ce-warning-row { padding: 16px 18px; }
    .ce-chat { padding: 16px; }
    .ce-composer { padding: 12px; }
    .ce-message { max-width: 88%; }
}
@media (max-width: 420px) {
    .ce-panel__header { flex-wrap: wrap; gap: 7px; }
    .ce-warning-row { grid-template-columns: minmax(0, 1fr); gap: 4px; }
    .ce-warning-date { grid-column: 1; }
    .ce-header-status { min-height: 30px; }
}
@media print {
    .seller-compliance-enterprise { max-width: none; }
    .ce-summary__item, .ce-panel { box-shadow: none; break-inside: avoid; }
    .ce-workspace { grid-template-columns: minmax(0, 1fr); }
    .ce-warnings, .ce-chat { min-height: 0; max-height: none; overflow: visible; }
    .ce-composer { display: none; }
}
@media (prefers-reduced-motion: reduce) {
    .ce-composer__form, .ce-send, .ce-warning-row { transition: none; }
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

       
        </div>



        <span class="ce-header-status {{ $isSuspended ? 'ce-header-status--restricted' : 'ce-header-status--active' }}">{{ $accountStatus }}</span>

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

            <div class="ce-progress" role="progressbar" aria-label="Compliance warning progress" aria-valuemin="0" aria-valuemax="{{ $warningLimit }}" aria-valuenow="{{ min($warningLimit, max(0, $warningCount)) }}">

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
