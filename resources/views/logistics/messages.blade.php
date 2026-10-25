@extends('layouts.logistics')

@section('title', 'Messages | SARI Logistics')
@section('page-title', 'Rider Messages')

@push('styles')
    <style>
/* =========================================================
   MERGED FROM public/css/logistics/messages.css
   Kept page-scoped/shared exactly as the original stylesheet.
========================================================= */
/*
|--------------------------------------------------------------------------
| SARI LOGISTICS — RIDER MESSAGING
|--------------------------------------------------------------------------
|
| Seller Messaging-aligned workspace.
|
| IMPORTANT:
| The Logistics layout uses:
|
| base : px-4 py-4 = 16px / 16px
| sm   : px-5 py-5 = 20px / 20px
| lg   : px-6       = 24px horizontal
| xl   : px-7       = 28px horizontal
| 2xl  : px-8       = 32px horizontal
|
| This file cancels those exact values only for the Messages workspace.
|
*/


/* ==========================================================================
   PAGE TOKENS
   ========================================================================== */

.logistics-messages-page {
    --chat-gold: #d59617;
    --chat-gold-hover: #c9890f;
    --chat-gold-soft: #fffaf0;

    --chat-ink: #111827;
    --chat-text: #344054;
    --chat-muted: #7c8592;
    --chat-soft-muted: #98a2b3;

    --chat-line: #e5e7eb;
    --chat-divider: #eceff2;

    --chat-canvas: #f7f8fa;
    --chat-panel: #ffffff;

    /*
    |--------------------------------------------------------------------------
    | Base Logistics main padding
    |--------------------------------------------------------------------------
    */

    width: auto;
    max-width: none;

    margin:
        -16px
        -16px;

    padding: 0;

    overflow: hidden;

    background: var(--chat-canvas);
    color: var(--chat-ink);

    font-family:
        "Poppins",
        ui-sans-serif,
        system-ui,
        sans-serif;
}


.logistics-messages-page *,
.logistics-messages-page *::before,
.logistics-messages-page *::after {
    box-sizing: border-box;
}


.logistics-messages-page [hidden] {
    display: none !important;
}


.logistics-messages-page button,
.logistics-messages-page input,
.logistics-messages-page textarea {
    font: inherit;
}


/* ==========================================================================
   EXACT LOGISTICS CONTENT-PADDING CANCELLATION
   ========================================================================== */

@media (min-width: 640px) {

    .logistics-messages-page {
        /*
         * sm:px-5 sm:py-5
         * 20px on all sides
         */
        margin:
            -20px
            -20px;
    }

}


@media (min-width: 1024px) {

    .logistics-messages-page {
        /*
         * Vertical remains sm:py-5 = 20px.
         * Horizontal becomes lg:px-6 = 24px.
         */
        margin-top: -20px;
        margin-bottom: -20px;

        margin-left: -24px;
        margin-right: -24px;
    }

}


@media (min-width: 1280px) {

    .logistics-messages-page {
        /*
         * xl:px-7 = 28px
         */
        margin-left: -28px;
        margin-right: -28px;
    }

}


@media (min-width: 1536px) {

    .logistics-messages-page {
        /*
         * 2xl:px-8 = 32px
         */
        margin-left: -32px;
        margin-right: -32px;
    }

}


/* ==========================================================================
   CHAT WORKSPACE
   ========================================================================== */

#logisticsPlatformChat {
    width: 100%;

    /*
     * Logistics header = 72px.
     * Page begins immediately below the header after cancelling
     * the main top padding.
     */
    height: calc(100dvh - 72px);

    min-height: 0;
    max-height: none;

    overflow: hidden;

    border: 0;
    border-top: 1px solid var(--chat-line);

    background: var(--chat-canvas);

    box-shadow: none;
}


.logistics-chat-grid {
    display: grid;

    width: 100%;
    height: 100%;

    min-width: 0;
    min-height: 0;

    background: var(--chat-canvas);
}


/*
|--------------------------------------------------------------------------
| Wide desktop
|--------------------------------------------------------------------------
|
| Matches Seller Messages:
| conversation list | main chat | details
|
*/

@media (min-width: 1536px) {

    .logistics-chat-grid {
        grid-template-columns:
            258px
            minmax(0, 1fr)
            244px;
    }

}


/*
|--------------------------------------------------------------------------
| Standard desktop / laptop
|--------------------------------------------------------------------------
|
| Same Seller behavior:
| conversation list | main chat
|
*/

@media (min-width: 1024px) and (max-width: 1535px) {

    .logistics-chat-grid {
        grid-template-columns:
            252px
            minmax(0, 1fr);
    }


    #logisticsDetailsPane {
        display: none;
    }

}


/* ==========================================================================
   FLASH ALERT
   ========================================================================== */

.logistics-chat-alert {
    position: fixed;

    z-index: 80;

    top: 84px;
    right: 18px;

    max-width: 360px;

    border: 1px solid #dce7df;
    border-radius: 10px;

    background: #f4f9f6;

    padding: 9px 12px;

    color: #4f7d63;

    font-size: 9px;
    line-height: 1.45;
    font-weight: 600;

    box-shadow:
        0 4px 10px rgba(15, 23, 42, .045),
        0 14px 30px rgba(15, 23, 42, .09);
}


.logistics-chat-alert.is-error {
    border-color: #efcece;

    background: #fff5f5;

    color: #a65353;
}


/* ==========================================================================
   LEFT CONVERSATION PANE
   ========================================================================== */

#logisticsConversationPane {
    position: relative;

    z-index: 1;

    display: flex;
    flex-direction: column;

    min-width: 0;
    min-height: 0;

    overflow: hidden;

    border-right: 1px solid var(--chat-divider);

    background: #ffffff;

    box-shadow:
        0 1px 3px rgba(15, 23, 42, .025),
        0 7px 18px rgba(15, 23, 42, .045);
}


/* ==========================================================================
   CONVERSATION HEADING
   ========================================================================== */

.logistics-conversation-heading {
    flex: 0 0 auto;

    padding:
        14px
        14px
        12px;

    border-bottom: 1px solid #edf0f3;

    background: #ffffff;
}


.logistics-conversation-heading h3 {
    margin: 0;

    color: #1f2937;

    font-size: 13px;
    line-height: 1.35;
    font-weight: 700;

    letter-spacing: -.015em;
}


.logistics-conversation-heading > p {
    margin: 2px 0 0;

    color: #98a2b3;

    font-size: 8.5px;
    line-height: 1.4;
}


/* ==========================================================================
   RIDER SEARCH
   ========================================================================== */

.logistics-search-wrap {
    position: relative;

    margin-top: 11px;
}


.logistics-search-wrap svg {
    position: absolute;

    z-index: 1;

    width: 14px;
    height: 14px;

    left: 12px;
    top: 50%;

    transform: translateY(-50%);

    color: #98a2b3;

    pointer-events: none;
}


#logisticsMessageSearch {
    width: 100%;
    height: 40px;

    border: 1px solid #d8dee6;
    border-radius: 9px;

    background: #f8fafc;

    padding:
        0
        11px
        0
        36px;

    color: #344054;

    font-size: 9.5px;

    outline: none;

    box-shadow: none;

    transition:
        border-color .15s ease,
        background-color .15s ease,
        box-shadow .15s ease;
}


#logisticsMessageSearch::placeholder {
    color: #98a2b3;
}


#logisticsMessageSearch:focus {
    border-color: var(--chat-gold);

    background: #ffffff;

    box-shadow:
        0 0 0 3px
        rgba(213, 150, 23, .08);
}


/* ==========================================================================
   CONVERSATION LIST
   ========================================================================== */

.logistics-conversation-list {
    display: grid;
    align-content: start;

    flex: 1;

    min-width: 0;
    min-height: 0;

    overflow-y: auto;
    overflow-x: hidden;

    overscroll-behavior: contain;

    background: #ffffff;

    scrollbar-width: thin;
    scrollbar-color:
        #cfd6df
        transparent;
}


.logistics-conversation-item {
    display: flex;

    min-width: 0;

    gap: 9px;

    border: 0;
    border-bottom: 1px solid #edf0f3;

    background: #ffffff;

    padding:
        12px
        14px;

    color: inherit;

    text-decoration: none;

    box-shadow: none;

    transform: none;

    transition:
        background-color .14s ease;
}


.logistics-conversation-item:hover {
    background: #f8fafc;
}


.logistics-conversation-item.is-active {
    background: #fbfcfd;

    box-shadow:
        inset 3px 0 0
        var(--chat-gold);
}


/* ==========================================================================
   RIDER AVATAR
   ========================================================================== */

.logistics-rider-avatar-wrap {
    position: relative;

    width: 36px;
    height: 36px;

    flex: 0 0 36px;
}


.logistics-rider-avatar,
.logistics-thread-avatar {
    display: grid;

    width: 36px;
    height: 36px;

    place-items: center;

    border: 1px solid #e1e6eb;
    border-radius: 50%;

    background: #f3f6f8;

    color: #52697a;

    font-size: 9px;
    font-weight: 800;

    letter-spacing: -.02em;
}


.logistics-online-dot {
    position: absolute;

    width: 9px;
    height: 9px;

    right: -1px;
    bottom: -1px;

    border: 2px solid #ffffff;
    border-radius: 999px;

    background: #aeb6c1;
}


.logistics-online-dot.is-online {
    background: #49a36f;
}


/* ==========================================================================
   CONVERSATION COPY
   ========================================================================== */

.logistics-conversation-copy {
    min-width: 0;

    flex: 1;
}


.logistics-conversation-name {
    display: flex;
    align-items: center;
    justify-content: space-between;

    min-width: 0;

    gap: 7px;
}


.logistics-conversation-name strong {
    display: block;

    min-width: 0;

    overflow: hidden;

    color: #344054;

    font-size: 9.8px;
    line-height: 1.4;
    font-weight: 700;

    text-overflow: ellipsis;
    white-space: nowrap;
}


.logistics-conversation-copy > p {
    margin: 2px 0 0;

    overflow: hidden;

    color: #98a2b3;

    font-size: 7.8px;
    line-height: 1.45;

    text-overflow: ellipsis;
    white-space: nowrap;
}


.logistics-conversation-copy > span {
    display: block;

    margin-top: 5px;

    overflow: hidden;

    color: #667085;

    font-size: 8.2px;
    line-height: 1.4;

    text-overflow: ellipsis;
    white-space: nowrap;
}


/* ==========================================================================
   UNREAD BADGE
   ========================================================================== */

.logistics-conversation-unread {
    display: grid;

    min-width: 18px;
    height: 18px;

    flex: 0 0 auto;

    place-items: center;

    border-radius: 999px;

    background: var(--chat-gold);

    padding:
        0
        5px;

    color: #ffffff;

    font-size: 7px;
    font-weight: 800;
}


/* ==========================================================================
   EMPTY CONVERSATION LIST
   ========================================================================== */

.logistics-conversation-empty {
    padding:
        30px
        16px;

    text-align: center;
}


.logistics-conversation-empty strong {
    display: block;

    color: #475467;

    font-size: 9px;
}


.logistics-conversation-empty p {
    margin: 4px 0 0;

    color: #98a2b3;

    font-size: 8px;
    line-height: 1.5;
}


/* ==========================================================================
   CENTER CONVERSATION PANE
   ========================================================================== */

#logisticsCenterPane {
    position: relative;

    z-index: 3;

    display: flex;
    flex-direction: column;

    min-width: 0;
    min-height: 0;

    overflow: hidden;

    border-left: 1px solid var(--chat-divider);
    border-right: 1px solid var(--chat-divider);

    background: #ffffff;

    box-shadow:
        0 3px 7px rgba(15, 23, 42, .045),
        0 14px 32px rgba(15, 23, 42, .075),
        0 30px 68px rgba(15, 23, 42, .07);

    transform: none;
}


/* ==========================================================================
   THREAD HEADER
   ========================================================================== */

.logistics-thread-header {
    position: relative;

    z-index: 2;

    display: flex;
    align-items: center;
    justify-content: space-between;

    flex: 0 0 auto;

    min-height: 62px;

    gap: 12px;

    border-bottom: 1px solid var(--chat-line);

    background: #ffffff;

    padding:
        9px
        14px;

    box-shadow: none;
}


.logistics-thread-person {
    display: flex;
    align-items: center;

    min-width: 0;

    gap: 9px;
}


.logistics-thread-person > div {
    min-width: 0;
}


.logistics-thread-person strong {
    display: block;

    min-width: 0;

    overflow: hidden;

    color: #1f2937;

    font-size: 11px;
    line-height: 1.35;
    font-weight: 700;

    letter-spacing: -.015em;

    text-overflow: ellipsis;
    white-space: nowrap;
}


.logistics-thread-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 7px;

    margin-top: 2px;

    color: #98a2b3;

    font-size: 8px;
    line-height: 1.4;
}


.logistics-thread-avatar {
    flex: 0 0 36px;

    border-radius: 11px;

    background: #fffaf0;

    color: #a96f06;

    box-shadow:
        inset 0 0 0 1px
        #efd9a7;
}


/* ==========================================================================
   RIDER STATUS
   ========================================================================== */

.logistics-platform-status {
    display: inline-flex;
    align-items: center;

    height: 26px;

    gap: 5px;

    border: 1px solid #e4e8ec;
    border-radius: 999px;

    background: #f8fafc;

    padding:
        0
        8px;

    color: #667085;

    font-size: 7.8px;
    font-weight: 700;
}


.logistics-platform-status::before {
    content: "";

    width: 5px;
    height: 5px;

    border-radius: 999px;

    background: currentColor;
}


.logistics-platform-status.is-online {
    border-color: #dce7df;

    background: #f4f9f6;

    color: #4f7d63;
}


.logistics-platform-status.is-offline {
    border-color: #e4e8ec;

    background: #f8fafc;

    color: #98a2b3;
}


.logistics-thread-badge {
    display: inline-flex;
    align-items: center;

    height: 25px;

    flex: 0 0 auto;

    border: 1px solid #e1e6eb;
    border-radius: 999px;

    background: #f8fafc;

    padding:
        0
        9px;

    color: #667085;

    font-size: 7.8px;
    font-weight: 700;
}


/* ==========================================================================
   MESSAGE CANVAS
   ========================================================================== */

#logisticsPlatformMessages {
    flex: 1;

    min-width: 0;
    min-height: 0;

    overflow-y: auto;
    overflow-x: hidden;

    overscroll-behavior: contain;

    background: #f8fafc;

    padding:
        18px
        18px
        22px;

    scrollbar-width: thin;
    scrollbar-color:
        #cfd6df
        transparent;

    box-shadow:
        inset 0 1px 0
        rgba(255, 255, 255, .9),

        inset 0 -1px 0
        rgba(226, 232, 240, .72);
}


/* ==========================================================================
   CUSTOM SCROLLBARS
   ========================================================================== */

#logisticsPlatformMessages::-webkit-scrollbar,
.logistics-conversation-list::-webkit-scrollbar,
#logisticsDetailsPane::-webkit-scrollbar {
    width: 6px;
}


#logisticsPlatformMessages::-webkit-scrollbar-track,
.logistics-conversation-list::-webkit-scrollbar-track,
#logisticsDetailsPane::-webkit-scrollbar-track {
    background: transparent;
}


#logisticsPlatformMessages::-webkit-scrollbar-thumb,
.logistics-conversation-list::-webkit-scrollbar-thumb,
#logisticsDetailsPane::-webkit-scrollbar-thumb {
    border-radius: 999px;

    background: #cfd6df;
}


/* ==========================================================================
   MESSAGE ROWS
   ========================================================================== */

.logistics-message-row {
    display: flex;

    align-items: flex-end;

    margin-top: 12px;
}


.logistics-message-row:first-child {
    margin-top: 0;
}


.logistics-message-content {
    display: flex;

    width: auto;
    inline-size: auto;

    min-width: 0;

    max-width:
        min(72%, 560px);

    flex: 0 1 auto;
    flex-direction: column;

    box-sizing: border-box;
}


.logistics-message-row.is-outgoing
.logistics-message-content {
    margin-left: auto;
    margin-right: 0;

    align-items: flex-end;
}


.logistics-message-row.is-incoming
.logistics-message-content {
    margin-left: 0;
    margin-right: auto;

    align-items: flex-start;
}


/* ==========================================================================
   MESSAGE LABEL
   ========================================================================== */

.logistics-message-label {
    margin-bottom: 5px;

    color: #7c7369;

    font-size: 8.5px;
    font-weight: 600;
    line-height: 1.3;
}


/* ==========================================================================
   MESSAGE BUBBLE
   ========================================================================== */

#logisticsPlatformMessages
[data-chat-bubble] {
    display: inline-block;

    width: auto;
    inline-size: auto;

    min-width: 0;
    min-inline-size: 0;

    max-width: 100%;
    max-inline-size: 100%;

    height: auto;
    min-height: 0;

    margin: 0;

    padding:
        8px
        12px;

    border: 1px solid #dfe4ea;
    border-radius: 12px;

    background: #ffffff;

    color: #3f4b5a;

    font-family:
        "Poppins",
        ui-sans-serif,
        system-ui,
        sans-serif;

    font-size: 10.5px;
    font-weight: 500;
    line-height: 1.48;

    letter-spacing: 0;

    white-space: normal;

    overflow-wrap: anywhere;
    word-break: normal;

    text-align: left;
    vertical-align: top;

    box-sizing: border-box;

    box-shadow:
        0 1px 2px rgba(15, 23, 42, .035),
        0 5px 14px rgba(15, 23, 42, .055),
        0 13px 28px rgba(15, 23, 42, .06);

    transform: none;
}


/* ==========================================================================
   OUTGOING MESSAGE
   ========================================================================== */

#logisticsPlatformMessages
[data-chat-bubble][data-chat-side="outgoing"] {
    align-self: flex-end;

    border-color: #cd900f;

    border-radius:
        12px
        12px
        4px
        12px;

    background: #d59617;

    color: #ffffff;

    box-shadow:
        0 1px 2px rgba(15, 23, 42, .04),
        0 6px 16px rgba(15, 23, 42, .07),
        0 14px 30px rgba(15, 23, 42, .075);
}


/* ==========================================================================
   INCOMING MESSAGE
   ========================================================================== */

#logisticsPlatformMessages
[data-chat-bubble][data-chat-side="incoming"] {
    align-self: flex-start;

    border-color: #dfe4ea;

    border-radius:
        12px
        12px
        12px
        4px;

    background: #ffffff;

    color: #3f4b5a;
}


#logisticsPlatformMessages
[data-chat-bubble]:hover {
    transform: none;
}


/* ==========================================================================
   MESSAGE TIME
   ========================================================================== */

.logistics-message-time {
    width: auto;

    min-width: 0;
    height: auto;

    margin-top: 4px;

    color: #98a2b3;

    font-size: 7.5px;
    font-weight: 400;
    line-height: 1.25;
}


.logistics-message-row.is-outgoing
.logistics-message-time {
    align-self: flex-end;

    text-align: right;
}


.logistics-message-row.is-incoming
.logistics-message-time {
    align-self: flex-start;

    text-align: left;
}


/* ==========================================================================
   EMPTY THREAD
   ========================================================================== */

.logistics-thread-empty {
    display: grid;

    flex: 1;

    min-width: 0;
    min-height: 0;

    place-items: center;
    align-content: center;

    padding: 30px;

    background: #f8fafc;

    text-align: center;
}


.logistics-thread-empty-icon {
    display: grid;

    width: 46px;
    height: 46px;

    place-items: center;

    margin-bottom: 10px;

    border: 1px solid #e1e6eb;
    border-radius: 50%;

    background: #ffffff;

    color: #667085;
}


.logistics-thread-empty-icon svg {
    width: 18px;
    height: 18px;
}


.logistics-thread-empty > strong {
    color: #344054;

    font-size: 10px;
    font-weight: 700;
}


.logistics-thread-empty > p {
    max-width: 330px;

    margin: 4px 0 0;

    color: #98a2b3;

    font-size: 8px;
    line-height: 1.55;
}


/* ==========================================================================
   COMPOSER
   ========================================================================== */

.logistics-platform-composer {
    position: relative;

    z-index: 20;

    flex: 0 0 auto;

    border-top: 1px solid var(--chat-line);

    background: #ffffff;

    padding:
        9px
        12px;

    box-shadow:
        0 -4px 14px
        rgba(15, 23, 42, .025);
}


/* ==========================================================================
   MESSAGE FORM
   ========================================================================== */

#logisticsMessageForm {
    display: flex;

    min-height: 50px;

    align-items: center;

    gap: 7px;

    border: 1px solid #d8dee6;
    border-radius: 13px;

    background: #ffffff;

    padding:
        5px
        6px;

    box-shadow:
        0 1px 2px
        rgba(15, 23, 42, .02);

    transition:
        border-color .15s ease,
        box-shadow .15s ease;
}


#logisticsMessageForm:focus-within {
    border-color: var(--chat-gold);

    box-shadow:
        0 0 0 3px
        rgba(213, 150, 23, .08);
}


/* ==========================================================================
   MESSAGE INPUT
   ========================================================================== */

#logisticsPlatformInput {
    min-width: 0;

    min-height: 38px;
    max-height: 112px;

    flex: 1 1 auto;

    resize: none;

    overflow-y: auto;

    border: 0;

    background: transparent;

    padding:
        8px
        6px;

    color: #344054;

    font-size: 10px;
    line-height: 1.5;

    outline: none;
}


#logisticsPlatformInput::placeholder {
    color: #98a2b3;
}


/* ==========================================================================
   SEND BUTTON
   ========================================================================== */

#logisticsPlatformSend {
    display: grid;

    width: 40px;
    height: 40px;

    min-width: 40px;

    flex: 0 0 40px;

    place-items: center;

    border: 0;
    border-radius: 10px;

    background: var(--chat-gold);

    padding: 0;

    color: #ffffff;

    box-shadow: none;

    cursor: pointer;

    transition:
        background-color .15s ease,
        opacity .15s ease;
}


#logisticsPlatformSend:hover:not(:disabled) {
    background: var(--chat-gold-hover);
}


#logisticsPlatformSend:disabled {
    cursor: not-allowed;

    opacity: .5;
}


#logisticsPlatformSend svg {
    width: 16px;
    height: 16px;

    color: #ffffff;
    stroke: #ffffff;
}


#logisticsPlatformSend:focus-visible {
    outline:
        2px solid rgba(213, 150, 23, .28);

    outline-offset: 2px;
}


/* ==========================================================================
   COMPOSER META
   ========================================================================== */

.logistics-composer-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 10px;

    margin-top: 5px;

    padding:
        0
        3px;

    color: #98a2b3;

    font-size: 7.5px;
    line-height: 1.3;
}


.logistics-composer-meta b {
    color: #667085;

    font-weight: 600;
}


/* ==========================================================================
   RIGHT RIDER DETAILS PANE
   ========================================================================== */

#logisticsDetailsPane {
    position: relative;

    z-index: 1;

    min-width: 0;
    min-height: 0;

    overflow-y: auto;
    overflow-x: hidden;

    overscroll-behavior: contain;

    border-left: 1px solid var(--chat-divider);

    background: #ffffff;

    box-shadow:
        0 1px 3px rgba(15, 23, 42, .025),
        0 7px 18px rgba(15, 23, 42, .045);
}


/* ==========================================================================
   DETAILS HEADING
   ========================================================================== */

.logistics-details-heading {
    border-bottom: 1px solid #edf0f3;

    padding: 13px;
}


.logistics-details-heading strong {
    display: block;

    color: #344054;

    font-size: 10px;
    font-weight: 700;
}


.logistics-details-heading p {
    margin: 2px 0 0;

    color: #98a2b3;

    font-size: 8px;
}


/* ==========================================================================
   DETAILS CONTENT
   ========================================================================== */

.logistics-details-content {
    padding: 14px;
}


.logistics-details-profile {
    text-align: center;
}


.logistics-details-avatar {
    display: grid;

    width: 48px;
    height: 48px;

    place-items: center;

    margin:
        0
        auto;

    border-radius: 50%;

    background: #f3f6f8;

    color: #5f7788;

    font-size: 11px;
    font-weight: 800;
}


.logistics-details-profile > strong {
    display: block;

    margin-top: 8px;

    color: #344054;

    font-size: 10px;
    font-weight: 700;
}


.logistics-details-profile > p {
    margin: 2px 0 0;

    color: #98a2b3;

    font-size: 7.8px;

    overflow-wrap: anywhere;
}


/* ==========================================================================
   DETAILS BLOCKS
   ========================================================================== */

.logistics-details-list {
    display: grid;

    gap: 8px;

    margin-top: 15px;
}


.logistics-details-list > div {
    border: 1px solid #e5e7eb;
    border-radius: 10px;

    background: #fafbfc;

    padding: 10px;

    box-shadow: none;
}


.logistics-details-list span {
    display: block;

    color: #98a2b3;

    font-size: 7px;
}


.logistics-details-list strong {
    display: block;

    margin-top: 3px;

    color: #344054;

    font-size: 8.5px;
    font-weight: 700;
}


.logistics-details-list p {
    margin: 2px 0 0;

    color: #98a2b3;

    font-size: 7px;
}


/* ==========================================================================
   TABLET
   ========================================================================== */

@media (max-width: 1023px) {

    #logisticsPlatformChat {
        height: calc(100dvh - 72px);

        min-height: 0;
    }


    .logistics-chat-grid {
        grid-template-columns:
            235px
            minmax(0, 1fr);
    }


    #logisticsDetailsPane {
        display: none;
    }


    .logistics-message-content {
        max-width:
            min(76%, 520px);
    }

}


/* ==========================================================================
   NARROW TABLET / MOBILE
   ========================================================================== */

@media (max-width: 700px) {

    #logisticsPlatformChat {
        height: calc(100dvh - 72px);

        min-height: 0;
    }


    .logistics-chat-grid {
        display: flex;

        height: 100%;

        min-height: 0;

        flex-direction: column;
    }


    #logisticsConversationPane {
        flex:
            0
            0
            218px;

        max-height: 218px;

        border-right: 0;

        border-bottom:
            1px solid var(--chat-line);

        box-shadow: none;
    }


    .logistics-conversation-heading {
        padding:
            10px
            12px
            9px;
    }


    .logistics-conversation-heading h3 {
        font-size: 11px;
    }


    .logistics-search-wrap {
        margin-top: 7px;
    }


    #logisticsMessageSearch {
        height: 36px;

        font-size: 9px;
    }


    .logistics-conversation-list {
        max-height: none;
    }


    .logistics-conversation-item {
        padding:
            9px
            12px;
    }


    #logisticsCenterPane {
        flex: 1;

        min-height: 0;

        border-left: 0;
        border-right: 0;

        box-shadow: none;
    }


    .logistics-thread-header {
        min-height: 58px;

        padding:
            8px
            10px;
    }


    .logistics-thread-badge {
        display: none;
    }


    #logisticsPlatformMessages {
        min-height: 0;

        padding:
            14px
            10px
            18px;
    }


    .logistics-message-content {
        max-width: 84%;
    }


    #logisticsPlatformMessages
    [data-chat-bubble] {
        padding:
            7px
            10px;

        border-radius: 11px;

        font-size: 10px;
    }


    #logisticsPlatformMessages
    [data-chat-bubble][data-chat-side="outgoing"] {
        border-radius:
            11px
            11px
            4px
            11px;
    }


    #logisticsPlatformMessages
    [data-chat-bubble][data-chat-side="incoming"] {
        border-radius:
            11px
            11px
            11px
            4px;
    }


    .logistics-platform-composer {
        padding:
            7px
            8px;
    }


    #logisticsMessageForm {
        min-height: 48px;

        border-radius: 12px;
    }


    #logisticsPlatformInput {
        min-height: 36px;

        padding:
            7px
            5px;

        font-size: 10px;
    }


    #logisticsPlatformSend {
        width: 38px;
        height: 38px;

        min-width: 38px;

        flex-basis: 38px;
    }


    .logistics-composer-meta {
        font-size: 7px;
    }

}


/* ==========================================================================
   VERY SMALL MOBILE
   ========================================================================== */

@media (max-width: 420px) {

    #logisticsConversationPane {
        flex-basis: 200px;

        max-height: 200px;
    }


    .logistics-thread-meta
    .logistics-platform-status {
        height: 23px;

        padding:
            0
            7px;

        font-size: 7px;
    }


    .logistics-message-content {
        max-width: 88%;
    }


    .logistics-composer-meta > span:first-child {
        display: none;
    }


    .logistics-composer-meta {
        justify-content: flex-end;
    }

}


/* ==========================================================================
   FOCUS STATES
   ========================================================================== */

.logistics-messages-page
:is(
    a,
    button,
    input,
    textarea
):focus-visible {
    outline:
        2px solid rgba(213, 150, 23, .28);

    outline-offset: 2px;
}


/* ==========================================================================
   REDUCED MOTION
   ========================================================================== */

@media (prefers-reduced-motion: reduce) {

    .logistics-messages-page *,
    .logistics-messages-page *::before,
    .logistics-messages-page *::after {
        animation: none !important;

        transition: none !important;

        scroll-behavior: auto !important;
    }

}
</style>
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