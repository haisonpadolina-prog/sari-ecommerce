@extends('layouts.admin')

@section('title', 'Messages — SARI Admin')
@section('page-title', 'Messages')

@section('content')
<div id="sariUniversalAdminMessages" class="mx-auto w-full max-w-[1840px]">
    <style>
        #sariUniversalAdminMessages {
            --sari-gold: #D29A28;
            --sari-gold-deep: #A97012;
            --sari-ink: #28231d;
            --sari-muted: #81796f;
            --sari-line: #e8e2d9;
            --sari-soft: #faf9f6;
            --sari-soft-2: #f5f3ef;
            --sari-green: #6F826A;
            --sari-teal: #5F7873;
            --sari-plum: #806F7F;
            --sari-red: #B86556;
            --sari-ivory: #FFF7E7;
            font-size: 14px;
        }

        #sariUniversalAdminMessages * {
            scrollbar-width: thin;
            scrollbar-color: #d9d2c8 transparent;
        }

        #sariUniversalAdminMessages .sari-shadow {
            box-shadow: 0 18px 48px rgba(42, 34, 24, 0.07);
        }

        #sariUniversalAdminMessages .sari-soft-shadow {
            box-shadow: 0 8px 24px rgba(42, 34, 24, 0.05);
        }

        #sariUniversalAdminMessages .sari-scroll::-webkit-scrollbar {
            width: 8px;
        }

        #sariUniversalAdminMessages .sari-scroll::-webkit-scrollbar-thumb {
            background: #d9d2c8;
            border-radius: 999px;
        }

        #sariUniversalAdminMessages .sari-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        #sariUniversalAdminMessages .sari-message-body {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        #sariUniversalAdminMessages .sari-role-admin { background:#fff4dd; color:#9a6815; border-color:#ecd5a7; }
        #sariUniversalAdminMessages .sari-role-buyer,
        #sariUniversalAdminMessages .sari-role-social_buyer { background:#f3f7fa; color:#5f7687; border-color:#dce6ed; }
        #sariUniversalAdminMessages .sari-role-seller { background:#fff7ea; color:#986c20; border-color:#edd9b5; }
        #sariUniversalAdminMessages .sari-role-logistics { background:#f2f7f6; color:#55736d; border-color:#d9e6e2; }
        #sariUniversalAdminMessages .sari-role-rider { background:#f5f4f8; color:#74657f; border-color:#e2ddea; }

        @media (max-width: 1279px) {
            #sariUniversalAdminMessages .sari-context-panel {
                display: none;
            }
        }

        @media (max-width: 1023px) {
            #sariUniversalAdminMessages .sari-message-grid {
                grid-template-columns: 1fr !important;
            }

            #sariUniversalAdminMessages .sari-inbox-panel {
                display: none;
            }

            #sariUniversalAdminMessages[data-mobile-pane="inbox"] .sari-inbox-panel {
                display: flex;
            }

            #sariUniversalAdminMessages[data-mobile-pane="inbox"] .sari-thread-panel {
                display: none;
            }
        }
    
        /* ============================================================
           SARI ADMIN MESSAGES — ENTERPRISE REFINEMENT
           Compact • clean • professional • Poppins-first
           Frontend presentation only; messaging contracts unchanged.
           ============================================================ */

        #sariUniversalAdminMessages{
            max-width:1640px !important;
            margin-inline:auto !important;
            font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif !important;
            color:#2b2722 !important;
        }

        #sariUniversalAdminMessages .sari-shadow{
            box-shadow:0 8px 26px rgba(48,38,26,.055) !important;
        }

        #sariUniversalAdminMessages .sari-soft-shadow{
            box-shadow:0 4px 14px rgba(48,38,26,.045) !important;
        }

        #adminMessagingNotice{
            margin-bottom:10px !important;
            border-radius:10px !important;
            padding:8px 10px !important;
            font-size:8px !important;
            line-height:1.45 !important;
            box-shadow:none !important;
        }

        /* ---------------- MAIN CHAT WORKSPACE ---------------- */
        #sariUniversalAdminMessages > section.sari-shadow{
            overflow:hidden !important;
            border-color:#e8e1d8 !important;
            border-radius:15px !important;
            background:#fff !important;
        }

        #sariUniversalAdminMessages .sari-message-grid{
            min-height:620px !important;
            height:clamp(620px,calc(100vh - 190px),760px) !important;
            grid-template-columns:292px minmax(0,1fr) 258px !important;
            background:#fff !important;
        }

        /* ---------------- INBOX ---------------- */
        #sariUniversalAdminMessages .sari-inbox-panel{
            border-color:#ece6de !important;
            background:#faf9f6 !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel > div:first-child{
            padding:13px !important;
            border-color:#ece6de !important;
            background:#fbfaf8 !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel > div:first-child > div:first-child{
            gap:10px !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel p.text-\[10px\].font-bold.uppercase{
            font-size:6.8px !important;
            letter-spacing:.12em !important;
            color:#9a7b43 !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel h2{
            margin-top:3px !important;
            font-size:16px !important;
            line-height:1.15 !important;
            letter-spacing:-.025em !important;
            color:#2b2621 !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel h2 + p{
            margin-top:3px !important;
            font-size:7.3px !important;
            line-height:1.45 !important;
            color:#91887d !important;
        }

        #adminNewConversationButton{
            width:32px !important;
            height:32px !important;
            border-radius:9px !important;
            border-color:#e6ddd1 !important;
            color:#a97012 !important;
            box-shadow:none !important;
        }

        #adminNewConversationButton:hover{
            border-color:#d7b978 !important;
            background:#fff8e9 !important;
            transform:translateY(-1px);
        }

        #adminNewConversationButton svg{
            width:14px !important;
            height:14px !important;
        }

        #adminConversationSearch{
            height:38px !important;
            border-radius:9px !important;
            padding-left:34px !important;
            padding-right:10px !important;
            font-size:8px !important;
            box-shadow:none !important;
        }

        #adminConversationSearch:focus{
            border-color:#d49a2b !important;
            box-shadow:0 0 0 3px rgba(217,149,0,.075) !important;
        }

        #adminConversationSearch + *{
            font-size:8px !important;
        }

        #adminConversationFilters{
            margin-top:8px !important;
            gap:5px !important;
        }

        #adminConversationFilters [data-role-filter]{
            min-height:24px !important;
            border-radius:999px !important;
            padding:0 8px !important;
            font-size:6.8px !important;
            line-height:1 !important;
        }

        #adminConversationList{
            background:#faf9f6 !important;
        }

        #adminConversationList [data-contact-role]{
            min-height:72px !important;
            border-color:#eee8df !important;
            padding:10px 12px !important;
            box-shadow:none !important;
        }

        #adminConversationList [data-contact-role]:hover{
            background:#fff !important;
        }

        #adminConversationList [data-contact-role].bg-\[\#fff8ea\]{
            background:#fffaf0 !important;
        }

        #adminConversationList [data-contact-role] > div{
            gap:9px !important;
        }

        #adminConversationList [data-contact-role] > div > div:first-child{
            width:34px !important;
            height:34px !important;
            flex-basis:34px !important;
            font-size:8px !important;
        }

        #adminConversationList [data-contact-role] p{
            line-height:1.35 !important;
        }

        #adminConversationList [data-contact-role] p.text-\[11px\]{
            font-size:8px !important;
        }

        #adminConversationList [data-contact-role] p.text-\[10px\]{
            margin-top:5px !important;
            font-size:7px !important;
        }

        #adminConversationList [data-contact-role] span.text-\[9px\]{
            font-size:6.4px !important;
        }

        #adminConversationList [data-contact-role] .rounded-full.border{
            padding:1px 6px !important;
            font-size:6.2px !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel > div:last-child{
            padding:8px 12px !important;
            border-color:#ece6de !important;
            background:#fbfaf8 !important;
        }

        #adminMessagingRefreshStatus,
        #adminRefreshInbox{
            font-size:6.8px !important;
        }

        /* ---------------- THREAD ---------------- */
        #sariUniversalAdminMessages .sari-thread-panel{
            background:#fff !important;
        }

        #sariUniversalAdminMessages .sari-thread-panel > header{
            min-height:58px !important;
            padding:9px 13px !important;
            border-color:#ece6de !important;
            background:#fff !important;
        }

        #sariUniversalAdminMessages .sari-thread-panel > header > div:first-child{
            gap:9px !important;
        }

        #adminMobileBack{
            width:30px !important;
            height:30px !important;
            border-radius:8px !important;
        }

        #adminThreadAvatar{
            width:36px !important;
            height:36px !important;
            flex-basis:36px !important;
            font-size:8px !important;
            background:#f2f4f3 !important;
            color:#65766f !important;
        }

        #adminThreadTitle{
            font-size:10px !important;
            line-height:1.35 !important;
            letter-spacing:-.01em !important;
        }

        #adminThreadSubtitle{
            font-size:6.8px !important;
            color:#958d82 !important;
        }

        #adminThreadTypeBadge,
        #adminThreadStatus{
            min-height:20px !important;
            padding:0 6px !important;
            font-size:6.3px !important;
            line-height:1 !important;
        }

        #adminThreadMessages{
            background:#faf9f6 !important;
            padding:16px 18px !important;
        }

        #adminThreadMessages > .flex.h-full{
            min-height:320px !important;
        }

        #adminThreadMessages > .flex.h-full > div{
            max-width:300px !important;
        }

        #adminThreadMessages > .flex.h-full .mx-auto.grid{
            width:46px !important;
            height:46px !important;
            border-radius:13px !important;
        }

        #adminThreadMessages > .flex.h-full .mx-auto.grid svg{
            width:20px !important;
            height:20px !important;
        }

        #adminThreadMessages > .flex.h-full p.font-bold{
            margin-top:11px !important;
            font-size:9px !important;
        }

        #adminThreadMessages > .flex.h-full p.leading-5,
        #adminThreadMessages > .flex.h-full p.text-\[10px\]{
            font-size:7px !important;
            line-height:1.5 !important;
        }

        #adminThreadMessages [data-message-id]{
            margin-bottom:13px !important;
        }

        #adminThreadMessages [data-message-id].flex.items-end{
            gap:7px !important;
        }

        #adminThreadMessages [data-message-id] > div:first-child.grid{
            width:26px !important;
            height:26px !important;
            flex-basis:26px !important;
            font-size:6px !important;
        }

        #adminThreadMessages [data-message-id] > div:last-child{
            max-width:70% !important;
        }

        #adminThreadMessages [data-message-id] .sari-message-body{
            border-radius:12px !important;
            padding:8px 10px !important;
            font-size:8px !important;
            line-height:1.55 !important;
            box-shadow:0 3px 10px rgba(31,27,22,.035) !important;
        }

        #adminThreadMessages [data-message-id] .rounded-br-\[5px\]{
            border-bottom-right-radius:4px !important;
        }

        #adminThreadMessages [data-message-id] .rounded-bl-\[5px\]{
            border-bottom-left-radius:4px !important;
        }

        #adminThreadMessages [data-message-id] .mb-1\.5{
            margin-bottom:4px !important;
        }

        #adminThreadMessages [data-message-id] .text-\[9px\]{
            font-size:6.3px !important;
        }

        #adminThreadMessages [data-message-id] p.mt-1\.5{
            margin-top:4px !important;
        }

        #adminComposerWrap{
            padding:10px 12px !important;
            border-color:#ece6de !important;
            background:#fff !important;
        }

        #adminUniversalMessageForm{
            border-radius:12px !important;
            padding:9px !important;
            box-shadow:none !important;
        }

        #adminUniversalMessageForm:focus-within{
            border-color:#d49a2b !important;
            box-shadow:0 0 0 3px rgba(217,149,0,.07) !important;
        }

        #adminUniversalMessageInput{
            min-height:52px !important;
            padding:2px 3px !important;
            font-size:8px !important;
            line-height:1.55 !important;
        }

        #adminUniversalMessageForm > div.mt-2{
            margin-top:6px !important;
            padding-top:7px !important;
        }

        #adminUniversalMessageForm p{
            font-size:6.5px !important;
            line-height:1.45 !important;
        }

        #adminUniversalSendButton{
            height:34px !important;
            min-width:76px !important;
            border-radius:8px !important;
            padding:0 11px !important;
            font-size:7.5px !important;
            box-shadow:0 4px 10px rgba(166,112,18,.11) !important;
        }

        #adminUniversalSendButton svg{
            width:12px !important;
            height:12px !important;
        }

        #adminComposerError{
            margin-top:5px !important;
            font-size:6.8px !important;
        }

        /* ---------------- CONTEXT RAIL ---------------- */
        #sariUniversalAdminMessages .sari-context-panel{
            border-color:#ece6de !important;
            background:#faf9f6 !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:first-child{
            padding:12px !important;
            border-color:#ece6de !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:first-child p:first-child{
            font-size:9px !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:first-child p:last-child{
            margin-top:3px !important;
            font-size:6.8px !important;
            line-height:1.45 !important;
        }

        #adminConversationContext{
            max-height:none !important;
            padding:12px !important;
        }

        #adminConversationContext > div,
        #adminConversationContext .rounded-\[16px\]{
            border-radius:10px !important;
            padding:10px !important;
            box-shadow:none !important;
        }

        #adminConversationContext p,
        #adminConversationContext dt,
        #adminConversationContext dd{
            line-height:1.45 !important;
        }

        #adminConversationContext .text-\[12px\]{
            font-size:8.5px !important;
        }

        #adminConversationContext .text-\[11px\],
        #adminConversationContext .text-\[10px\]{
            font-size:7px !important;
        }

        #adminConversationContext .text-\[9px\]{
            font-size:6.3px !important;
        }

        #adminConversationContext .mt-4{
            margin-top:9px !important;
        }

        #adminConversationContext .mt-3{
            margin-top:7px !important;
        }

        #adminConversationContext .space-y-3 > :not([hidden]) ~ :not([hidden]){
            margin-top:7px !important;
        }

        #adminConversationContext .space-y-2 > :not([hidden]) ~ :not([hidden]){
            margin-top:6px !important;
        }

        #adminConversationContext .h-9.w-9{
            width:30px !important;
            height:30px !important;
            flex-basis:30px !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:last-child{
            padding:12px !important;
            border-color:#ece6de !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:last-child > div{
            border-radius:10px !important;
            padding:10px !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:last-child p:first-child{
            font-size:6.5px !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:last-child p:last-child{
            margin-top:5px !important;
            font-size:6.8px !important;
            line-height:1.55 !important;
        }

        /* ---------------- LAPTOP ---------------- */
        @media(max-height:850px) and (min-width:1024px){
            #sariUniversalAdminMessages .sari-message-grid{
                min-height:560px !important;
                height:calc(100vh - 165px) !important;
                max-height:690px !important;
            }

            #adminThreadMessages{
                padding-top:13px !important;
                padding-bottom:13px !important;
            }
        }

        @media(max-width:1350px){
            #sariUniversalAdminMessages .sari-message-grid{
                grid-template-columns:270px minmax(0,1fr) 235px !important;
            }
        }

        @media(max-width:1279px){
            #sariUniversalAdminMessages .sari-message-grid{
                grid-template-columns:280px minmax(0,1fr) !important;
            }
        }

        @media(max-width:1023px){
            #sariUniversalAdminMessages .sari-message-grid{
                min-height:560px !important;
                height:calc(100vh - 150px) !important;
                grid-template-columns:1fr !important;
            }

            #adminThreadMessages [data-message-id] > div:last-child{
                max-width:82% !important;
            }
        }

        @media(max-width:640px){
            #sariUniversalAdminMessages > section.sari-shadow{
                border-radius:12px !important;
            }

            #adminThreadMessages{
                padding:12px !important;
            }

            #adminComposerWrap{
                padding:8px !important;
            }

        }

        @media(prefers-reduced-motion:reduce){
            #sariUniversalAdminMessages *{
                animation:none !important;
                transition:none !important;
                transform:none !important;
            }
        }


        /* ---------------- ACCOUNT DIRECTORY ---------------- */
        #sariUniversalAdminMessages .sari-message-grid{
            grid-template-columns:310px minmax(0,1fr) 258px !important;
        }

        #adminConversationFilters{
            row-gap:6px !important;
        }

        #adminConversationFilters [data-role-filter]{
            min-height:25px !important;
            padding:0 9px !important;
            border-radius:999px !important;
            font-size:6.8px !important;
            line-height:1 !important;
        }

        #adminConversationList [data-contact-role]{
            display:block !important;
            width:100% !important;
            min-height:70px !important;
            border-left:2px solid transparent !important;
            border-bottom:1px solid #eee8df !important;
            padding:10px 12px !important;
            background:transparent !important;
            text-align:left !important;
            box-shadow:none !important;
            transition:background-color .12s ease,border-color .12s ease !important;
        }

        #adminConversationList [data-contact-role]:hover{
            background:#fff !important;
        }

        #adminConversationList [data-contact-role].is-selected{
            border-left-color:#d29a28 !important;
            background:#fffaf0 !important;
        }

        #adminConversationList .account-directory-avatar{
            display:grid;
            width:34px;
            height:34px;
            flex:0 0 34px;
            place-items:center;
            border-radius:50%;
            background:#f0f3f4;
            color:#657985;
            font-size:8px;
            font-weight:700;
        }

        #adminConversationList .account-directory-name{
            overflow:hidden;
            color:#332d27;
            font-size:8.3px;
            font-weight:700;
            line-height:1.35;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        #adminConversationList .account-directory-meta{
            overflow:hidden;
            margin-top:4px;
            color:#91887d;
            font-size:6.7px;
            line-height:1.35;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        #adminConversationList .account-directory-preview{
            overflow:hidden;
            margin-top:5px;
            color:#9a9186;
            font-size:6.5px;
            line-height:1.35;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        #adminConversationList .account-directory-time{
            color:#9b9286;
            font-size:6.2px;
            white-space:nowrap;
        }

        #adminConversationList .account-directory-unread{
            display:grid;
            min-width:18px;
            min-height:18px;
            place-items:center;
            border-radius:999px;
            background:#d29a28;
            padding:0 5px;
            color:#fff;
            font-size:6.2px;
            font-weight:700;
        }

        @media(max-width:1350px){
            #sariUniversalAdminMessages .sari-message-grid{
                grid-template-columns:285px minmax(0,1fr) 235px !important;
            }
        }

        @media(max-width:1279px){
            #sariUniversalAdminMessages .sari-message-grid{
                grid-template-columns:285px minmax(0,1fr) !important;
            }
        }


        /* ============================================================
           SARI ADMIN MESSAGES — ENTERPRISE MESSENGER WORKSPACE
           Clean directory • segmented role tabs • modern chat surface
           Presentation only; messaging API contracts remain unchanged.
           ============================================================ */

        #sariUniversalAdminMessages{
            max-width:1660px !important;
        }

        #sariUniversalAdminMessages > section.sari-shadow{
            border:1px solid #e6e1da !important;
            border-radius:16px !important;
            background:#fff !important;
            box-shadow:0 10px 28px rgba(42,34,24,.055) !important;
        }

        #sariUniversalAdminMessages .sari-message-grid{
            min-height:640px !important;
            height:clamp(640px,calc(100vh - 176px),780px) !important;
            grid-template-columns:320px minmax(0,1fr) 246px !important;
            background:#fff !important;
        }

        /* LEFT: messenger-style people directory */
        #sariUniversalAdminMessages .sari-inbox-panel{
            border-right:1px solid #ebe7e0 !important;
            background:#fff !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel > div:first-child{
            padding:16px 14px 13px !important;
            border-bottom:1px solid #eeeae4 !important;
            background:#fff !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel p.text-\[10px\].font-bold.uppercase{
            font-size:7px !important;
            font-weight:700 !important;
            letter-spacing:.14em !important;
            color:#9b742f !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel h2{
            margin-top:4px !important;
            font-size:19px !important;
            font-weight:700 !important;
            line-height:1.12 !important;
            letter-spacing:-.035em !important;
            color:#201c18 !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel h2 + p{
            margin-top:4px !important;
            max-width:250px !important;
            font-size:7.8px !important;
            line-height:1.5 !important;
            color:#8f877d !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel .relative.mt-4{
            margin-top:13px !important;
        }

        #adminConversationSearch{
            height:40px !important;
            border:1px solid #e8e3dc !important;
            border-radius:11px !important;
            background:#f8f7f5 !important;
            padding-left:35px !important;
            padding-right:11px !important;
            color:#37312b !important;
            font-size:8.3px !important;
            box-shadow:none !important;
        }

        #adminConversationSearch::placeholder{
            color:#9d958b !important;
        }

        #adminConversationSearch:focus{
            border-color:#dcc58d !important;
            background:#fff !important;
            box-shadow:0 0 0 3px rgba(210,154,40,.07) !important;
        }

        /* Role filters: real segmented control, not touching pills/text */
        #adminConversationFilters{
            display:grid !important;
            grid-template-columns:repeat(5,minmax(0,1fr)) !important;
            gap:3px !important;
            margin-top:10px !important;
            padding:3px !important;
            border:1px solid #ebe6df !important;
            border-radius:10px !important;
            background:#f6f4f1 !important;
            overflow:hidden !important;
        }

        #adminConversationFilters [data-role-filter]{
            display:flex !important;
            min-width:0 !important;
            height:29px !important;
            min-height:29px !important;
            align-items:center !important;
            justify-content:center !important;
            border:0 !important;
            border-radius:7px !important;
            background:transparent !important;
            padding:0 4px !important;
            color:#777067 !important;
            font-size:7.1px !important;
            font-weight:600 !important;
            line-height:1 !important;
            letter-spacing:-.01em !important;
            box-shadow:none !important;
            white-space:nowrap !important;
            transition:background-color .13s ease,color .13s ease,box-shadow .13s ease !important;
        }

        #adminConversationFilters [data-role-filter]:hover{
            background:#fff !important;
            color:#4e473f !important;
        }

        #adminConversationFilters [data-role-filter].is-active{
            background:#fff !important;
            color:#9a6815 !important;
            box-shadow:0 1px 3px rgba(54,43,29,.08) !important;
        }

        #adminConversationList{
            background:#fff !important;
        }

        #adminConversationList [data-contact-role]{
            min-height:76px !important;
            border-left:0 !important;
            border-bottom:1px solid #f0ece6 !important;
            padding:10px 12px !important;
            background:#fff !important;
            box-shadow:none !important;
        }

        #adminConversationList [data-contact-role]:hover{
            background:#faf9f7 !important;
        }

        #adminConversationList [data-contact-role].is-selected{
            border-left:0 !important;
            background:#fff8e9 !important;
        }

        #adminConversationList [data-contact-role].is-selected::before{
            content:"";
            position:absolute;
            left:0;
            top:12px;
            bottom:12px;
            width:3px;
            border-radius:0 999px 999px 0;
            background:#d29a28;
        }

        #adminConversationList [data-contact-role]{
            position:relative !important;
        }

        #adminConversationList .account-directory-avatar{
            width:40px !important;
            height:40px !important;
            flex:0 0 40px !important;
            border:1px solid #e2e5e5 !important;
            background:#f1f4f4 !important;
            color:#60746f !important;
            font-size:9px !important;
        }

        #adminConversationList .account-directory-name{
            color:#302a25 !important;
            font-size:9px !important;
            font-weight:700 !important;
        }

        #adminConversationList .account-directory-meta{
            margin-top:3px !important;
            color:#91887e !important;
            font-size:6.8px !important;
        }

        #adminConversationList .account-directory-preview{
            margin-top:5px !important;
            color:#7f776e !important;
            font-size:7px !important;
        }

        #adminConversationList .account-directory-time{
            font-size:6.3px !important;
            color:#9b9389 !important;
        }

        #adminConversationList .account-directory-unread{
            min-width:18px !important;
            min-height:18px !important;
            background:#d29a28 !important;
            font-size:6.2px !important;
        }

        #adminConversationList .inline-flex.rounded-full.border{
            min-height:18px !important;
            align-items:center !important;
            padding:0 6px !important;
            font-size:6px !important;
            font-weight:600 !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel > div:last-child{
            padding:8px 12px !important;
            border-top:1px solid #eeeae4 !important;
            background:#fff !important;
        }

        #adminMessagingRefreshStatus,
        #adminRefreshInbox{
            font-size:6.7px !important;
        }

        /* CENTER: modern messenger conversation */
        #sariUniversalAdminMessages .sari-thread-panel{
            background:#f7f6f4 !important;
        }

        #sariUniversalAdminMessages .sari-thread-panel > header{
            min-height:64px !important;
            padding:10px 15px !important;
            border-bottom:1px solid #eae5de !important;
            background:#fff !important;
        }

        #adminThreadAvatar{
            width:40px !important;
            height:40px !important;
            flex-basis:40px !important;
            border:1px solid #dde4e2 !important;
            background:#eef3f1 !important;
            color:#5d746d !important;
            font-size:9px !important;
        }

        #adminThreadTitle{
            font-size:10.5px !important;
            font-weight:700 !important;
            color:#2b2621 !important;
        }

        #adminThreadSubtitle{
            font-size:7px !important;
            color:#938b81 !important;
        }

        #adminThreadTypeBadge,
        #adminThreadStatus{
            min-height:20px !important;
            border-radius:999px !important;
            padding:0 7px !important;
            font-size:6.2px !important;
        }

        #adminThreadMessages{
            background:#f7f6f4 !important;
            padding:20px 22px !important;
            scroll-behavior:smooth;
        }

        #adminThreadMessages [data-message-id]{
            margin-bottom:12px !important;
        }

        #adminThreadMessages [data-message-id] > div:first-child.grid{
            width:28px !important;
            height:28px !important;
            flex-basis:28px !important;
            border:1px solid #e2e5e4 !important;
            box-shadow:none !important;
        }

        #adminThreadMessages [data-message-id] > div:last-child{
            max-width:68% !important;
        }

        #adminThreadMessages [data-message-id] .sari-message-body{
            border-radius:14px !important;
            padding:9px 11px !important;
            font-size:8.2px !important;
            line-height:1.55 !important;
            box-shadow:none !important;
        }

        #adminThreadMessages [data-message-id] .bg-\[\#d29a28\]{
            background:#d09524 !important;
        }

        #adminThreadMessages [data-message-id] .border.border-\[\#e7e1d8\]{
            border-color:#e5e0d9 !important;
            background:#fff !important;
        }

        #adminThreadMessages [data-message-id] p.mt-1\.5{
            margin-top:4px !important;
            font-size:6.2px !important;
            color:#9a9289 !important;
        }

        /* Composer: Messenger-like, compact and anchored */
        #adminComposerWrap{
            padding:10px 13px 12px !important;
            border-top:1px solid #eae5de !important;
            background:#fff !important;
        }

        #adminUniversalMessageForm{
            display:grid !important;
            grid-template-columns:minmax(0,1fr) 38px !important;
            gap:8px !important;
            align-items:end !important;
            border:1px solid #e5e0d9 !important;
            border-radius:14px !important;
            background:#f8f7f5 !important;
            padding:7px 7px 7px 11px !important;
            box-shadow:none !important;
        }

        #adminUniversalMessageForm:focus-within{
            border-color:#ddc58a !important;
            background:#fff !important;
            box-shadow:0 0 0 3px rgba(210,154,40,.06) !important;
        }

        #adminUniversalMessageInput{
            min-height:34px !important;
            max-height:92px !important;
            padding:7px 2px !important;
            color:#3c3630 !important;
            font-size:8.5px !important;
            line-height:1.5 !important;
        }

        #adminUniversalMessageForm > div.mt-2{
            display:contents !important;
            margin:0 !important;
            padding:0 !important;
            border:0 !important;
        }

        #adminUniversalMessageForm > div.mt-2 > div{
            display:none !important;
        }

        #adminUniversalSendButton{
            width:38px !important;
            min-width:38px !important;
            height:38px !important;
            border-radius:11px !important;
            padding:0 !important;
            font-size:0 !important;
            box-shadow:0 4px 10px rgba(166,112,18,.12) !important;
        }

        #adminUniversalSendButton svg{
            width:14px !important;
            height:14px !important;
        }

        #adminComposerError{
            margin-top:5px !important;
            font-size:6.8px !important;
        }

        /* RIGHT: quieter enterprise detail rail */
        #sariUniversalAdminMessages .sari-context-panel{
            border-left:1px solid #ebe7e0 !important;
            background:#fbfaf8 !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:first-child{
            padding:14px !important;
            background:#fff !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:first-child p:first-child{
            font-size:9.2px !important;
            color:#312b26 !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:first-child p:last-child{
            margin-top:4px !important;
            font-size:6.8px !important;
            line-height:1.45 !important;
        }

        #adminConversationContext{
            padding:12px !important;
        }

        #adminConversationContext > div,
        #adminConversationContext .rounded-\[16px\]{
            border-radius:10px !important;
            border-color:#e8e2da !important;
            padding:10px !important;
            background:#fff !important;
            box-shadow:none !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:last-child{
            padding:12px !important;
            background:#fbfaf8 !important;
        }

        #sariUniversalAdminMessages .sari-context-panel > div:last-child > div{
            border-color:#e8e2d8 !important;
            border-radius:10px !important;
            background:#fffdf8 !important;
            padding:10px !important;
        }

        /* Responsive tuning */
        @media(max-width:1350px){
            #sariUniversalAdminMessages .sari-message-grid{
                grid-template-columns:295px minmax(0,1fr) 225px !important;
            }
        }

        @media(max-width:1279px){
            #sariUniversalAdminMessages .sari-message-grid{
                grid-template-columns:295px minmax(0,1fr) !important;
            }
        }

        @media(max-width:1023px){
            #sariUniversalAdminMessages .sari-message-grid{
                grid-template-columns:1fr !important;
            }

            #adminConversationFilters{
                grid-template-columns:repeat(5,minmax(54px,1fr)) !important;
                overflow-x:auto !important;
            }
        }

        @media(max-width:640px){
            #sariUniversalAdminMessages > section.sari-shadow{
                border-radius:12px !important;
            }

            #sariUniversalAdminMessages .sari-inbox-panel > div:first-child{
                padding:13px 11px 11px !important;
            }

            #adminConversationFilters{
                grid-template-columns:repeat(5,minmax(60px,1fr)) !important;
            }

            #adminThreadMessages{
                padding:14px 12px !important;
            }

            #adminThreadMessages [data-message-id] > div:last-child{
                max-width:82% !important;
            }

            #adminComposerWrap{
                padding:8px !important;
            }
        }


        /* ============================================================
           SARI MESSAGING — ENTERPRISE REALTIME + AI VISUAL LAYER
           Clearer contrast, calmer hierarchy, explicit AI disclosure.
           ============================================================ */
        #sariUniversalAdminMessages{
            --chat-canvas:#f3f4f5;
            --chat-panel:#fbfaf8;
            --chat-panel-strong:#f7f5f2;
            --chat-border:#e3ded7;
            --chat-ink:#211d19;
            --chat-muted:#847b72;
            --chat-gold:#cf900f;
        }

        #sariUniversalAdminMessages > section.sari-shadow{
            border-color:var(--chat-border) !important;
            box-shadow:0 12px 32px rgba(40,32,22,.07) !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel{
            background:var(--chat-panel) !important;
        }

        #sariUniversalAdminMessages .sari-inbox-panel > div:first-child{
            background:#fff !important;
        }

        #adminConversationFilters{
            padding:4px !important;
            border-color:#e4dfd8 !important;
            background:#efede9 !important;
        }

        #adminConversationFilters [data-role-filter]{
            height:30px !important;
            min-height:30px !important;
            border-radius:7px !important;
            color:#6f675f !important;
            font-size:7.2px !important;
            font-weight:650 !important;
        }

        #adminConversationFilters [data-role-filter].is-active{
            background:#fff !important;
            color:#8d5d0a !important;
            box-shadow:0 1px 4px rgba(51,41,29,.10) !important;
        }

        #adminConversationList{
            padding:6px !important;
            background:#f7f6f3 !important;
        }

        #adminConversationList [data-contact-role]{
            min-height:74px !important;
            margin-bottom:3px !important;
            border:1px solid transparent !important;
            border-radius:11px !important;
            background:transparent !important;
            padding:10px !important;
        }

        #adminConversationList [data-contact-role]:hover{
            border-color:#e7e1d8 !important;
            background:#fff !important;
        }

        #adminConversationList [data-contact-role].is-selected{
            border-color:#ead7ab !important;
            background:#fffaf0 !important;
            box-shadow:0 3px 10px rgba(84,61,25,.045) !important;
        }

        #adminConversationList [data-contact-role].is-selected::before{
            top:10px !important;
            bottom:10px !important;
            width:3px !important;
            background:#cf900f !important;
        }

        #adminConversationList .account-directory-avatar{
            width:38px !important;
            height:38px !important;
            flex-basis:38px !important;
            border-color:#dde2e1 !important;
            background:#eef2f1 !important;
            color:#536c65 !important;
        }

        #adminConversationList .account-directory-name{
            font-size:9.1px !important;
            color:#27221e !important;
        }

        #adminConversationList .account-directory-preview{
            max-width:190px !important;
            color:#7d756c !important;
        }

        #sariUniversalAdminMessages .sari-thread-panel{
            background:var(--chat-canvas) !important;
        }

        #sariUniversalAdminMessages .sari-thread-panel > header{
            min-height:66px !important;
            border-bottom-color:#e3ded7 !important;
            background:#fff !important;
        }

        #adminThreadMessages{
            background:var(--chat-canvas) !important;
            background-image:linear-gradient(rgba(255,255,255,.42),rgba(255,255,255,.42)) !important;
        }

        #adminThreadMessages [data-message-id] .sari-message-body{
            min-width:64px;
            border-radius:14px !important;
            padding:9px 11px !important;
            box-shadow:0 1px 2px rgba(40,34,28,.025) !important;
        }

        #adminThreadMessages .sari-message-incoming{
            border:1px solid #e2e0dc !important;
            background:#fff !important;
            color:#403a34 !important;
        }

        #adminThreadMessages .sari-message-outgoing{
            border:1px solid #ecd9ac !important;
            background:#fff0d1 !important;
            color:#3e321f !important;
        }

        #adminThreadMessages .sari-message-ai{
            border:1px solid #d8dfec !important;
            background:#f6f8fc !important;
            color:#354252 !important;
        }

        #adminThreadMessages .sari-ai-message-label{
            display:inline-flex;
            align-items:center;
            gap:5px;
            margin-bottom:5px;
            color:#65758a;
            font-size:6.5px;
            font-weight:700;
            letter-spacing:.02em;
        }

        #adminThreadMessages .sari-ai-message-label svg{
            width:10px;
            height:10px;
        }

        #adminComposerWrap{
            border-top-color:#e1ddd6 !important;
            background:#fff !important;
        }

        #adminUniversalMessageForm{
            border-color:#dfdbd4 !important;
            background:#f5f4f2 !important;
        }

        #adminUniversalMessageForm:focus-within{
            background:#fff !important;
        }

        #adminUniversalSendButton{
            background:#cf900f !important;
        }

        #adminUniversalSendButton:hover{
            background:#b97f0a !important;
        }

        .sari-live-pill,
        .sari-ai-pill{
            display:inline-flex;
            min-height:25px;
            align-items:center;
            gap:6px;
            border:1px solid #e1ddd6;
            border-radius:999px;
            background:#fff;
            padding:0 8px;
            color:#716961;
            font-size:6.5px;
            font-weight:650;
            white-space:nowrap;
        }

        .sari-live-dot{
            width:6px;
            height:6px;
            border-radius:50%;
            background:#2f9b5c;
            box-shadow:0 0 0 3px rgba(47,155,92,.10);
        }

        .sari-live-pill.is-fallback .sari-live-dot{
            background:#c28a2b;
            box-shadow:0 0 0 3px rgba(194,138,43,.10);
        }

        .sari-ai-pill{
            border-color:#e2d7bd;
            background:#fffbf2;
            color:#8a641e;
        }

        .sari-ai-pill svg{
            width:11px;
            height:11px;
        }

        #sariUniversalAdminMessages .sari-context-panel{
            background:#f7f6f3 !important;
        }

        #adminConversationContext > div,
        #adminConversationContext .rounded-\[16px\]{
            border-color:#e0dcd5 !important;
            box-shadow:0 2px 7px rgba(43,34,24,.025) !important;
        }

        .sari-ai-cover-card{
            border:1px solid #dfd7c7 !important;
            border-radius:11px !important;
            background:#fffaf0 !important;
            padding:11px !important;
        }

        .sari-ai-cover-card .sari-ai-cover-title{
            display:flex;
            align-items:center;
            gap:6px;
            color:#8b641c;
            font-size:6.7px;
            font-weight:800;
            letter-spacing:.10em;
            text-transform:uppercase;
        }

        .sari-ai-cover-card .sari-ai-cover-title svg{
            width:12px;
            height:12px;
        }

        .sari-ai-cover-card p:last-child{
            margin-top:6px !important;
            color:#776d60 !important;
            font-size:6.8px !important;
            line-height:1.55 !important;
        }

        @media(max-width:1279px){
            .sari-ai-pill{display:none !important;}
        }


        /* ============================================================
           SARI MESSAGING — ATTACHMENTS + REACTIONS + ZERO-RELOAD SEND
           Final interaction layer. Keeps the enterprise Messenger look.
           ============================================================ */

        #adminUniversalMessageForm{
            display:block !important;
            border:1px solid #e4dfd8 !important;
            border-radius:14px !important;
            background:#fff !important;
            padding:7px !important;
        }

        #adminUniversalMessageForm:focus-within{
            border-color:#d9c28d !important;
            box-shadow:0 0 0 3px rgba(210,154,40,.055) !important;
        }

        .sari-composer-row{
            display:grid;
            grid-template-columns:36px minmax(0,1fr) 38px;
            align-items:end;
            gap:7px;
        }

        #adminAttachmentButton{
            display:grid;
            width:36px;
            height:36px;
            place-items:center;
            border:1px solid transparent;
            border-radius:10px;
            background:#f6f4f1;
            color:#746b61;
            transition:background-color .14s ease,color .14s ease,border-color .14s ease;
        }

        #adminAttachmentButton:hover,
        #adminAttachmentButton:focus-visible{
            outline:none;
            border-color:#e1d8c9;
            background:#fff8ea;
            color:#a97012;
        }

        #adminAttachmentButton svg{
            width:15px;
            height:15px;
        }

        #adminUniversalMessageInput{
            min-height:36px !important;
            max-height:110px !important;
            padding:8px 4px !important;
            font-size:8.7px !important;
            line-height:1.5 !important;
        }

        #adminUniversalSendButton{
            display:grid !important;
            width:38px !important;
            min-width:38px !important;
            height:38px !important;
            place-items:center !important;
            border:0 !important;
            border-radius:11px !important;
            padding:0 !important;
            background:#d09524 !important;
            color:#fff !important;
            font-size:0 !important;
            box-shadow:0 5px 12px rgba(166,112,18,.14) !important;
        }

        #adminUniversalSendButton:hover:not(:disabled){
            background:#b97e18 !important;
            transform:translateY(-1px);
        }

        #adminUniversalSendButton:disabled{
            opacity:.45 !important;
            cursor:not-allowed !important;
            transform:none !important;
        }

        #adminUniversalSendButton svg{
            width:14px !important;
            height:14px !important;
        }

        #adminAttachmentPreview{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            margin:0 1px 7px;
            border:1px solid #e8e2da;
            border-radius:10px;
            background:#f8f7f5;
            padding:7px 8px;
        }

        #adminAttachmentPreview.hidden{
            display:none !important;
        }

        .sari-attachment-preview-main{
            display:flex;
            min-width:0;
            align-items:center;
            gap:8px;
        }

        .sari-attachment-preview-icon{
            display:grid;
            width:28px;
            height:28px;
            flex:0 0 28px;
            place-items:center;
            border:1px solid #e6ded0;
            border-radius:8px;
            background:#fff;
            color:#9b6d1c;
        }

        .sari-attachment-preview-icon svg{
            width:13px;
            height:13px;
        }

        #adminAttachmentName{
            overflow:hidden;
            color:#4a433b;
            font-size:7.6px;
            font-weight:700;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        #adminAttachmentMeta{
            margin-top:2px;
            color:#978e84;
            font-size:6.4px;
        }

        #adminAttachmentRemove{
            display:grid;
            width:27px;
            height:27px;
            flex:0 0 27px;
            place-items:center;
            border-radius:8px;
            color:#8c8379;
        }

        #adminAttachmentRemove:hover{
            background:#eee9e2;
            color:#554d45;
        }

        #adminAttachmentRemove svg{
            width:12px;
            height:12px;
        }

        #adminComposerWrap.is-dragging{
            background:#fffaf0 !important;
        }

        #adminComposerWrap.is-dragging #adminUniversalMessageForm{
            border-color:#d4b86f !important;
            box-shadow:0 0 0 3px rgba(210,154,40,.07) !important;
        }

        .sari-message-attachment{
            margin-top:6px;
        }

        .sari-message-image-link{
            display:block;
            overflow:hidden;
            max-width:330px;
            border:1px solid #e2ddd6;
            border-radius:13px;
            background:#fff;
        }

        .sari-message-image{
            display:block;
            width:100%;
            max-height:270px;
            object-fit:cover;
        }

        .sari-message-file{
            display:grid;
            min-width:220px;
            max-width:330px;
            grid-template-columns:34px minmax(0,1fr) 20px;
            align-items:center;
            gap:9px;
            border:1px solid #e5e0d9;
            border-radius:12px;
            background:#fff;
            padding:9px 10px;
            color:#4d463f;
            text-decoration:none;
            transition:border-color .14s ease,background-color .14s ease;
        }

        .sari-message-file:hover{
            border-color:#d9c89f;
            background:#fffdf8;
        }

        .sari-message-file-icon{
            display:grid;
            width:34px;
            height:34px;
            place-items:center;
            border-radius:9px;
            background:#f5f2ed;
            color:#8c6a30;
        }

        .sari-message-file-icon svg,
        .sari-message-file-arrow{
            width:14px;
            height:14px;
        }

        .sari-message-file-name{
            overflow:hidden;
            font-size:7.5px;
            font-weight:700;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .sari-message-file-meta{
            margin-top:2px;
            color:#958c82;
            font-size:6.2px;
        }

        .sari-message-tools{
            display:flex;
            align-items:center;
            gap:5px;
            margin-top:4px;
        }

        .sari-message-tools.is-right{
            justify-content:flex-end;
        }

        .sari-reaction-wrap{
            position:relative;
            display:inline-flex;
            align-items:center;
        }

        .sari-reaction-toggle{
            display:grid;
            width:24px;
            height:24px;
            place-items:center;
            border-radius:7px;
            color:#9b9288;
            opacity:.72;
            transition:background-color .13s ease,color .13s ease,opacity .13s ease;
        }

        [data-message-id]:hover .sari-reaction-toggle,
        .sari-reaction-toggle:focus-visible{
            opacity:1;
        }

        .sari-reaction-toggle:hover,
        .sari-reaction-toggle:focus-visible{
            outline:none;
            background:#ece8e2;
            color:#6f665d;
        }

        .sari-reaction-toggle svg{
            width:13px;
            height:13px;
        }

        .sari-reaction-picker{
            position:absolute;
            z-index:40;
            bottom:30px;
            left:0;
            display:flex;
            align-items:center;
            gap:2px;
            border:1px solid #e4ddd4;
            border-radius:999px;
            background:#fff;
            padding:4px;
            box-shadow:0 10px 28px rgba(36,29,21,.12);
        }

        .sari-message-tools.is-right .sari-reaction-picker{
            right:0;
            left:auto;
        }

        .sari-reaction-picker.hidden{
            display:none !important;
        }

        .sari-reaction-option{
            display:grid;
            width:27px;
            height:27px;
            place-items:center;
            border-radius:999px;
            font-size:14px;
            line-height:1;
            transition:background-color .12s ease,transform .12s ease;
        }

        .sari-reaction-option:hover,
        .sari-reaction-option:focus-visible{
            outline:none;
            background:#f2efea;
            transform:translateY(-1px);
        }

        .sari-reaction-summary{
            display:flex;
            align-items:center;
            gap:3px;
        }

        .sari-reaction-chip{
            display:inline-flex;
            min-height:21px;
            align-items:center;
            gap:3px;
            border:1px solid #e6e0d9;
            border-radius:999px;
            background:#fff;
            padding:0 6px;
            color:#756d64;
            font-size:9px;
            line-height:1;
        }

        .sari-reaction-chip.is-mine{
            border-color:#e4c981;
            background:#fff8e8;
            color:#8f6417;
        }

        .sari-reaction-count{
            font-size:6.3px;
            font-weight:700;
        }

        .sari-message-time{
            margin-left:auto;
            color:#9b9286;
            font-size:6.2px;
            white-space:nowrap;
        }

        .sari-message-tools.is-right .sari-message-time{
            margin-left:0;
        }

        @media(max-width:640px){
            .sari-message-file{
                min-width:190px;
                max-width:270px;
            }

            .sari-message-image-link{
                max-width:270px;
            }
        }


        /* ============================================================
           FINAL HEADER + COMPOSER REFINEMENT
           Fixes: BuyerBuyer duplication feel, uneven header layout,
           and oversized send button.
           ============================================================ */

        #sariUniversalAdminMessages .sari-thread-panel > header{
            min-height:74px !important;
            padding:12px 18px !important;
            border-bottom:1px solid #e9e4dc !important;
            background:#fff !important;
        }

        #adminThreadAvatar{
            width:44px !important;
            height:44px !important;
            flex-basis:44px !important;
            border:1px solid #dde4e2 !important;
            background:#eef3f1 !important;
            color:#5e736d !important;
            font-size:11px !important;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.7) !important;
        }

        #adminThreadTitle{
            font-size:15px !important;
            font-weight:700 !important;
            line-height:1.15 !important;
            letter-spacing:-.02em !important;
            color:#231f1b !important;
        }

        #adminThreadTypeBadge{
            display:inline-flex !important;
            min-height:22px !important;
            align-items:center !important;
            padding:0 8px !important;
            border-radius:999px !important;
            font-size:9px !important;
            font-weight:700 !important;
            line-height:1 !important;
        }

        #adminThreadSubtitle{
            display:inline-block !important;
            max-width:340px !important;
            overflow:hidden !important;
            color:#8b8379 !important;
            font-size:10px !important;
            line-height:1.35 !important;
            text-overflow:ellipsis !important;
            white-space:nowrap !important;
        }

        #adminThreadStatus{
            min-height:22px !important;
            align-items:center !important;
            border-radius:999px !important;
            padding:0 9px !important;
            font-size:9px !important;
            font-weight:600 !important;
        }

        #adminComposerWrap{
            padding:12px 14px 14px !important;
            border-top:1px solid #e9e4dc !important;
            background:#fff !important;
        }

        #adminUniversalMessageForm{
            border:1px solid #e5dfd7 !important;
            border-radius:18px !important;
            background:#fff !important;
            padding:9px !important;
            box-shadow:0 4px 14px rgba(33, 26, 18, .035) !important;
        }

        #adminUniversalMessageForm:focus-within{
            border-color:#ddc58b !important;
            box-shadow:0 0 0 4px rgba(210,154,40,.06) !important;
        }

        .sari-composer-row{
            display:grid !important;
            grid-template-columns:46px minmax(0,1fr) auto !important;
            align-items:center !important;
            gap:10px !important;
        }

        #adminAttachmentButton{
            width:42px !important;
            height:42px !important;
            border:1px solid #e7e1d8 !important;
            border-radius:14px !important;
            background:#f8f7f5 !important;
            color:#72685d !important;
            transition:background-color .14s ease,border-color .14s ease,color .14s ease !important;
        }

        #adminAttachmentButton:hover{
            background:#fffaf0 !important;
            border-color:#e2c98f !important;
            color:#9b6a18 !important;
        }

        #adminUniversalMessageInput{
            min-height:42px !important;
            max-height:120px !important;
            padding:10px 0 !important;
            color:#342f29 !important;
            font-size:13px !important;
            line-height:1.5 !important;
        }

        #adminUniversalMessageInput::placeholder{
            color:#a39a90 !important;
        }

        #adminUniversalSendButton{
            width:44px !important;
            min-width:44px !important;
            height:44px !important;
            border-radius:14px !important;
            background:#c99321 !important;
            color:#fff !important;
            box-shadow:0 8px 18px rgba(166,112,18,.18) !important;
        }

        #adminUniversalSendButton:hover:not(:disabled){
            background:#b88419 !important;
            transform:translateY(-1px) !important;
        }

        #adminUniversalSendButton:disabled{
            opacity:.5 !important;
            transform:none !important;
        }

        #adminUniversalSendButton svg{
            width:18px !important;
            height:18px !important;
        }

        @media(max-width:1023px){
            #adminThreadSubtitle{
                max-width:250px !important;
            }
        }

        @media(max-width:639px){
            #sariUniversalAdminMessages .sari-thread-panel > header{
                padding:10px 12px !important;
            }

            #adminThreadAvatar{
                width:40px !important;
                height:40px !important;
                flex-basis:40px !important;
                font-size:10px !important;
            }

            #adminThreadTitle{
                font-size:14px !important;
            }

            #adminThreadSubtitle{
                max-width:180px !important;
                font-size:9.5px !important;
            }

            .sari-composer-row{
                grid-template-columns:42px minmax(0,1fr) auto !important;
                gap:8px !important;
            }

            #adminAttachmentButton,
            #adminUniversalSendButton{
                width:40px !important;
                min-width:40px !important;
                height:40px !important;
                border-radius:12px !important;
            }
        }


        /* ============================================================
           UNIVERSAL ACCOUNT MODERATION
           Quiet enterprise actions; gold-first with restrained danger.
           ============================================================ */
        .sari-account-card{
            border:1px solid #e6e1da;
            border-radius:12px;
            background:#fff;
            padding:12px;
        }

        .sari-account-card__avatar{
            display:grid;
            width:38px;
            height:38px;
            flex:0 0 38px;
            place-items:center;
            border:1px solid #dde3e1;
            border-radius:50%;
            background:#eef3f1;
            color:#5e736d;
            font-size:9px;
            font-weight:700;
        }

        .sari-account-status{
            display:inline-flex;
            min-height:21px;
            align-items:center;
            gap:5px;
            border:1px solid #d9e5dd;
            border-radius:999px;
            background:#f3f8f4;
            padding:0 7px;
            color:#5f7866;
            font-size:6.4px;
            font-weight:700;
        }

        .sari-account-status::before{
            content:"";
            width:5px;
            height:5px;
            border-radius:50%;
            background:#439765;
        }

        .sari-account-status.is-suspended{
            border-color:#e5d9be;
            background:#fffaf0;
            color:#8b6828;
        }

        .sari-account-status.is-suspended::before{background:#c5963e;}

        .sari-account-status.is-banned{
            border-color:#ead6d1;
            background:#fff6f4;
            color:#9e5f53;
        }

        .sari-account-status.is-banned::before{background:#b86657;}

        .sari-admin-actions{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:7px;
            margin-top:9px;
        }

        .sari-admin-action{
            display:flex;
            min-height:34px;
            align-items:center;
            justify-content:center;
            gap:6px;
            border:1px solid #e5dfd6;
            border-radius:9px;
            background:#fff;
            padding:0 8px;
            color:#655d54;
            font-size:6.8px;
            font-weight:700;
            line-height:1;
            transition:background-color .13s ease,border-color .13s ease,color .13s ease;
        }

        .sari-admin-action svg{
            width:12px;
            height:12px;
        }

        .sari-admin-action:hover,
        .sari-admin-action:focus-visible{
            outline:none;
            border-color:#dbc995;
            background:#fffaf0;
            color:#8e6418;
        }

        .sari-admin-action--primary{
            border-color:#e2c987;
            background:#fff8e8;
            color:#8e6418;
        }

        .sari-admin-action--danger{
            border-color:#ead8d3;
            background:#fff;
            color:#9c6156;
        }

        .sari-admin-action--danger:hover,
        .sari-admin-action--danger:focus-visible{
            border-color:#ddb8af;
            background:#fff6f4;
            color:#8f4e43;
        }

        .sari-admin-action--wide{
            grid-column:1 / -1;
        }

        .sari-moderation-modal{
            position:fixed;
            inset:0;
            z-index:120;
            display:grid;
            place-items:center;
            padding:18px;
            background:rgba(27,22,17,.24);
        }

        .sari-moderation-modal.hidden{display:none !important;}

        .sari-moderation-dialog{
            width:min(430px,100%);
            overflow:hidden;
            border:1px solid #e4ddd3;
            border-radius:16px;
            background:#fff;
            box-shadow:0 22px 55px rgba(39,31,23,.18);
        }

        .sari-moderation-dialog__header{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:14px;
            border-bottom:1px solid #eee8e1;
            padding:16px 17px 13px;
        }

        .sari-moderation-dialog__eyebrow{
            color:#a2762f;
            font-size:7px;
            font-weight:800;
            letter-spacing:.13em;
            text-transform:uppercase;
        }

        .sari-moderation-dialog__title{
            margin-top:4px;
            color:#29241f;
            font-size:14px;
            font-weight:750;
            letter-spacing:-.02em;
        }

        .sari-moderation-dialog__body{padding:15px 17px 17px;}

        .sari-moderation-dialog__copy{
            color:#7f766c;
            font-size:8px;
            line-height:1.6;
        }

        #adminModerationReason{
            width:100%;
            min-height:92px;
            margin-top:12px;
            resize:vertical;
            border:1px solid #e4ddd4;
            border-radius:11px;
            background:#f8f7f5;
            padding:10px 11px;
            color:#3d3731;
            font-size:9px;
            line-height:1.5;
            outline:none;
        }

        #adminModerationReason:focus{
            border-color:#d9bf7f;
            background:#fff;
            box-shadow:0 0 0 3px rgba(210,154,40,.07);
        }

        .sari-moderation-dialog__footer{
            display:flex;
            justify-content:flex-end;
            gap:8px;
            border-top:1px solid #eee8e1;
            padding:11px 17px;
            background:#fbfaf8;
        }

        .sari-moderation-dialog__button{
            min-height:34px;
            border-radius:9px;
            padding:0 13px;
            font-size:7px;
            font-weight:700;
        }

        #adminModerationCancel{
            border:1px solid #e3ddd5;
            background:#fff;
            color:#6f675f;
        }

        #adminModerationConfirm{
            border:1px solid #c89428;
            background:#c89428;
            color:#fff;
            box-shadow:0 5px 12px rgba(166,112,18,.12);
        }

        #adminModerationConfirm.is-danger{
            border-color:#a85d50;
            background:#a85d50;
            box-shadow:none;
        }



        /* ============================================================
           CLEAN ACCOUNT MENU + COMPACT COMPOSER — FINAL OVERRIDES
           ============================================================ */

        /* Header status: same visual weight as the other header pills. */
        #adminThreadStatus{
            min-height:24px !important;
            height:24px !important;
            align-items:center !important;
            border:1px solid #dce6df !important;
            border-radius:999px !important;
            background:#f5f8f5 !important;
            padding:0 9px !important;
            color:#627567 !important;
            font-size:7.2px !important;
            font-weight:700 !important;
            line-height:1 !important;
            box-shadow:none !important;
        }

        .sari-account-card{
            position:relative;
            overflow:visible;
            padding:12px !important;
            border-color:#e7e2db !important;
            border-radius:13px !important;
            background:#fff !important;
            box-shadow:none !important;
        }

        .sari-account-card__head{
            display:flex;
            align-items:flex-start;
            gap:10px;
        }

        .sari-account-card__avatar{
            width:38px !important;
            height:38px !important;
            flex:0 0 38px !important;
            border:1px solid #dfe5e2 !important;
            background:#f0f4f2 !important;
            color:#60736d !important;
            font-size:8.5px !important;
        }

        .sari-account-meta-row{
            display:flex;
            flex-wrap:wrap;
            align-items:center;
            gap:6px;
            margin-top:7px;
        }

        .sari-account-meta-row > .inline-flex.rounded-full.border,
        .sari-account-status{
            min-height:20px !important;
            height:20px !important;
            align-items:center !important;
            border-radius:999px !important;
            padding:0 7px !important;
            font-size:6.2px !important;
            font-weight:700 !important;
            line-height:1 !important;
            box-shadow:none !important;
        }

        .sari-account-status{
            gap:5px !important;
            border-color:#dce7df !important;
            background:#f6f9f6 !important;
            color:#617568 !important;
        }

        .sari-account-status::before{
            width:5px !important;
            height:5px !important;
            background:#439765 !important;
        }

        .sari-account-status.is-suspended{
            border-color:#e6dcc4 !important;
            background:#fffaf1 !important;
            color:#8a692d !important;
        }
        .sari-account-status.is-suspended::before{background:#c5963e !important;}

        .sari-account-status.is-banned{
            border-color:#ead9d4 !important;
            background:#fff7f5 !important;
            color:#995d53 !important;
        }
        .sari-account-status.is-banned::before{background:#b86657 !important;}

        /* 3-dot account action menu. */
        .sari-account-menu-wrap{
            position:relative;
            flex:0 0 auto;
            margin-left:auto;
        }

        .sari-account-menu-trigger{
            display:grid;
            width:30px;
            height:30px;
            place-items:center;
            border:1px solid #e5e0d9;
            border-radius:9px;
            background:#fff;
            color:#756d64;
            transition:background-color .13s ease,border-color .13s ease,color .13s ease;
        }

        .sari-account-menu-trigger:hover,
        .sari-account-menu-trigger:focus-visible,
        .sari-account-menu-trigger[aria-expanded="true"]{
            outline:none;
            border-color:#ddca9c;
            background:#fffaf0;
            color:#956b21;
        }

        .sari-account-menu-trigger svg{
            width:15px;
            height:15px;
        }

        .sari-account-action-menu{
            position:absolute;
            z-index:70;
            top:36px;
            right:0;
            width:205px;
            overflow:hidden;
            border:1px solid #e5dfd7;
            border-radius:12px;
            background:#fff;
            padding:5px;
            box-shadow:0 14px 34px rgba(41,33,24,.13);
        }

        .sari-account-action-menu.hidden{display:none !important;}

        .sari-account-action-menu__head{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            padding:6px 7px 7px;
            color:#9a9187;
            font-size:6.2px;
            font-weight:700;
            letter-spacing:.06em;
            text-transform:uppercase;
        }

        .sari-account-action-menu .sari-admin-actions{
            display:flex !important;
            flex-direction:column !important;
            gap:2px !important;
            margin-top:0 !important;
        }

        .sari-account-action-menu .sari-admin-action{
            display:flex !important;
            width:100% !important;
            min-height:34px !important;
            align-items:center !important;
            justify-content:flex-start !important;
            gap:8px !important;
            grid-column:auto !important;
            border:0 !important;
            border-radius:8px !important;
            background:transparent !important;
            padding:0 9px !important;
            color:#5f574f !important;
            font-size:7px !important;
            font-weight:650 !important;
            text-align:left !important;
            box-shadow:none !important;
        }

        .sari-account-action-menu .sari-admin-action svg{
            width:13px !important;
            height:13px !important;
            flex:0 0 13px !important;
        }

        .sari-account-action-menu .sari-admin-action:hover,
        .sari-account-action-menu .sari-admin-action:focus-visible{
            outline:none !important;
            background:#f7f5f1 !important;
            color:#7f5b1e !important;
        }

        .sari-account-action-menu .sari-admin-action--primary{
            color:#8d651d !important;
        }

        .sari-account-action-menu .sari-admin-action--danger{
            color:#985f56 !important;
        }

        .sari-account-action-menu .sari-admin-action--danger:hover,
        .sari-account-action-menu .sari-admin-action--danger:focus-visible{
            background:#fff6f4 !important;
            color:#8d4f45 !important;
        }

        /* Composer: smaller, quieter attachment + send controls. */
        #adminComposerWrap{
            padding:10px 12px 12px !important;
        }

        #adminUniversalMessageForm{
            border-radius:15px !important;
            padding:7px 8px !important;
            box-shadow:0 3px 12px rgba(33,26,18,.025) !important;
        }

        .sari-composer-row{
            grid-template-columns:36px minmax(0,1fr) 36px !important;
            align-items:center !important;
            gap:8px !important;
        }

        #adminAttachmentButton{
            width:34px !important;
            min-width:34px !important;
            height:34px !important;
            border:1px solid #e8e3dc !important;
            border-radius:10px !important;
            background:#f8f7f5 !important;
            color:#746c63 !important;
            box-shadow:none !important;
        }

        #adminAttachmentButton svg{
            width:14px !important;
            height:14px !important;
        }

        #adminUniversalMessageInput{
            min-height:36px !important;
            max-height:104px !important;
            padding:7px 1px !important;
            font-size:12px !important;
            line-height:1.5 !important;
        }

        #adminUniversalSendButton{
            width:36px !important;
            min-width:36px !important;
            height:36px !important;
            border-radius:11px !important;
            background:#c9921f !important;
            box-shadow:0 4px 10px rgba(166,112,18,.14) !important;
        }

        #adminUniversalSendButton:hover:not(:disabled){
            background:#b78319 !important;
            transform:none !important;
        }

        #adminUniversalSendButton svg{
            width:15px !important;
            height:15px !important;
        }

        @media(max-width:639px){
            .sari-account-action-menu{
                width:190px;
            }

            .sari-composer-row{
                grid-template-columns:34px minmax(0,1fr) 34px !important;
                gap:7px !important;
            }

            #adminAttachmentButton,
            #adminUniversalSendButton{
                width:34px !important;
                min-width:34px !important;
                height:34px !important;
                border-radius:10px !important;
            }
        }



        /* ============================================================
           SELLER MESSAGE UI → ADMIN THREAD
           Visual parity only. Messaging/realtime/moderation logic stays intact.
           ============================================================ */

        #adminThreadMessages{
            background:#fbfaf7 !important;
            padding:24px 26px !important;
            scroll-behavior:smooth;
        }

        #adminThreadMessages [data-message-id]{
            margin-top:18px !important;
            margin-bottom:0 !important;
        }

        #adminThreadMessages [data-message-id]:first-child{
            margin-top:0 !important;
        }

        #adminThreadMessages [data-message-id].flex.items-end{
            gap:10px !important;
        }

        #adminThreadMessages [data-message-id] > div:first-child.grid,
        #adminThreadMessages .sari-admin-bot-avatar{
            width:32px !important;
            min-width:32px !important;
            height:32px !important;
            flex-basis:32px !important;
            border-radius:999px !important;
            box-shadow:none !important;
        }

        #adminThreadMessages [data-message-id] > div:first-child.grid{
            border:0 !important;
            background:#f4f6f7 !important;
            color:#607a8f !important;
            font-size:9px !important;
            font-weight:700 !important;
        }

        #adminThreadMessages .sari-admin-bot-avatar{
            display:grid;
            place-items:center;
            flex:0 0 32px;
            border:1px solid #dce8ef !important;
            background:#f2f7fa !important;
            color:#5d788b !important;
        }

        #adminThreadMessages .sari-admin-bot-avatar svg{
            width:18px !important;
            height:18px !important;
        }

        #adminThreadMessages [data-message-id] > div:last-child{
            max-width:84% !important;
        }

        @media(min-width:640px){
            #adminThreadMessages [data-message-id] > div:last-child{
                max-width:70% !important;
            }
        }

        @media(min-width:1024px){
            #adminThreadMessages [data-message-id] > div:last-child{
                max-width:64% !important;
            }
        }

        #adminThreadMessages .sari-admin-message-label{
            margin-bottom:6px !important;
            color:#7c7369 !important;
            font-size:9px !important;
            font-weight:600 !important;
            line-height:1.2 !important;
        }

        #adminThreadMessages .sari-ai-message-label{
            display:flex !important;
            align-items:center !important;
            justify-content:flex-start !important;
            gap:5px !important;
            margin-bottom:6px !important;
            color:#6e899c !important;
            font-size:9px !important;
            font-weight:600 !important;
            letter-spacing:.08em !important;
            text-transform:uppercase !important;
        }

        #adminThreadMessages .sari-ai-message-label svg{
            display:none !important;
        }

        #adminThreadMessages [data-message-id] .sari-message-body{
            min-width:0 !important;
            border-radius:14px !important;
            padding:12px 16px !important;
            font-size:12px !important;
            line-height:1.5 !important;
            box-shadow:0 7px 18px rgba(35,28,20,.055) !important;
        }

        #adminThreadMessages .sari-message-incoming{
            border:1px solid #e6dfd6 !important;
            border-bottom-left-radius:5px !important;
            background:#fff !important;
            color:#514a42 !important;
        }

        #adminThreadMessages .sari-message-outgoing{
            border:0 !important;
            border-bottom-right-radius:5px !important;
            background:#c99128 !important;
            color:#fff !important;
        }

        #adminThreadMessages .sari-message-ai{
            border:1px solid #dce8ef !important;
            border-bottom-left-radius:5px !important;
            background:#f4f8fa !important;
            color:#526a79 !important;
        }

        /* Attachments follow the same calmer Seller card treatment. */
        #adminThreadMessages .sari-message-attachment{
            margin-top:8px !important;
        }

        #adminThreadMessages .sari-message-image-link{
            overflow:hidden !important;
            max-width:360px !important;
            border:1px solid #e6dfd6 !important;
            border-radius:14px !important;
            background:#fff !important;
            padding:7px !important;
            box-shadow:0 7px 18px rgba(35,28,20,.045) !important;
        }

        #adminThreadMessages .sari-message-image{
            max-height:300px !important;
            border-radius:10px !important;
            object-fit:contain !important;
        }

        #adminThreadMessages .sari-message-file{
            min-width:220px !important;
            max-width:360px !important;
            grid-template-columns:36px minmax(0,1fr) 20px !important;
            gap:10px !important;
            border:1px solid #e6dfd6 !important;
            border-radius:14px !important;
            background:#fff !important;
            padding:10px !important;
            box-shadow:0 7px 18px rgba(35,28,20,.045) !important;
        }

        #adminThreadMessages .sari-message-file-icon{
            width:36px !important;
            height:36px !important;
            border-radius:9px !important;
            background:#f5efe4 !important;
            color:#a8731f !important;
        }

        #adminThreadMessages .sari-message-file-name{
            font-size:10.5px !important;
        }

        #adminThreadMessages .sari-message-file-meta{
            margin-top:3px !important;
            font-size:9px !important;
        }

        /* Seller-like reaction + timestamp row. */
        #adminThreadMessages .sari-message-tools{
            display:flex !important;
            flex-wrap:wrap !important;
            align-items:center !important;
            gap:6px !important;
            margin-top:8px !important;
        }

        #adminThreadMessages .sari-message-tools.is-right{
            justify-content:flex-end !important;
        }

        #adminThreadMessages .sari-reaction-toggle{
            width:28px !important;
            height:28px !important;
            border:1px solid #e5ded4 !important;
            border-radius:999px !important;
            background:#fff !important;
            color:#8c8378 !important;
            opacity:1 !important;
            box-shadow:0 4px 10px rgba(35,28,20,.05) !important;
        }

        #adminThreadMessages .sari-reaction-toggle svg{
            width:14px !important;
            height:14px !important;
        }

        #adminThreadMessages .sari-reaction-chip{
            min-height:28px !important;
            gap:4px !important;
            border-radius:999px !important;
            padding:0 9px !important;
            font-size:12px !important;
            box-shadow:0 4px 10px rgba(35,28,20,.05) !important;
        }

        #adminThreadMessages .sari-reaction-count{
            font-size:9px !important;
        }

        #adminThreadMessages .sari-message-time{
            margin-left:0 !important;
            color:#968d82 !important;
            font-size:10px !important;
            line-height:1 !important;
        }

        #adminThreadMessages .sari-reaction-picker{
            bottom:36px !important;
            gap:4px !important;
            padding:6px !important;
        }

        #adminThreadMessages .sari-reaction-option{
            width:32px !important;
            height:32px !important;
            font-size:17px !important;
        }

        /* Empty-state sizing now matches the Seller thread. */
        #adminThreadMessages > .flex.h-full{
            min-height:360px !important;
        }

        #adminThreadMessages > .flex.h-full .mx-auto.grid{
            width:56px !important;
            height:56px !important;
            border:0 !important;
            border-radius:16px !important;
            background:#f6efe2 !important;
            color:#9d6f22 !important;
        }

        #adminThreadMessages > .flex.h-full .mx-auto.grid svg{
            width:24px !important;
            height:24px !important;
        }

        #adminThreadMessages > .flex.h-full p.font-bold{
            margin-top:16px !important;
            font-size:13px !important;
        }

        #adminThreadMessages > .flex.h-full p.leading-5,
        #adminThreadMessages > .flex.h-full p.text-\[10px\]{
            font-size:11px !important;
            line-height:1.55 !important;
        }

        @media(max-width:639px){
            #adminThreadMessages{
                padding:18px 14px !important;
            }

            #adminThreadMessages [data-message-id] > div:last-child{
                max-width:84% !important;
            }

            #adminThreadMessages [data-message-id] .sari-message-body{
                padding:10px 13px !important;
                font-size:11.5px !important;
            }

            #adminThreadMessages .sari-message-file,
            #adminThreadMessages .sari-message-image-link{
                min-width:0 !important;
                max-width:280px !important;
            }
        }

</style>

    <div id="adminMessagingNotice" class="mb-4 hidden rounded-[16px] border px-4 py-3 text-[12px]"></div>

    <section class="sari-shadow overflow-hidden rounded-[24px] border border-[#e8e2d9] bg-white">
        <div class="sari-message-grid grid min-h-[720px] grid-cols-[330px_minmax(0,1fr)_300px]">
            {{-- ACCOUNT DIRECTORY --}}
            <aside class="sari-inbox-panel flex min-h-0 flex-col border-r border-[#ebe6df] bg-[#fbfaf8]">
                <div class="border-b border-[#ebe6df] p-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#9f7a3c]">SARI Communications</p>
                        <h2 class="mt-1 text-[20px] font-bold tracking-[-0.03em] text-[#2c2721]">Messages</h2>
                        <p class="mt-1 text-[11px] text-[#91887d]">Choose an account and continue the conversation.</p>
                    </div>

                    <div class="relative mt-4">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#aaa197]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                        </svg>
                        <input id="adminConversationSearch" type="search" placeholder="Search people..."
                            class="h-11 w-full rounded-xl border border-[#e5dfd6] bg-white pl-10 pr-4 text-[12px] text-[#3f3932] outline-none transition placeholder:text-[#aaa197] focus:border-[#d29a28] focus:ring-4 focus:ring-[#d29a28]/10">
                    </div>

                    <div id="adminConversationFilters" class="mt-3 flex flex-wrap gap-1.5" aria-label="Filter people by role">
                        <button type="button" data-role-filter="all" class="is-active">All</button>
                        <button type="button" data-role-filter="buyer">Buyer</button>
                        <button type="button" data-role-filter="seller">Seller</button>
                        <button type="button" data-role-filter="logistics">Logistics</button>
                        <button type="button" data-role-filter="rider">Rider</button>
                    </div>
                </div>

                <div id="adminConversationList" class="sari-scroll min-h-0 flex-1 overflow-y-auto">
                    <div class="p-5 text-center text-[11px] text-[#9a9186]">Loading accounts…</div>
                </div>

                <div class="border-t border-[#ebe6df] px-4 py-3">
                    <div class="flex items-center justify-between gap-3">
                        <span id="adminMessagingRefreshStatus" class="text-[10px] text-[#989084]">Directory synced</span>
                        <button id="adminRefreshInbox" type="button" class="text-[10px] font-semibold text-[#9e7020] hover:text-[#7d5715]">Refresh</button>
                    </div>
                </div>
            </aside>

            {{-- THREAD --}}
            <main class="sari-thread-panel flex min-h-0 min-w-0 flex-col bg-white">
                <header class="flex min-h-[76px] items-center justify-between gap-4 border-b border-[#ebe6df] px-4 py-3 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <button id="adminMobileBack" type="button"
                            class="hidden h-9 w-9 shrink-0 place-items-center rounded-xl border border-[#e5ded5] text-[#6c645a] lg:hidden"
                            aria-label="Back to inbox">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </button>

                        <div id="adminThreadAvatar" class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#f2f4f5] text-[11px] font-bold text-[#677986]">SA</div>
                        <div class="min-w-0">
                            <h3 id="adminThreadTitle" class="truncate text-[14px] font-bold text-[#302a24]">Select a conversation</h3>
                            <div class="mt-1 flex flex-wrap items-center gap-2.5">
                                <span id="adminThreadTypeBadge" class="hidden rounded-full border px-2 py-0.5 text-[9px] font-bold uppercase tracking-[0.08em]"></span>
                                <span id="adminThreadSubtitle" class="truncate text-[10px] text-[#948b80]">Choose an account from the directory to open its direct conversation.</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <span id="adminRealtimeStatus" class="sari-live-pill" title="Realtime messaging status">
                            <span class="sari-live-dot" aria-hidden="true"></span>
                            <span data-live-label>Realtime</span>
                        </span>

                        @if(config('sari_assistant.enabled', true) && filled(config('sari_assistant.api_key')))
                            <span class="sari-ai-pill" title="SARI Assistant can provide a limited first response when Admin has not replied yet">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M12 3l1.2 3.8L17 8l-3.8 1.2L12 13l-1.2-3.8L7 8l3.8-1.2L12 3Z"></path>
                                    <path d="M18.5 14l.7 2.3 2.3.7-2.3.7-.7 2.3-.7-2.3-2.3-.7 2.3-.7.7-2.3Z"></path>
                                </svg>
                                SARI AI standby
                            </span>
                        @endif

                        <span id="adminThreadStatus" class="hidden rounded-full border border-[#dde6df] bg-[#f4f8f5] px-2.5 py-1 text-[9px] font-semibold text-[#5f7866]">Active</span>
                    </div>
                </header>

                <div id="adminThreadMessages" class="sari-scroll flex-1 overflow-y-auto bg-[#faf9f6] px-4 py-5 sm:px-6">
                    <div class="flex h-full min-h-[420px] items-center justify-center text-center">
                        <div class="max-w-[340px]">
                            <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-[#e4ded6] bg-white text-[#8d7d68]">
                                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-[13px] font-bold text-[#554d44]">No conversation selected</p>
                            <p class="mt-1.5 text-[11px] leading-5 text-[#948b80]">Select a Buyer, Seller, Logistics, or Rider account from the left panel to communicate with them.</p>
                        </div>
                    </div>
                </div>

                <div id="adminComposerWrap" class="hidden border-t border-[#ebe6df] bg-white p-4">
                    <form id="adminUniversalMessageForm" novalidate class="sari-soft-shadow rounded-[16px] border border-[#e4ded6] bg-white p-3 transition focus-within:border-[#d29a28] focus-within:ring-4 focus-within:ring-[#d29a28]/10">
                        <input
                            id="adminAttachmentInput"
                            type="file"
                            class="hidden"
                            accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.txt,.csv,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.mp4,.mov,.mp3,.m4a,.wav"
                        >

                        <div id="adminAttachmentPreview" class="hidden">
                            <div class="sari-attachment-preview-main">
                                <span class="sari-attachment-preview-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M8 12.5 14.8 5.7a3 3 0 1 1 4.2 4.2L10.8 18a5 5 0 0 1-7.1-7.1l8-8"></path>
                                    </svg>
                                </span>
                                <span class="min-w-0">
                                    <span id="adminAttachmentName" class="block">Attachment</span>
                                    <span id="adminAttachmentMeta" class="block">Ready to send</span>
                                </span>
                            </div>
                            <button id="adminAttachmentRemove" type="button" aria-label="Remove attachment">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="m7 7 10 10"></path><path d="m17 7-10 10"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="sari-composer-row">
                            <button id="adminAttachmentButton" type="button" title="Attach image or file" aria-label="Attach image or file">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M8 12.5 14.8 5.7a3 3 0 1 1 4.2 4.2L10.8 18a5 5 0 0 1-7.1-7.1l8-8"></path>
                                </svg>
                            </button>

                            <textarea id="adminUniversalMessageInput" rows="1" maxlength="3000"
                                placeholder="Message…"
                                class="w-full resize-none bg-transparent px-1 text-[13px] leading-6 text-[#3f3932] outline-none placeholder:text-[#aaa197]"></textarea>
                            <span id="adminMessageCounter" class="sr-only" aria-live="polite">0 / 3000</span>

                            <button id="adminUniversalSendButton" type="button" aria-label="Send message" title="Send message"
                                class="inline-flex h-11 w-11 items-center justify-center rounded-[14px] bg-[#c9921f] text-white shadow-[0_8px_18px_rgba(166,112,18,0.18)] transition hover:bg-[#b88317] disabled:cursor-not-allowed disabled:opacity-50">
                                <span class="sr-only">Send</span>
                                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="m4 4 17 8-17 8 3-8-3-8Z"/><path d="M7 12h14"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                    <p id="adminComposerError" class="mt-2 hidden text-[10px] text-[#b15f55]"></p>
                </div>
            </main>

            {{-- CONTEXT --}}
            <aside class="sari-context-panel min-h-0 border-l border-[#ebe6df] bg-[#fbfaf8]">
                <div class="border-b border-[#ebe6df] p-5">
                    <p class="text-[12px] font-bold text-[#332d27]">Contact & Conversation</p>
                    <p class="mt-1 text-[10px] leading-4 text-[#948b80]">Account identity, participants, and conversation status</p>
                </div>

                <div id="adminConversationContext" class="sari-scroll max-h-[640px] overflow-y-auto p-5">
                    <div class="rounded-[16px] border border-[#e7e1d8] bg-white p-4 text-[11px] leading-5 text-[#8e857a]">
                        Select a conversation to view its context.
                    </div>
                </div>

                <div class="border-t border-[#ebe6df] p-5">
                    <div class="sari-ai-cover-card">
                        <p class="sari-ai-cover-title">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 3l1.2 3.8L17 8l-3.8 1.2L12 13l-1.2-3.8L7 8l3.8-1.2L12 3Z"></path>
                                <path d="M18.5 14l.7 2.3 2.3.7-2.3.7-.7 2.3-.7-2.3-2.3-.7 2.3-.7.7-2.3Z"></path>
                            </svg>
                            AI Cover
                        </p>
                        <p>SARI Assistant only covers SARI marketplace questions while the human Admin is still unavailable. Human Admin replies always take priority.</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>



    <div id="adminModerationModal" class="sari-moderation-modal hidden" aria-hidden="true">
        <div class="sari-moderation-dialog" role="dialog" aria-modal="true" aria-labelledby="adminModerationTitle">
            <div class="sari-moderation-dialog__header">
                <div class="min-w-0">
                    <p class="sari-moderation-dialog__eyebrow">Admin Action</p>
                    <h3 id="adminModerationTitle" class="sari-moderation-dialog__title">Account action</h3>
                </div>
                <button id="adminModerationClose" type="button" class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-[#81786e] hover:bg-[#f1eee9]" aria-label="Close">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="m7 7 10 10"></path><path d="m17 7-10 10"></path>
                    </svg>
                </button>
            </div>
            <div class="sari-moderation-dialog__body">
                <p id="adminModerationCopy" class="sari-moderation-dialog__copy"></p>
                <label id="adminModerationReasonLabel" for="adminModerationReason" class="mt-3 block text-[7px] font-bold uppercase tracking-[.09em] text-[#8c8175]">Reason</label>
                <textarea id="adminModerationReason" maxlength="500" placeholder="Enter a clear administrative reason…"></textarea>
                <p id="adminModerationError" class="mt-2 hidden text-[7px] font-medium text-[#a85d50]"></p>
            </div>
            <div class="sari-moderation-dialog__footer">
                <button id="adminModerationCancel" type="button" class="sari-moderation-dialog__button">Cancel</button>
                <button id="adminModerationConfirm" type="button" class="sari-moderation-dialog__button">Confirm</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(() => {
    const root = document.getElementById('sariUniversalAdminMessages');
    if (!root) return;

    window.__SARI_UNIVERSAL_ADMIN_MESSAGES_CLEANUP__?.();

    const endpoints = {
        me: @json(route('messaging.api.me')),
        connections: @json(route('messaging.api.connections')),
        conversations: @json(route('messaging.api.index')),
        direct: @json(route('messaging.api.direct')),
        showTemplate: @json(route('messaging.api.show', ['conversation' => '__CONVERSATION__'])),
        sendTemplate: @json(route('messaging.api.send', ['conversation' => '__CONVERSATION__'])),
        readTemplate: @json(route('messaging.api.read', ['conversation' => '__CONVERSATION__'])),
        reactionTemplate: @json(route('messaging.api.react', ['message' => '__MESSAGE__'])),
        suspendAccountTemplate: @json(route('admin.users.suspend', ['role' => '__ROLE__', 'id' => '__ID__'])),
        restoreAccountTemplate: @json(route('admin.users.restore', ['role' => '__ROLE__', 'id' => '__ID__'])),
        banAccountTemplate: @json(route('admin.users.ban', ['role' => '__ROLE__', 'id' => '__ID__'])),
        unbanAccountTemplate: @json(route('admin.users.unban', ['role' => '__ROLE__', 'id' => '__ID__'])),
        noteAccountTemplate: @json(route('admin.users.note', ['role' => '__ROLE__', 'id' => '__ID__'])),
    };

    const csrf = @json(csrf_token());

    const state = {
        actor: null,
        conversations: [],
        contacts: [],
        selectedUuid: null,
        selectedContactKey: null,
        selectedDetail: null,
        roleFilter: 'all',
        search: '',
        loadingThread: false,
        openingContact: false,
        pendingAttachment: null,
        pendingModeration: null,
        destroyed: false,
    };

    const elements = {
        notice: document.getElementById('adminMessagingNotice'),
        conversationList: document.getElementById('adminConversationList'),
        conversationSearch: document.getElementById('adminConversationSearch'),
        filters: document.getElementById('adminConversationFilters'),
        refreshInbox: document.getElementById('adminRefreshInbox'),
        refreshStatus: document.getElementById('adminMessagingRefreshStatus'),
        mobileBack: document.getElementById('adminMobileBack'),
        threadAvatar: document.getElementById('adminThreadAvatar'),
        threadTitle: document.getElementById('adminThreadTitle'),
        threadTypeBadge: document.getElementById('adminThreadTypeBadge'),
        threadSubtitle: document.getElementById('adminThreadSubtitle'),
        threadStatus: document.getElementById('adminThreadStatus'),
        threadMessages: document.getElementById('adminThreadMessages'),
        composerWrap: document.getElementById('adminComposerWrap'),
        form: document.getElementById('adminUniversalMessageForm'),
        input: document.getElementById('adminUniversalMessageInput'),
        counter: document.getElementById('adminMessageCounter'),
        sendButton: document.getElementById('adminUniversalSendButton'),
        attachmentButton: document.getElementById('adminAttachmentButton'),
        attachmentInput: document.getElementById('adminAttachmentInput'),
        attachmentPreview: document.getElementById('adminAttachmentPreview'),
        attachmentName: document.getElementById('adminAttachmentName'),
        attachmentMeta: document.getElementById('adminAttachmentMeta'),
        attachmentRemove: document.getElementById('adminAttachmentRemove'),
        composerError: document.getElementById('adminComposerError'),
        context: document.getElementById('adminConversationContext'),
        realtimeStatus: document.getElementById('adminRealtimeStatus'),
        moderationModal: document.getElementById('adminModerationModal'),
        moderationTitle: document.getElementById('adminModerationTitle'),
        moderationCopy: document.getElementById('adminModerationCopy'),
        moderationReasonLabel: document.getElementById('adminModerationReasonLabel'),
        moderationReason: document.getElementById('adminModerationReason'),
        moderationError: document.getElementById('adminModerationError'),
        moderationConfirm: document.getElementById('adminModerationConfirm'),
        moderationCancel: document.getElementById('adminModerationCancel'),
        moderationClose: document.getElementById('adminModerationClose'),
    };

    const intervals = [];
    const realtimeChannels = new Set();
    let realtimeRefreshFrame = 0;

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const normalizeRole = (role) => role === 'social_buyer'
        ? 'Social Buyer'
        : role
            ? role.replaceAll('_', ' ').replace(/\b\w/g, c => c.toUpperCase())
            : 'User';

    const directoryRole = (role) => {
        const normalized = String(role || '').toLowerCase();
        return normalized === 'social_buyer' ? 'buyer' : normalized;
    };

    const allowedDirectoryRoles = new Set(['buyer', 'social_buyer', 'seller', 'logistics', 'rider']);

    const contactKey = (role, id) => `${String(role || '').toLowerCase()}:${Number(id)}`;

    const initials = (value) => {
        const parts = String(value || 'SARI').trim().split(/\s+/).filter(Boolean);
        return ((parts[0]?.[0] || 'S') + (parts[1]?.[0] || parts[0]?.[1] || 'A')).toUpperCase();
    };

    const humanDateFormatter = new Intl.DateTimeFormat('en-PH', {
        month: 'short',
        day: 'numeric',
    });

    const clockFormatter = new Intl.DateTimeFormat('en-PH', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });

    const fullDateFormatter = new Intl.DateTimeFormat('en-PH', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });

    const humanDate = (value) => {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';

        const now = new Date();
        return date.toDateString() === now.toDateString()
            ? clockFormatter.format(date)
            : humanDateFormatter.format(date);
    };

    const fullDate = (value) => {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '';
        return fullDateFormatter.format(date);
    };

    const urlFor = (template, token, value) => template.replace(token, encodeURIComponent(value));

    const accountActionUrl = (template, role, id) => template
        .replace('__ROLE__', encodeURIComponent(role))
        .replace('__ID__', encodeURIComponent(id));

    function debounce(callback, wait = 120) {
        let timer = 0;

        return (...args) => {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => callback(...args), wait);
        };
    }

    function showNotice(message, type = 'error') {
        if (!elements.notice) return;

        elements.notice.textContent = message;
        elements.notice.className = 'mb-4 rounded-[16px] border px-4 py-3 text-[12px]';

        if (type === 'success') {
            elements.notice.classList.add('border-[#d7e3d9]', 'bg-[#f3f8f4]', 'text-[#617667]');
        } else {
            elements.notice.classList.add('border-[#ead8d4]', 'bg-[#fdf6f4]', 'text-[#a45f55]');
        }

        elements.notice.classList.remove('hidden');
        window.setTimeout(() => elements.notice?.classList.add('hidden'), 4200);
    }

    async function request(url, options = {}) {
        const isFormData = typeof FormData !== 'undefined' && options.body instanceof FormData;

        const config = {
            method: options.method || 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.body && !isFormData ? { 'Content-Type': 'application/json' } : {}),
                ...(options.method && options.method !== 'GET' ? { 'X-CSRF-TOKEN': csrf } : {}),
                ...(options.headers || {}),
            },
            credentials: 'same-origin',
            cache: 'no-store',
        };

        if (options.body) {
            config.body = isFormData
                ? options.body
                : JSON.stringify(options.body);
        }

        const response = await fetch(url, config);
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validation = payload.errors
                ? Object.values(payload.errors).flat().find(Boolean)
                : null;

            throw new Error(validation || payload.message || 'Request failed.');
        }

        return payload;
    }

    function otherParticipants(conversation) {
        const actor = state.actor;

        return (conversation?.participants || []).filter(participant => {
            if (!actor) return true;

            return !(
                participant.role === actor.role
                && Number(participant.id) === Number(actor.id)
            );
        });
    }

    function conversationDisplayName(conversation) {
        const others = otherParticipants(conversation);

        if (conversation?.type === 'report_support') {
            const reporter = others.find(row => row.role !== 'admin');
            return reporter?.label || conversation?.subject || 'Report Support';
        }

        return others.map(row => row.label).filter(Boolean).join(', ')
            || conversation?.subject
            || 'Conversation';
    }

    function conversationRole(conversation) {
        const others = otherParticipants(conversation);

        if (conversation?.type === 'report_support') {
            return 'report_support';
        }

        return others[0]?.role || 'user';
    }

    function roleBadge(role) {
        const label = role === 'report_support' ? 'Support' : normalizeRole(role);
        const cls = role === 'report_support'
            ? 'border-[#ead8bb] bg-[#fff8eb] text-[#956c24]'
            : `sari-role-${role}`;

        return `<span class="inline-flex rounded-full border px-2 py-0.5 text-[9px] font-semibold ${cls}">${escapeHtml(label)}</span>`;
    }


    function isAccountConversation(conversation) {
        return ['admin_support', 'direct'].includes(String(conversation?.type || ''));
    }

    function selectedContactRecord(conversation) {
        if (!isAccountConversation(conversation)) return null;

        const participant = otherParticipants(conversation)
            .find(row => row.role !== 'admin');

        if (!participant?.role || participant?.id == null) return null;

        return state.contacts.find(contact => (
            String(contact.role) === String(participant.role)
            && Number(contact.id) === Number(participant.id)
        )) || null;
    }

    function accountConversationForContact(contact) {
        const matches = state.conversations.filter(conversation => {
            if (!isAccountConversation(conversation)) return false;

            return (conversation.participants || []).some(participant => (
                String(participant.role) === String(contact.role)
                && Number(participant.id) === Number(contact.id)
            ));
        });

        if (!matches.length) return null;

        // The user -> Admin support thread is the canonical support history.
        // Prefer it over an older/accidental direct thread.
        return matches.sort((a, b) => {
            const priority = value => value?.type === 'admin_support' ? 0 : 1;
            const priorityDiff = priority(a) - priority(b);
            if (priorityDiff !== 0) return priorityDiff;

            const aTime = new Date(a.last_message_at || a.updated_at || a.created_at || 0).getTime();
            const bTime = new Date(b.last_message_at || b.updated_at || b.created_at || 0).getTime();
            return bTime - aTime;
        })[0];
    }

    function syncSelectedContactFromConversation(conversation) {
        if (!isAccountConversation(conversation)) return;

        const participant = otherParticipants(conversation)
            .find(row => row.role !== 'admin');

        if (participant?.role && participant?.id != null) {
            state.selectedContactKey = contactKey(participant.role, participant.id);
        }
    }

    function renderAccountDirectory() {
        if (!elements.conversationList) return;

        const query = state.search.trim().toLowerCase();

        const roleCounts = state.contacts
            .filter(contact => allowedDirectoryRoles.has(String(contact.role || '').toLowerCase()))
            .reduce((counts, contact) => {
                const group = directoryRole(contact.role);
                counts.all += 1;
                counts[group] = (counts[group] || 0) + 1;
                return counts;
            }, { all: 0, buyer: 0, seller: 0, logistics: 0, rider: 0 });

        elements.filters?.querySelectorAll('[data-role-filter]').forEach(button => {
            const key = button.dataset.roleFilter || 'all';
            const label = key === 'all'
                ? 'All'
                : key.charAt(0).toUpperCase() + key.slice(1);
            button.textContent = `${label} ${roleCounts[key] ?? 0}`;
        });

        const filtered = state.contacts
            .filter(contact => allowedDirectoryRoles.has(String(contact.role || '').toLowerCase()))
            .filter(contact => {
                const group = directoryRole(contact.role);

                if (state.roleFilter !== 'all' && group !== state.roleFilter) {
                    return false;
                }

                if (!query) return true;

                return [
                    contact.name,
                    contact.email,
                    contact.role,
                ].join(' ').toLowerCase().includes(query);
            })
            .sort((a, b) => {
                const aConversation = accountConversationForContact(a);
                const bConversation = accountConversationForContact(b);
                const aTime = new Date(aConversation?.last_message_at || aConversation?.updated_at || aConversation?.created_at || 0).getTime();
                const bTime = new Date(bConversation?.last_message_at || bConversation?.updated_at || bConversation?.created_at || 0).getTime();

                if (aTime !== bTime) return bTime - aTime;
                return String(a.name || '').localeCompare(String(b.name || ''));
            });

        if (!filtered.length) {
            elements.conversationList.innerHTML = `
                <div class="p-8 text-center">
                    <div class="mx-auto grid h-11 w-11 place-items-center rounded-xl border border-[#e6e0d8] bg-white text-[#93877a]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                            <circle cx="9" cy="8" r="3"></circle>
                            <path d="M3 20a6 6 0 0 1 12 0"></path>
                            <path d="M17 11h4"></path>
                        </svg>
                    </div>
                    <p class="mt-3 text-[11px] font-semibold text-[#5d554c]">No matching accounts</p>
                    <p class="mt-1 text-[10px] leading-4 text-[#968d82]">Try another name or role filter.</p>
                </div>
            `;
            return;
        }

        elements.conversationList.innerHTML = filtered.map(contact => {
            const conversation = accountConversationForContact(contact);
            const selected = state.selectedContactKey === contactKey(contact.role, contact.id)
                || (conversation && conversation.uuid === state.selectedUuid);

            const unread = Number(conversation?.unread_count || 0);
            const lastMessage = conversation?.last_message;
            const preview = lastMessage?.body
                || (lastMessage?.attachment ? 'Attachment' : 'No conversation yet');

            const time = conversation
                ? humanDate(conversation.last_message_at || lastMessage?.created_at || conversation.created_at)
                : '';

            return `
                <button
                    type="button"
                    data-contact-role="${escapeHtml(contact.role)}"
                    data-contact-id="${escapeHtml(contact.id)}"
                    class="${selected ? 'is-selected' : ''}"
                    aria-label="Open conversation with ${escapeHtml(contact.name || normalizeRole(contact.role))}"
                >
                    <div class="flex gap-3">
                        <div class="account-directory-avatar">${escapeHtml(initials(contact.name))}</div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="account-directory-name">${escapeHtml(contact.name || normalizeRole(contact.role))}</p>
                                    <div class="mt-1">${roleBadge(contact.role)}</div>
                                </div>

                                <div class="flex shrink-0 flex-col items-end gap-1">
                                    ${time ? `<span class="account-directory-time">${escapeHtml(time)}</span>` : ''}
                                    ${unread > 0 ? `<span class="account-directory-unread">${unread}</span>` : ''}
                                </div>
                            </div>

                            <p class="account-directory-meta">${escapeHtml(contact.email || `Account #${contact.id}`)}</p>
                            <p class="account-directory-preview">${escapeHtml(preview)}</p>
                        </div>
                    </div>
                </button>
            `;
        }).join('');

        elements.conversationList.querySelectorAll('[data-contact-role]').forEach(button => {
            button.addEventListener('click', () => {
                openContactConversation(
                    button.dataset.contactRole,
                    Number(button.dataset.contactId)
                );
            });
        });
    }


    function formatFileSize(bytes) {
        const value = Number(bytes || 0);
        if (!value) return 'File';
        if (value < 1024) return `${value} B`;
        if (value < 1024 * 1024) return `${(value / 1024).toFixed(1)} KB`;
        return `${(value / (1024 * 1024)).toFixed(1)} MB`;
    }

    function renderAttachment(attachment) {
        if (!attachment?.url) return '';

        const name = escapeHtml(attachment.name || 'Attachment');
        const size = escapeHtml(formatFileSize(attachment.size));
        const url = escapeHtml(attachment.url);
        const downloadUrl = escapeHtml(attachment.download_url || attachment.url);

        if (attachment.is_image || String(attachment.mime || '').startsWith('image/')) {
            return `
                <div class="sari-message-attachment">
                    <a class="sari-message-image-link" href="${url}" target="_blank" rel="noopener noreferrer" title="Open image">
                        <img class="sari-message-image" src="${url}" alt="${name}" loading="lazy">
                    </a>
                    <a href="${downloadUrl}" class="mt-1.5 inline-flex text-[6.4px] font-semibold text-[#8d6a2d] hover:text-[#6f4e15]">${name} · ${size}</a>
                </div>
            `;
        }

        return `
            <div class="sari-message-attachment">
                <a class="sari-message-file" href="${downloadUrl}">
                    <span class="sari-message-file-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M6 3h8l4 4v14H6z"></path><path d="M14 3v5h5"></path>
                        </svg>
                    </span>
                    <span class="min-w-0">
                        <span class="sari-message-file-name block">${name}</span>
                        <span class="sari-message-file-meta block">${size} · Download attachment</span>
                    </span>
                    <svg class="sari-message-file-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 4v11"></path><path d="m8 11 4 4 4-4"></path><path d="M5 20h14"></path>
                    </svg>
                </a>
            </div>
        `;
    }

    function reactionSummaryHtml(reactions = []) {
        if (!Array.isArray(reactions) || !reactions.length) return '';

        return reactions.map(reaction => `
            <span class="sari-reaction-chip ${reaction.mine ? 'is-mine' : ''}" title="${reaction.mine ? 'You reacted' : 'Reaction'}">
                <span>${escapeHtml(reaction.emoji)}</span>
                ${Number(reaction.count || 0) > 1 ? `<span class="sari-reaction-count">${Number(reaction.count)}</span>` : ''}
            </span>
        `).join('');
    }

    function reactionPickerHtml(messageId) {
        const emojis = ['👍', '❤️', '😂', '😮', '😢', '🙏'];

        return `
            <span class="sari-reaction-wrap">
                <button type="button" class="sari-reaction-toggle" data-reaction-toggle="${escapeHtml(messageId)}" aria-label="React to message" title="React">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="8"></circle><path d="M9 10h.01M15 10h.01"></path><path d="M8.5 14a4.5 4.5 0 0 0 7 0"></path>
                    </svg>
                </button>
                <span class="sari-reaction-picker hidden" data-reaction-picker="${escapeHtml(messageId)}">
                    ${emojis.map(emoji => `<button type="button" class="sari-reaction-option" data-reaction-option="${escapeHtml(messageId)}" data-emoji="${emoji}" aria-label="React ${emoji}">${emoji}</button>`).join('')}
                </span>
            </span>
        `;
    }

    function updateReactionSummary(messageId, reactions) {
        const container = elements.threadMessages?.querySelector(`[data-reaction-summary="${CSS.escape(String(messageId))}"]`);
        if (container) container.innerHTML = reactionSummaryHtml(reactions);

        const message = state.selectedDetail?.messages?.find(row => Number(row.id) === Number(messageId));
        if (message) message.reactions = reactions;
    }

    function renderThread(detail) {
        const conversation = detail?.conversation;
        if (!conversation) return;

        const name = conversationDisplayName(conversation);
        const role = conversationRole(conversation);
        const participantRoles = otherParticipants(conversation).map(row => normalizeRole(row.role)).join(', ');
        const contactRecord = selectedContactRecord(conversation);

        elements.threadAvatar.textContent = initials(name);
        elements.threadTitle.textContent = name;
        elements.threadSubtitle.textContent = conversation.type === 'report_support'
            ? `Report support${conversation.context?.id ? ` · Complaint #${conversation.context.id}` : ''}`
            : (contactRecord?.email || participantRoles || 'Direct conversation');

        elements.threadTypeBadge.textContent = conversation.type === 'report_support' ? 'Support' : normalizeRole(role);
        elements.threadTypeBadge.className = `rounded-full border px-2 py-0.5 text-[9px] font-bold tracking-[0.02em] ${
            conversation.type === 'report_support'
                ? 'border-[#ead8bb] bg-[#fff8eb] text-[#956c24]'
                : `sari-role-${role}`
        }`;

        elements.threadTypeBadge.classList.remove('hidden');
        elements.threadStatus.textContent = conversation.status ? normalizeRole(conversation.status) : 'Active';
        elements.threadStatus.classList.remove('hidden');
        elements.composerWrap.classList.toggle('hidden', conversation.status !== 'active');

        const messages = detail.messages || [];

        if (!messages.length) {
            elements.threadMessages.innerHTML = `
                <div class="flex h-full min-h-[420px] items-center justify-center text-center">
                    <div class="max-w-[300px]">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-[13px] border border-[#ddd8d0] bg-white text-[#8d7d68]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"/>
                            </svg>
                        </div>
                        <p class="mt-3 text-[10px] font-bold text-[#554d44]">Start the conversation</p>
                        <p class="mt-1 text-[7.5px] leading-5 text-[#948b80]">Messages will appear here instantly when realtime is connected.</p>
                    </div>
                </div>
            `;
        } else {
            elements.threadMessages.innerHTML = messages.map(message => {
                const isAssistant = Boolean(message.metadata?.ai_assistant);
                const mine = state.actor
                    && message.sender_role === state.actor.role
                    && Number(message.sender_id) === Number(state.actor.id);

                // Human Admin replies stay on the right.
                // User messages and SARI Assistant use the Seller-style incoming layout.
                const alignRight = mine;

                const sender = isAssistant
                    ? 'SARI Assistant'
                    : (message.sender || normalizeRole(message.sender_role));
                const body = message.body || '';
                const time = fullDate(message.created_at);

                const bubbleClass = isAssistant
                    ? 'sari-message-ai'
                    : mine
                        ? 'sari-message-outgoing'
                        : 'sari-message-incoming';

                const avatar = isAssistant
                    ? `<div class="sari-admin-bot-avatar" aria-label="SARI Assistant">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 5V3"></path>
                                <circle cx="12" cy="2.5" r=".7" fill="currentColor" stroke="none"></circle>
                                <rect x="5.5" y="6" width="13" height="11" rx="4"></rect>
                                <path d="M8 17v2M16 17v2M5.5 10H4M20 10h-1.5"></path>
                                <circle cx="9.5" cy="11" r="1" fill="currentColor" stroke="none"></circle>
                                <circle cx="14.5" cy="11" r="1" fill="currentColor" stroke="none"></circle>
                                <path d="M9.5 14h5"></path>
                            </svg>
                       </div>`
                    : `<div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#f4f6f7] text-[9px] font-bold text-[#607a8f]">${escapeHtml(initials(sender))}</div>`;

                return `
                    <div class="${alignRight ? 'flex justify-end' : 'flex items-end gap-2.5'}" data-message-id="${escapeHtml(message.id)}">
                        ${alignRight ? '' : avatar}
                        <div class="max-w-[84%] sm:max-w-[70%] lg:max-w-[64%]">
                            ${isAssistant
                                ? `<div class="sari-ai-message-label">SARI Assistant · automated</div>`
                                : (!alignRight
                                    ? `<div class="sari-admin-message-label">${escapeHtml(sender)}</div>`
                                    : '')
                            }
                            ${body ? `<div class="sari-message-body ${bubbleClass}">${escapeHtml(body)}</div>` : ''}
                            ${renderAttachment(message.attachment)}
                            <div class="sari-message-tools ${alignRight ? 'is-right' : ''}">
                                ${reactionPickerHtml(message.id)}
                                <span class="sari-reaction-summary" data-reaction-summary="${escapeHtml(message.id)}">${reactionSummaryHtml(message.reactions || [])}</span>
                                <span class="sari-message-time">${escapeHtml(time)}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        renderContext(detail);
        requestAnimationFrame(() => {
            elements.threadMessages.scrollTop = elements.threadMessages.scrollHeight;
        });
    }

    function renderContext(detail) {
        const conversation = detail?.conversation;
        if (!conversation) return;

        const participants = detail.participants || conversation.participants || [];
        const contact = selectedContactRecord(conversation);
        const accountStatus = String(contact?.status || 'active').toLowerCase();
        const statusLabel = accountStatus === 'deactivated'
            ? 'Suspended'
            : normalizeRole(accountStatus);
        const statusClass = accountStatus === 'banned'
            ? 'is-banned'
            : accountStatus === 'deactivated'
                ? 'is-suspended'
                : '';

        const supportBlock = conversation.type === 'report_support' && conversation.context?.id
            ? `
                <div class="rounded-[12px] border border-[#eadfca] bg-[#fffaf0] p-3">
                    <p class="text-[7px] font-bold uppercase tracking-[0.1em] text-[#9b742f]">Report Context</p>
                    <p class="mt-1.5 text-[9px] font-bold text-[#4c4339]">Platform Complaint #${escapeHtml(conversation.context.id)}</p>
                    <p class="mt-1 text-[7px] leading-4 text-[#81776a]">This support thread is tied to a platform complaint.</p>
                </div>
            `
            : '';

        let accountActions = '';

        if (contact && conversation.type === 'direct') {
            const role = String(contact.role || '').toLowerCase();
            const id = Number(contact.id);

            if (accountStatus === 'banned') {
                accountActions = `
                    <button type="button" class="sari-admin-action sari-admin-action--primary sari-admin-action--wide" data-account-action="unban" data-role="${escapeHtml(role)}" data-id="${id}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 7v5h-5"></path><path d="M18 16a7 7 0 1 1 1-8l1 4"></path></svg>
                        Unban account
                    </button>
                `;
            } else if (accountStatus === 'deactivated') {
                accountActions = `
                    <button type="button" class="sari-admin-action sari-admin-action--primary" data-account-action="restore" data-role="${escapeHtml(role)}" data-id="${id}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 7v5h-5"></path><path d="M18 16a7 7 0 1 1 1-8l1 4"></path></svg>
                        Restore access
                    </button>
                    <button type="button" class="sari-admin-action sari-admin-action--danger" data-account-action="ban" data-role="${escapeHtml(role)}" data-id="${id}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="m7 7 10 10"></path></svg>
                        Ban account
                    </button>
                `;
            } else {
                accountActions = `
                    <button type="button" class="sari-admin-action" data-account-action="suspend" data-role="${escapeHtml(role)}" data-id="${id}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="M9 9v6M15 9v6"></path></svg>
                        Suspend access
                    </button>
                    <button type="button" class="sari-admin-action sari-admin-action--danger" data-account-action="ban" data-role="${escapeHtml(role)}" data-id="${id}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="m7 7 10 10"></path></svg>
                        Ban account
                    </button>
                `;
            }

            accountActions += `
                <button type="button" class="sari-admin-action sari-admin-action--wide" data-account-action="note" data-role="${escapeHtml(role)}" data-id="${id}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h12v18H6z"></path><path d="M9 8h6M9 12h6M9 16h4"></path></svg>
                    Add internal admin note
                </button>
            `;
        }

        const accountBlock = contact && conversation.type === 'direct'
            ? `
                <div class="${supportBlock ? 'mt-3' : ''} sari-account-card">
                    <div class="sari-account-card__head">
                        <div class="sari-account-card__avatar">${escapeHtml(initials(contact.name || normalizeRole(contact.role)))}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[9px] font-bold text-[#322c27]">${escapeHtml(contact.name || normalizeRole(contact.role))}</p>
                            <p class="mt-0.5 truncate text-[6.8px] text-[#91887d]">${escapeHtml(contact.email || `Account #${contact.id}`)}</p>
                            <div class="sari-account-meta-row">
                                ${roleBadge(contact.role)}
                                <span class="sari-account-status ${statusClass}">${escapeHtml(statusLabel)}</span>
                            </div>
                        </div>

                        <div class="sari-account-menu-wrap">
                            <button type="button" class="sari-account-menu-trigger" data-account-menu-toggle aria-expanded="false" aria-label="Account actions" title="Account actions">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <circle cx="5" cy="12" r="1.7"></circle>
                                    <circle cx="12" cy="12" r="1.7"></circle>
                                    <circle cx="19" cy="12" r="1.7"></circle>
                                </svg>
                            </button>

                            <div class="sari-account-action-menu hidden" data-account-menu>
                                <div class="sari-account-action-menu__head">
                                    <span>Account actions</span>
                                    <span>#${escapeHtml(contact.id)}</span>
                                </div>
                                <div class="sari-admin-actions">${accountActions}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `
            : '';

        elements.context.innerHTML = `
            ${supportBlock}
            ${accountBlock}

            <div class="${supportBlock || accountBlock ? 'mt-3' : ''} rounded-[12px] border border-[#e7e1d8] bg-white p-3">
                <p class="text-[7px] font-bold uppercase tracking-[0.1em] text-[#948777]">Conversation</p>
                <dl class="mt-2.5 space-y-2.5">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-[7px] text-[#978e83]">Type</dt>
                        <dd class="text-right text-[7px] font-semibold text-[#4e473f]">${escapeHtml(conversation.type === 'report_support' ? 'Report Support' : 'Direct')}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-[7px] text-[#978e83]">Conversation status</dt>
                        <dd class="text-right text-[7px] font-semibold text-[#5f7866]">${escapeHtml(normalizeRole(conversation.status || 'active'))}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-3">
                <p class="mb-2 text-[7px] font-bold uppercase tracking-[0.1em] text-[#948777]">Participants</p>
                <div class="space-y-2">
                    ${participants.map(participant => `
                        <div class="rounded-[11px] border border-[#e7e1d8] bg-white p-2.5">
                            <div class="flex items-center gap-2.5">
                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#f2f4f5] text-[7px] font-bold text-[#657985]">${escapeHtml(initials(participant.label || normalizeRole(participant.role)))}</div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[8px] font-semibold text-[#4f473e]">${escapeHtml(participant.label || normalizeRole(participant.role))}</p>
                                    <div class="mt-1">${roleBadge(participant.role)}</div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }


    const moderationConfig = {
        suspend: {
            title: 'Suspend account access',
            copy: 'This blocks account access until an Admin restores it. Enter the reason for the suspension.',
            endpoint: 'suspendAccountTemplate',
            field: 'reason',
            requiresText: true,
            danger: false,
            confirm: 'Suspend access',
        },
        restore: {
            title: 'Restore account access',
            copy: 'Restore this account to active access?',
            endpoint: 'restoreAccountTemplate',
            field: null,
            requiresText: false,
            danger: false,
            confirm: 'Restore access',
        },
        ban: {
            title: 'Ban account',
            copy: 'Banning is a stronger restriction than suspension. Enter a clear administrative reason.',
            endpoint: 'banAccountTemplate',
            field: 'reason',
            requiresText: true,
            danger: true,
            confirm: 'Ban account',
        },
        unban: {
            title: 'Unban account',
            copy: 'Remove the ban and restore this account to active access?',
            endpoint: 'unbanAccountTemplate',
            field: null,
            requiresText: false,
            danger: false,
            confirm: 'Unban account',
        },
        note: {
            title: 'Add internal admin note',
            copy: 'This note is stored in the Admin account activity timeline and is not sent as a chat message.',
            endpoint: 'noteAccountTemplate',
            field: 'note',
            requiresText: true,
            danger: false,
            confirm: 'Save note',
        },
    };

    function openModerationModal(action, role, id) {
        const config = moderationConfig[action];
        if (!config || !role || !id) return;

        const contact = state.contacts.find(row => (
            String(row.role) === String(role)
            && Number(row.id) === Number(id)
        ));

        state.pendingModeration = {
            action,
            role,
            id: Number(id),
            contact,
        };

        if (elements.moderationTitle) {
            elements.moderationTitle.textContent = config.title;
        }

        if (elements.moderationCopy) {
            const name = contact?.name ? ` ${contact.name}` : ' this account';
            elements.moderationCopy.textContent = config.copy.replace('this account', name.trim());
        }

        if (elements.moderationReasonLabel) {
            elements.moderationReasonLabel.textContent = action === 'note'
                ? 'Internal note'
                : 'Reason';
            elements.moderationReasonLabel.classList.toggle('hidden', !config.requiresText);
        }

        if (elements.moderationReason) {
            elements.moderationReason.value = '';
            elements.moderationReason.placeholder = action === 'note'
                ? 'Write a concise internal note…'
                : 'Enter a clear administrative reason…';
            elements.moderationReason.classList.toggle('hidden', !config.requiresText);
        }

        elements.moderationError?.classList.add('hidden');

        if (elements.moderationConfirm) {
            elements.moderationConfirm.textContent = config.confirm;
            elements.moderationConfirm.classList.toggle('is-danger', Boolean(config.danger));
            elements.moderationConfirm.disabled = false;
        }

        elements.moderationModal?.classList.remove('hidden');
        elements.moderationModal?.setAttribute('aria-hidden', 'false');

        if (config.requiresText) {
            window.setTimeout(() => elements.moderationReason?.focus(), 20);
        }
    }

    function closeModerationModal() {
        state.pendingModeration = null;
        elements.moderationModal?.classList.add('hidden');
        elements.moderationModal?.setAttribute('aria-hidden', 'true');
        if (elements.moderationReason) elements.moderationReason.value = '';
        elements.moderationError?.classList.add('hidden');
    }

    async function submitModerationAction() {
        const pending = state.pendingModeration;
        const config = pending ? moderationConfig[pending.action] : null;
        if (!pending || !config) return;

        const text = elements.moderationReason?.value?.trim() || '';

        if (config.requiresText && text.length < 5 && pending.action !== 'note') {
            if (elements.moderationError) {
                elements.moderationError.textContent = 'Please enter at least 5 characters.';
                elements.moderationError.classList.remove('hidden');
            }
            return;
        }

        if (config.requiresText && pending.action === 'note' && text.length < 2) {
            if (elements.moderationError) {
                elements.moderationError.textContent = 'Please enter a short admin note.';
                elements.moderationError.classList.remove('hidden');
            }
            return;
        }

        const template = endpoints[config.endpoint];
        const url = accountActionUrl(template, pending.role, pending.id);
        const body = config.field ? { [config.field]: text } : {};

        if (elements.moderationConfirm) elements.moderationConfirm.disabled = true;
        elements.moderationError?.classList.add('hidden');

        try {
            const payload = await request(url, {
                method: 'POST',
                body,
            });

            closeModerationModal();
            await loadConnections({ silent: true });

            if (state.selectedDetail) {
                renderContext(state.selectedDetail);
            }

            showNotice(payload.message || 'Account action completed.', 'success');
        } catch (error) {
            if (elements.moderationError) {
                elements.moderationError.textContent = error.message;
                elements.moderationError.classList.remove('hidden');
            }
        } finally {
            if (elements.moderationConfirm) elements.moderationConfirm.disabled = false;
        }
    }

    function setRealtimeStatus(mode) {
        if (!elements.realtimeStatus) return;

        const label = elements.realtimeStatus.querySelector('[data-live-label]');
        const connected = mode === 'connected';

        elements.realtimeStatus.classList.toggle('is-fallback', !connected);
        if (label) label.textContent = connected ? 'Realtime' : 'Sync fallback';
        elements.realtimeStatus.title = connected
            ? 'Realtime messaging connected'
            : 'Realtime is unavailable; safety sync is active';
    }

    function scheduleRealtimeRefresh(event) {
        if (!event || state.destroyed) return;

        // The sender already receives the POST response and updates locally.
        const isOwnMessage = state.actor
            && event.sender_role === state.actor.role
            && Number(event.sender_id) === Number(state.actor.id)
            && !event.metadata?.ai_assistant;

        if (isOwnMessage) return;

        window.cancelAnimationFrame(realtimeRefreshFrame);
        realtimeRefreshFrame = window.requestAnimationFrame(async () => {
            await loadInbox({ silent: true });

            if (
                state.selectedUuid
                && event.conversation_uuid === state.selectedUuid
                && !state.loadingThread
            ) {
                await selectConversation(state.selectedUuid, {
                    pushState: false,
                    silent: true,
                });
            }
        });
    }

    function syncRealtimeSubscriptions() {
        if (!window.Echo) {
            setRealtimeStatus('fallback');
            return;
        }

        const channels = new Set(
            state.conversations
                .map(conversation => conversation.realtime_channel)
                .filter(Boolean)
        );

        channels.forEach(channelName => {
            if (realtimeChannels.has(channelName)) return;

            try {
                window.Echo
                    .channel(channelName)
                    .listen('.platform.message', scheduleRealtimeRefresh)
                    .listen('.platform.reaction', scheduleRealtimeRefresh);

                realtimeChannels.add(channelName);
                setRealtimeStatus('connected');
            } catch (_) {
                setRealtimeStatus('fallback');
            }
        });
    }

    async function loadInbox({ silent = false } = {}) {
        if (!silent && elements.refreshStatus) {
            elements.refreshStatus.textContent = 'Refreshing…';
        }

        try {
            const payload = await request(endpoints.conversations);
            state.conversations = Array.isArray(payload.conversations)
                ? payload.conversations
                : [];

            renderAccountDirectory();
            syncRealtimeSubscriptions();

            if (!silent && elements.refreshStatus) {
                elements.refreshStatus.textContent = 'Updated just now';

                window.setTimeout(() => {
                    if (!state.destroyed && elements.refreshStatus) {
                        elements.refreshStatus.textContent = 'Directory synced';
                    }
                }, 1800);
            }
        } catch (error) {
            if (!silent) showNotice(error.message);

            if (elements.refreshStatus) {
                elements.refreshStatus.textContent = 'Refresh failed';
            }
        }
    }

    async function loadConnections({ silent = false } = {}) {
        try {
            const payload = await request(endpoints.connections);

            state.actor = payload.actor || state.actor;
            state.contacts = Array.isArray(payload.contacts)
                ? payload.contacts
                : [];

            renderAccountDirectory();
        } catch (error) {
            if (!silent) showNotice(error.message);
            throw error;
        }
    }

    async function loadActor() {
        const payload = await request(endpoints.me);
        state.actor = payload.actor || null;
    }

    async function selectConversation(uuid, { pushState = true, silent = false } = {}) {
        if (!uuid || state.loadingThread) return;

        state.loadingThread = true;
        state.selectedUuid = uuid;

        const knownConversation = state.conversations.find(row => row.uuid === uuid);
        if (knownConversation) {
            syncSelectedContactFromConversation(knownConversation);
        }

        renderAccountDirectory();

        if (!silent) {
            elements.threadMessages.innerHTML = '<div class="p-8 text-center text-[11px] text-[#968d82]">Loading conversation…</div>';
        }

        try {
            const payload = await request(
                urlFor(endpoints.showTemplate, '__CONVERSATION__', uuid)
            );

            state.selectedDetail = payload;

            if (payload?.conversation) {
                syncSelectedContactFromConversation(payload.conversation);
            }

            renderThread(payload);

            await request(
                urlFor(endpoints.readTemplate, '__CONVERSATION__', uuid),
                { method: 'POST' }
            ).catch(() => null);

            state.conversations = state.conversations.map(row => (
                row.uuid === uuid
                    ? { ...row, unread_count: 0 }
                    : row
            ));

            renderAccountDirectory();

            if (pushState) {
                const url = new URL(window.location.href);
                url.searchParams.set('conversation', uuid);
                window.history.replaceState({}, '', url.toString());
            }

            root.dataset.mobilePane = 'thread';
        } catch (error) {
            showNotice(error.message);
        } finally {
            state.loadingThread = false;
        }
    }

    async function openContactConversation(role, id) {
        if (!role || !id || state.openingContact) return;

        state.openingContact = true;
        state.selectedContactKey = contactKey(role, id);
        renderAccountDirectory();

        elements.threadMessages.innerHTML = '<div class="p-8 text-center text-[11px] text-[#968d82]">Opening conversation…</div>';

        try {
            const existing = state.contacts.find(contact => (
                String(contact.role) === String(role)
                && Number(contact.id) === Number(id)
            ));

            const existingConversation = existing
                ? accountConversationForContact(existing)
                : null;

            if (existingConversation?.uuid) {
                await selectConversation(existingConversation.uuid);
                return;
            }

            const payload = await request(endpoints.direct, {
                method: 'POST',
                body: {
                    target_role: role,
                    target_id: id,
                },
            });

            const uuid = payload.conversation?.uuid;

            if (!uuid) {
                throw new Error('Unable to open the direct conversation.');
            }

            await loadInbox({ silent: true });
            await selectConversation(uuid);
        } catch (error) {
            showNotice(error.message);
        } finally {
            state.openingContact = false;
        }
    }

    function clearPendingAttachment() {
        state.pendingAttachment = null;
        if (elements.attachmentInput) elements.attachmentInput.value = '';
        elements.attachmentPreview?.classList.add('hidden');
    }

    function setPendingAttachment(file) {
        if (!file) {
            clearPendingAttachment();
            return;
        }

        const maxBytes = 15 * 1024 * 1024;
        if (file.size > maxBytes) {
            showNotice('Attachment must be 15 MB or smaller.');
            clearPendingAttachment();
            return;
        }

        state.pendingAttachment = file;
        if (elements.attachmentName) elements.attachmentName.textContent = file.name || 'Attachment';
        if (elements.attachmentMeta) {
            elements.attachmentMeta.textContent = `${formatFileSize(file.size)} · Ready to send`;
        }
        elements.attachmentPreview?.classList.remove('hidden');
    }

    async function sendMessage(event = null) {
        event?.preventDefault?.();

        if (!state.selectedUuid || elements.sendButton?.disabled) return;

        const body = elements.input?.value?.trim() || '';
        const attachment = state.pendingAttachment;
        if (!body && !attachment) return;

        elements.sendButton.disabled = true;
        elements.composerError?.classList.add('hidden');

        const formData = new FormData();
        if (body) formData.append('body', body);
        if (attachment) formData.append('attachment', attachment, attachment.name);

        try {
            const payload = await request(
                urlFor(endpoints.sendTemplate, '__CONVERSATION__', state.selectedUuid),
                {
                    method: 'POST',
                    body: formData,
                }
            );

            if (elements.input) elements.input.value = '';
            if (elements.counter) elements.counter.textContent = '0 / 3000';
            clearPendingAttachment();

            // Update the open thread directly from the POST response. This is
            // intentionally AJAX-only: no form navigation and no page reload.
            if (payload?.message && state.selectedDetail) {
                const currentMessages = Array.isArray(state.selectedDetail.messages)
                    ? state.selectedDetail.messages
                    : [];

                if (!currentMessages.some(row => Number(row.id) === Number(payload.message.id))) {
                    state.selectedDetail.messages = [...currentMessages, payload.message];
                }

                renderThread(state.selectedDetail);
            }

            await loadInbox({ silent: true });
        } catch (error) {
            if (elements.composerError) {
                elements.composerError.textContent = error.message;
                elements.composerError.classList.remove('hidden');
            }
        } finally {
            elements.sendButton.disabled = false;
            elements.input?.focus();
        }
    }

    async function toggleReaction(messageId, emoji) {
        if (!messageId || !emoji) return;

        try {
            const payload = await request(
                urlFor(endpoints.reactionTemplate, '__MESSAGE__', messageId),
                {
                    method: 'POST',
                    body: { emoji },
                }
            );

            updateReactionSummary(messageId, payload.reactions || []);
        } catch (error) {
            showNotice(error.message);
        }
    }

    async function refreshDirectory() {
        if (elements.refreshStatus) {
            elements.refreshStatus.textContent = 'Refreshing…';
        }

        try {
            await Promise.all([
                loadConnections({ silent: true }),
                loadInbox({ silent: true }),
            ]);

            if (elements.refreshStatus) {
                elements.refreshStatus.textContent = 'Updated just now';

                window.setTimeout(() => {
                    if (!state.destroyed && elements.refreshStatus) {
                        elements.refreshStatus.textContent = 'Directory synced';
                    }
                }, 1800);
            }
        } catch (error) {
            showNotice(error.message);

            if (elements.refreshStatus) {
                elements.refreshStatus.textContent = 'Refresh failed';
            }
        }
    }

    async function initialLoad() {
        try {
            setRealtimeStatus(window.Echo ? 'connected' : 'fallback');
            await loadActor();

            await Promise.all([
                loadConnections({ silent: true }),
                loadInbox({ silent: true }),
            ]);

            renderAccountDirectory();

            const url = new URL(window.location.href);
            const requestedUuid = url.searchParams.get('conversation');

            if (requestedUuid) {
                await selectConversation(requestedUuid, { pushState: false });
            } else {
                const newestConversation = [...state.conversations]
                    .sort((a, b) => {
                        const aTime = new Date(a.last_message_at || a.updated_at || a.created_at || 0).getTime();
                        const bTime = new Date(b.last_message_at || b.updated_at || b.created_at || 0).getTime();
                        return bTime - aTime;
                    })[0];

                if (newestConversation?.uuid) {
                    await selectConversation(newestConversation.uuid, {
                        pushState: false,
                        silent: true,
                    });
                }
            }
        } catch (error) {
            showNotice(error.message);
        }
    }

    const renderDirectoryFromSearch = debounce(() => {
        renderAccountDirectory();
    }, 120);

    elements.conversationSearch?.addEventListener('input', event => {
        state.search = event.target.value || '';
        renderDirectoryFromSearch();
    }, { passive: true });

    elements.filters?.querySelectorAll('[data-role-filter]').forEach(button => {
        button.addEventListener('click', () => {
            state.roleFilter = button.dataset.roleFilter || 'all';

            elements.filters.querySelectorAll('[data-role-filter]').forEach(item => {
                item.classList.toggle('is-active', item === button);
            });

            renderAccountDirectory();
        });
    });

    elements.refreshInbox?.addEventListener('click', refreshDirectory);

    elements.mobileBack?.addEventListener('click', () => {
        root.dataset.mobilePane = 'inbox';
    });

    elements.input?.addEventListener('input', () => {
        if (elements.counter && elements.input) elements.counter.textContent = `${elements.input.value.length} / 3000`;
    });

    // The send control is deliberately type=button. Even if another script
    // fails, clicking Send cannot fall back to a native form navigation.
    elements.sendButton?.addEventListener('click', () => sendMessage());
    elements.form?.addEventListener('submit', event => {
        event.preventDefault();
        sendMessage();
    });

    elements.attachmentButton?.addEventListener('click', () => {
        elements.attachmentInput?.click();
    });

    elements.attachmentInput?.addEventListener('change', () => {
        setPendingAttachment(elements.attachmentInput.files?.[0] || null);
    });

    elements.attachmentRemove?.addEventListener('click', clearPendingAttachment);

    elements.composerWrap?.addEventListener('dragover', event => {
        event.preventDefault();
        elements.composerWrap.classList.add('is-dragging');
    });

    elements.composerWrap?.addEventListener('dragleave', event => {
        if (!elements.composerWrap.contains(event.relatedTarget)) {
            elements.composerWrap.classList.remove('is-dragging');
        }
    });

    elements.composerWrap?.addEventListener('drop', event => {
        event.preventDefault();
        elements.composerWrap.classList.remove('is-dragging');
        setPendingAttachment(event.dataTransfer?.files?.[0] || null);
    });

    elements.threadMessages?.addEventListener('click', event => {
        const reactionOption = event.target.closest('[data-reaction-option]');
        if (reactionOption) {
            event.preventDefault();
            const messageId = reactionOption.dataset.reactionOption;
            const emoji = reactionOption.dataset.emoji;
            elements.threadMessages.querySelectorAll('[data-reaction-picker]').forEach(picker => picker.classList.add('hidden'));
            toggleReaction(messageId, emoji);
            return;
        }

        const reactionToggle = event.target.closest('[data-reaction-toggle]');
        if (reactionToggle) {
            event.preventDefault();
            const messageId = reactionToggle.dataset.reactionToggle;
            const picker = elements.threadMessages.querySelector(`[data-reaction-picker="${CSS.escape(String(messageId))}"]`);

            elements.threadMessages.querySelectorAll('[data-reaction-picker]').forEach(item => {
                if (item !== picker) item.classList.add('hidden');
            });

            picker?.classList.toggle('hidden');
        }
    });


    elements.context?.addEventListener('click', event => {
        const menuToggle = event.target.closest('[data-account-menu-toggle]');
        if (menuToggle) {
            event.preventDefault();

            const wrap = menuToggle.closest('.sari-account-menu-wrap');
            const menu = wrap?.querySelector('[data-account-menu]');
            const opening = menu?.classList.contains('hidden');

            elements.context.querySelectorAll('[data-account-menu]').forEach(item => {
                if (item !== menu) item.classList.add('hidden');
            });
            elements.context.querySelectorAll('[data-account-menu-toggle]').forEach(item => {
                if (item !== menuToggle) item.setAttribute('aria-expanded', 'false');
            });

            menu?.classList.toggle('hidden', !opening);
            menuToggle.setAttribute('aria-expanded', opening ? 'true' : 'false');
            return;
        }

        const button = event.target.closest('[data-account-action]');
        if (!button) return;

        event.preventDefault();
        button.closest('[data-account-menu]')?.classList.add('hidden');
        button.closest('.sari-account-menu-wrap')?.querySelector('[data-account-menu-toggle]')?.setAttribute('aria-expanded', 'false');

        openModerationModal(
            button.dataset.accountAction,
            button.dataset.role,
            Number(button.dataset.id)
        );
    });

    elements.moderationConfirm?.addEventListener('click', submitModerationAction);
    elements.moderationCancel?.addEventListener('click', closeModerationModal);
    elements.moderationClose?.addEventListener('click', closeModerationModal);
    elements.moderationModal?.addEventListener('click', event => {
        if (event.target === elements.moderationModal) closeModerationModal();
    });

    const onDocumentClick = event => {
        if (!event.target.closest('.sari-reaction-wrap')) {
            elements.threadMessages?.querySelectorAll('[data-reaction-picker]').forEach(picker => picker.classList.add('hidden'));
        }

        if (!event.target.closest('.sari-account-menu-wrap')) {
            elements.context?.querySelectorAll('[data-account-menu]').forEach(menu => menu.classList.add('hidden'));
            elements.context?.querySelectorAll('[data-account-menu-toggle]').forEach(button => button.setAttribute('aria-expanded', 'false'));
        }
    };

    const onKeydown = event => {
        if (event.key === 'Escape' && !elements.moderationModal?.classList.contains('hidden')) {
            closeModerationModal();
            return;
        }

        if (event.key === 'Escape') {
            elements.context?.querySelectorAll('[data-account-menu]').forEach(menu => menu.classList.add('hidden'));
            elements.context?.querySelectorAll('[data-account-menu-toggle]').forEach(button => button.setAttribute('aria-expanded', 'false'));
        }

        if (
            event.key === 'Enter'
            && !event.shiftKey
            && document.activeElement === elements.input
        ) {
            event.preventDefault();
            sendMessage();
        }
    };

    document.addEventListener('click', onDocumentClick);
    document.addEventListener('keydown', onKeydown);

    // Safety fallback only. Reverb/Echo is now the primary message transport.
    intervals.push(window.setInterval(async () => {
        if (document.hidden || state.destroyed) return;

        await loadInbox({ silent: true });

        if (state.selectedUuid && !state.loadingThread) {
            await selectConversation(state.selectedUuid, {
                pushState: false,
                silent: true,
            });
        }
    }, 30000));

    // Account additions/changes are much less frequent than new messages.
    intervals.push(window.setInterval(async () => {
        if (document.hidden || state.destroyed) return;
        await loadConnections({ silent: true }).catch(() => null);
    }, 60000));

    initialLoad();

    let cleaned = false;

    function cleanup() {
        if (cleaned) return;

        cleaned = true;
        state.destroyed = true;

        intervals.forEach(id => window.clearInterval(id));
        window.cancelAnimationFrame(realtimeRefreshFrame);
        document.removeEventListener('click', onDocumentClick);
        document.removeEventListener('keydown', onKeydown);

        if (window.Echo) {
            realtimeChannels.forEach(channelName => {
                try { window.Echo.leave(channelName); } catch (_) {}
            });
        }
        realtimeChannels.clear();

        if (window.__SARI_UNIVERSAL_ADMIN_MESSAGES_CLEANUP__ === cleanup) {
            window.__SARI_UNIVERSAL_ADMIN_MESSAGES_CLEANUP__ = null;
        }
    }

    window.__SARI_UNIVERSAL_ADMIN_MESSAGES_CLEANUP__ = cleanup;
    document.addEventListener('livewire:navigating', cleanup, { once: true });
})();
</script>
@endpush
