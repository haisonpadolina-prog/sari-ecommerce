@extends('layouts.seller')

@section('title', 'Chat / Messaging — SARI Seller')
@section('page-title', 'Chat / Messaging')

@section('content')
@php
    // SellerAdminChatController already resolves these values. Keep the view
    // presentation-only and avoid a duplicate database/schema query per visit.
    $sellerChatRestriction = $sellerChatRestriction ?? null;
    $sellerChatBlocked = (bool) ($sellerChatBlocked ?? false);
@endphp

<style>
    #sellerPlatformChat {
        height: calc(100dvh - 150px);
        min-height: 620px;
        max-height: 900px;
    }

    #sellerPlatformChat .chat-grid { height: 100%; min-height: 0; }
    #sellerConversationPane,
    #sellerCenterPane,
    #sellerDetailsPane { min-height: 0; }
    #sellerConversationPane,
    #sellerDetailsPane { overflow-y: auto; overscroll-behavior: contain; }
    #sellerCenterPane { height: 100%; overflow: hidden; }
    #sellerPlatformMessages {
        min-height: 0;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #d8c7a7 transparent;
    }
    #sellerPlatformMessages::-webkit-scrollbar,
    #sellerConversationPane::-webkit-scrollbar,
    #sellerDetailsPane::-webkit-scrollbar { width: 6px; }
    #sellerPlatformMessages::-webkit-scrollbar-track,
    #sellerConversationPane::-webkit-scrollbar-track,
    #sellerDetailsPane::-webkit-scrollbar-track { background: transparent; }
    #sellerPlatformMessages::-webkit-scrollbar-thumb,
    #sellerConversationPane::-webkit-scrollbar-thumb,
    #sellerDetailsPane::-webkit-scrollbar-thumb {
        background: #d8c7a7;
        border-radius: 999px;
    }

    .seller-platform-composer {
        position: relative;
        z-index: 20;
        flex: 0 0 auto;
        background: #fff;
        box-shadow: 0 -8px 24px rgba(35, 28, 20, .035);
    }

    .seller-platform-status {
        display: inline-flex;
        height: 24px;
        align-items: center;
        gap: 6px;
        border: 1px solid #dce7df;
        border-radius: 999px;
        background: #f6f9f6;
        padding: 0 9px;
        color: #617568;
        font-size: 10px;
        font-weight: 700;
    }

    .seller-platform-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #4f9a69;
    }

    .seller-platform-ai {
        display: inline-flex;
        height: 24px;
        align-items: center;
        gap: 6px;
        border: 1px solid #e4e8ec;
        border-radius: 999px;
        background: #f7f9fb;
        padding: 0 9px;
        color: #647888;
        font-size: 10px;
        font-weight: 700;
    }

    .seller-platform-ai svg { width: 12px; height: 12px; }

    .seller-message-row { margin-top: 18px; }
    .seller-message-bubble { white-space: pre-wrap; word-break: break-word; }

    .seller-platform-loading {
        position: absolute;
        inset: 0;
        z-index: 40;
        display: grid;
        place-items: center;
        background: rgba(255,255,255,.96);
    }

    #sellerPlatformChat[data-ready="1"] .seller-platform-loading { display: none; }


    /* Compact composer sizing for 100% zoom */
    #sellerPlatformMessageForm {
        border-radius: 14px;
        padding: 10px;
    }

    #sellerPlatformInput {
        min-height: 54px;
        max-height: 112px;
        font-size: 12px;
        line-height: 1.45;
    }

    #sellerAttachmentName {
        margin-top: 8px;
        padding: 8px 10px;
        font-size: 10px;
    }

    .seller-composer-actions {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
        border-top: 1px solid #eee8df;
        padding-top: 10px;
    }

    .seller-composer-attachment {
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .seller-composer-attachment-label {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border-radius: 10px;
    }

    .seller-composer-attachment-label svg {
        width: 14px;
        height: 14px;
    }

    .seller-composer-attachment-text {
        min-width: 0;
        font-size: 9px;
        line-height: 1.3;
        color: #958c80;
    }

    #sellerPlatformSend {
        height: 36px;
        padding-left: 14px;
        padding-right: 14px;
        border-radius: 10px;
        font-size: 11px;
        gap: 6px;
        flex-shrink: 0;
    }

    #sellerPlatformSend svg {
        width: 13px;
        height: 13px;
    }

    @media (max-width: 640px) {
        .seller-platform-composer {
            padding: 10px !important;
        }

        #sellerPlatformMessageForm {
            padding: 9px;
        }

        #sellerPlatformInput {
            min-height: 48px;
            max-height: 96px;
            font-size: 11.5px;
        }

        .seller-composer-actions {
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
        }

        .seller-composer-attachment-text {
            font-size: 8.8px;
        }

        #sellerPlatformSend {
            height: 34px;
            padding-left: 12px;
            padding-right: 12px;
            font-size: 10.5px;
        }
    }

    @media (max-width: 1023px) {
        #sellerPlatformChat {
            height: calc(100dvh - 132px);
            min-height: 560px;
            max-height: none;
        }
    }

    @media (max-width: 639px) {
        #sellerPlatformChat {
            height: calc(100dvh - 118px);
            min-height: 520px;
        }
    }


    /* SARI Assistant identity + typing presence */
    .sari-bot-avatar {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        border: 1px solid #dce8ef;
        border-radius: 999px;
        background: #f2f7fa;
        color: #5d788b;
        box-shadow: 0 4px 12px rgba(52, 78, 96, .06);
    }

    .sari-bot-avatar svg {
        width: 58%;
        height: 58%;
    }

    .sari-assistant-typing {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .sari-assistant-typing__bubble {
        min-width: 72px;
        border: 1px solid #dce8ef;
        border-radius: 14px 14px 14px 5px;
        background: #f4f8fa;
        padding: 11px 14px;
        box-shadow: 0 7px 18px rgba(35, 28, 20, .045);
    }

    .sari-assistant-typing__dots {
        display: flex;
        align-items: center;
        gap: 4px;
        height: 10px;
    }

    .sari-assistant-typing__dots span {
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: #718896;
        animation: sariAssistantTypingDot 1.05s ease-in-out infinite;
    }

    .sari-assistant-typing__dots span:nth-child(2) { animation-delay: .14s; }
    .sari-assistant-typing__dots span:nth-child(3) { animation-delay: .28s; }

    @keyframes sariAssistantTypingDot {
        0%, 60%, 100% { transform: translateY(0); opacity: .42; }
        30% { transform: translateY(-3px); opacity: 1; }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-assistant-typing__dots span { animation: none !important; opacity: .8; }
    }


    /* ============================================================
       FULL-WORKSPACE MESSAGING
       Flat Messenger-style layout: no floating outer card/container.
       ============================================================ */
    .seller-platform-page {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
    }

    #sellerPlatformChat {
        width: 100%;
        height: calc(100dvh - 112px);
        min-height: 560px;
        max-height: none;
        overflow: hidden;
        border: 0 !important;
        border-radius: 0 !important;
        background: #fbfaf7 !important;
        box-shadow: none !important;
    }

    #sellerPlatformChat .chat-grid {
        width: 100%;
        height: 100%;
        min-height: 0;
        background: #fbfaf7;
    }

    #sellerConversationPane {
        background: #fff !important;
        border-color: #ece5dc !important;
    }

    #sellerCenterPane {
        min-width: 0;
        background: #fbfaf7 !important;
    }

    #sellerDetailsPane {
        background: #fff !important;
        border-color: #ece5dc !important;
    }

    #sellerPlatformMessages {
        background: #fbfaf7 !important;
        padding-top: 18px !important;
        padding-bottom: 18px !important;
    }

    /* Integrated thread header — part of the workspace, not another card. */
    #sellerCenterPane > div:first-child {
        min-height: 64px;
        padding-top: 10px !important;
        padding-bottom: 10px !important;
        background: #fff !important;
        border-color: #ece5dc !important;
    }

    /* Bottom composer stays attached to the conversation surface. */
    .seller-platform-composer {
        border-color: #ece5dc !important;
        background: #fff !important;
        box-shadow: 0 -6px 18px rgba(35, 28, 20, .025) !important;
    }

    #sellerPlatformMessageForm {
        border: 1px solid #e7dfd5 !important;
        border-radius: 13px !important;
        background: #fbfaf7 !important;
        box-shadow: none !important;
    }

    #sellerPlatformMessageForm:focus-within {
        border-color: #d3aa59 !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(201, 145, 40, .07) !important;
    }

    /* Let the actual conversation be the dominant background. */
    .seller-message-row {
        margin-top: 14px;
    }

    .seller-message-bubble {
        box-shadow: none !important;
    }

    /* Do not block first paint with a full-page "connecting" cover. */
    .seller-platform-loading {
        display: none !important;
    }

    @media (min-width: 1536px) {
        #sellerPlatformChat .chat-grid {
            grid-template-columns: 260px minmax(0, 1fr) 260px !important;
        }
    }

    @media (min-width: 1024px) and (max-width: 1535px) {
        #sellerPlatformChat .chat-grid {
            grid-template-columns: 250px minmax(0, 1fr) !important;
        }
    }

    @media (max-width: 1023px) {
        #sellerPlatformChat {
            height: calc(100dvh - 104px);
            min-height: 520px;
        }
    }

    @media (max-width: 639px) {
        #sellerPlatformChat {
            height: calc(100dvh - 92px);
            min-height: 500px;
        }

        #sellerPlatformMessages {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
    }


    /* ============================================================
       FULL-BLEED SELLER CONTENT
       The seller layout wraps every page in responsive padding:
       px-4/6/8/10 + py-5/6/7. Cancel that padding only for chat
       so the workspace touches the header, right edge, and bottom.
       ============================================================ */
    .seller-platform-page {
        margin: -20px -16px;
        width: calc(100% + 32px);
        max-width: none !important;
    }

    #sellerPlatformChat {
        height: calc(100dvh - 86px) !important;
        min-height: 0 !important;
        max-height: none !important;
    }

    @media (min-width: 640px) {
        .seller-platform-page {
            margin: -24px;
            width: calc(100% + 48px);
        }
    }

    @media (min-width: 1024px) {
        .seller-platform-page {
            margin-top: -28px;
            margin-bottom: -28px;
            margin-left: -32px;
            margin-right: -32px;
            width: calc(100% + 64px);
        }

        #sellerPlatformChat {
            height: calc(100dvh - 86px) !important;
        }
    }

    @media (min-width: 1280px) {
        .seller-platform-page {
            margin-left: -40px;
            margin-right: -40px;
            width: calc(100% + 80px);
        }
    }

    @media (max-width: 639px) {
        #sellerPlatformChat {
            height: calc(100dvh - 86px) !important;
        }
    }


    /* ============================================================
       COMPACT MESSENGER-STYLE COMPOSER
       One horizontal row: attach | message | send.
       ============================================================ */
    .seller-platform-composer {
        padding: 10px 14px !important;
    }

    #sellerPlatformMessageForm {
        display: flex !important;
        min-height: 54px !important;
        align-items: center !important;
        gap: 8px !important;
        border: 1px solid #e6ded4 !important;
        border-radius: 16px !important;
        background: #fff !important;
        padding: 6px 8px !important;
        box-shadow: 0 3px 12px rgba(35, 28, 20, .035) !important;
    }

    #sellerPlatformMessageForm:focus-within {
        border-color: #d9c49a !important;
        box-shadow: 0 0 0 3px rgba(201, 145, 40, .06) !important;
    }

    #sellerPlatformInput {
        min-width: 0 !important;
        min-height: 38px !important;
        max-height: 76px !important;
        flex: 1 1 auto !important;
        padding: 9px 6px !important;
        border: 0 !important;
        background: transparent !important;
        font-size: 12px !important;
        line-height: 20px !important;
        overflow-y: auto !important;
    }

    #sellerAttachmentName {
        position: absolute !important;
        left: 14px !important;
        bottom: calc(100% + 7px) !important;
        z-index: 30 !important;
        max-width: min(420px, 70vw) !important;
        margin: 0 !important;
        border: 1px solid #e7dfd5 !important;
        border-radius: 9px !important;
        background: #fff !important;
        padding: 6px 9px !important;
        color: #655d54 !important;
        font-size: 8px !important;
        line-height: 1.3 !important;
        box-shadow: 0 7px 18px rgba(35, 28, 20, .07) !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .seller-composer-actions {
        display: contents !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
    }

    .seller-composer-attachment {
        display: contents !important;
    }

    .seller-composer-attachment-label {
        display: grid !important;
        width: 38px !important;
        height: 38px !important;
        flex: 0 0 38px !important;
        place-items: center !important;
        border: 1px solid #e8e0d6 !important;
        border-radius: 11px !important;
        background: #faf8f5 !important;
        color: #7e756b !important;
    }

    .seller-composer-attachment-label:hover {
        border-color: #dac59f !important;
        background: #fff8eb !important;
        color: #a8731f !important;
    }

    .seller-composer-attachment-label svg {
        width: 15px !important;
        height: 15px !important;
    }

    .seller-composer-attachment-text {
        display: none !important;
    }

    #sellerPlatformSend {
        display: grid !important;
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        flex: 0 0 40px !important;
        place-items: center !important;
        gap: 0 !important;
        border-radius: 12px !important;
        padding: 0 !important;
        background: #d39412 !important;
        box-shadow: 0 5px 12px rgba(201, 145, 40, .15) !important;
        font-size: 0 !important;
    }

    #sellerPlatformSend:hover {
        background: #bd8209 !important;
    }

    #sellerPlatformSend svg {
        width: 16px !important;
        height: 16px !important;
        margin: 0 !important;
    }

    #sellerPlatformError {
        margin-top: 5px !important;
        font-size: 8px !important;
    }

    @media (max-width: 639px) {
        .seller-platform-composer {
            padding: 7px 8px !important;
        }

        #sellerPlatformMessageForm {
            min-height: 50px !important;
            gap: 6px !important;
            border-radius: 14px !important;
            padding: 5px 6px !important;
        }

        .seller-composer-attachment-label {
            width: 36px !important;
            height: 36px !important;
            flex-basis: 36px !important;
        }

        #sellerPlatformSend {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-basis: 38px !important;
        }

        #sellerPlatformInput {
            min-height: 36px !important;
            padding: 8px 4px !important;
            font-size: 11.5px !important;
        }
    }



    /* ============================================================
       SARI SELLER MESSAGING — CLEAN MODERN PROFESSIONAL FINAL LAYER
       ============================================================ */
    .seller-platform-page {
        --chat-gold: #d59617;
        --chat-gold-hover: #c9890f;
        --chat-gold-soft: #fffaf0;
        --chat-ink: #111827;
        --chat-text: #344054;
        --chat-muted: #7c8592;
        --chat-soft-muted: #98a2b3;
        --chat-line: #e5e7eb;
        --chat-canvas: #f7f8fa;
        --chat-panel: #ffffff;
        --chat-ai: #5f7788;

        background: var(--chat-canvas) !important;
        color: var(--chat-ink) !important;
        font-family: "Poppins", ui-sans-serif, system-ui, sans-serif !important;
    }

    #sellerPlatformChat {
        background: var(--chat-canvas) !important;
        border-top: 1px solid var(--chat-line) !important;
    }

    #sellerPlatformChat .chat-grid {
        background: var(--chat-canvas) !important;
    }

    @media (min-width: 1536px) {
        #sellerPlatformChat .chat-grid {
            grid-template-columns: 258px minmax(0, 1fr) 244px !important;
        }
    }

    @media (min-width: 1024px) and (max-width: 1535px) {
        #sellerPlatformChat .chat-grid {
            grid-template-columns: 252px minmax(0, 1fr) !important;
        }
    }

    /* Left conversation rail */
    #sellerConversationPane {
        border-color: var(--chat-line) !important;
        background: #fff !important;
    }

    #sellerConversationPane > div:first-child {
        padding: 14px 14px 12px !important;
        border-color: #edf0f3 !important;
    }

    #sellerConversationPane h3 {
        color: #1f2937 !important;
        font-size: 13px !important;
        font-weight: 700 !important;
    }

    #sellerConversationPane h3 + p {
        color: #98a2b3 !important;
        font-size: 8.5px !important;
    }

    #sellerMessageSearch {
        height: 40px !important;
        border-color: #d8dee6 !important;
        border-radius: 9px !important;
        background: #f8fafc !important;
        padding-left: 36px !important;
        font-size: 9.5px !important;
        color: #344054 !important;
        box-shadow: none !important;
    }

    #sellerMessageSearch:focus {
        border-color: var(--chat-gold) !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .08) !important;
    }

    #sellerConversationPane > div:nth-child(2) {
        border-left: 0 !important;
        border-bottom: 1px solid #edf0f3 !important;
        background: #fffaf0 !important;
        padding: 12px 14px !important;
    }

    #sellerConversationPane > div:nth-child(2) > div {
        gap: 9px !important;
    }

    #sellerConversationPane > div:nth-child(2) .h-11.w-11 {
        width: 36px !important;
        height: 36px !important;
        flex-basis: 36px !important;
        background: #fff !important;
        color: #a96f06 !important;
        box-shadow: inset 0 0 0 1px #efd9a7 !important;
    }

    #sellerConversationPane > div:nth-child(2) p.text-\[13px\] {
        color: #344054 !important;
        font-size: 9.8px !important;
    }

    #sellerConversationPane > div:nth-child(2) p.text-\[10px\] {
        color: #98a2b3 !important;
        font-size: 7.8px !important;
    }

    #sellerLastMessagePreview {
        margin-top: 5px !important;
        color: #667085 !important;
        font-size: 8.5px !important;
        line-height: 1.45 !important;
    }

    /* Main thread */
    #sellerCenterPane {
        background: var(--chat-canvas) !important;
    }

    #sellerCenterPane > div:first-child {
        min-height: 62px !important;
        border-color: var(--chat-line) !important;
        padding: 9px 14px !important;
        box-shadow: 0 1px 0 rgba(15, 23, 42, .01) !important;
    }

    #sellerCenterPane > div:first-child .h-11.w-11 {
        width: 36px !important;
        height: 36px !important;
        flex-basis: 36px !important;
        background: #fffaf0 !important;
        color: #a96f06 !important;
        box-shadow: inset 0 0 0 1px #efd9a7 !important;
    }

    #sellerCenterPane > div:first-child p.text-\[14px\] {
        color: #1f2937 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
    }

    #sellerCenterPane > div:first-child p.text-\[10px\] {
        margin-top: 2px !important;
        color: #98a2b3 !important;
        font-size: 8px !important;
    }

    .seller-platform-status,
    .seller-platform-ai {
        height: 26px !important;
        border-radius: 999px !important;
        padding: 0 8px !important;
        font-size: 7.8px !important;
        font-weight: 700 !important;
    }

    .seller-platform-status {
        border-color: #dce7df !important;
        background: #f4f9f6 !important;
        color: #4f7d63 !important;
    }

    .seller-platform-status::before {
        width: 5px !important;
        height: 5px !important;
        background: currentColor !important;
    }

    .seller-platform-status[data-state="connecting"],
    .seller-platform-status[data-state="syncing"] {
        border-color: #eadfc9 !important;
        background: #fff9ef !important;
        color: #a8731f !important;
    }

    .seller-platform-status[data-state="fallback"] {
        border-color: #d8e2eb !important;
        background: #f5f8fb !important;
        color: #5d768a !important;
    }

    .seller-platform-status[data-state="offline"],
    .seller-platform-status[data-state="unavailable"] {
        border-color: #efcece !important;
        background: #fff5f5 !important;
        color: #a65353 !important;
    }

    .seller-platform-ai {
        border-color: #dde5eb !important;
        background: #f6f9fb !important;
        color: #617889 !important;
    }

    .seller-platform-ai[data-disabled="1"] {
        opacity: .58;
    }

    #sellerPlatformMessages {
        background: var(--chat-canvas) !important;
        padding: 18px 18px 22px !important;
        scrollbar-color: #cfd6df transparent !important;
    }

    .seller-message-row {
        margin-top: 12px !important;
    }

    .seller-message-row:first-child {
        margin-top: 0 !important;
    }

    .seller-message-bubble {
        border-radius: 13px !important;
        padding: 9px 11px !important;
        font-size: 10px !important;
        line-height: 1.55 !important;
        box-shadow: 0 2px 7px rgba(15, 23, 42, .035) !important;
    }

    .seller-message-row.justify-end .seller-message-bubble {
        background: var(--chat-gold) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(213, 150, 23, .12) !important;
    }

    .seller-message-row:not(.justify-end) .seller-message-bubble {
        border-color: #e1e6eb !important;
        background: #fff !important;
        color: #475467 !important;
    }

    .seller-message-row .max-w-\[84\%\] {
        max-width: min(72%, 720px) !important;
    }

    .sari-bot-avatar {
        border-color: #dce5eb !important;
        background: #f2f7fa !important;
        color: #5f7788 !important;
        box-shadow: none !important;
    }

    .sari-assistant-typing {
        gap: 8px !important;
        margin-top: 12px !important;
    }

    .sari-assistant-typing__bubble {
        min-width: 62px !important;
        border-color: #dde6ec !important;
        border-radius: 12px 12px 12px 4px !important;
        background: #f4f8fa !important;
        padding: 9px 11px !important;
        box-shadow: none !important;
    }

    /* Composer */
    .seller-platform-composer {
        border-color: var(--chat-line) !important;
        background: #fff !important;
        padding: 9px 12px !important;
        box-shadow: 0 -4px 14px rgba(15, 23, 42, .025) !important;
    }

    #sellerPlatformMessageForm {
        min-height: 50px !important;
        gap: 7px !important;
        border-color: #d8dee6 !important;
        border-radius: 13px !important;
        background: #fff !important;
        padding: 5px 6px !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .02) !important;
    }

    #sellerPlatformMessageForm:focus-within {
        border-color: var(--chat-gold) !important;
        box-shadow: 0 0 0 3px rgba(213, 150, 23, .08) !important;
    }

    #sellerPlatformInput {
        min-height: 38px !important;
        max-height: 112px !important;
        padding: 8px 6px !important;
        color: #344054 !important;
        font-size: 10px !important;
        line-height: 1.5 !important;
    }

    .seller-composer-attachment-label {
        width: 38px !important;
        height: 38px !important;
        flex-basis: 38px !important;
        border-color: #e1e6eb !important;
        border-radius: 10px !important;
        background: #f8fafc !important;
        color: #667085 !important;
    }

    .seller-composer-attachment-label:hover,
    .seller-composer-attachment-label:focus-within {
        border-color: #efd9a7 !important;
        background: #fffaf0 !important;
        color: #9b6505 !important;
    }

    #sellerPlatformSend {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        flex-basis: 40px !important;
        border-radius: 10px !important;
        background: var(--chat-gold) !important;
        color: #fff !important;
        box-shadow: 0 5px 12px rgba(213, 150, 23, .14) !important;
    }

    #sellerPlatformSend svg {
        color: #fff !important;
        stroke: #fff !important;
    }

    #sellerPlatformSend:hover:not(:disabled) {
        background: var(--chat-gold-hover) !important;
    }

    #sellerPlatformSend:focus-visible,
    .seller-composer-attachment-label:focus-within {
        outline: 2px solid rgba(213, 150, 23, .28) !important;
        outline-offset: 2px !important;
    }

    #sellerAttachmentName {
        left: 10px !important;
        bottom: calc(100% + 6px) !important;
        border-color: #e1e6eb !important;
        border-radius: 9px !important;
        color: #475467 !important;
        font-size: 8px !important;
        box-shadow: 0 8px 20px rgba(15,23,42,.07) !important;
    }

    #sellerPlatformError {
        margin-top: 5px !important;
        font-size: 8.5px !important;
        line-height: 1.4 !important;
    }


    .seller-message-pending {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
        color: #98a2b3;
        font-size: 7.8px;
        font-weight: 600;
    }

    .seller-message-pending::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 999px;
        background: #d59617;
        animation: sariMessagePendingPulse .9s ease-in-out infinite;
    }

    .seller-message-uploading {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        padding: 8px 9px;
        color: #667085;
        font-size: 8.5px;
    }

    .seller-message-uploading svg {
        width: 13px;
        height: 13px;
        flex: 0 0 auto;
        color: #b97805;
    }

    @keyframes sariMessagePendingPulse {
        0%, 100% { opacity: .35; }
        50% { opacity: 1; }
    }

    @media (prefers-reduced-motion: reduce) {
        .seller-message-pending::before {
            animation: none !important;
            opacity: .8;
        }
    }

    /* Right context */
    #sellerDetailsPane {
        border-color: var(--chat-line) !important;
        background: #fff !important;
    }

    #sellerDetailsPane > div:first-child {
        border-color: #edf0f3 !important;
        padding: 13px !important;
    }

    #sellerDetailsPane > div:first-child p:first-child {
        color: #344054 !important;
        font-size: 10px !important;
    }

    #sellerDetailsPane > div:first-child p:last-child {
        color: #98a2b3 !important;
        font-size: 8px !important;
    }

    #sellerDetailsPane > div:last-child {
        padding: 14px !important;
    }

    #sellerDetailsPane > div:last-child .h-16.w-16 {
        width: 48px !important;
        height: 48px !important;
        background: #f3f6f8 !important;
        color: #5f7788 !important;
        font-size: 11px !important;
    }

    #sellerDetailsPane > div:last-child > div:first-child p.mt-3 {
        margin-top: 8px !important;
        color: #344054 !important;
        font-size: 10px !important;
    }

    #sellerDetailsPane > div:last-child > div:first-child p.mt-1 {
        color: #98a2b3 !important;
        font-size: 7.8px !important;
    }

    #sellerDetailsPane .mt-6.space-y-3 {
        margin-top: 12px !important;
    }

    #sellerDetailsPane .mt-6.space-y-3 > div {
        border-color: #e5e7eb !important;
        border-radius: 10px !important;
        background: #fafbfc !important;
        padding: 10px !important;
    }

    /* Search/reaction/accessory polish */
    [data-reaction-picker] {
        border-color: #e1e6eb !important;
        box-shadow: 0 12px 30px rgba(15,23,42,.12) !important;
    }

    [data-reaction-toggle] {
        border-color: #e1e6eb !important;
        box-shadow: none !important;
    }

    /* Tablet/mobile: this page has one seller support thread, so prioritize it. */
    @media (max-width: 1023px) {
        #sellerPlatformChat .chat-grid {
            grid-template-columns: minmax(0, 1fr) !important;
        }

        #sellerConversationPane,
        #sellerDetailsPane {
            display: none !important;
        }

        #sellerCenterPane {
            display: flex !important;
            height: 100% !important;
        }
    }

    @media (max-width: 639px) {
        #sellerCenterPane > div:first-child {
            min-height: 58px !important;
            padding: 8px 10px !important;
        }

        #sellerCenterPane > div:first-child .seller-platform-ai {
            display: none !important;
        }

        #sellerPlatformMessages {
            padding: 14px 10px 18px !important;
        }

        .seller-message-row .max-w-\[84\%\] {
            max-width: 84% !important;
        }

        .seller-platform-composer {
            padding: 7px 8px !important;
        }

        #sellerPlatformMessageForm {
            min-height: 48px !important;
            border-radius: 12px !important;
        }

        #sellerPlatformInput {
            min-height: 36px !important;
            font-size: 10px !important;
        }

        .seller-composer-attachment-label {
            width: 36px !important;
            height: 36px !important;
            flex-basis: 36px !important;
        }

        #sellerPlatformSend {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-basis: 38px !important;
        }
    }


    /* ============================================================
       ADMIN SUPPORT BRAND IDENTITY + INSTANT ACKNOWLEDGEMENT
       ============================================================ */
    .sari-support-brand-avatar,
    .sari-admin-avatar {
        display: grid;
        place-items: center;
        overflow: hidden;
        flex: 0 0 auto;
        border: 1px solid #e1e5ea;
        border-radius: 11px;
        background: #202329;
        box-shadow: none;
    }

    .sari-support-brand-avatar {
        width: 36px;
        height: 36px;
    }

    .sari-support-brand-avatar img,
    .sari-admin-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sari-support-online-dot {
        position: absolute;
        right: -1px;
        bottom: -1px;
        width: 9px;
        height: 9px;
        border: 2px solid #fff;
        border-radius: 999px;
        background: #49a36f;
    }

    .sari-admin-avatar {
        border-radius: 9px;
        background: #202329;
    }

    .sari-bot-avatar {
        overflow: hidden !important;
        border: 0 !important;
        border-radius: 50% !important;
        background: transparent !important;
        color: transparent !important;
        box-shadow: none !important;
    }

    .sari-bot-avatar img {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        object-fit: contain !important;
    }

    .seller-platform-ai {
        gap: 5px !important;
        border-color: #e1e5e9 !important;
        background: #fff !important;
        color: #667085 !important;
    }

    .seller-platform-ai img {
        width: 16px !important;
        height: 16px !important;
        flex: 0 0 16px !important;
        object-fit: contain !important;
    }

    #sellerConversationPane > div:nth-child(2) {
        border-left-width: 0 !important;
        background: #fbfcfd !important;
    }

    #sellerConversationPane > div:nth-child(2) .sari-support-brand-avatar,
    #sellerCenterPane > div:first-child .sari-support-brand-avatar {
        width: 36px !important;
        height: 36px !important;
        flex-basis: 36px !important;
        box-shadow: none !important;
    }

    #sellerCenterPane > div:first-child {
        background: #fff !important;
    }

    #sellerCenterPane > div:first-child p:first-of-type,
    #sellerConversationPane > div:nth-child(2) p:first-of-type {
        letter-spacing: -.015em;
    }

    .seller-message-row:not(.justify-end) .seller-message-bubble {
        box-shadow: none !important;
    }

    .seller-message-row.justify-end .seller-message-bubble {
        box-shadow: none !important;
    }

    .sari-assistant-typing {
        display: none !important;
    }

</style>

<div class="seller-platform-page">
    @if (session('success'))
        <div class="mb-5 rounded-[18px] border border-[#d5e5dc] bg-[#f3f8f5] px-5 py-4 text-[12px] font-medium text-[#56816a]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-[18px] border border-[#ead7d7] bg-[#fdf5f5] px-5 py-4 text-[12px] text-[#a45f5f]">
            {{ $errors->first() }}
        </div>
    @endif

    <section
        id="sellerPlatformChat"
        data-ready="0"
        class="relative overflow-hidden bg-[#fbfaf7]"
    >
        <div class="seller-platform-loading">
            <div class="text-center">
                <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#f6efe2] text-[#9b6b1a]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 animate-pulse" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path>
                    </svg>
                </div>
                <p class="mt-3 text-[11px] font-semibold text-[#655d54]">Connecting to SARI Support…</p>
            </div>
        </div>

        <div class="chat-grid grid grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] 2xl:grid-cols-[280px_minmax(0,1fr)_280px]">
            {{-- LEFT: SUPPORT CONVERSATION --}}
            <aside id="sellerConversationPane" class="border-b border-[#ebe5dc] bg-white lg:border-b-0 lg:border-r">
                <div class="border-b border-[#eee8df] px-4 py-4 sm:px-5">
                    <h3 class="font-bold tracking-[-0.02em] text-[#24201b]" style="font-size:clamp(14px,.88vw,16px)">Conversations</h3>
                    <p class="mt-1 text-[#938a7f]" style="font-size:clamp(10px,.66vw,12px)">SARI platform support</p>

                    <div class="relative mt-4">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9b9287]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="6.5"></circle><path d="m16 16 4 4"></path>
                        </svg>
                        <input id="sellerMessageSearch" type="search" autocomplete="off" placeholder="Search messages..." class="h-11 w-full rounded-[12px] border border-[#e3ddd4] bg-[#fbfaf8] pl-10 pr-10 text-[12px] text-[#3d3730] outline-none transition placeholder:text-[#aaa197] focus:border-[#c99128] focus:bg-white focus:ring-4 focus:ring-[#c99128]/10">
                        <button id="sellerMessageSearchClear" type="button" class="absolute right-2.5 top-1/2 hidden h-7 w-7 -translate-y-1/2 place-items-center rounded-lg text-[#91887d] transition hover:bg-[#f1ede7] hover:text-[#4d463e]" aria-label="Clear search">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m7 7 10 10"></path><path d="m17 7-10 10"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="border-l-2 border-[#c99128] bg-[#fbf7ef] px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="sari-support-brand-avatar">
                                <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI">
                            </div>
                            <span class="sari-support-online-dot" aria-hidden="true"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] font-bold text-[#2d2822]">SARI Admin Support</p>
                            <p class="mt-0.5 truncate text-[10px] text-[#8f867b]">Human administrator support</p>
                            <p id="sellerLastMessagePreview" class="mt-2 line-clamp-2 text-[11px] leading-5 text-[#72695f]">Loading conversation…</p>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- CENTER: PLATFORM SUPPORT THREAD --}}
            <div id="sellerCenterPane" class="flex min-h-0 min-w-0 flex-col bg-white">
                <div class="flex items-center justify-between gap-4 border-b border-[#ebe5dc] bg-white px-4 py-4 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="relative shrink-0">
                            <div class="sari-support-brand-avatar">
                                <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI">
                            </div>
                            <span class="sari-support-online-dot" aria-hidden="true"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[14px] font-bold tracking-[-0.02em] text-[#29241f]">SARI Admin Support</p>
                            <p class="mt-1 text-[10px] text-[#8f867b]">Official human support channel</p>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <span id="sellerRealtimeStatus" class="seller-platform-status" role="status" aria-live="polite" data-state="connecting">Connecting</span>
                        <span id="sellerAssistantBadge" class="seller-platform-ai" title="SARI Assistant automatically confirms receipt while you wait for a human administrator.">
                            <img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" aria-hidden="true">
                            Receipt confirmation
                        </span>
                    </div>
                </div>

                <div id="sellerPlatformMessages" class="min-h-0 flex-1 overflow-y-auto bg-[#fbfaf7] px-4 py-6 sm:px-6 lg:px-7">
                    <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                        <div>
                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f6efe2] text-[#9d6f22]">
                                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg>
                            </div>
                            <p class="mt-4 text-[13px] font-bold text-[#4f473e]">Start a conversation</p>
                            <p class="mt-1 text-[11px] text-[#958c80]">Send your concern to SARI Admin Support. SARI Assistant will confirm receipt while you wait for a human administrator.</p>
                        </div>
                    </div>
                </div>

                <div class="seller-platform-composer border-t border-[#ebe5dc] bg-white p-3 sm:p-3.5">
                    <div id="sellerChatBlockedNotice" class="{{ $sellerChatBlocked ? 'flex' : 'hidden' }} mb-3 items-start gap-3 rounded-[14px] border border-[#e8cccc] bg-[#fff7f7] px-4 py-3">
                        <div class="grid h-8 w-8 shrink-0 place-items-center rounded-[10px] bg-white text-[#a65f5f] shadow-sm">
                            <svg viewBox="0 0 24 24" class="h-[15px] w-[15px]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle><path d="m8 8 8 8"></path></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-[#8f5050]">Messaging temporarily unavailable</p>
                            <p id="sellerChatBlockedReason" class="mt-1 text-[9.5px] leading-5 text-[#9a6d6d]">{{ $sellerChatRestriction?->block_reason ?: 'SARI Admin has restricted this support conversation.' }}</p>
                        </div>
                    </div>

                    <form id="sellerPlatformMessageForm" class="rounded-[16px] border border-[#e3ddd4] bg-[#fdfcfa] p-3 shadow-[0_6px_18px_rgba(35,28,20,.035)] transition focus-within:border-[#c99a3d] focus-within:bg-white focus-within:ring-4 focus-within:ring-[#c99a3d]/10">
                        @csrf
                        <textarea id="sellerPlatformInput" rows="1" @if($sellerChatBlocked) disabled @endif placeholder="{{ $sellerChatBlocked ? 'Messaging is restricted by SARI Admin.' : 'Message...' }}" class="w-full resize-none bg-transparent px-1 py-1 text-[12px] leading-[1.45] text-[#3e3831] outline-none placeholder:text-[#aaa197]"></textarea>

                        <div id="sellerAttachmentName" class="mt-2 hidden rounded-xl border border-[#e8e1d7] bg-white px-3 py-2 text-[10px] text-[#62594e]"></div>

                        <div class="seller-composer-actions">
                            <div class="seller-composer-attachment">
                                <label class="seller-composer-attachment-label cursor-pointer border border-transparent text-[#756d63] transition hover:border-[#e8dfd0] hover:bg-[#f8f4ed] hover:text-[#a8731f]" title="Attach file">
                                    <input id="sellerPlatformAttachment" type="file" @if($sellerChatBlocked) disabled @endif accept="image/*,.pdf,.txt,.csv,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.mp4,.mov,.mp3,.m4a,.wav" class="hidden">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 12.5 14.5 6a3 3 0 0 1 4.2 4.2l-8 8a5 5 0 0 1-7.1-7.1l8.3-8.3"></path></svg>
                                </label>
                                <span class="seller-composer-attachment-text">Images/files up to 15 MB</span>
                            </div>

                            <button id="sellerPlatformSend" type="submit" @if($sellerChatBlocked) disabled @endif class="disabled:cursor-not-allowed disabled:opacity-50" title="Send message" aria-label="Send message">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 4 17 8-17 8 3-8-3-8Z"></path><path d="M7 12h14"></path></svg>
                            </button>
                        </div>
                    </form>
                    <p id="sellerPlatformError" class="mt-2 hidden text-[10px] text-[#a45f5f]" role="alert" aria-live="assertive"></p>
                </div>
            </div>

            {{-- RIGHT: SELLER CONTEXT --}}
            <aside id="sellerDetailsPane" class="hidden border-l border-[#ebe5dc] bg-white 2xl:block">
                <div class="border-b border-[#eee8df] px-5 py-4">
                    <p class="text-[13px] font-bold text-[#302a24]">Account Details</p>
                    <p class="mt-1 text-[10px] text-[#948b7f]">Your seller support context</p>
                </div>

                <div class="p-5">
                    <div class="flex flex-col items-center text-center">
                        <div class="grid h-16 w-16 place-items-center rounded-full bg-[#f4f6f7] text-[15px] font-bold text-[#607a8f]">{{ strtoupper(substr($seller->store_name ?: 'SS', 0, 2)) }}</div>
                        <p class="mt-3 text-[14px] font-bold text-[#2e2923]">{{ $seller->store_name ?: 'SARI Seller Store' }}</p>
                        <p class="mt-1 text-[11px] text-[#92897e]">{{ $seller->email }}</p>
                        <span class="mt-3 rounded-full border border-[#dce5ed] bg-[#f4f7fa] px-2.5 py-1 text-[10px] font-semibold text-[#617d96]">Seller</span>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4">
                            <p class="text-[10px] text-[#948b7f]">Account Status</p>
                            <p class="mt-2 text-[12px] font-semibold {{ $seller->isSuspended() ? 'text-[#a96565]' : 'text-[#56816a]' }}">{{ ucfirst($seller->account_status ?: 'active') }}</p>
                        </div>

                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4">
                            <p class="text-[10px] text-[#948b7f]">Compliance Warnings</p>
                            <p class="mt-2 text-[16px] font-bold text-[#a8731f]">{{ (int) ($seller->warning_count ?? 0) }} / 3</p>
                        </div>

                        <div class="rounded-[14px] border border-[#e8e2d9] bg-[#fdfcf9] p-4">
                            <p class="text-[10px] text-[#948b7f]">Conversation</p>
                            <p class="mt-2 text-[12px] font-semibold text-[#50483f]"><span id="sellerTotalMessages">0</span> total messages</p>
                        </div>

                        <div class="rounded-[14px] border border-[#dfe7ec] bg-[#f7fafc] p-4">
                            <div class="flex items-start gap-3">
                                <div class="sari-bot-avatar h-9 w-9">
                                    <img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" aria-hidden="true">
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-[#5c7180]">SARI Assistant</p>
                                    <p class="mt-1 text-[10px] leading-5 text-[#81909a]">Confirms receipt of your concern while you wait for a human SARI administrator to review and respond.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
(function () {
    function bootSellerPlatformChat() {
        const root = document.getElementById('sellerPlatformChat');
        if (!root || root.dataset.initialized === '1') return;
        root.dataset.initialized = '1';

        const apiBase = @json(url('/messaging/api'));
        const csrfToken = @json(csrf_token());
        const sellerId = {{ (int) $seller->id }};
        const allowedReactionEmojis = ['👍', '❤️', '😂', '😮', '😢', '🙏'];
        const assistantEnabled = @json((bool) config('sari.assistant.enabled', true));
        const assistantDelayMs = Math.max(0, Number(@json((int) config('sari.assistant.delay_seconds', 0))) * 1000);
        const assistantTypingStartMs = 0; // receipt acknowledgement is generated server-side without an AI-provider wait

        const messagesEl = document.getElementById('sellerPlatformMessages');
        const form = document.getElementById('sellerPlatformMessageForm');
        const input = document.getElementById('sellerPlatformInput');
        const attachment = document.getElementById('sellerPlatformAttachment');
        const attachmentName = document.getElementById('sellerAttachmentName');
        const sendButton = document.getElementById('sellerPlatformSend');
        const errorBox = document.getElementById('sellerPlatformError');
        const statusEl = document.getElementById('sellerRealtimeStatus');
        const previewEl = document.getElementById('sellerLastMessagePreview');
        const totalEl = document.getElementById('sellerTotalMessages');
        const searchInput = document.getElementById('sellerMessageSearch');
        const searchClear = document.getElementById('sellerMessageSearchClear');
        const blockedNotice = document.getElementById('sellerChatBlockedNotice');
        const blockedReason = document.getElementById('sellerChatBlockedReason');
        const assistantBadge = document.getElementById('sellerAssistantBadge');

        let conversationUuid = null;
        let realtimeChannel = null;
        let pollTimer = null;
        let loadingDetail = false;
        let detailReloadPending = false;
        let sendingMessage = false;
        let sellerChatBlocked = @json($sellerChatBlocked);
        let currentMessages = [];
        let assistantTypingTimer = null;
        let assistantTypingExpiryTimer = null;
        let assistantBurstTimer = null;
        let assistantBurstAttempt = 0;
        let supportConnecting = false;
        let assistantRequestController = null;

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const formatTime = (value) => {
            if (!value) return '';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '';
            return new Intl.DateTimeFormat('en-PH', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true,
            }).format(date);
        };

        const formatFileSize = (bytes) => {
            const size = Number(bytes || 0);
            if (!size) return '';
            if (size < 1024) return `${size} B`;
            if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`;
            return `${(size / (1024 * 1024)).toFixed(1)} MB`;
        };

        function setError(message = '') {
            if (!errorBox) return;
            errorBox.textContent = message;
            errorBox.classList.toggle('hidden', !message);
        }


        if (assistantBadge && !assistantEnabled) {
            assistantBadge.dataset.disabled = '1';
            assistantBadge.setAttribute('title', 'Automatic receipt acknowledgement is currently unavailable. Human SARI Admin support remains available.');
            assistantBadge.lastChild.textContent = ' Confirmation off';
        }

        function resizeComposerInput() {
            if (!input) return;
            input.style.height = 'auto';
            input.style.height = `${Math.min(112, Math.max(38, input.scrollHeight))}px`;
        }

        function lockComposer(reason = '') {
            sellerChatBlocked = true;
            if (input) {
                input.disabled = true;
                input.placeholder = 'Messaging is restricted by SARI Admin.';
            }
            if (attachment) attachment.disabled = true;
            if (sendButton) sendButton.disabled = true;
            blockedNotice?.classList.remove('hidden');
            blockedNotice?.classList.add('flex');
            if (blockedReason && reason) blockedReason.textContent = reason;
        }

        function isNearBottom(threshold = 140) {
            if (!messagesEl) return true;
            return messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight <= threshold;
        }

        function scrollBottom(behavior = 'auto') {
            if (!messagesEl) return;
            messagesEl.scrollTo({ top: messagesEl.scrollHeight, behavior });
        }

        function attachmentHtml(message) {
            const file = message?.attachment;
            if (!file) return '';

            if (file.is_image) {
                return `
                    <div class="mt-2 overflow-hidden rounded-[14px] border border-[#e6dfd6] bg-white p-2 shadow-[0_7px_18px_rgba(35,28,20,.05)]">
                        <a href="${escapeHtml(file.url)}" target="_blank" rel="noopener">
                            <img src="${escapeHtml(file.url)}" alt="${escapeHtml(file.name || 'Attachment')}" class="max-h-[300px] w-full rounded-[10px] object-contain">
                        </a>
                    </div>`;
            }

            return `
                <div class="mt-2 overflow-hidden rounded-[14px] border border-[#e6dfd6] bg-white p-2 shadow-[0_7px_18px_rgba(35,28,20,.05)]">
                    <a href="${escapeHtml(file.download_url || file.url)}" class="flex items-center gap-3 rounded-xl bg-[#fbfaf7] p-3">
                        <div class="grid h-9 w-9 place-items-center rounded-lg bg-[#f5efe4] text-[#a8731f]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 3h10l4 4v14H5z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-[11px] font-semibold text-[#50483f]">${escapeHtml(file.name || 'Attachment')}</p>
                            <p class="mt-1 text-[9px] text-[#958c80]">${escapeHtml(formatFileSize(file.size) || 'Download file')}</p>
                        </div>
                    </a>
                </div>`;
        }

        function reactionHtml(message, mine) {
            const reactions = Array.isArray(message.reactions) ? message.reactions : [];
            const chips = reactions.map(reaction => `
                <span class="inline-flex h-7 items-center gap-1 rounded-full border px-2.5 text-[10px] shadow-[0_4px_10px_rgba(35,28,20,.05)] ${reaction.mine ? 'border-[#dfbb74] bg-[#fff8ea] text-[#8d6218]' : 'border-[#e7e0d7] bg-white text-[#6f675d]'}">
                    <span class="text-[13px] leading-none">${escapeHtml(reaction.emoji)}</span>
                    <span class="font-semibold">${Number(reaction.count || 0)}</span>
                </span>`).join('');

            return `
                <div class="mt-2 flex flex-wrap items-center gap-1.5 ${mine ? 'justify-end' : 'justify-start'}">
                    <div class="flex flex-wrap items-center gap-1">${chips}</div>
                    <div class="relative" data-reaction-wrap>
                        <button type="button" data-reaction-toggle class="grid h-7 w-7 place-items-center rounded-full border border-[#e5ded4] bg-white text-[#8c8378] shadow-[0_4px_10px_rgba(35,28,20,.05)] transition hover:border-[#d8bd8a] hover:bg-[#fffaf2] hover:text-[#a8731f]" aria-label="React to message">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8"></circle><path d="M9 10h.01M15 10h.01"></path><path d="M8.5 14c1 1.3 2.1 2 3.5 2s2.5-.7 3.5-2"></path></svg>
                        </button>
                        <div data-reaction-picker class="absolute bottom-9 ${mine ? 'right-0' : 'left-0'} z-30 hidden items-center gap-1 rounded-full border border-[#e5ded4] bg-white p-1.5 shadow-[0_14px_36px_rgba(38,30,20,.14)]">
                            ${allowedReactionEmojis.map(emoji => `<button type="button" data-reaction-emoji="${emoji}" class="grid h-8 w-8 place-items-center rounded-full text-[17px] transition hover:bg-[#f7f2ea]">${emoji}</button>`).join('')}
                        </div>
                    </div>
                    <p class="text-[10px] text-[#968d82]">${escapeHtml(formatTime(message.created_at))}</p>
                </div>`;
        }

        function botAvatarHtml(sizeClass = 'h-8 w-8') {
            return `
                <div class="sari-bot-avatar ${sizeClass}" aria-label="SARI Assistant">
                    <img src="{{ asset('images/sari-seller-ai-assistant.png') }}" alt="" aria-hidden="true">
                </div>`;
        }

        function adminAvatarHtml(sizeClass = 'h-8 w-8') {
            return `
                <div class="sari-admin-avatar ${sizeClass}" aria-label="SARI Admin">
                    <img src="{{ asset('images/sari-main-logo.png') }}" alt="" aria-hidden="true">
                </div>`;
        }

        function hideAssistantTyping() {
            if (assistantTypingTimer) {
                window.clearTimeout(assistantTypingTimer);
                assistantTypingTimer = null;
            }
            if (assistantTypingExpiryTimer) {
                window.clearTimeout(assistantTypingExpiryTimer);
                assistantTypingExpiryTimer = null;
            }
            document.getElementById('sellerAssistantTyping')?.remove();
        }

        function showAssistantTyping() {
            if (!assistantEnabled || !messagesEl || document.getElementById('sellerAssistantTyping')) return;

            messagesEl.insertAdjacentHTML('beforeend', `
                <div id="sellerAssistantTyping" class="sari-assistant-typing" aria-live="polite" aria-label="SARI Assistant is typing">
                    ${botAvatarHtml('h-8 w-8')}
                    <div class="max-w-[72%]">
                        <div class="mb-1.5 text-[9px] font-semibold uppercase tracking-[.08em] text-[#6e899c]">SARI Assistant</div>
                        <div class="sari-assistant-typing__bubble">
                            <div class="sari-assistant-typing__dots" aria-hidden="true"><span></span><span></span><span></span></div>
                        </div>
                    </div>
                </div>`);

            requestAnimationFrame(() => scrollBottom('smooth'));
        }

        function syncAssistantTyping() {
            // Admin Support uses an immediate persisted receipt acknowledgement.
            // Avoid a fake typing delay; the direct endpoint returns the saved
            // acknowledgement while polling/realtime remains a fallback.
            hideAssistantTyping();
        }


        function messageHtml(message) {
            const mine = message.sender_role === 'seller' && Number(message.sender_id) === sellerId;
            const isBot = Boolean(message?.metadata?.ai_assistant);
            const isAcknowledgement = Boolean(
                message?.metadata?.support_acknowledgement
            );
            const isPending = Boolean(message?._optimistic);
            const senderLabel = isBot
                ? (
                    isAcknowledgement
                        ? 'SARI Assistant · acknowledgement'
                        : 'SARI Assistant · previous automated response'
                )
                : (
                    message.sender_role === 'admin'
                        ? 'SARI Admin'
                        : escapeHtml(message.sender || 'Support')
                );

            const body = message.body
                ? `<div class="seller-message-bubble rounded-[14px] px-4 py-3 text-[12px] leading-6 shadow-[0_7px_18px_rgba(35,28,20,.055)] ${mine ? 'rounded-br-[5px] bg-[#c99128] text-white' : (isBot ? 'rounded-bl-[5px] border border-[#dce8ef] bg-[#f4f8fa] text-[#526a79]' : 'rounded-bl-[5px] border border-[#e6dfd6] bg-white text-[#514a42]')}">${escapeHtml(message.body)}</div>`
                : '';

            const pendingAttachment = isPending && message?._optimisticAttachment
                ? `
                    <div class="seller-message-uploading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M8 12.5 14.5 6a3 3 0 0 1 4.2 4.2l-8 8a5 5 0 0 1-7.1-7.1l8.3-8.3"></path>
                        </svg>
                        <span class="min-w-0 truncate">${escapeHtml(message._optimisticAttachment.name || 'Attachment')}</span>
                        <span class="shrink-0 text-[#98a2b3]">Uploading…</span>
                    </div>`
                : '';

            const meta = isPending
                ? `<div class="seller-message-pending">Sending…</div>`
                : reactionHtml(message, mine);

            return `
                <div class="seller-message-row ${mine ? 'flex justify-end' : 'flex items-end gap-2.5'}"
                     data-message-id="${escapeHtml(message.id)}"
                     data-search="${escapeHtml(`${message.body || ''} ${message.attachment?.name || message?._optimisticAttachment?.name || ''} ${senderLabel}`.toLowerCase())}">
                    ${mine ? '' : (isBot ? botAvatarHtml('h-8 w-8') : adminAvatarHtml('h-8 w-8'))}
                    <div class="max-w-[84%] sm:max-w-[70%] lg:max-w-[64%]">
                        ${mine ? '' : `<div class="mb-1.5 text-[9px] font-semibold ${isBot ? 'uppercase tracking-[.08em] text-[#6e899c]' : 'text-[#7c7369]'}">${senderLabel}</div>`}
                        ${body}
                        ${pendingAttachment || attachmentHtml(message)}
                        ${meta}
                    </div>
                </div>`;
        }

        function renderMessages(nextMessages, followLatest = false) {
            const wasNearBottom = isNearBottom();
            currentMessages = Array.isArray(nextMessages) ? nextMessages : [];

            if (!messagesEl) return;

            if (!currentMessages.length) {
                messagesEl.innerHTML = `
                    <div id="sellerEmptyChat" class="flex h-full min-h-[360px] items-center justify-center text-center">
                        <div>
                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-[#f6efe2] text-[#9d6f22]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"></path></svg></div>
                            <p class="mt-4 text-[13px] font-bold text-[#4f473e]">Start a conversation</p>
                            <p class="mt-1 text-[11px] text-[#958c80]">Send a message to SARI Admin below.</p>
                        </div>
                    </div>`;
            } else {
                messagesEl.innerHTML = currentMessages.map(messageHtml).join('');
            }

            if (totalEl) totalEl.textContent = String(currentMessages.length);
            const last = currentMessages[currentMessages.length - 1];
            if (previewEl) previewEl.textContent = last?.body || last?.attachment?.name || 'No messages yet.';

            applySearch();
            syncAssistantTyping();

            if (followLatest || wasNearBottom) {
                requestAnimationFrame(() => scrollBottom(followLatest ? 'auto' : 'smooth'));
            }
        }

        function upsertMessage(message, followLatest = true) {
            if (!message?.id) return;

            const id = Number(message.id);
            const next = [...currentMessages];
            const index = next.findIndex(row => Number(row.id) === id);

            if (index >= 0) {
                next[index] = { ...next[index], ...message };
            } else {
                next.push(message);
            }

            next.sort((a, b) => Number(a.id || 0) - Number(b.id || 0));
            renderMessages(next, followLatest);
        }

        function removeOptimisticMessage(token) {
            if (!token) return;

            renderMessages(
                currentMessages.filter(
                    (message) => message?._optimisticToken !== token
                ),
                true
            );
        }

        function makeOptimisticMessage(body, file) {
            const maxId = currentMessages.reduce(
                (max, message) => Math.max(max, Number(message?.id || 0)),
                0
            );
            const token = `seller-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;

            return {
                id: maxId + 0.5,
                sender_role: 'seller',
                sender_id: sellerId,
                sender: 'You',
                message_type: file ? 'attachment' : 'text',
                body: body || null,
                attachment: null,
                reactions: [],
                metadata: {},
                created_at: new Date().toISOString(),
                _optimistic: true,
                _optimisticToken: token,
                _optimisticAttachment: file
                    ? {
                        name: file.name || 'Attachment',
                        size: Number(file.size || 0),
                    }
                    : null,
            };
        }

        function applySearch() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            messagesEl?.querySelectorAll('[data-message-id]').forEach(row => {
                const haystack = String(row.dataset.search || '').toLowerCase();
                row.classList.toggle('hidden', query !== '' && !haystack.includes(query));
            });
            if (searchClear) {
                searchClear.classList.toggle('hidden', query === '');
                searchClear.classList.toggle('grid', query !== '');
            }
        }

        async function parseResponse(response) {
            const raw = await response.text();
            try { return raw ? JSON.parse(raw) : {}; }
            catch (_) { return {}; }
        }

        function setRealtimeBadge(mode = 'connecting') {
            if (!statusEl) return;

            const labels = {
                live: 'Live',
                syncing: 'Syncing',
                fallback: 'Polling',
                offline: 'Offline',
                unavailable: 'Unavailable',
                connecting: 'Connecting',
            };

            statusEl.dataset.state = mode;
            statusEl.textContent = labels[mode] || labels.connecting;
        }

        function setSupportConnectionState(mode = 'connecting') {
            const connected = mode === 'connected';
            const unavailable = mode === 'unavailable';

            if (unavailable) {
                setRealtimeBadge('unavailable');
            } else if (!connected) {
                setRealtimeBadge('connecting');
            } else if (!window.Echo) {
                setRealtimeBadge('fallback');
            } else if (statusEl?.dataset.state !== 'live') {
                setRealtimeBadge('syncing');
                window.setTimeout(() => {
                    if (statusEl?.dataset.state === 'syncing') {
                        setRealtimeBadge('fallback');
                    }
                }, 1800);
            }

            if (!sellerChatBlocked && sendButton) {
                sendButton.disabled = !connected || sendingMessage;
            }

            if (!sellerChatBlocked && input) {
                input.disabled = !connected;
                input.placeholder = connected
                    ? 'Write a message to SARI Admin…'
                    : 'Connecting to SARI Admin…';
            }

            if (!sellerChatBlocked && attachment) {
                attachment.disabled = !connected;
            }
        }

        async function recoverExistingSupportConversation() {
            const response = await fetch(`${apiBase}/conversations`, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await parseResponse(response);
            if (!response.ok) return false;

            const rows = Array.isArray(data?.conversations) ? data.conversations : [];
            const support = rows.find(row => (
                row?.type === 'admin_support'
                && row?.uuid
                && (row.participants || []).some(participant => (
                    participant.role === 'seller'
                    && Number(participant.id) === sellerId
                ))
            ));

            if (!support?.uuid) return false;

            conversationUuid = String(support.uuid).trim();
            return conversationUuid !== '';
        }

        async function ensureSupportConversation() {
            if (conversationUuid) return true;
            if (supportConnecting) return false;

            supportConnecting = true;
            setSupportConnectionState('connecting');

            try {
                if (await recoverExistingSupportConversation()) {
                    setSupportConnectionState('connected');
                    return true;
                }

                await openSupportConversation();
                setSupportConnectionState('connected');
                return Boolean(conversationUuid);
            } finally {
                supportConnecting = false;
            }
        }

        async function openSupportConversation() {
            const controller = new AbortController();
            const timeout = window.setTimeout(() => controller.abort(), 8000);

            try {
                const response = await fetch(`${apiBase}/support`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    signal: controller.signal,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await parseResponse(response);
                if (!response.ok) throw new Error(data.message || 'Unable to open SARI Support.');
                conversationUuid = String(data?.conversation?.uuid || '').trim();
                if (!conversationUuid) throw new Error('Support conversation is unavailable.');
            } catch (error) {
                if (await recoverExistingSupportConversation()) return;
                if (error?.name === 'AbortError') {
                    throw new Error('SARI Support took too long to connect. Please try again.');
                }
                throw error;
            } finally {
                window.clearTimeout(timeout);
            }
        }

        async function markRead() {
            if (!conversationUuid) return;
            fetch(`${apiBase}/conversations/${encodeURIComponent(conversationUuid)}/read`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).catch(() => {});
        }

        function subscribeRealtime(channelName) {
            if (!channelName || realtimeChannel === channelName) return;

            if (realtimeChannel && window.Echo) {
                try { window.Echo.leave(realtimeChannel); } catch (_) {}
            }

            realtimeChannel = channelName;

            if (!window.Echo) {
                setRealtimeBadge('fallback');
                return;
            }

            try {
                const channel = window.Echo.channel(channelName);
                const refresh = (event = {}) => {
                    if (event?.conversation_uuid && String(event.conversation_uuid) !== String(conversationUuid)) {
                        return;
                    }
                    loadDetail(false);
                };

                channel
                    .listen('.platform.message', refresh)
                    .listen('.platform.reaction', refresh);

                if (typeof channel.subscribed === 'function') {
                    channel.subscribed(() => setRealtimeBadge('live'));
                } else {
                    setRealtimeBadge('live');
                }

                if (typeof channel.error === 'function') {
                    channel.error(() => setRealtimeBadge('fallback'));
                }
            } catch (_) {
                setRealtimeBadge('fallback');
            }
        }

        async function loadDetail(initial = false) {
            if (!conversationUuid) return;

            if (loadingDetail) {
                detailReloadPending = true;
                return;
            }

            loadingDetail = true;

            try {
                const response = await fetch(`${apiBase}/conversations/${encodeURIComponent(conversationUuid)}`, {
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await parseResponse(response);
                if (!response.ok) throw new Error(data.message || 'Unable to load messages.');

                renderMessages(data.messages || [], initial);
                subscribeRealtime(data?.conversation?.realtime_channel || null);
                markRead();
                setSupportConnectionState('connected');
                setError('');
            } catch (error) {
                setError(error.message || 'Unable to load messages.');
                if (!navigator.onLine) setRealtimeBadge('offline');
            } finally {
                loadingDetail = false;
                root.dataset.ready = '1';

                if (detailReloadPending) {
                    detailReloadPending = false;
                    queueMicrotask(() => loadDetail(false));
                }
            }
        }

        async function requestAssistantReply(triggerMessage) {
            if (
                !assistantEnabled
                || !conversationUuid
                || !triggerMessage?.id
            ) {
                hideAssistantTyping();
                return null;
            }

            assistantRequestController?.abort();
            assistantRequestController = new AbortController();
            const controller = assistantRequestController;
            const timeout = window.setTimeout(() => controller.abort(), 5000);

            try {
                const response = await fetch(
                    `${apiBase}/conversations/${encodeURIComponent(conversationUuid)}/assistant-reply`,
                    {
                        method: 'POST',
                        credentials: 'same-origin',
                        signal: controller.signal,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            trigger_message_id: Number(triggerMessage.id),
                        }),
                    }
                );

                const data = await parseResponse(response);

                if (!response.ok) {
                    const validationMessage = Object.values(data.errors || {})?.[0]?.[0];
                    throw new Error(data.message || validationMessage || 'SARI Assistant could not respond.');
                }

                if (data?.message) {
                    upsertMessage(data.message, true);
                    setError('');
                    return data.message;
                }

                await loadDetail(false);
                return null;
            } catch (error) {
                if (error?.name !== 'AbortError') {
                    console.warn('SARI Assistant reply request failed:', error);
                }

                hideAssistantTyping();
                startAssistantBurstRefresh();

                if (error?.name === 'AbortError') {
                    setError('Your message was sent to SARI Admin Support. The automatic receipt confirmation is taking longer than expected, but a human administrator can still reply in this conversation.');
                }

                return null;
            } finally {
                window.clearTimeout(timeout);

                if (assistantRequestController === controller) {
                    assistantRequestController = null;
                }

                hideAssistantTyping();
            }
        }

        function stopAssistantBurstRefresh() {
            if (assistantBurstTimer) {
                window.clearTimeout(assistantBurstTimer);
                assistantBurstTimer = null;
            }
            assistantBurstAttempt = 0;
        }

        function startAssistantBurstRefresh() {
            stopAssistantBurstRefresh();
            if (!assistantEnabled || !conversationUuid) return;

            const delays = [120, 280, 450, 700, 1000, 1400, 2000, 2800, 3800, 5000];

            const tick = async () => {
                assistantBurstAttempt += 1;
                await loadDetail(false);

                const latest = currentMessages[currentMessages.length - 1];
                const receivedReply = latest && latest.sender_role === 'admin';

                if (receivedReply || assistantBurstAttempt >= delays.length) {
                    stopAssistantBurstRefresh();
                    return;
                }

                assistantBurstTimer = window.setTimeout(
                    tick,
                    delays[Math.min(assistantBurstAttempt, delays.length - 1)]
                );
            };

            assistantBurstTimer = window.setTimeout(tick, delays[0]);
        }

        attachment?.addEventListener('change', function () {
            const file = this.files?.[0];
            if (file && file.size > 15 * 1024 * 1024) {
                this.value = '';
                attachmentName?.classList.add('hidden');
                setError('Attachments must be 15 MB or smaller.');
                return;
            }
            if (attachmentName) {
                attachmentName.textContent = file ? `Attached: ${file.name} · ${formatFileSize(file.size)}` : '';
                attachmentName.classList.toggle('hidden', !file);
            }
        });

        form?.addEventListener('submit', async function (event) {
            event.preventDefault();
            setError('');

            if (sendingMessage) return;

            if (sellerChatBlocked) {
                lockComposer(blockedReason?.textContent || 'Messaging is restricted by SARI Admin.');
                return;
            }

            const body = (input?.value || '').trim();
            const file = attachment?.files?.[0] || null;

            if (!body && !file) {
                setError('Write a message or attach a file.');
                input?.focus();
                return;
            }

            if (!conversationUuid) {
                try {
                    const connected = await ensureSupportConversation();
                    if (!connected || !conversationUuid) {
                        setError('Unable to connect to SARI Support. Please try again.');
                        return;
                    }
                    await loadDetail(false);
                } catch (error) {
                    setSupportConnectionState('unavailable');
                    setError(error.message || 'Unable to connect to SARI Support.');
                    return;
                }
            }

            sendingMessage = true;
            if (sendButton) sendButton.disabled = true;

            const submittedBody = body;
            const optimisticMessage = makeOptimisticMessage(submittedBody, file);
            const optimisticToken = optimisticMessage._optimisticToken;

            // Show the outgoing message immediately. The API response replaces
            // this temporary row with the authoritative saved message.
            upsertMessage(optimisticMessage, true);

            if (input && submittedBody) {
                input.value = '';
                resizeComposerInput();
            }

            try {
                const payload = new FormData();
                if (submittedBody) payload.append('body', submittedBody);
                if (file) payload.append('attachment', file);

                const response = await fetch(`${apiBase}/conversations/${encodeURIComponent(conversationUuid)}/messages`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: payload,
                });
                const data = await parseResponse(response);

                if (!response.ok) {
                    if (response.status === 423) {
                        lockComposer(data.message || 'Messaging is restricted by SARI Admin.');
                    }
                    const validationMessage = Object.values(data.errors || {})?.[0]?.[0];
                    throw new Error(data.message || validationMessage || 'Unable to send message.');
                }

                removeOptimisticMessage(optimisticToken);

                if (data?.message) {
                    upsertMessage(data.message, true);
                } else {
                    loadDetail(false);
                }

                if (attachment) attachment.value = '';
                attachmentName?.classList.add('hidden');
                if (attachmentName) attachmentName.textContent = '';

                /*
                | The acknowledgement is created by the same server-side send
                | flow. Reconcile once; no second assistant endpoint is needed.
                */
                await loadDetail(false);
                input?.focus();
            } catch (error) {
                removeOptimisticMessage(optimisticToken);

                if (submittedBody && input) {
                    const newerDraft = input.value.trim();
                    input.value = newerDraft ? `${submittedBody}\n${newerDraft}` : submittedBody;
                    resizeComposerInput();
                }

                setError(error.message || 'Unable to send message.');
            } finally {
                sendingMessage = false;
                if (!sellerChatBlocked && sendButton) sendButton.disabled = false;
            }
        });

        messagesEl?.addEventListener('click', async function (event) {
            const toggle = event.target.closest('[data-reaction-toggle]');
            if (toggle) {
                event.preventDefault();
                const wrap = toggle.closest('[data-reaction-wrap]');
                const picker = wrap?.querySelector('[data-reaction-picker]');
                messagesEl.querySelectorAll('[data-reaction-picker]').forEach(item => {
                    if (item !== picker) {
                        item.classList.add('hidden');
                        item.classList.remove('flex');
                    }
                });
                if (picker) {
                    const opening = picker.classList.contains('hidden');
                    picker.classList.toggle('hidden', !opening);
                    picker.classList.toggle('flex', opening);
                }
                return;
            }

            const reactionButton = event.target.closest('[data-reaction-emoji]');
            if (!reactionButton) return;
            const row = reactionButton.closest('[data-message-id]');
            const messageId = Number(row?.dataset.messageId || 0);
            const emoji = reactionButton.dataset.reactionEmoji;
            if (!messageId || !allowedReactionEmojis.includes(emoji)) return;

            reactionButton.disabled = true;
            try {
                const response = await fetch(`${apiBase}/messages/${messageId}/reaction`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ emoji }),
                });
                const data = await parseResponse(response);
                if (!response.ok) throw new Error(data.message || 'Unable to save reaction.');
                await loadDetail(false);
            } catch (error) {
                setError(error.message || 'Unable to save reaction.');
            } finally {
                reactionButton.disabled = false;
            }
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('[data-reaction-wrap]')) {
                messagesEl?.querySelectorAll('[data-reaction-picker]').forEach(item => {
                    item.classList.add('hidden');
                    item.classList.remove('flex');
                });
            }
        });

        searchInput?.addEventListener('input', applySearch);
        searchClear?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            applySearch();
            searchInput?.focus();
        });

        input?.addEventListener('input', function () {
            resizeComposerInput();
            if (errorBox && !errorBox.classList.contains('hidden')) setError('');
        });

        input?.addEventListener('keydown', function (event) {
            if (event.isComposing) return;
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                form?.requestSubmit();
            }
        });

        window.addEventListener('sari:realtime-state', (event) => {
            const state = String(event?.detail?.state || '');

            if (state === 'live') {
                setRealtimeBadge('live');
                loadDetail(false);
            } else if (state === 'connecting') {
                if (statusEl?.dataset.state !== 'live') {
                    setRealtimeBadge('connecting');
                }
            } else if (state === 'fallback') {
                setRealtimeBadge('fallback');
            }
        });

        window.addEventListener('offline', () => setRealtimeBadge('offline'));
        window.addEventListener('online', () => {
            setRealtimeBadge(window.Echo ? 'syncing' : 'fallback');
            loadDetail(false);
        });

        resizeComposerInput();

        async function start() {
            setSupportConnectionState('connecting');

            try {
                const connected = await ensureSupportConversation();
                if (!connected || !conversationUuid) {
                    throw new Error('Unable to establish the SARI Support conversation.');
                }

                await loadDetail(true);
                setSupportConnectionState('connected');
                pollTimer = window.setInterval(() => loadDetail(false), 3000);
            } catch (error) {
                root.dataset.ready = '1';
                setSupportConnectionState('unavailable');
                setError(error.message || 'Unable to connect to SARI Support.');
            }
        }

        start();

        const cleanup = function () {
            hideAssistantTyping();
            stopAssistantBurstRefresh();

            if (assistantRequestController) {
                assistantRequestController.abort();
                assistantRequestController = null;
            }

            if (pollTimer) window.clearInterval(pollTimer);
            if (realtimeChannel && window.Echo) {
                try { window.Echo.leave(realtimeChannel); } catch (_) {}
            }
            root.dataset.initialized = '0';
        };

        document.addEventListener('livewire:navigating', cleanup, { once: true });
        window.addEventListener('beforeunload', cleanup, { once: true });
    }

    /* Livewire evaluates this body script when the page arrives. Boot once here;
       bootSellerPlatformChat registers its own one-shot navigation cleanup. */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootSellerPlatformChat, { once: true });
    } else {
        bootSellerPlatformChat();
    }
})();
</script>
@endpush
