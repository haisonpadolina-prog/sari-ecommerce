@extends('layouts.courier')

@section('title', 'Chat / Messaging — SARI Rider')
@section('header-title', 'Chat / Messaging')
@section('header-subtitle', 'Rider conversations')

@push('styles')
<style id="sariRiderUnifiedMessengerInlineV4">
@verbatim
/* SARI Rider unified messaging — Buyer Messenger visual language, neutral surfaces */
#sariRiderMessenger{--rm-gold:#c99128;--rm-gold-dark:#ad781b;--rm-ink:#303740;--rm-muted:#87929f;--rm-border:#e7ebef;--rm-canvas:#f5f6f8;font-family:Poppins,ui-sans-serif,system-ui,sans-serif;width:100%;min-width:0;color:var(--rm-ink);font-size:12px}
#sariRiderMessenger *,#sariRiderMessenger *::before,#sariRiderMessenger *::after{box-sizing:border-box}
#sariRiderMessenger [hidden]{display:none!important}
#sariRiderMessenger button,#sariRiderMessenger input,#sariRiderMessenger textarea{font:inherit}
#sariRiderMessenger button{cursor:pointer}
#sariRiderMessenger button:disabled{opacity:.55;cursor:wait}
#sariRiderMessenger button:focus-visible,#sariRiderMessenger input:focus-visible,#sariRiderMessenger textarea:focus-visible,#sariRiderMessenger a:focus-visible{outline:2px solid var(--rm-gold);outline-offset:2px}
#sariRiderMessenger .sari-rm-sr-only{position:absolute!important;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap}
#sariRiderMessenger .sari-rm-server-alert{padding:10px 13px;margin:0 0 12px;border:1px solid #eec7c7;border-radius:11px;background:#fff7f7;color:#a74242;font-size:11px}
#sariRiderMessenger .sari-rm-server-alert.is-success{background:#f2faf5;border-color:#d1e8da;color:#386c4b}
#sariRiderMessenger .sari-rm-shell{position:relative;display:grid;grid-template-columns:310px minmax(0,1fr);width:100%;height:min(790px,calc(100dvh - 132px));min-height:540px;overflow:hidden;border:1px solid #e4e8ed;border-radius:20px;background:#fff;box-shadow:0 8px 30px rgba(21,30,42,.065)}
#sariRiderMessenger .sari-rm-inbox{display:flex;flex-direction:column;min-width:0;min-height:0;border-right:1px solid var(--rm-border);background:#fff}
#sariRiderMessenger .sari-rm-inbox-header{padding:19px 15px 12px;border-bottom:1px solid var(--rm-border);flex:0 0 auto}
#sariRiderMessenger .sari-rm-heading-row{display:flex;align-items:center;justify-content:space-between;gap:12px}
#sariRiderMessenger .sari-rm-eyebrow{margin:0 0 3px;color:#a87316;font-size:9px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
#sariRiderMessenger .sari-rm-heading-row h1{margin:0;font-size:20px;font-weight:750;letter-spacing:-.035em;line-height:1.2;color:var(--rm-ink)}
#sariRiderMessenger .sari-rm-new{display:grid;place-items:center;width:36px;height:36px;flex:0 0 36px;border:1px solid #ead7b5;border-radius:11px;background:#fffaf0;color:var(--rm-gold-dark)}
#sariRiderMessenger .sari-rm-new:hover{background:#fff3dd}
#sariRiderMessenger .sari-rm-new svg{width:19px;height:19px}
#sariRiderMessenger .sari-rm-search-box{position:relative;display:flex;align-items:center;margin-top:15px}
#sariRiderMessenger .sari-rm-search-box svg{position:absolute;left:13px;width:17px;height:17px;color:var(--rm-gold);pointer-events:none}
#sariRiderMessenger .sari-rm-search-box input,#sariRiderMessenger .sari-rm-contact-search input{width:100%;height:42px;min-width:0;padding:0 12px 0 40px;border:1px solid #e4e7ec;border-radius:999px;background:#f5f6f8;color:#344054;font-size:11px;outline:0}
#sariRiderMessenger .sari-rm-search-box input:focus,#sariRiderMessenger .sari-rm-contact-search input:focus{border:1px solid var(--rm-gold);background:#fff;box-shadow:none}
#sariRiderMessenger .sari-rm-search-box input::placeholder,#sariRiderMessenger .sari-rm-contact-search input::placeholder{color:#9ba5af}
#sariRiderMessenger .sari-rm-tabs,#sariRiderMessenger .sari-rm-contact-tabs{display:flex;gap:5px;overflow-x:auto;scrollbar-width:thin;margin-top:13px;padding-bottom:2px}
#sariRiderMessenger .sari-rm-tabs button,#sariRiderMessenger .sari-rm-contact-tabs button{border:0;border-radius:8px;background:transparent;padding:7px 8px;color:#89939e;font-size:10px;font-weight:650;white-space:nowrap}
#sariRiderMessenger .sari-rm-tabs button.is-active,#sariRiderMessenger .sari-rm-contact-tabs button.is-active{background:#f1f3f6;color:#a16c15;font-weight:800}
#sariRiderMessenger .sari-rm-threads{flex:1;min-height:0;overflow-y:auto;padding:9px 7px 14px;scrollbar-width:thin;scrollbar-color:#d3d9df transparent}
#sariRiderMessenger .sari-rm-thread{display:flex;align-items:center;gap:11px;width:100%;min-height:75px;margin:2px 0;padding:10px;border:0;border-left:3px solid transparent;border-radius:12px;background:#fff;text-align:left;color:inherit}
#sariRiderMessenger .sari-rm-thread:hover{background:#f7f8fa}
#sariRiderMessenger .sari-rm-thread.is-active{border-left-color:#cbd2db;background:#eef1f4}
#sariRiderMessenger .sari-rm-avatar{display:grid;place-items:center;width:47px;height:47px;flex:0 0 47px;overflow:hidden;border:1px solid #e1e6ec;border-radius:50%;background:#f0f3f7;color:#778391;font-size:14px;font-weight:750;text-transform:uppercase}
#sariRiderMessenger .sari-rm-avatar.is-small{width:41px;height:41px;flex-basis:41px;font-size:12px}
#sariRiderMessenger .sari-rm-avatar.is-tiny{width:29px;height:29px;flex-basis:29px;font-size:10px}
#sariRiderMessenger .sari-rm-avatar.is-large{width:85px;height:85px;flex-basis:85px;margin:0 auto;font-size:25px;border:3px solid #fff;box-shadow:0 0 0 2px #f0dfbb}
#sariRiderMessenger .sari-rm-avatar.is-admin{background:#fff4de;border-color:#eed7ab;color:#a4721c}
#sariRiderMessenger .sari-rm-avatar.is-logistics{background:#edf5ff;border-color:#d6e7f9;color:#527eac}
#sariRiderMessenger .sari-rm-thread-copy{display:grid;gap:5px;min-width:0;flex:1}
#sariRiderMessenger .sari-rm-thread-copy strong{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#303740;font-size:12px;font-weight:750}
#sariRiderMessenger .sari-rm-thread-copy small{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#939eaa;font-size:10px;line-height:1.5}
#sariRiderMessenger .sari-rm-unread{display:grid;place-items:center;min-width:20px;height:20px;padding:0 5px;border-radius:999px;background:var(--rm-gold);color:#fff;font-size:10px;font-weight:750}
#sariRiderMessenger .sari-rm-inbox-bottom{display:flex;justify-content:space-between;align-items:center;gap:9px;border-top:1px solid var(--rm-border);padding:12px 14px;font-size:9.5px;color:#9ca6b0}
#sariRiderMessenger .sari-rm-inbox-count{color:#9c7025;font-weight:700}
#sariRiderMessenger .sari-rm-chat{display:flex;flex-direction:column;min-height:0;min-width:0;overflow:hidden;background:var(--rm-canvas)}
#sariRiderMessenger .sari-rm-chat-header{display:flex;justify-content:space-between;align-items:center;gap:10px;min-height:77px;flex:0 0 auto;border-bottom:1px solid var(--rm-border);background:#fff;padding:12px 22px}
#sariRiderMessenger .sari-rm-peer{display:flex;align-items:center;gap:12px;min-width:0}
#sariRiderMessenger .sari-rm-peer-copy{min-width:0}
#sariRiderMessenger .sari-rm-peer-copy h2{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin:0;color:#303740;font-size:14px;font-weight:750}
#sariRiderMessenger .sari-rm-peer-copy p{margin:5px 0 0;color:#9aa4af;font-size:10px}
#sariRiderMessenger .sari-rm-info-button{display:grid;place-items:center;width:36px;height:36px;flex:0 0 36px;border:0;border-radius:50%;background:transparent;color:var(--rm-gold)}
#sariRiderMessenger .sari-rm-info-button:hover{background:#fff7e8}
#sariRiderMessenger .sari-rm-info-button svg{width:19px;height:19px}
#sariRiderMessenger .sari-rm-conversation{display:flex;flex-direction:column;gap:17px;flex:1;min-height:0;overflow-y:auto;overflow-wrap:anywhere;padding:25px 20px;scrollbar-width:thin;scrollbar-color:#d0d8e0 transparent}
#sariRiderMessenger .sari-rm-message{display:flex;align-items:flex-end;gap:9px;min-width:0}
#sariRiderMessenger .sari-rm-message.is-mine{justify-content:flex-end}
#sariRiderMessenger .sari-rm-message-body{min-width:0;max-width:min(78%,580px)}
#sariRiderMessenger .sari-rm-sender{display:block;margin-bottom:6px;color:#8995a1;font-size:9.5px;font-weight:650}
#sariRiderMessenger .sari-rm-message.is-mine .sari-rm-sender,#sariRiderMessenger .sari-rm-message.is-mine .sari-rm-time{text-align:right}
#sariRiderMessenger .sari-rm-bubble{margin:0;padding:12px 15px;border:1px solid #e9ecf0;border-radius:16px 16px 16px 5px;background:#fff;color:#394451;font-size:11.5px;font-weight:450;line-height:1.65;white-space:pre-wrap;overflow-wrap:anywhere;box-shadow:0 2px 7px rgba(22,34,49,.035)}
#sariRiderMessenger .sari-rm-message.is-mine .sari-rm-bubble{border-radius:16px 16px 5px 16px;background:#fff}
#sariRiderMessenger .sari-rm-time{display:block;margin:6px 2px 0;color:#9da6b0;font-size:9px}
#sariRiderMessenger .sari-rm-attachment{display:flex;align-items:center;gap:8px;max-width:100%;margin:7px 0 0;padding:9px 11px;border:1px solid #e8ebf0;border-radius:12px;background:#fff;color:#6b7580;font-size:10px;text-decoration:none}
#sariRiderMessenger .sari-rm-attachment:hover{border-color:#cfac6b;color:#936c29}
#sariRiderMessenger .sari-rm-attachment img{display:block;max-width:260px;max-height:250px;object-fit:contain;border-radius:9px}
#sariRiderMessenger .sari-rm-empty{margin:auto;max-width:330px;padding:30px 17px;text-align:center;color:#97a2ae}
#sariRiderMessenger .sari-rm-empty-icon{display:grid;place-items:center;width:64px;height:64px;margin:0 auto 15px;border-radius:50%;background:#eef1f5;color:var(--rm-gold)}
#sariRiderMessenger .sari-rm-empty-icon svg{width:29px;height:29px}
#sariRiderMessenger .sari-rm-empty h3{margin:0;color:#535e6c;font-size:14px;font-weight:750}
#sariRiderMessenger .sari-rm-empty p{margin:8px 0 0;color:#929fac;font-size:11px;line-height:1.7}
#sariRiderMessenger .sari-rm-hint{margin:12px;color:#949fac;font-size:11px;line-height:1.7}
#sariRiderMessenger .sari-rm-feedback{margin:0;padding:10px 16px;border-top:1px solid #f0caca;background:#fff5f5;color:#a63f45;font-size:11px}
#sariRiderMessenger .sari-rm-composer{flex:0 0 auto;border-top:1px solid var(--rm-border);background:#fff;padding:13px 15px}
#sariRiderMessenger .sari-rm-composer form{display:flex;align-items:center;gap:10px;width:100%;min-width:0;margin:0;padding:0;border:0;box-shadow:none;background:transparent}
#sariRiderMessenger .sari-rm-attach-button{display:grid;place-items:center;width:31px;height:37px;flex:0 0 31px;border-radius:8px;color:var(--rm-gold);cursor:pointer}
#sariRiderMessenger .sari-rm-attach-button:hover{background:#fff6e8}
#sariRiderMessenger .sari-rm-attach-button svg{width:20px;height:20px}
#sariRiderMessenger .sari-rm-compose-field{flex:1;min-width:0;border:1px solid #d6aa59;border-radius:17px;padding:0 13px;background:#fff}
#sariRiderMessenger .sari-rm-compose-field:focus-within{border-color:var(--rm-gold);box-shadow:none}
#sariRiderMessenger .sari-rm-composer textarea{display:block;width:100%;min-width:0;min-height:54px;max-height:120px;padding:16px 0 12px;border:0;outline:0;resize:none;background:transparent;color:#303740;font-size:11.5px;line-height:20px;box-shadow:none}
#sariRiderMessenger .sari-rm-composer textarea:focus{outline:0;box-shadow:none}
#sariRiderMessenger .sari-rm-composer textarea::placeholder{color:#a2aab5}
#sariRiderMessenger .sari-rm-file-chip{display:flex;align-items:center;justify-content:space-between;gap:8px;max-width:100%;border-radius:10px;background:#f4f6f8;padding:7px 10px;margin-top:9px;color:#677585;font-size:10px}
#sariRiderMessenger .sari-rm-file-chip span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
#sariRiderMessenger .sari-rm-file-chip button{border:0;background:transparent;color:#627181;font-size:18px}
#sariRiderMessenger .sari-rm-send{display:grid;place-items:center;width:42px;height:42px;flex:0 0 42px;border:0;border-radius:50%;background:var(--rm-gold);color:#fff;box-shadow:0 4px 10px rgba(201,145,40,.20)}
#sariRiderMessenger .sari-rm-send:hover{background:var(--rm-gold-dark)}
#sariRiderMessenger .sari-rm-send svg{width:20px;height:20px}
#sariRiderMessenger #sariRmLegacyForm textarea{flex:1;min-height:52px;padding:13px 15px;border:1px solid #d6aa59;border-radius:15px}
#sariRiderMessenger .sari-rm-drawer-backdrop{position:absolute;inset:0;z-index:8;width:100%;height:100%;border:0;background:rgba(23,30,40,.12);backdrop-filter:blur(2px)}
#sariRiderMessenger .sari-rm-drawer{position:absolute;right:0;top:0;bottom:0;z-index:9;display:flex;flex-direction:column;width:min(360px,100%);border-left:1px solid var(--rm-border);background:#f5f7fa;box-shadow:-14px 0 40px rgba(24,33,45,.14);transform:translateX(105%);visibility:hidden;transition:transform .27s ease,visibility .27s}
#sariRiderMessenger .sari-rm-drawer.is-open{transform:translateX(0);visibility:visible}
#sariRiderMessenger .sari-rm-drawer header,#sariRiderMessenger .sari-rm-contact-panel header{display:flex;align-items:center;justify-content:space-between;gap:12px;border-bottom:1px solid var(--rm-border);padding:17px;background:#fff}
#sariRiderMessenger .sari-rm-drawer header h2,#sariRiderMessenger .sari-rm-contact-panel header h2{margin:0;color:#303740;font-size:14px;font-weight:750}
#sariRiderMessenger .sari-rm-drawer header p,#sariRiderMessenger .sari-rm-contact-panel header p{margin:4px 0 0;color:#98a3af;font-size:10px}
#sariRiderMessenger .sari-rm-drawer header button,#sariRiderMessenger .sari-rm-contact-panel header button{display:grid;place-items:center;width:34px;height:34px;border:0;border-radius:50%;background:#f3f5f7;color:#586475;font-size:22px}
#sariRiderMessenger .sari-rm-drawer-body{padding:25px 18px;text-align:center}
#sariRiderMessenger .sari-rm-drawer-body h3{margin:15px 0 0;font-size:16px;color:#303740}
#sariRiderMessenger .sari-rm-drawer-body>p{margin:5px 0 0;color:#9da7b1;font-size:11px}
#sariRiderMessenger .sari-rm-info-note{margin:26px 0 0;padding:17px;border:1px solid #e5e9ef;border-radius:14px;background:#fff;text-align:left}
#sariRiderMessenger .sari-rm-info-note strong{font-size:11px;color:#4c5864}
#sariRiderMessenger .sari-rm-info-note p{font-size:10px;color:#8b96a2;line-height:1.7}
#sariRiderMessenger .sari-rm-contact-overlay{position:fixed;inset:0;z-index:9999;display:grid;place-items:center;background:rgba(18,28,40,.42);padding:15px}
#sariRiderMessenger .sari-rm-contact-panel{display:flex;flex-direction:column;min-width:0;width:min(560px,100%);height:min(680px,88dvh);overflow:hidden;border-radius:19px;background:#fff;box-shadow:0 24px 70px rgba(17,25,39,.25)}
#sariRiderMessenger .sari-rm-contact-search{display:block;margin:15px 16px 0}
#sariRiderMessenger .sari-rm-contact-search input{padding-left:16px}
#sariRiderMessenger .sari-rm-contact-tabs{margin:11px 16px}
#sariRiderMessenger .sari-rm-contacts{min-height:0;flex:1;overflow-y:auto;padding:7px 12px 19px}
#sariRiderMessenger .sari-rm-contact-option{display:flex;align-items:center;gap:11px;width:100%;min-height:67px;padding:10px 12px;border:0;border-radius:12px;background:#fff;text-align:left;color:#344054}
#sariRiderMessenger .sari-rm-contact-option:hover{background:#f5f6f8}
#sariRiderMessenger .sari-rm-contact-option strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#303740;font-size:12px}
#sariRiderMessenger .sari-rm-contact-option small{display:block;margin-top:5px;color:#99a3af;font-size:10px}
@media(max-width:1080px){#sariRiderMessenger .sari-rm-shell{grid-template-columns:260px minmax(0,1fr)}}
@media(max-width:650px){#sariRiderMessenger .sari-rm-shell{grid-template-columns:minmax(0,1fr);grid-template-rows:210px minmax(0,1fr);height:calc(100dvh - 110px);min-height:590px;border-radius:14px}#sariRiderMessenger .sari-rm-inbox{border-right:0;border-bottom:1px solid var(--rm-border)}#sariRiderMessenger .sari-rm-inbox-header{padding:9px 11px 6px}#sariRiderMessenger .sari-rm-eyebrow{display:none}#sariRiderMessenger .sari-rm-heading-row h1{font-size:15px}#sariRiderMessenger .sari-rm-new{width:31px;height:31px;flex-basis:31px}#sariRiderMessenger .sari-rm-search-box{margin-top:6px}#sariRiderMessenger .sari-rm-search-box input{height:34px}#sariRiderMessenger .sari-rm-tabs{margin-top:5px}#sariRiderMessenger .sari-rm-threads{display:flex;overflow-x:auto;overflow-y:hidden;gap:7px;padding:5px 8px 7px}#sariRiderMessenger .sari-rm-thread{flex:0 0 195px;min-height:63px;padding:7px;border:1px solid transparent;border-bottom:3px solid transparent}#sariRiderMessenger .sari-rm-thread.is-active{border:1px solid #d4dbe4;border-bottom:3px solid #b5beca}#sariRiderMessenger .sari-rm-thread-copy strong{font-size:10.5px}#sariRiderMessenger .sari-rm-avatar{width:35px;height:35px;flex-basis:35px;font-size:11px}#sariRiderMessenger .sari-rm-inbox-bottom{display:none}#sariRiderMessenger .sari-rm-chat-header{min-height:62px;padding:8px 12px}#sariRiderMessenger .sari-rm-conversation{padding:13px 10px;gap:14px}#sariRiderMessenger .sari-rm-message-body{max-width:85%}#sariRiderMessenger .sari-rm-bubble{font-size:11px;padding:10px 12px}#sariRiderMessenger .sari-rm-composer{padding:9px}#sariRiderMessenger .sari-rm-composer form{gap:6px}#sariRiderMessenger .sari-rm-send{width:38px;height:38px;flex-basis:38px}#sariRiderMessenger .sari-rm-compose-field{padding-inline:10px}#sariRiderMessenger .sari-rm-composer textarea{font-size:11px;min-height:47px;padding-top:13px}#sariRiderMessenger .sari-rm-contact-panel{height:min(670px,91dvh)}}
@media(max-width:390px){#sariRiderMessenger .sari-rm-tabs button{padding:6px 7px;font-size:9px}#sariRiderMessenger .sari-rm-thread{flex-basis:170px}#sariRiderMessenger .sari-rm-contact-tabs{gap:1px}#sariRiderMessenger .sari-rm-contact-tabs button{padding-inline:7px}}
@media(prefers-reduced-motion:reduce){#sariRiderMessenger *{transition:none!important;scroll-behavior:auto!important}}


/* ===== SARI Rider v3: exact Buyer Messenger workspace proportions =====
   Buyer reference: 310px inbox; full-bleed white workspace; #f7f8fa thread;
   78px chat header; 50px inbox avatars; 17px radius bubbles; 42px Send.
   Do not style unrelated Courier pages. All message functions remain in original JS.
   SARI-RIDER-BUYER-MESSENGER-EXACT-V3
*/
body:has(#sariRiderMessenger) #courierMain > main {
  padding: 0 !important;
  margin: 0 !important;
  width: 100%;
  max-width: none;
}
body:has(#sariRiderMessenger) {background:#f5f6f8!important;}
#sariRiderMessenger{
  --rm-gold:#c99128;
  --rm-gold-dark:#ae7a1e;
  --rm-ink:#303740;
  --rm-muted:#8e99a6;
  --rm-border:#ebedf0;
  --rm-canvas:#f7f8fa;
  background:#fff;
  margin:0;
  padding:0;
  font-family:Poppins,ui-sans-serif,system-ui,sans-serif;
}
#sariRiderMessenger .sari-rm-shell{
  position:relative;
  display:grid;
  grid-template-columns:310px minmax(0,1fr);
  width:100%;
  height:calc(100dvh - 82px);
  min-height:510px;
  max-height:none;
  margin:0;
  padding:0;
  overflow:hidden;
  border:0;
  border-radius:0;
  background:#fff;
  box-shadow:none;
}
#sariRiderMessenger .sari-rm-inbox{
  border-right:1px solid var(--rm-border);
  background:#fff;
}
#sariRiderMessenger .sari-rm-inbox-header{
  flex-shrink:0;
  padding:24px 18px 13px;
  border-bottom:1px solid var(--rm-border);
}
#sariRiderMessenger .sari-rm-heading-row{align-items:flex-start;gap:12px;}
#sariRiderMessenger .sari-rm-heading-row h1{
  margin:0;
  color:#303740;
  font-size:20px;
  font-weight:750;
  line-height:1.25;
  letter-spacing:-.04em;
}
#sariRiderMessenger .sari-rm-inbox-subtitle{
  margin:6px 0 0;
  color:#9aa3af;
  font-size:10.5px;
  font-weight:450;
  line-height:1.55;
}
#sariRiderMessenger .sari-rm-new{
  width:37px;height:37px;flex-basis:37px;
  border:1px solid transparent;
  border-radius:50%;
  background:#fff;
  color:var(--rm-gold);
  box-shadow:none;
}
#sariRiderMessenger .sari-rm-new:hover{
  background:#f7f8fa;
  border-color:#e8eaf0;
}
#sariRiderMessenger .sari-rm-search-box{margin-top:20px;}
#sariRiderMessenger .sari-rm-search-box svg{left:14px;width:17px;height:17px;stroke:var(--rm-gold);}
#sariRiderMessenger .sari-rm-search-box input{
  height:43px;
  padding:0 13px 0 42px;
  border:1px solid #e7eaee;
  border-radius:100px;
  background:#f5f6f8;
  color:#303740;
  font-size:11px;
}
#sariRiderMessenger .sari-rm-search-box input:focus{
  border:1px solid #d5a64a;
  background:#fff;
  box-shadow:none;
}
#sariRiderMessenger .sari-rm-tabs{
  display:flex;
  gap:5px;
  margin-top:11px;
  padding:1px 0 2px;
  flex-wrap:nowrap;
  overflow-x:auto;
  scrollbar-width:none;
}
#sariRiderMessenger .sari-rm-tabs::-webkit-scrollbar{display:none;}
#sariRiderMessenger .sari-rm-tabs button{
  border:0;
  border-radius:7px;
  padding:6px 8px;
  background:transparent;
  color:#919ca8;
  font-size:9.5px;
  font-weight:600;
  line-height:1.35;
}
#sariRiderMessenger .sari-rm-tabs button.is-active{
  background:#eef1f4;
  color:#3d4651;
  font-weight:750;
}
#sariRiderMessenger .sari-rm-threads{
  flex:1;
  min-height:0;
  overflow-y:auto;
  padding:11px 8px 18px;
  scrollbar-width:thin;
  scrollbar-color:#d7dce2 transparent;
}
#sariRiderMessenger .sari-rm-thread{
  position:relative;
  min-height:78px;
  gap:12px;
  margin:2px 0;
  padding:12px 10px;
  border:0;
  border-left:3px solid transparent;
  border-radius:12px;
  background:#fff;
  text-decoration:none;
  transition:background .15s,border-color .15s;
}
#sariRiderMessenger .sari-rm-thread:hover{background:#f8f9fa;}
#sariRiderMessenger .sari-rm-thread.is-active{
  border-left-color:#c8ced6;
  background:#eef1f4;
}
#sariRiderMessenger .sari-rm-avatar{
  width:50px;height:50px;flex-basis:50px;
  border:1px solid #e6e9ed;
  background:#f1f4f7;
  color:#697886;
  font-size:14px;
}
#sariRiderMessenger .sari-rm-avatar.is-small{
  width:42px;height:42px;flex-basis:42px;font-size:12px;
}
#sariRiderMessenger .sari-rm-avatar.is-tiny{
  width:32px;height:32px;flex-basis:32px;font-size:11px;
}
#sariRiderMessenger .sari-rm-avatar.is-large{
  width:84px;height:84px;flex-basis:84px;
  font-size:26px;
  border:3px solid #fff;
  box-shadow:0 0 0 2px #f0dfbb,0 8px 24px rgba(38,45,55,.08);
}
#sariRiderMessenger .sari-rm-thread-copy strong{
  color:#303740;font-size:12px;font-weight:750;line-height:1.4;
}
#sariRiderMessenger .sari-rm-thread-copy small{
  color:#98a1ad;font-size:10px;font-weight:450;line-height:1.55;
}
#sariRiderMessenger .sari-rm-inbox-bottom{display:none;}
#sariRiderMessenger .sari-rm-chat{
  background:var(--rm-canvas);
  min-width:0;
  min-height:0;
}
#sariRiderMessenger .sari-rm-chat-header{
  min-height:78px;
  padding:12px 22px;
  border-bottom:1px solid var(--rm-border);
  background:#fff;
}
#sariRiderMessenger .sari-rm-peer{gap:12px;}
#sariRiderMessenger .sari-rm-peer-copy h2{
  color:#303740;
  font-size:14px;
  font-weight:750;
  line-height:1.4;
  letter-spacing:-.018em;
}
#sariRiderMessenger .sari-rm-peer-copy p{
  margin:5px 0 0;color:#9aa3ae;font-size:10px;line-height:1.35;
}
#sariRiderMessenger .sari-rm-info-button{width:37px;height:37px;flex-basis:37px;}
#sariRiderMessenger .sari-rm-info-button:hover{background:#fff7e8;}
#sariRiderMessenger .sari-rm-conversation{
  flex:1;
  min-height:0;
  gap:17px;
  padding:30px 22px 24px;
  overflow-y:auto;
  background:var(--rm-canvas);
  scrollbar-width:thin;
  scrollbar-color:#d2d7de transparent;
}
#sariRiderMessenger .sari-rm-message{gap:10px;}
#sariRiderMessenger .sari-rm-message-body{max-width:min(77%,580px);}
#sariRiderMessenger .sari-rm-sender{
  margin:0 0 6px;color:#89929e;font-size:10px;font-weight:650;
}
#sariRiderMessenger .sari-rm-bubble{
  display:block;
  margin:0;
  padding:12px 15px;
  border:1px solid #eceef1;
  border-radius:17px 17px 17px 5px;
  background:#fff;
  color:#3c4652;
  font-size:11.5px;
  font-weight:450;
  line-height:1.65;
  overflow-wrap:anywhere;
  white-space:pre-wrap;
  box-shadow:0 2px 8px rgba(22,34,49,.04);
}
#sariRiderMessenger .sari-rm-message.is-mine .sari-rm-bubble{
  border-color:#e9e7e1;
  border-radius:17px 17px 5px 17px;
  background:#fff;
  color:#303740;
  box-shadow:0 3px 10px rgba(22,34,49,.05);
}
#sariRiderMessenger .sari-rm-time{margin:6px 2px 0;color:#a3abb5;font-size:9px;line-height:1.4;}
#sariRiderMessenger .sari-rm-composer{
  flex-shrink:0;
  padding:13px 17px 15px;
  border-top:1px solid var(--rm-border);
  background:#fff;
}
#sariRiderMessenger .sari-rm-composer form{
  display:flex;align-items:center;gap:10px;
  min-height:auto;
  margin:0;padding:0;border:0;
  background:transparent;box-shadow:none;
}
#sariRiderMessenger .sari-rm-attach-button{
  width:28px;height:28px;flex-basis:28px;
  border-radius:8px;
  color:var(--rm-gold);
}
#sariRiderMessenger .sari-rm-attach-button svg{width:18px;height:18px;}
#sariRiderMessenger .sari-rm-compose-field{
  min-height:54px;
  padding:0 14px;
  border:1px solid #d5a64a;
  border-radius:18px;
  background:#fff;
}
#sariRiderMessenger .sari-rm-compose-field:focus-within{border-color:#d5a64a;box-shadow:none;}
#sariRiderMessenger .sari-rm-composer textarea{
  min-height:54px;
  max-height:96px;
  padding:14px 0 13px;
  border:0;
  background:transparent;
  outline:0;
  font-size:11.5px;
  line-height:19px;
  box-shadow:none;
}
#sariRiderMessenger .sari-rm-send{
  width:42px;height:42px;flex-basis:42px;
  border:0;
  border-radius:50%;
  background:var(--rm-gold);
  color:#fff;
  box-shadow:0 4px 10px rgba(201,145,40,.2);
}
#sariRiderMessenger .sari-rm-send:hover{background:var(--rm-gold-dark);}
#sariRiderMessenger .sari-rm-drawer-backdrop{
  background:rgba(255,255,255,.06);
  -webkit-backdrop-filter:blur(2px);
  backdrop-filter:blur(2px);
}
#sariRiderMessenger .sari-rm-drawer{
  width:min(372px,100%);
  border-left:1px solid #e4e8ed;
  background:#f5f7fa;
  box-shadow:-18px 0 45px rgba(28,35,45,.14);
  transition:transform .32s cubic-bezier(.22,1,.36,1),visibility .32s;
}
#sariRiderMessenger .sari-rm-drawer header{
  padding:16px 18px 14px;
  background:#fff;
}
#sariRiderMessenger .sari-rm-drawer header h2{font-size:12.5px;}
#sariRiderMessenger .sari-rm-drawer-body{padding:18px 18px 24px;}
#sariRiderMessenger .sari-rm-info-note{
  padding:14px 13px;
  border:1px solid #e5e9ee;
  border-radius:13px;
  background:#f8fafb;
}
@media(max-width:1320px){
  #sariRiderMessenger .sari-rm-shell{grid-template-columns:275px minmax(0,1fr);}
}
@media(max-width:850px){
  #sariRiderMessenger .sari-rm-shell{grid-template-columns:230px minmax(0,1fr);}
  #sariRiderMessenger .sari-rm-inbox-header{padding:18px 12px 12px;}
  #sariRiderMessenger .sari-rm-heading-row h1{font-size:17px;}
  #sariRiderMessenger .sari-rm-thread{gap:8px;padding:10px 7px;}
  #sariRiderMessenger .sari-rm-conversation{padding:20px 13px;}
  #sariRiderMessenger .sari-rm-chat-header{padding:11px 12px;}
}
@media(max-width:650px){
  #sariRiderMessenger .sari-rm-shell{
    grid-template-columns:minmax(0,1fr);
    grid-template-rows:185px minmax(0,1fr);
    height:calc(100dvh - 74px);
    min-height:420px;
    border:0;
    border-radius:0;
  }
  #sariRiderMessenger .sari-rm-inbox{border-right:0;border-bottom:1px solid var(--rm-border);}
  #sariRiderMessenger .sari-rm-inbox-header{padding:9px 12px 5px;border-bottom:0;}
  #sariRiderMessenger .sari-rm-heading-row h1{font-size:14px;}
  #sariRiderMessenger .sari-rm-inbox-subtitle{display:none;}
  #sariRiderMessenger .sari-rm-new{width:31px;height:31px;flex-basis:31px;}
  #sariRiderMessenger .sari-rm-search-box{margin-top:7px;}
  #sariRiderMessenger .sari-rm-search-box input{height:32px;font-size:10px;}
  #sariRiderMessenger .sari-rm-tabs{margin-top:5px;}
  #sariRiderMessenger .sari-rm-threads{
    display:flex;
    flex:1 1 auto;
    gap:6px;
    min-height:0;
    overflow-x:auto;
    overflow-y:hidden;
    padding:4px 8px 8px;
  }
  #sariRiderMessenger .sari-rm-thread{
    flex:0 0 185px;
    min-height:60px;
    margin:0;
    padding:6px 9px;
    border:1px solid transparent;
    border-bottom:3px solid transparent;
    border-radius:10px;
  }
  #sariRiderMessenger .sari-rm-thread.is-active{
    border:1px solid #d6dde5;
    border-bottom:3px solid #aeb7c2;
    background:#eef1f4;
  }
  #sariRiderMessenger .sari-rm-avatar{width:36px;height:36px;flex-basis:36px;}
  #sariRiderMessenger .sari-rm-thread-copy strong{font-size:10.5px;}
  #sariRiderMessenger .sari-rm-thread-copy small{font-size:9px;}
  #sariRiderMessenger .sari-rm-chat-header{min-height:62px;padding:9px 10px;}
  #sariRiderMessenger .sari-rm-peer{gap:8px;}
  #sariRiderMessenger .sari-rm-peer-copy h2{font-size:12px;}
  #sariRiderMessenger .sari-rm-peer-copy p{font-size:9px;}
  #sariRiderMessenger .sari-rm-info-button{width:33px;height:33px;flex-basis:33px;}
  #sariRiderMessenger .sari-rm-conversation{padding:13px 10px 15px;gap:15px;}
  #sariRiderMessenger .sari-rm-message-body{max-width:85%;}
  #sariRiderMessenger .sari-rm-avatar.is-tiny{width:27px;height:27px;flex-basis:27px;}
  #sariRiderMessenger .sari-rm-bubble{font-size:11px;padding:10px 12px;}
  #sariRiderMessenger .sari-rm-composer{padding:8px;}
  #sariRiderMessenger .sari-rm-attach-button{width:30px;height:30px;flex-basis:30px;}
  #sariRiderMessenger .sari-rm-send{width:38px;height:38px;flex-basis:38px;}
  #sariRiderMessenger .sari-rm-compose-field{padding-inline:10px;}
  #sariRiderMessenger .sari-rm-composer textarea{min-height:45px;padding:10px 0 8px;}
  #sariRiderMessenger #sariRmLegacyForm textarea{min-height:45px;padding:9px 12px;}
  #sariRiderMessenger .sari-rm-drawer{width:min(355px,100%);}
}
@media(max-width:390px){
  #sariRiderMessenger .sari-rm-tabs button{padding:6px 7px;font-size:9px;}
  #sariRiderMessenger .sari-rm-thread{flex-basis:170px;}
}
@media(prefers-reduced-motion:reduce){
  #sariRiderMessenger *{transition:none!important;scroll-behavior:auto!important;}
}

/* SARI-RIDER-MESSENGER-PROFILE-DRAWER-V5 — real contact data, Buyer-style detail cards */
#sariRiderMessenger .sari-rm-drawer{width:min(372px,100%);overflow-y:auto;scrollbar-width:thin;scrollbar-color:#d8dfe6 transparent}
#sariRiderMessenger .sari-rm-drawer-body{padding:18px 18px 25px;text-align:left}
#sariRiderMessenger .sari-rm-profile-top{display:flex;flex-direction:column;align-items:center;text-align:center;padding:4px 0 5px}
#sariRiderMessenger .sari-rm-profile-top h3{max-width:100%;margin:12px 0 0;color:#303740;font-size:16px;font-weight:750;line-height:1.5;overflow-wrap:anywhere}
#sariRiderMessenger .sari-rm-profile-top>p{margin:3px 0 0;color:#8e99a6;font-size:10.5px}
#sariRiderMessenger .sari-rm-profile-status{margin-top:10px;padding:5px 12px;border:1px solid #d9e7dd;border-radius:30px;background:#f0f8f2;color:#427a53;font-size:10px;font-weight:750;text-transform:capitalize}
#sariRiderMessenger .sari-rm-profile-status.is-neutral{background:#f3f5f7;border-color:#e3e8ec;color:#6b7580}
#sariRiderMessenger .sari-rm-profile-card{margin-top:20px;overflow:hidden;border:1px solid #e5e9ef;border-radius:16px;background:#fff;box-shadow:0 3px 12px rgba(31,41,55,.025)}
#sariRiderMessenger .sari-rm-profile-card-title{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:15px 15px 12px;border-bottom:1px solid #edf0f3}
#sariRiderMessenger .sari-rm-profile-card-title h4{margin:0;color:#303740;font-size:12px;font-weight:750}
#sariRiderMessenger .sari-rm-profile-card-title>span{color:#a7b0bb;font-size:9.5px}
#sariRiderMessenger .sari-rm-profile-fields{margin:0;padding:2px 15px}
#sariRiderMessenger .sari-rm-profile-field{display:grid;grid-template-columns:minmax(0,1fr);gap:3px;padding:12px 0;border-bottom:1px solid #f0f2f4;text-align:left}
#sariRiderMessenger .sari-rm-profile-field:last-child{border-bottom:0}
#sariRiderMessenger .sari-rm-profile-field dt{color:#929eaa;font-size:10px;font-weight:550}
#sariRiderMessenger .sari-rm-profile-field dd{min-width:0;margin:0;color:#394451;font-size:11.5px;font-weight:700;line-height:1.55;overflow-wrap:anywhere}
#sariRiderMessenger .sari-rm-profile-field dd a{color:#a6741e;text-decoration:none;word-break:break-word}
#sariRiderMessenger .sari-rm-profile-field dd a:hover{text-decoration:underline;text-underline-offset:3px}
#sariRiderMessenger .sari-rm-profile-help{margin:0;border-top:1px solid #edf0f3;padding:11px 15px;color:#9aa4b0;font-size:10px;line-height:1.6;text-align:left}
#sariRiderMessenger .sari-rm-info-note{margin-top:13px;padding:14px 15px;background:#f8fafb;border:1px solid #e8ecf0}
#sariRiderMessenger .sari-rm-info-note strong{color:#54616d;font-size:11px}
#sariRiderMessenger .sari-rm-info-note p{margin:6px 0 0;color:#8c98a5;font-size:10px;line-height:1.65}
@media(max-width:650px){#sariRiderMessenger .sari-rm-drawer{width:min(355px,100%)}#sariRiderMessenger .sari-rm-drawer-body{padding:15px 14px 23px}}


/* V6: display real public-disk / social avatar photos; fallback to initials on missing image. */
#sariRiderMessenger .sari-rm-avatar img {display:block;width:100%;height:100%;object-fit:cover;object-position:center;border-radius:50%;max-width:none;}
#sariRiderMessenger .sari-rm-avatar.is-large img {border-radius:50%;}
@media(max-width:650px) {
  #sariRiderMessenger .sari-rm-inbox {position:relative;z-index:0;overflow:hidden;}
  #sariRiderMessenger .sari-rm-chat {position:relative;z-index:1;min-height:0;overflow:hidden;}
}
@endverbatim
</style>
@endpush

@section('content')
{{-- SARI RIDER MESSENGER - BUYER EXACT UI V3. Buyer/Seller/Admin + original Logistics preserved. --}}
{{-- Uses SARI's existing universal messaging API for Buyer/Seller/Admin and
     the ORIGINAL LogisticsMessage sender/thread for linked Logistics. --}}
<div id="sariRiderMessenger" class="sari-rm" 
    data-api-base="{{ url('/messaging/api') }}"
    data-self-url="{{ url('/messaging/api/me') }}"
    data-inbox-url="{{ url('/messaging/api/conversations') }}"
    data-contacts-url="{{ url('/messaging/api/connections') }}"
    data-open-direct-url="{{ url('/messaging/api/conversations/direct') }}"
    data-open-support-url="{{ url('/messaging/api/support') }}"
    data-csrf="{{ csrf_token() }}"
    data-legacy-enabled="{{ (int) ($account->logistics_account_id ?? 0) > 0 ? '1' : '0' }}"
    data-legacy-count="{{ ($messages ?? collect())->count() }}"
    data-legacy-url="{{ route('courier.messages') }}"
    data-sari-logo="{{ asset('images/sari-main-logo.png') }}">
    @if($errors->any())
        <p class="sari-rm-server-alert" role="alert">{{ $errors->first() }}</p>
    @endif
    @if(session('success'))
        <p class="sari-rm-server-alert is-success" role="status">{{ session('success') }}</p>
    @endif
    <section class="sari-rm-shell" aria-label="Rider messaging workspace">
        <aside class="sari-rm-inbox" aria-label="Conversations">
            <header class="sari-rm-inbox-header">
                <div class="sari-rm-heading-row">
                    <div><h1>Messages</h1><p class="sari-rm-inbox-subtitle">Your conversations with SARI Marketplace</p></div>
                    <button type="button" id="sariRmNew" class="sari-rm-new" title="Start a conversation" aria-label="Start a conversation">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    </button>
                </div>
                <label class="sari-rm-search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
                    <span class="sari-rm-sr-only">Search conversations</span>
                    <input type="search" id="sariRmSearch" placeholder="Search messages..." autocomplete="off">
                </label>
                <nav class="sari-rm-tabs" aria-label="Contact roles">
                    <button type="button" class="is-active" data-rm-role="all" aria-pressed="true">All</button>
                    <button type="button" data-rm-role="buyer" aria-pressed="false">Buyer</button>
                    <button type="button" data-rm-role="seller" aria-pressed="false">Seller</button>
                    <button type="button" data-rm-role="logistics" aria-pressed="false">Logistics</button>
                    <button type="button" data-rm-role="admin" aria-pressed="false">Admin</button>
                </nav>
            </header>
            <div id="sariRmThreads" class="sari-rm-threads" role="list" aria-live="polite">
                <p class="sari-rm-hint">Loading your conversations…</p>
            </div>
            <div class="sari-rm-inbox-bottom"><span id="sariRmInboxStatus">Private SARI conversations</span><span id="sariRmUnread" class="sari-rm-inbox-count" hidden>0 unread</span></div>
        </aside>

        <section class="sari-rm-chat" aria-label="Selected conversation">
            <div id="sariRmChatHeader" class="sari-rm-chat-header" hidden>
                <div class="sari-rm-peer"><span class="sari-rm-avatar is-small" id="sariRmPeerAvatar" aria-hidden="true">S</span>
                    <div class="sari-rm-peer-copy"><h2 id="sariRmPeerName">Conversation</h2><p id="sariRmPeerSubtitle">Secure SARI messaging</p></div>
                </div>
                <button id="sariRmInfoButton" type="button" class="sari-rm-info-button" aria-label="Conversation information" aria-controls="sariRmInfoDrawer" aria-expanded="false" title="Conversation information"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5h.01"/></svg></button>
            </div>
            <div id="sariRmConversation" class="sari-rm-conversation" role="log" aria-label="Chat messages" aria-live="polite" aria-relevant="additions">
                <div class="sari-rm-empty"><span class="sari-rm-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M20.5 13.5a7.5 7.5 0 0 1-7.5 7.5H6l-3 2V9a7 7 0 0 1 7-7h3.5a7 7 0 0 1 7 7z"/></svg></span><h3>Your messages</h3><p>Select a conversation or start a chat with a Buyer, Seller, Logistics, or SARI Admin.</p></div>
            </div>
            <p id="sariRmFeedback" class="sari-rm-feedback" role="alert" hidden></p>
            <div id="sariRmComposerWrap" class="sari-rm-composer" hidden>
                <form id="sariRmSendForm" autocomplete="off">
                    <label class="sari-rm-attach-button" for="sariRmAttachment" aria-label="Attach file" title="Attach image or document">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21 11.5-8.9 8.9a6 6 0 0 1-8.5-8.5L13 2.5a4 4 0 0 1 5.7 5.7l-9 9a2 2 0 1 1-2.8-2.8l8.4-8.4"/></svg>
                    </label>
                    <input id="sariRmAttachment" type="file" name="attachment" accept="image/jpeg,image/png,image/webp,image/gif,.pdf,.txt,.csv,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.mp4,.mov,.mp3,.m4a,.wav" hidden>
                    <div class="sari-rm-compose-field">
                        <div id="sariRmFileChip" class="sari-rm-file-chip" hidden><span id="sariRmFileName"></span><button type="button" id="sariRmFileRemove" aria-label="Remove attachment">×</button></div>
                        <label for="sariRmBody" class="sari-rm-sr-only">Write a message</label>
                        <textarea id="sariRmBody" name="body" maxlength="3000" rows="1" placeholder="Write a message..."></textarea>
                    </div>
                    <button type="submit" id="sariRmSend" class="sari-rm-send" title="Send message" aria-label="Send message"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 4 17 8-17 8 3-8-3-8Z"/><path d="M7 12h14"/></svg></button>
                </form>
                <form id="sariRmLegacyForm" action="{{ route('courier.messages.send') }}" method="POST" hidden>
                    @csrf
                    <label for="sariRmLegacyBody" class="sari-rm-sr-only">Message your linked Logistics provider</label>
                    <textarea id="sariRmLegacyBody" name="body" rows="1" maxlength="3000" required placeholder="Write to Logistics..."></textarea>
                    <button class="sari-rm-send" type="submit" title="Send to Logistics" aria-label="Send to Logistics"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m4 4 17 8-17 8 3-8-3-8Z"/><path d="M7 12h14"/></svg></button>
                </form>
            </div>
        </section>
        <button type="button" class="sari-rm-drawer-backdrop" id="sariRmBackdrop" aria-label="Close conversation information" tabindex="-1" hidden></button>
        <aside class="sari-rm-drawer" id="sariRmInfoDrawer" role="dialog" aria-modal="true" aria-labelledby="sariRmDrawerTitle" aria-hidden="true" inert>
            <header><div><h2 id="sariRmDrawerTitle">Conversation details</h2><p>Private SARI Marketplace messaging</p></div><button type="button" id="sariRmDrawerClose" aria-label="Close information">×</button></header>
            <div class="sari-rm-drawer-body" id="sariRmProfileBody">
                <div class="sari-rm-profile-top">
                    <span class="sari-rm-avatar is-large" id="sariRmInfoAvatar" aria-hidden="true">S</span>
                    <h3 id="sariRmInfoName">SARI contact</h3>
                    <p id="sariRmInfoRole">SARI Marketplace account</p>
                    <span class="sari-rm-profile-status" id="sariRmInfoStatus" hidden></span>
                </div>
                <section class="sari-rm-profile-card" aria-labelledby="sariRmProfileCardTitle">
                    <div class="sari-rm-profile-card-title"><h4 id="sariRmProfileCardTitle">Profile information</h4><span>Contact details</span></div>
                    <dl class="sari-rm-profile-fields">
                        <div class="sari-rm-profile-field"><dt id="sariRmProfileNameLabel">Full name</dt><dd id="sariRmProfileName">—</dd></div>
                        <div class="sari-rm-profile-field"><dt>Account type</dt><dd id="sariRmProfileType">—</dd></div>
                        <div class="sari-rm-profile-field" id="sariRmProfileEmailRow"><dt>Email address</dt><dd><a id="sariRmProfileEmail" href="#" rel="nofollow">—</a></dd></div>
                        <div class="sari-rm-profile-field" id="sariRmProfileStateRow"><dt>Account status</dt><dd id="sariRmProfileState">—</dd></div>
                    </dl>
                    <p id="sariRmProfileHelp" class="sari-rm-profile-help">Details shown here come from your authorized SARI messaging contacts.</p>
                </section>
                <div class="sari-rm-info-note"><strong>Secure conversation</strong><p id="sariRmPrivacyText">Messages are available only to authorized participants. Private phone numbers and addresses are not disclosed by the messaging service.</p></div>
            </div>
        </aside>
    </section>
    <div id="sariRmContactOverlay" class="sari-rm-contact-overlay" hidden>
        <section class="sari-rm-contact-panel" role="dialog" aria-modal="true" aria-labelledby="sariRmContactTitle">
            <header><div><h2 id="sariRmContactTitle">New conversation</h2><p>Choose a contact to send a private message.</p></div><button id="sariRmContactClose" type="button" aria-label="Close contact picker">×</button></header>
            <label class="sari-rm-contact-search"><span class="sari-rm-sr-only">Search contacts</span><input id="sariRmContactSearch" type="search" placeholder="Search people or stores..." autocomplete="off"></label>
            <nav class="sari-rm-contact-tabs" aria-label="Contact type"><button type="button" data-rm-contact-role="all" class="is-active">All</button><button type="button" data-rm-contact-role="buyer">Buyer</button><button type="button" data-rm-contact-role="seller">Seller</button><button type="button" data-rm-contact-role="logistics">Logistics</button><button type="button" data-rm-contact-role="admin">Admin</button></nav>
            <div id="sariRmContacts" class="sari-rm-contacts"><p class="sari-rm-hint">Loading contacts…</p></div>
        </section>
    </div>
    {{-- Existing LogisticsMessage chat history remains tied to the original send route. --}}
    <template id="sariRmLegacyMessages">
        @forelse(($messages ?? collect()) as $message)
            <div class="sari-rm-message {{ $message->sender_role === 'rider' ? 'is-mine' : '' }}">
                @if($message->sender_role !== 'rider')<span class="sari-rm-avatar is-tiny" aria-hidden="true">L</span>@endif
                <div class="sari-rm-message-body"><span class="sari-rm-sender">{{ $message->sender_role === 'rider' ? 'You' : 'Logistics' }}</span><p class="sari-rm-bubble">{{ $message->body }}</p><time class="sari-rm-time">{{ $message->created_at?->setTimezone('Asia/Manila')->format('M d, h:i A') }}</time></div>
            </div>
        @empty
            <div class="sari-rm-empty"><h3>No messages yet</h3><p>Start coordinating with your linked Logistics provider.</p></div>
        @endforelse
    </template>
</div>
@endsection

@push('scripts')
<script id="sariRiderUnifiedMessengerJsInlineV4">
/* SARI Rider unified messaging: Buyer, Seller, linked Logistics, Admin Support.
 * Uses authenticated platform messaging APIs and existing Rider/Logistics POST form.
 * No untrusted HTML injection; messages rendered via textContent. */
(function () {
  'use strict';
  const root = document.getElementById('sariRiderMessenger');
  if (!root || root.dataset.initialized === '1') return;
  root.dataset.initialized = '1';
  const $ = id => document.getElementById(id);
  const threadsNode = $('sariRmThreads');
  const messageNode = $('sariRmConversation');
  const searchNode = $('sariRmSearch');
  const contactListNode = $('sariRmContacts');
  const contactOverlay = $('sariRmContactOverlay');
  const contactSearch = $('sariRmContactSearch');
  const feedback = $('sariRmFeedback');
  const bodyInput = $('sariRmBody');
  const attachment = $('sariRmAttachment');
  const sendForm = $('sariRmSendForm');
  const legacyForm = $('sariRmLegacyForm');
  const LEGACY = 'legacy-logistics';
  const allowedRoles = new Set(['buyer', 'social_buyer', 'seller', 'logistics', 'admin']);
  let actor = null;
  let inbox = [];
  let contacts = [];
  let selected = null;
  let activeRole = 'all';
  let contactRole = 'all';
  let currentMessages = [];
  let lastMessageFingerprint = '';
  let inFlight = false;
  let isSending = false;
  let currentRequestSeq = 0;
  let touched = false;
  let drawerFocus = null;
  let contactsFocus = null;
  let loadingLegacy = false;
  let lastLegacyRefresh = 0;

  const escapeUrlPart = value => encodeURIComponent(String(value));
  const category = role => role === 'social_buyer' ? 'buyer' : (role || '');
  const roleLabel = role => ({buyer:'Buyer',social_buyer:'Buyer',seller:'Seller',logistics:'Logistics',admin:'SARI Admin',rider:'Rider'})[role] || 'SARI Contact';
  const initials = name => String(name || 'S').trim().split(/\s+/).slice(0,2).map(x=>x[0]||'').join('').toUpperCase();
  const make = (tag, cls, text) => {
    const node = document.createElement(tag);
    if (cls) node.className = cls;
    if (text !== undefined) node.textContent = text;
    return node;
  };
  const safePhotoUrl = value => {
    if (typeof value !== 'string' || !value.trim()) return null;
    try {
      const u = new URL(value, window.location.origin);
      if (u.protocol !== 'https:' && !(u.protocol === 'http:' && u.origin === window.location.origin)) return null;
      if (u.username || u.password) return null;
      return u.href;
    } catch (_) { return null; }
  };
  const contactPhoto = (role, id) => {
    const found = contacts.find(c => c.role === role && Number(c.id) === Number(id));
    return found?.avatar_url || null;
  };
  const photoForThread = item => {
    if (!item) return null;
    if (item.role === 'admin' || item.type === 'admin_support' || item.context?.type === 'account_support') return root.dataset.sariLogo;
    if (item.kind === 'legacy') return contacts.find(c => c.role === 'logistics')?.avatar_url || null;
    if (item.avatar_url) return item.avatar_url;
    const p = currentUserParticipant(item).find(p => p.role === item.role);
    return p?.avatar_url || (p ? contactPhoto(p.role,p.id) : null);
  };
  const setAvatarPhoto = (elt, name, role, url) => {
    elt.replaceChildren(document.createTextNode(initials(name)));
    elt.classList.remove('is-photo');
    const src = safePhotoUrl(url);
    // Token prevents an old async image request overwriting a newly selected account.
    elt.dataset.avatarSource = src || '';
    if (!src) return;
    const img = new Image();
    img.alt = ''; img.loading = 'eager'; img.decoding = 'async';
    img.referrerPolicy = 'no-referrer';
    img.onload = () => {
      if (elt.dataset.avatarSource !== src) return;
      elt.replaceChildren(img);
      elt.classList.add('is-photo');
    };
    img.onerror = () => {
      if (elt.dataset.avatarSource !== src) return;
      elt.replaceChildren(document.createTextNode(initials(name)));
      elt.classList.remove('is-photo');
    };
    img.src = src;
  };
  const avatar = (name, role, size, url) => {
    const elt = make('span', 'sari-rm-avatar' + (size ? ' is-'+size : '') + ((role==='admin'||role==='logistics') ? ' is-'+role : ''), initials(name));
    elt.setAttribute('aria-hidden','true');
    setAvatarPhoto(elt, name, role, url);
    return elt;
  };
  const selectedId = () => selected?.kind === 'platform' ? selected.uuid : (selected?.kind === 'legacy' ? LEGACY : null);
  const isLegacyAvailable = root.dataset.legacyEnabled === '1';
  const isSameOrigin = url => {
    try { const parsed=new URL(url, location.origin);return parsed.origin===location.origin && /^https?:$/.test(parsed.protocol); }
    catch (_) { return false; }
  };
  const formatDate = date => {
    if (!date) return '';
    const d = new Date(date);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleString('en-PH',{timeZone:'Asia/Manila',month:'short',day:'numeric',hour:'numeric',minute:'2-digit'});
  };
  function showFeedback(text) {
    feedback.textContent=text;
    feedback.hidden=!text;
    if (text) window.SariCourierToast?.error?.(text);
  }
  function parseError(data, response) {
    if (data && typeof data === 'object') {
      const first=Object.values(data.errors || {})[0];
      if (Array.isArray(first)&&first.length) return first[0];
      if (typeof data.message === 'string' && data.message && data.message !== 'Server Error') return data.message;
    }
    if (response.status === 419) return 'Your session expired. Refresh and try again.';
    if (response.status === 403) return 'This conversation is not available for your account.';
    if (response.status === 404) return 'Messaging endpoint was not found. Check your Laravel routes.';
    return 'Messaging request failed ('+response.status+').';
  }
  async function api(url, options = {}) {
    const headers={'Accept':'application/json','X-Requested-With':'XMLHttpRequest',...(options.headers||{})};
    if (options.method && options.method !== 'GET') headers['X-CSRF-TOKEN']=root.dataset.csrf;
    if (options.body && !(options.body instanceof FormData)) headers['Content-Type']='application/json';
    const response = await fetch(url,{credentials:'same-origin',cache:'no-store',...options,headers});
    let data=null;
    try {data=await response.json();}catch(_){}
    if (!response.ok) throw new Error(parseError(data,response));
    if (!data) throw new Error('Unexpected server response.');
    return data;
  }
  const apiFor=(uuid, suffix='') => root.dataset.apiBase+'/conversations/'+escapeUrlPart(uuid)+suffix;
  function currentUserParticipant(conversation) {
    return (conversation.participants||[]).filter(p=>!(p.role===actor?.role && Number(p.id)===Number(actor?.id)));
  }
  function detailsFor(conversation) {
    if (conversation.type === 'admin_support' || conversation.context?.type === 'account_support') return {name:'SARI Admin Support',role:'admin'};
    const other=currentUserParticipant(conversation);
    const peer=other.find(p=>allowedRoles.has(p.role)) || other[0];
    return {name:peer?.label || conversation.subject || 'SARI Conversation',role:peer?.role || 'admin',peer_id:peer?.id,avatar_url:peer?.avatar_url || null};
  }
  function legacyItem() {const link=contacts.find(c=>c.role==='logistics');return {uuid:LEGACY,kind:'legacy',id:LEGACY,type:'legacy',role:'logistics',name:link?.name||'Linked Logistics',avatar_url:link?.avatar_url || null,last_message:{body:root.dataset.legacyCount==='0'?'No messages yet':'Existing Logistics conversation'},unread_count:0};}
  function decoratedThreads() {
    const result=inbox.filter(c=>c.uuid && ['direct','admin_support','report_support'].includes(c.type)).map(c=>{
      const peer=detailsFor(c);
      return {...c,kind:'platform',name:peer.name,role:peer.role};
    }).filter(c=>allowedRoles.has(c.role));
    if (isLegacyAvailable) result.unshift(legacyItem());
    return result;
  }
  function threadPasses(item) {
    const query=searchNode.value.trim().toLowerCase();
    if (activeRole!=='all' && category(item.role)!==activeRole) return false;
    return !query || (item.name+' '+roleLabel(item.role)+' '+(item.last_message?.body||'')).toLowerCase().includes(query);
  }
  function renderThreads() {
    const items=decoratedThreads().filter(threadPasses);
    threadsNode.replaceChildren();
    if (!items.length) {
      threadsNode.append(make('p','sari-rm-hint',inbox.length || isLegacyAvailable?'No conversations match this filter.':'No conversations yet. Click + to start one.'));
    }
    for (const item of items) {
      const button=make('button','sari-rm-thread'+(selectedId()===item.uuid?' is-active':''));
      button.type='button';button.dataset.thread=item.uuid;button.setAttribute('role','listitem');
      button.append(avatar(item.name,item.role,null,photoForThread(item)));
      const copy=make('span','sari-rm-thread-copy');
      copy.append(make('strong','',item.name));
      let summary = item.kind==='legacy' ? 'Original Logistics chat' : (item.last_message?.body || roleLabel(item.role)+' conversation');
      copy.append(make('small','',summary || 'Attachment received'));
      button.append(copy);
      if ((item.unread_count||0)>0) button.append(make('span','sari-rm-unread',String(item.unread_count>99?'99+':item.unread_count)));
      button.addEventListener('click',()=>selectThread(item));
      threadsNode.append(button);
    }
    const unread=inbox.reduce((sum,c)=>sum+Number(c.unread_count||0),0);
    const unreadBadge=$('sariRmUnread');unreadBadge.textContent=unread+' unread';unreadBadge.hidden=!unread;
    document.dispatchEvent(new CustomEvent('sari:rider-message-count',{detail:{unread}}));
  }
  function updateRoleTabs() {
    root.querySelectorAll('[data-rm-role]').forEach(button=>{
      const yes=button.dataset.rmRole===activeRole;
      button.classList.toggle('is-active',yes);button.setAttribute('aria-pressed',String(yes));
    });
  }
  // Contacts come from /messaging/api/connections for the authenticated Rider.
  // Only display fields explicitly returned by that authorized endpoint.
  function authorizedContactFor(item) {
    if (!item || item.role === 'admin' || item.type === 'admin_support' || item.context?.type === 'account_support') return null;
    if (item.kind === 'legacy') return contacts.find(c => c.role === 'logistics') || null;
    const peer = currentUserParticipant(item).find(p => p.role === item.role);
    if (!peer || !Number.isFinite(Number(peer.id))) return null;
    return contacts.find(c => c.role === peer.role && Number(c.id) === Number(peer.id)) || null;
  }
  function renderProfileDetails(item) {
    if (!item) return;
    const support = item.role === 'admin' || item.type === 'admin_support' || item.context?.type === 'account_support';
    const record = authorizedContactFor(item);
    const photo = photoForThread(item) || record?.avatar_url || null;
    const type = support ? 'SARI Admin Support' : roleLabel(item.role);
    const name = record?.name || item.name || 'SARI Contact';
    const displayName = support ? 'SARI Admin Support' : name;
    const status = (typeof record?.status === 'string' ? record.status.trim() : '');
    const email = !support && typeof record?.email === 'string' ? record.email.trim() : '';
    const acceptableEmail = email.length <= 254 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    $('sariRmInfoName').textContent = displayName;
    $('sariRmInfoRole').textContent = type;
    const av = $('sariRmInfoAvatar');
    av.className = 'sari-rm-avatar is-large' + (item.role === 'admin' || item.role === 'logistics' ? ' is-' + item.role : '');
    setAvatarPhoto(av, displayName, item.role, photo);
    $('sariRmProfileNameLabel').textContent = item.role === 'seller' ? 'Store name' : (support ? 'Support team' : 'Full name');
    $('sariRmProfileName').textContent = displayName;
    $('sariRmProfileType').textContent = type;
    const emailRow = $('sariRmProfileEmailRow');
    emailRow.hidden = support;
    const emailLink = $('sariRmProfileEmail');
    emailLink.textContent = acceptableEmail ? email : 'Not shared';
    if (acceptableEmail) {emailLink.href = 'mailto:' + email; emailLink.removeAttribute('aria-disabled');}
    else {emailLink.removeAttribute('href');emailLink.setAttribute('aria-disabled','true');}
    $('sariRmProfileStateRow').hidden = support;
    $('sariRmProfileState').textContent = status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Not available';
    const badge = $('sariRmInfoStatus');
    badge.hidden = !status || support;
    badge.className = 'sari-rm-profile-status' + (status.toLowerCase() === 'active' ? '' : ' is-neutral');
    badge.textContent = status.toLowerCase() === 'active' ? 'Active account' : status;
    $('sariRmProfileHelp').textContent = support
      ? 'This is the official SARI support channel. Private administrator contact information is not disclosed.'
      : record ? 'Profile details from your authorized SARI Marketplace messaging contact.'
      : 'Only the available conversation identity is shown. Contact details are not available from the messaging service.';
    $('sariRmPrivacyText').textContent = support
      ? 'Contact SARI Admin through this official support chat. Individual administrator personal details are private.'
      : 'Messages are available to authorized participants. Private phone numbers and residential addresses are not disclosed by the messaging service.';
  }
  function updateChatHeader(name,role) {
    $('sariRmChatHeader').hidden=false;
    $('sariRmPeerName').textContent=name;
    $('sariRmPeerSubtitle').textContent=role==='admin'?'Official SARI Admin Support':roleLabel(role)+' · Secure conversation';
    const av=$('sariRmPeerAvatar');av.className='sari-rm-avatar is-small'+(role==='admin'||role==='logistics'?' is-'+role:'');setAvatarPhoto(av,name,role,photoForThread(selected));
    $('sariRmInfoName').textContent=name;
    $('sariRmInfoRole').textContent=roleLabel(role);
    const infoAv=$('sariRmInfoAvatar');infoAv.className='sari-rm-avatar is-large'+(role==='admin'||role==='logistics'?' is-'+role:'');setAvatarPhoto(infoAv,name,role,photoForThread(selected));
    $('sariRmComposerWrap').hidden=false;
    renderProfileDetails(selected);
  }
  function messageFingerprint(messages) {
    const last=messages[messages.length-1];
    return messages.length+':'+(last?.id||'')+':'+(last?.created_at||'')+':'+(last?.body||'');
  }
  function renderMessages(messages, force=false) {
    const fingerprint=messageFingerprint(messages);
    if (!force && fingerprint===lastMessageFingerprint) return;
    const nearBottom=messageNode.scrollHeight-messageNode.scrollTop-messageNode.clientHeight<130;
    const wasEmpty=lastMessageFingerprint==='';
    lastMessageFingerprint=fingerprint;
    messageNode.replaceChildren();
    if (!messages.length) {
      const empty=make('div','sari-rm-empty');
      empty.append(make('h3','','Start your conversation'),make('p','','Send a message to coordinate with this SARI contact.'));
      messageNode.append(empty);return;
    }
    for (const msg of messages) {
      const mine=msg.sender_role===actor?.role && Number(msg.sender_id)===Number(actor?.id);
      const who=(typeof msg.sender==='string' ? msg.sender : (msg.sender?.name||msg.sender?.label))||(mine?'You':roleLabel(msg.sender_role));
      const wrap=make('div','sari-rm-message'+(mine?' is-mine':''));
      if (!mine) {
        const sender = selected?.participants?.find(p => p.role === msg.sender_role && Number(p.id) === Number(msg.sender_id));
        const url = sender?.avatar_url || (sender ? contactPhoto(sender.role, sender.id) : photoForThread(selected));
        wrap.append(avatar(who,msg.sender_role,'tiny',url));
      }
      const content=make('div','sari-rm-message-body');
      content.append(make('span','sari-rm-sender',mine?'You':who));
      if (msg.body) content.append(make('p','sari-rm-bubble',msg.body));
      const a=msg.attachment;
      if (a && isSameOrigin(a.url)) {
        const link=make('a','sari-rm-attachment');
        link.href=a.url;link.target='_blank';link.rel='noopener noreferrer';
        if (a.is_image){const img=make('img');img.src=a.url;img.alt=a.name||'Shared image';img.loading='lazy';link.append(img);}
        else link.textContent='📎 '+(a.name||'Open attachment');
        content.append(link);
      }
      content.append(make('time','sari-rm-time',formatDate(msg.created_at)));
      wrap.append(content);messageNode.append(wrap);
    }
    if (nearBottom||wasEmpty||force) messageNode.scrollTop=messageNode.scrollHeight;
  }
  async function selectThread(item, opts={}) {
    touched=true;showFeedback('');
    const old=selectedId();
    selected={...item};
    const isLegacy=item.kind==='legacy';
    renderThreads();
    updateChatHeader(item.name,item.role);
    sendForm.hidden=isLegacy;
    legacyForm.hidden=!isLegacy;
    $('sariRmAttachment').value='';$('sariRmFileChip').hidden=true;
    const url=new URL(location.href);
    url.searchParams.set('channel',isLegacy?'logistics':'platform');
    if(isLegacy) url.searchParams.delete('conversation');
    else url.searchParams.set('conversation',item.uuid);
    history.replaceState(history.state,'',url.toString());
    closeDrawer();
    if(isLegacy){
      messageNode.replaceChildren($('sariRmLegacyMessages').content.cloneNode(true));
      messageNode.querySelectorAll('.sari-rm-message:not(.is-mine) .sari-rm-avatar').forEach(a => setAvatarPhoto(a, selected.name, 'logistics', photoForThread(selected)));
      messageNode.scrollTop=messageNode.scrollHeight;
      currentMessages=[];lastMessageFingerprint='';
      return;
    }
    if(old!==item.uuid || !opts.keepMessages){messageNode.replaceChildren(make('p','sari-rm-hint','Loading messages…'));lastMessageFingerprint='';}
    await fetchThread(item.uuid, true);
  }
  async function fetchThread(uuid, markRead=false) {
    const seq=++currentRequestSeq;
    try {
      const data=await api(apiFor(uuid));
      if(selectedId()!==uuid || seq!==currentRequestSeq) return;
      currentMessages=Array.isArray(data.messages)?data.messages:[];
      renderMessages(currentMessages);
      if(markRead && document.visibilityState==='visible' && (Number(inbox.find(c=>c.uuid===uuid)?.unread_count||0)>0)) await markThreadRead(uuid);
    } catch(e) {
      if(selectedId()===uuid) showFeedback(e.message);
    }
  }
  async function markThreadRead(uuid) {
    try {await api(apiFor(uuid,'/read'),{method:'POST'});}
    catch(_){return;}
    const item=inbox.find(c=>c.uuid===uuid);
    if(item){item.unread_count=0;renderThreads();}
  }
  async function refreshLegacy() {
    if(loadingLegacy || !isLegacyAvailable || selectedId()!==LEGACY || document.visibilityState!=='visible') return;
    if(Date.now()-lastLegacyRefresh<18000)return;
    loadingLegacy=true;lastLegacyRefresh=Date.now();
    try {
      const url=new URL(root.dataset.legacyUrl,location.origin);
      const response=await fetch(url.toString(),{credentials:'same-origin',cache:'no-store',headers:{'Accept':'text/html'}});
      if(!response.ok)return;
      const html=await response.text();
      const doc=new DOMParser().parseFromString(html,'text/html');
      const remote=doc.querySelector('#sariRmLegacyMessages');
      if(!remote || selectedId()!==LEGACY)return;
      const current=$('sariRmLegacyMessages');
      const previous=current.innerHTML;
      if(remote.innerHTML!==previous){
        const nearBottom=messageNode.scrollHeight-messageNode.scrollTop-messageNode.clientHeight<140;
        current.innerHTML=remote.innerHTML;
        messageNode.replaceChildren(current.content.cloneNode(true));
        messageNode.querySelectorAll('.sari-rm-message:not(.is-mine) .sari-rm-avatar').forEach(a => setAvatarPhoto(a, selected.name, 'logistics', photoForThread(selected)));
        if(nearBottom)messageNode.scrollTop=messageNode.scrollHeight;
      }
    }catch(_){}finally{loadingLegacy=false;}
  }
  async function refreshInbox(force=false) {
    if(inFlight)return;
    inFlight=true;
    try {
      const data=await api(root.dataset.inboxUrl);
      inbox=Array.isArray(data.conversations)?data.conversations:[];
      renderThreads();
      if (selected?.kind === 'platform') {
        const latest = decoratedThreads().find(c => c.uuid === selected.uuid);
        if (latest) { selected = {...selected, ...latest}; updateChatHeader(selected.name, selected.role); }
      }
      $('sariRmInboxStatus').textContent='Secure SARI conversations';
      if (selected?.kind==='platform' && !inbox.some(c=>c.uuid===selected.uuid)) {
        selected=null;updateBlank();
      }
      if(!touched && force){
        const qs=new URLSearchParams(location.search);
        const target=qs.get('conversation');
        const saved=inbox.find(c=>c.uuid===target);
        if(qs.get('channel')==='logistics' && isLegacyAvailable) await selectThread(legacyItem());
        else if(saved) await selectThread({...saved,...detailsFor(saved),kind:'platform'});
        else if(inbox.length) {
          const candidate=decoratedThreads().find(c=>c.kind==='platform');
          if(candidate) await selectThread(candidate);
        } else if(isLegacyAvailable) await selectThread(legacyItem());
      }
    }catch(e){
      if(force){threadsNode.replaceChildren(make('p','sari-rm-hint',e.message));showFeedback(e.message);}
    }finally{inFlight=false;}
  }
  function updateBlank() {
    $('sariRmChatHeader').hidden=true;$('sariRmComposerWrap').hidden=true;
    messageNode.replaceChildren(make('div','sari-rm-empty','Choose a conversation to begin.'));
  }
  function contactPasses(item) {
    if(!allowedRoles.has(item.role))return false;
    if(contactRole!=='all' && category(item.role)!==contactRole)return false;
    const q=contactSearch.value.trim().toLowerCase();
    return !q || (String(item.name||'')+' '+roleLabel(item.role)).toLowerCase().includes(q);
  }
  function allContacts() {
    const unique=new Map();
    contacts.filter(c=>allowedRoles.has(c.role)&&c.role!=='admin'&&c.role!=='logistics').forEach(c=>unique.set(c.role+':'+c.id,c));
    if(isLegacyAvailable) unique.set('logistics:legacy',{role:'logistics',id:LEGACY,name:legacyItem().name,legacy:true});
    return [{role:'admin',id:'support',name:'SARI Admin Support',support:true},...unique.values()];
  }
  function renderContacts(){
    contactListNode.replaceChildren();
    const results=allContacts().filter(contactPasses);
    if(!results.length){contactListNode.append(make('p','sari-rm-hint','No available contacts match this filter.'));return;}
    for(const contact of results){
      const btn=make('button','sari-rm-contact-option');btn.type='button';
      btn.append(avatar(contact.name,contact.role,null,contact.support ? root.dataset.sariLogo : contact.avatar_url));
      const text=make('span','sari-rm-thread-copy');
      text.append(make('strong','',contact.name),make('small','',contact.support?'Official SARI Administrator conversation':roleLabel(contact.role)));
      btn.append(text);
      btn.addEventListener('click',()=>openContact(contact,btn));
      contactListNode.append(btn);
    }
  }
  async function openContact(contact,button){
    if(button.disabled)return;
    button.disabled=true;showFeedback('');
    try {
      let data;
      if(contact.legacy){closeContacts();await selectThread(legacyItem());return;}
      if(contact.support) data=await api(root.dataset.openSupportUrl,{method:'POST'});
      else data=await api(root.dataset.openDirectUrl,{method:'POST',body:JSON.stringify({target_role:contact.role,target_id:Number(contact.id)})});
      if(!data.conversation?.uuid)throw new Error('Conversation could not be opened.');
      closeContacts();
      await refreshInbox();
      const entry=inbox.find(c=>c.uuid===data.conversation.uuid)||{...data.conversation,participants:[{role:contact.role,id:contact.id,label:contact.name}]};
      await selectThread({...entry,kind:'platform',name:contact.name,role:contact.role});
      bodyInput.focus();
    }catch(e){showFeedback(e.message);button.disabled=false;}
  }
  async function loadContacts(){
    try {
      const data=await api(root.dataset.contactsUrl);
      actor=data.actor;
      contacts=Array.isArray(data.contacts)?data.contacts:[];
      renderContacts();
      if (selected) { renderProfileDetails(selected); updateChatHeader(selected.name,selected.role); }
      renderThreads();
    }catch(e){contactListNode.replaceChildren(make('p','sari-rm-hint',e.message));}
  }
  function openContacts(){
    contactsFocus=document.activeElement;
    contactOverlay.hidden=false;contactSearch.value='';contactRole='all';renderContactTabs();renderContacts();
    contactSearch.focus();
  }
  function closeContacts(){contactOverlay.hidden=true;contactsFocus?.focus?.();}
  function renderContactTabs(){root.querySelectorAll('[data-rm-contact-role]').forEach(btn=>btn.classList.toggle('is-active',btn.dataset.rmContactRole===contactRole));}
  function openDrawer(){
    if(!selected)return;
    renderProfileDetails(selected);
    drawerFocus=document.activeElement;
    const drawer=$('sariRmInfoDrawer');drawer.classList.add('is-open');drawer.inert=false;drawer.setAttribute('aria-hidden','false');
    $('sariRmBackdrop').hidden=false;$('sariRmInfoButton').setAttribute('aria-expanded','true');$('sariRmDrawerClose').focus();
  }
  function closeDrawer(){
    const drawer=$('sariRmInfoDrawer');drawer.classList.remove('is-open');drawer.inert=true;drawer.setAttribute('aria-hidden','true');
    $('sariRmBackdrop').hidden=true;$('sariRmInfoButton').setAttribute('aria-expanded','false');
  }
  function fileInfo(){
    const file=attachment.files?.[0];
    $('sariRmFileChip').hidden=!file;
    $('sariRmFileName').textContent=file ? file.name+' · '+(file.size/1024/1024).toFixed(1)+' MB':'';
    if(file && file.size>15*1024*1024){showFeedback('Maximum attachment size is 15 MB.');attachment.value='';$('sariRmFileChip').hidden=true;}
  }
  async function send(event){
    event.preventDefault();
    if(!selected||selected.kind!=='platform'||isSending)return;
    const content=bodyInput.value.trim();const file=attachment.files?.[0];
    if(!content&&!file){showFeedback('Write a message or attach a file.');bodyInput.focus();return;}
    if(file&&file.size>15*1024*1024){showFeedback('Maximum attachment size is 15 MB.');return;}
    isSending=true;$('sariRmSend').disabled=true;showFeedback('');
    const fd=new FormData();if(content)fd.append('body',content);if(file)fd.append('attachment',file);
    const uuid=selected.uuid;
    try {
      const response=await api(apiFor(uuid,'/messages'),{method:'POST',body:fd});
      if(selectedId()!==uuid)return;
      bodyInput.value='';bodyInput.style.height='';attachment.value='';fileInfo();
      if(response.message?.id && !currentMessages.some(m=>m.id===response.message.id)) currentMessages.push(response.message);
      renderMessages(currentMessages,true);
      await refreshInbox();
    } catch(e){showFeedback(e.message);}finally{isSending=false;$('sariRmSend').disabled=false;}
  }
  function wireEvents(){
    root.querySelectorAll('[data-rm-role]').forEach(btn=>btn.addEventListener('click',()=>{activeRole=btn.dataset.rmRole;updateRoleTabs();renderThreads();}));
    searchNode.addEventListener('input',renderThreads);
    $('sariRmNew').addEventListener('click',openContacts);
    $('sariRmContactClose').addEventListener('click',closeContacts);
    contactOverlay.addEventListener('click',e=>{if(e.target===contactOverlay)closeContacts();});
    contactSearch.addEventListener('input',renderContacts);
    root.querySelectorAll('[data-rm-contact-role]').forEach(btn=>btn.addEventListener('click',()=>{contactRole=btn.dataset.rmContactRole;renderContactTabs();renderContacts();}));
    $('sariRmInfoButton').addEventListener('click',()=>{if($('sariRmInfoDrawer').classList.contains('is-open'))closeDrawer();else openDrawer();});
    $('sariRmDrawerClose').addEventListener('click',()=>{closeDrawer();drawerFocus?.focus?.();});
    $('sariRmBackdrop').addEventListener('click',closeDrawer);
    root.addEventListener('keydown',e=>{
      if(e.key==='Escape') {if(!contactOverlay.hidden)closeContacts();else if($('sariRmInfoDrawer').classList.contains('is-open')){closeDrawer();drawerFocus?.focus?.();}}
      if(e.key==='Tab'&&!contactOverlay.hidden){
        const nodes=[...contactOverlay.querySelectorAll('button:not(:disabled),input')].filter(n=>n.offsetParent!==null);
        if(!nodes.length)return;const first=nodes[0],last=nodes[nodes.length-1];
        if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus();}
        else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus();}
      }
    });
    sendForm.addEventListener('submit',send);
    attachment.addEventListener('change',fileInfo);
    $('sariRmFileRemove').addEventListener('click',()=>{attachment.value='';fileInfo();});
    bodyInput.addEventListener('keydown',e=>{if(e.key==='Enter'&&!e.shiftKey&&!e.isComposing){e.preventDefault();sendForm.requestSubmit();}});
    bodyInput.addEventListener('input',()=>{bodyInput.style.height='';bodyInput.style.height=Math.min(bodyInput.scrollHeight,120)+'px';});
    legacyForm.addEventListener('submit',e=>{
      const value=$('sariRmLegacyBody').value.trim();
      if(!value){e.preventDefault();showFeedback('Write a message to Logistics.');return;}
      legacyForm.querySelector('button[type="submit"]').disabled=true;
    });
  }
  async function initialize(){
    wireEvents();
    await loadContacts();
    await refreshInbox(true);
    if(!actor) {try{actor=(await api(root.dataset.selfUrl)).actor;}catch(_){}}
    if(selected?.kind==='platform') await fetchThread(selected.uuid,true);
    window.setInterval(()=>{
      if(document.visibilityState!=='visible')return;
      refreshInbox();
      if(selected?.kind==='platform'&&!isSending)fetchThread(selected.uuid,true);
      if(selected?.kind==='legacy')refreshLegacy();
    },12000);
    document.addEventListener('visibilitychange',()=>{
      if(document.visibilityState==='visible'){refreshInbox();if(selected?.kind==='platform')fetchThread(selected.uuid,true);else if(selected?.kind==='legacy')refreshLegacy();}
    });
  }
  initialize();
})();
</script>
@endpush
