@extends('layouts.buyer')

@section('title', 'Chat / Messaging — SARI Buyer')
@section('page-title', 'Chat / Messaging')

@push('styles')
<style id="sariBuyerMessagesInlineStyles">
/* Buyer chat — Messenger-inspired workspace, SARI white/gold theme. */
.sari-bm-page{
  --bm-gold:#c99128;--bm-gold-hover:#ae7a1e;--bm-gold-soft:#fff7e7;
  --bm-charcoal:#303740;--bm-ink:#3a424d;--bm-muted:#8e99a6;
  --bm-line:#ebedf0;--bm-canvas:#f7f8fa;
  color:var(--bm-ink);font-family:Poppins,ui-sans-serif,system-ui,sans-serif;
  width:100%;margin:0;padding:0;background:#fff;
}
.sari-bm-page *, .sari-bm-page *::before,.sari-bm-page *::after{box-sizing:border-box}
.sari-bm-shell{position:relative;display:grid;grid-template-columns:310px minmax(0,1fr);
  width:100%;height:calc(100dvh - 88px);min-height:510px;margin:0;padding:0;overflow:hidden;background:#fff}
.sari-bm-sidebar,.sari-bm-chat,.sari-bm-context{min-width:0;min-height:0}
/* Inbox */
.sari-bm-sidebar{display:flex;flex-direction:column;background:#fff;border-right:1px solid var(--bm-line)}
.sari-bm-side-header{padding:24px 18px 16px;border-bottom:1px solid var(--bm-line);flex-shrink:0}
.sari-bm-side-header h2{margin:0;color:var(--bm-charcoal);font-size:20px;font-weight:750;letter-spacing:-.04em;line-height:1.25}
.sari-bm-side-header p{margin:6px 0 0;color:#9aa3af;font-size:10.5px;line-height:1.55}
.sari-bm-search-wrap{position:relative;display:flex;align-items:center;margin-top:20px}
.sari-bm-search-wrap svg{position:absolute;left:14px;width:17px;height:17px;stroke:var(--bm-gold);pointer-events:none}
.sari-bm-search-wrap input{width:100%;height:43px;padding:0 13px 0 42px;border:1px solid #e7eaee;border-radius:100px;
  color:var(--bm-charcoal);background:#f5f6f8;outline:0;font:inherit;font-size:11px;transition:border-color .15s,box-shadow .15s,background .15s}
.sari-bm-search-wrap input::placeholder{color:#9ba5b0}
.sari-bm-search-wrap input:focus{border-color:#e4bf75;background:#fff;box-shadow:0 0 0 3px rgba(201,145,40,.10)}
.sari-bm-sellers{flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain;padding:11px 8px 18px;scrollbar-width:thin;scrollbar-color:#d7dce2 transparent}
.sari-bm-seller{position:relative;display:flex;align-items:center;gap:12px;min-height:78px;margin:2px 0;padding:12px 10px;
  border-left:3px solid transparent;border-radius:12px;background:#fff;color:inherit;text-decoration:none;transition:background .15s,border-color .15s}
.sari-bm-seller:hover{background:#f8f9fa}
.sari-bm-seller.is-active{border-left-color:#c8ced6;background:#eef1f4}
.sari-bm-seller[hidden]{display:none}
.sari-bm-avatar{display:grid;place-items:center;width:50px;height:50px;flex:0 0 50px;overflow:hidden;border:1px solid #e6e9ed;
  border-radius:50%;background:#f1f4f7;color:#697886;font-size:14px;font-weight:750}
.sari-bm-avatar img{display:block;width:100%;height:100%;object-fit:cover}
.sari-bm-avatar.is-small{width:42px;height:42px;flex-basis:42px;font-size:12px}
.sari-bm-avatar.is-large{width:84px;height:84px;flex-basis:84px;font-size:26px;border:3px solid #fff;
  box-shadow:0 0 0 2px #f0dfbb,0 8px 24px rgba(38,45,55,.08)}
.sari-bm-seller-copy{display:grid;gap:5px;min-width:0;flex:1}
.sari-bm-seller-copy strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#303740;font-size:12px;font-weight:750;line-height:1.4}
.sari-bm-seller-copy small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#98a1ad;font-size:10px;font-weight:450;line-height:1.55}
.sari-bm-seller.is-active .sari-bm-seller-copy strong{color:#2c3037}
.sari-bm-unread{display:grid;place-items:center;min-width:20px;height:20px;flex:0 0 auto;border-radius:50px;padding:0 5px;background:var(--bm-gold);
  color:#fff;font-size:10px;font-weight:700}
.sari-bm-no-stores{margin:0;padding:32px 20px;color:#9aa3ae;font-size:11px;line-height:1.65}
/* Conversation */
.sari-bm-chat{display:flex;flex-direction:column;overflow:hidden;background:var(--bm-canvas)}
.sari-bm-chat-header{display:flex;align-items:center;justify-content:space-between;gap:14px;min-height:78px;padding:12px 22px;
  background:#fff;border-bottom:1px solid var(--bm-line);flex-shrink:0}
.sari-bm-header-copy{display:flex;align-items:center;gap:12px;min-width:0}
.sari-bm-header-copy>div{min-width:0}
.sari-bm-header-copy h2{margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--bm-charcoal);
  font-size:14px;font-weight:750;letter-spacing:-.018em;line-height:1.4}
.sari-bm-header-copy p{margin:5px 0 0;color:#9aa3ae;font-size:10px;line-height:1.35}
.sari-bm-header-right{display:flex;align-items:center;gap:8px;flex-shrink:0}
.sari-bm-live{display:inline-flex;align-items:center;gap:6px;margin-right:8px;color:#9aa3ae;font-size:10px;font-weight:600;white-space:nowrap}
.sari-bm-live::before{content:"";display:block;width:7px;height:7px;border-radius:50%;background:#b5bdc6}
.sari-bm-live.is-live{color:#648976}.sari-bm-live.is-live::before{background:#46a371}
.sari-bm-role-mini{display:none}
.sari-bm-header-action{display:grid;place-items:center;width:37px;height:37px;flex:0 0 37px;border:1px solid transparent;
  border-radius:50%;background:transparent;color:var(--bm-gold);text-decoration:none;transition:background .15s,border-color .15s}
.sari-bm-header-action:hover{background:#fff7e8;border-color:#f0e1c3;color:var(--bm-gold-hover)}
.sari-bm-header-action svg{width:19px;height:19px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}
.sari-bm-thread{display:flex;flex-direction:column;gap:17px;flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain;
  padding:30px 22px 24px;background:var(--bm-canvas);scrollbar-width:thin;scrollbar-color:#d2d7de transparent}
.sari-bm-message{display:flex;align-items:flex-end;justify-content:flex-start;gap:10px;min-width:0}
.sari-bm-message.is-mine{justify-content:flex-end}
.sari-bm-message-avatar{display:grid;place-items:center;width:32px;height:32px;flex:0 0 32px;overflow:hidden;border:1px solid #e2e7ed;
  border-radius:50%;background:#fff;color:#9d762f;font-size:11px;font-weight:750}
.sari-bm-message-avatar img{display:block;width:100%;height:100%;object-fit:cover}
.sari-bm-message-content{min-width:0;max-width:min(77%,580px)}
.sari-bm-message:has(.sari-order-card) .sari-bm-message-content{width:min(100%,470px);max-width:min(100%,470px)}
.sari-bm-sender{display:block;margin:0 0 6px;color:#89929e;font-size:10px;font-weight:650}
.sari-bm-message.is-mine .sari-bm-sender{text-align:right}
.sari-bm-bubble{display:block;margin:0;padding:12px 15px;border:1px solid #eceef1;border-radius:17px 17px 17px 5px;
  background:#fff;color:#3c4652;font-size:11.5px;font-weight:450;line-height:1.65;overflow-wrap:anywhere;white-space:pre-wrap;
  box-shadow:0 2px 8px rgba(22,34,49,.04)}
.sari-bm-message.is-mine .sari-bm-bubble{border-color:#e9e7e1;border-radius:17px 17px 5px 17px;
  background:#fff;color:#303740;box-shadow:0 3px 10px rgba(22,34,49,.05)}
.sari-bm-time{display:block;margin:6px 2px 0;color:#a3abb5;font-size:9px;line-height:1.4}
.sari-bm-message.is-mine .sari-bm-time{text-align:right}
.sari-bm-empty{max-width:360px;margin:auto;padding:30px 20px;text-align:center;color:#97a2ae}
.sari-bm-empty-icon{display:grid;place-items:center;width:62px;height:62px;margin:0 auto 15px;border-radius:50%;background:#f1f3f6;color:#c99128}
.sari-bm-empty-icon svg{width:26px;height:26px}
.sari-bm-empty h3{margin:0;color:#4b5563;font-size:13px;font-weight:700}
.sari-bm-empty p{margin:9px 0 0;font-size:11px;line-height:1.7}
.sari-bm-empty.is-page{display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1}
/* Composer, retain the original send form and its JS hooks. */
.sari-bm-composer{flex-shrink:0;padding:13px 17px 15px;border-top:1px solid var(--bm-line);background:#fff}
.sari-bm-feedback{margin:0 0 8px;font-size:11px;color:#b54049}
.sari-bm-feedback[hidden]{display:none}
.sari-bm-composer form{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:10px;min-height:auto;padding:0;border:0;background:transparent;box-shadow:none}
.sari-bm-composer form:focus-within{border-color:transparent;background:transparent;box-shadow:none}
.sari-bm-compose-tools{display:flex;align-items:center;gap:4px;flex:0 0 auto;padding:0}
.sari-bm-compose-tool{display:grid;place-items:center;width:28px;height:28px;flex:0 0 28px;border:0;border-radius:8px;background:transparent;color:var(--bm-gold);cursor:pointer;transition:background .15s,color .15s}
.sari-bm-compose-tool:hover{background:#fff3dd;color:var(--bm-gold-hover)}
.sari-bm-compose-tool svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}
.sari-bm-compose-main{display:flex;flex-direction:column;gap:6px;flex:1;min-width:0;padding:0 14px;border:1px solid #d5a64a;border-radius:18px;background:#fff;min-height:54px}
.sari-bm-composer textarea{flex:1;min-width:0;min-height:22px;max-height:96px;padding:14px 0 13px;resize:none;border:0;background:transparent;
  outline:0;color:#303740;font:inherit;font-size:11.5px;line-height:19px;box-shadow:none}
.sari-bm-composer textarea::placeholder{color:#9ea7b2}
.sari-bm-composer textarea:focus-visible{outline:none;box-shadow:none}
.sari-bm-attachment-preview[hidden]{display:none}
.sari-bm-attachment-preview{display:flex;align-items:center;gap:10px;min-width:0}
.sari-bm-attachment-chip{display:flex;align-items:center;gap:10px;min-width:0;max-width:100%;padding:8px 10px;border:1px solid #d9dde3;border-radius:14px;background:#f5f7f9}
.sari-bm-attachment-thumb{width:36px;height:36px;flex:0 0 36px;overflow:hidden;border-radius:10px;background:#f2f3f5;border:1px solid #e6e8ec;display:grid;place-items:center;color:#8b97a3}
.sari-bm-attachment-thumb img{display:block;width:100%;height:100%;object-fit:cover}
.sari-bm-attachment-thumb svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:1.8}
.sari-bm-attachment-meta{min-width:0;display:grid;gap:2px}.sari-bm-attachment-meta strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#303740;font-size:11px;font-weight:700}.sari-bm-attachment-meta span{display:block;color:#98a2ae;font-size:10px;line-height:1.45}
.sari-bm-attachment-remove{display:grid;place-items:center;flex:0 0 28px;width:28px;height:28px;border:0;border-radius:50%;background:#fff;color:#8f99a4;cursor:pointer;transition:background .15s,color .15s}
.sari-bm-attachment-remove:hover{background:#f5e9d2;color:#8a5d10}.sari-bm-attachment-remove svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2}
.sari-bm-composer button{display:grid;place-items:center;flex:0 0 42px;width:42px;height:42px;padding:0;border:0;border-radius:50%;
  background:var(--bm-gold);color:#fff;cursor:pointer;box-shadow:0 4px 10px rgba(201,145,40,.2);transition:background .15s,transform .15s}
.sari-bm-composer button:hover{background:var(--bm-gold-hover)}
.sari-bm-composer button:active{transform:translateY(1px)}
.sari-bm-composer button:disabled{opacity:.58;cursor:wait}
.sari-bm-composer button svg{width:19px;height:19px}
.sari-bm-composer button span{display:none}
/* Seller profile drawer — hidden by default, opens from the info icon beside Live chat. */
/* Smooth JS-controlled seller info drawer. No hash navigation or page refresh. */
.sari-bm-context-backdrop{position:absolute;inset:0;z-index:23;width:100%;height:100%;padding:0;border:0;
  background:rgba(255,255,255,.06);-webkit-backdrop-filter:blur(2px);backdrop-filter:blur(2px);
  opacity:0;visibility:hidden;pointer-events:none;cursor:default;
  transition:opacity .25s ease,visibility 0s linear .3s}
.sari-bm-context-backdrop.is-open{opacity:1;visibility:visible;pointer-events:auto;transition:opacity .25s ease,visibility 0s}
.sari-bm-context{display:flex;position:absolute;z-index:24;top:0;right:0;bottom:0;width:min(372px,100%);flex-direction:column;overflow-y:auto;
  border-left:1px solid #e4e8ed;background:#f5f7fa;box-shadow:-18px 0 45px rgba(28,35,45,.14);
  scrollbar-width:thin;scrollbar-color:#e0e4e9 transparent;transform:translateX(102%);opacity:0;visibility:hidden;pointer-events:none;
  transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .22s ease,visibility 0s linear .34s}
.sari-bm-context.is-open{transform:translateX(0);opacity:1;visibility:visible;pointer-events:auto;
  transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .22s ease,visibility 0s}
.sari-bm-context-header{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px 14px;border-bottom:1px solid var(--bm-line);background:#fff}
.sari-bm-context-header h2{margin:0;color:var(--bm-charcoal);font-size:12.5px;font-weight:750}
.sari-bm-context-header p{margin:5px 0 0;color:#9ba4ae;font-size:10px;line-height:1.45}
.sari-bm-context-close{display:grid;width:34px;height:34px;place-items:center;border:0;border-radius:50%;background:#f5f6f8;color:#606c78;text-decoration:none}
.sari-bm-context-close:hover{background:#eceff3;color:#374151}
.sari-bm-context-close svg{width:16px;height:16px;stroke:currentColor;stroke-width:2;fill:none}
.sari-bm-context-body{padding:18px 18px 24px}
.sari-bm-context-profile{display:flex;flex-direction:column;align-items:center;text-align:center;margin-top:0;padding:4px 0 0}
.sari-bm-context-profile h3{margin:12px 0 0;max-width:100%;color:#303740;font-size:16px;font-weight:750;line-height:1.5;overflow-wrap:anywhere}
.sari-bm-context-profile p{margin:3px 0 0;color:#9aa3ae;font-size:10px}
.sari-bm-role{display:inline-flex;align-items:center;justify-content:center;min-height:28px;margin-top:10px;padding:0 16px;border-radius:999px;border:1px solid #eedcb8;background:#fff9ee;color:#a67819;font-size:10px;font-weight:700;letter-spacing:.02em;text-transform:uppercase}
.sari-bm-profile-actions{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:18px 0 0}.sari-bm-profile-action{display:flex;align-items:center;gap:11px;padding:12px 13px;border:1px solid #e6eaef;border-radius:16px;background:#fff;color:#586270;text-decoration:none;font-size:11px;font-weight:650;transition:border-color .15s,box-shadow .15s,transform .15s}
.sari-bm-profile-action:hover{border-color:#e0c387;box-shadow:0 10px 22px rgba(31,41,55,.07);transform:translateY(-1px)}
.sari-bm-profile-action-icon{display:grid;place-items:center;width:40px;height:40px;flex:0 0 40px;border-radius:12px;background:#fff7e8;color:var(--bm-gold);border:1px solid #f0dfbb;transition:background .15s,border-color .15s,color .15s}
.sari-bm-profile-action-icon svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}.sari-bm-profile-action:hover .sari-bm-profile-action-icon{background:#fffbf2;border-color:#ead8b1;color:var(--bm-gold-hover)}
.sari-bm-info-list{display:grid;gap:10px;margin-top:18px;padding-top:0;border-top:0}.sari-bm-info-accordion{overflow:hidden;border:1px solid #e5e9ef;border-radius:16px;background:#fff}
.sari-bm-info-accordion summary{list-style:none;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 15px;cursor:pointer}.sari-bm-info-accordion summary::-webkit-details-marker{display:none}
.sari-bm-info-summary{display:flex;align-items:center;gap:12px;min-width:0}.sari-bm-info-summary svg{width:18px;height:18px;flex:0 0 18px;fill:none;stroke:var(--bm-gold);stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}.sari-bm-info-summary span{color:#303740;font-size:12px;font-weight:730;letter-spacing:-.01em;line-height:1.45}
.sari-bm-info-chevron{color:#9ca5af;transition:transform .15s ease}.sari-bm-info-chevron svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}.sari-bm-info-accordion[open] .sari-bm-info-chevron{transform:rotate(180deg)}
.sari-bm-info-body{padding:0 15px 14px 45px}.sari-bm-info-body p{margin:0;color:#6f7a87;font-size:11px;line-height:1.75;overflow-wrap:anywhere}.sari-bm-info-body strong{display:block;color:#46505c;font-size:11.5px;font-weight:600}
.sari-bm-info-link-row{display:flex;flex-wrap:wrap;gap:14px;margin-top:10px}.sari-bm-info-link{display:inline-flex;align-items:center;gap:5px;color:#7d8896;text-decoration:none;font-size:10.5px;font-weight:600}.sari-bm-info-link:hover{color:var(--bm-gold-hover);text-decoration:underline;text-underline-offset:3px}.sari-bm-info-link span{color:var(--bm-gold)}
.sari-bm-store-rating{display:flex;align-items:baseline;gap:6px;margin:0 0 6px}.sari-bm-store-rating-star{font-size:16px;color:var(--bm-gold);line-height:1}.sari-bm-store-rating-value{color:#46505c;font-size:18px;font-weight:750;letter-spacing:-.03em}.sari-bm-store-rating-outof{color:#9aa4af;font-size:11px;font-weight:600}.sari-bm-store-rating-empty{margin:0;color:#98a2ad;font-size:11px;line-height:1.6}
.sari-bm-context-panel{margin-top:14px;padding:14px 13px;border:1px solid #e5e9ee;border-radius:13px;background:#f8fafb}.sari-bm-context-panel h4{margin:0;color:#607488;font-size:11px}.sari-bm-context-panel p{margin:7px 0 0;color:#8d99a6;font-size:10px;line-height:1.7}
.sari-bm-attachment-message{display:grid;gap:8px;margin-top:8px}.sari-bm-attachment-message.is-only{margin-top:0}
.sari-bm-attachment-link{display:flex;align-items:center;gap:10px;max-width:100%;padding:9px 11px;border:1px solid #eceef2;border-radius:16px;background:#fff;text-decoration:none;box-shadow:0 2px 8px rgba(22,34,49,.04)}.sari-bm-message.is-mine .sari-bm-attachment-link{background:#fff}.sari-bm-attachment-link:hover{border-color:#ead9b6}
.sari-bm-attachment-image{display:block;max-width:100%;width:min(270px,100%);border-radius:16px;border:1px solid #eceef2;background:#fff;overflow:hidden;box-shadow:0 2px 8px rgba(22,34,49,.04)}.sari-bm-attachment-image img{display:block;width:100%;height:auto;max-height:260px;object-fit:cover}
.sari-bm-attachment-file-icon{display:grid;place-items:center;width:38px;height:38px;flex:0 0 38px;border-radius:12px;background:#f5f6f8;color:var(--bm-gold)}.sari-bm-attachment-file-icon svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8}
.sari-bm-attachment-file-copy{min-width:0;display:grid;gap:2px}.sari-bm-attachment-file-copy strong{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#3a424d;font-size:11px;font-weight:700}.sari-bm-attachment-file-copy span{display:block;color:#9aa4af;font-size:10px}

.sari-bm-alert{margin:0;padding:10px 18px;border-bottom:1px solid #e8d5ad;background:#fffaf0;color:#9d7129;font-size:11px}
.sari-bm-alert.is-error{background:#fff5f5;color:#af3e45;border-color:#efc5c5}
.sari-bm-alert.is-success{background:#f3faf5;color:#537b5f;border-color:#cfe7d6}
.sari-bm-page .sari-order-card{width:100%;max-width:100%;border-radius:17px;box-shadow:0 10px 25px rgba(22,30,42,.11)}
.sari-bm-page .sari-order-confirm-button{background:var(--bm-gold)}
.sari-bm-page .sari-order-confirm-button:hover{background:var(--bm-gold-hover)}
.sari-bm-page a:focus-visible,.sari-bm-page button:focus-visible{
  outline:2px solid var(--bm-gold);outline-offset:3px}
@media(max-width:1320px){
 .sari-bm-shell{grid-template-columns:275px minmax(0,1fr)}
 .sari-bm-context{width:min(350px,100%)}
}
@media(max-width:850px){
 .sari-bm-shell{grid-template-columns:230px minmax(0,1fr)}
 .sari-bm-seller{gap:8px;padding:10px 7px}
 .sari-bm-side-header{padding:18px 12px 14px}
 .sari-bm-side-header h2{font-size:17px}
 .sari-bm-thread{padding:20px 13px}
 .sari-bm-chat-header{padding:11px 12px}
 .sari-bm-live{display:none}
}
@media(max-width:650px){
 .sari-bm-shell{height:calc(100dvh - 76px);min-height:420px;grid-template-columns:minmax(0,1fr);grid-template-rows:auto minmax(0,1fr)}
 .sari-bm-sidebar{max-height:160px;border-right:0;border-bottom:1px solid var(--bm-line)}
 .sari-bm-side-header{padding:9px 12px 5px;border-bottom:0}
 .sari-bm-side-header h2{font-size:14px}
 .sari-bm-side-header p{display:none}
 .sari-bm-search-wrap{margin-top:7px}
 .sari-bm-search-wrap input{height:32px;font-size:10px}
 .sari-bm-sellers{display:flex;flex:0 0 auto;gap:6px;min-height:0;overflow-x:auto;overflow-y:hidden;padding:4px 8px 8px}
 .sari-bm-seller{flex:0 0 185px;min-height:60px;margin:0;padding:6px 9px;border:1px solid transparent;border-bottom:3px solid transparent;border-radius:10px}
 .sari-bm-seller.is-active{border:1px solid #d6dde5;border-bottom:3px solid #aeb7c2;background:#eef1f4}
 .sari-bm-avatar{width:36px;height:36px;flex-basis:36px}
 .sari-bm-seller-copy strong{font-size:10.5px}.sari-bm-seller-copy small{font-size:9px}
 .sari-bm-chat-header{min-height:62px;padding:9px 10px}
 .sari-bm-header-copy{gap:8px}.sari-bm-header-copy h2{font-size:12px}.sari-bm-header-copy p{font-size:9px}
 .sari-bm-header-action{width:33px;height:33px;flex-basis:33px}
 .sari-bm-header-action svg{width:17px;height:17px}
 .sari-bm-thread{padding:13px 10px 15px;gap:15px}
 .sari-bm-message-content{max-width:85%}.sari-bm-message:has(.sari-order-card) .sari-bm-message-content{width:100%;max-width:calc(100% - 4px)}
 .sari-bm-message-avatar{width:27px;height:27px;flex-basis:27px}
 .sari-bm-bubble{font-size:11px;padding:10px 12px}
 .sari-bm-composer{padding:8px}.sari-bm-composer form{min-height:auto;padding:0}
 .sari-bm-compose-tools{gap:4px;padding:0}
 .sari-bm-compose-tool{width:30px;height:30px;flex-basis:30px}
 .sari-bm-compose-tool svg{width:16px;height:16px}
 .sari-bm-attachment-chip{padding:7px 8px}
 .sari-bm-composer button{width:38px;height:38px;flex-basis:38px}
 .sari-bm-context{width:min(355px,100%)}
}
@media(prefers-reduced-motion:reduce){.sari-bm-page *{transition:none!important;scroll-behavior:auto!important}}

/* Inline order-confirmation card styling for buyer chat — cleaner, lighter and modern. */
.sari-order-card{width:min(100%,430px);margin-top:10px;overflow:hidden;border:1px solid #e3e7ee;border-radius:22px;background:linear-gradient(180deg,#ffffff 0%,#fbfcfd 100%);color:#334155;box-shadow:0 14px 30px rgba(15,23,42,.08);font-family:Poppins,ui-sans-serif,system-ui,sans-serif}
.sari-order-card-head{display:flex;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid #edf1f5;background:linear-gradient(180deg,#fffdfa 0%,#ffffff 100%)}
.sari-order-icon{width:40px;height:40px;display:grid;place-items:center;flex-shrink:0;border-radius:14px;background:#fff4dd;color:#c99128;border:1px solid #f2dfb6}
.sari-order-icon svg{height:20px;width:20px}
.sari-order-headline{min-width:0;flex:1;display:grid;gap:4px}.sari-order-headline strong{font-size:13px;font-weight:700;line-height:1.35;color:#1f2937}.sari-order-headline small{font-size:10px;color:#7b8794;overflow-wrap:anywhere}
.sari-order-status{flex-shrink:0;border:1px solid #efd59d;border-radius:999px;background:#fff8e8;padding:6px 10px;color:#9d7129;font-size:9px;font-weight:700;letter-spacing:.02em;text-transform:uppercase}.sari-order-status.is-confirmed{background:#eef9f0;border-color:#b7ddc0;color:#2f7a43}
.sari-order-products{margin:14px 16px 12px;padding:14px;border:1px solid #e7ecf2;border-radius:16px;background:#f7f9fb;display:grid;gap:10px}.sari-order-products-label{font-size:10px;font-weight:700;color:#7b8794;text-transform:uppercase;letter-spacing:.03em}.sari-order-product{display:flex;align-items:center;gap:11px;font-size:11px;line-height:1.6;color:#334155;overflow-wrap:anywhere}.sari-order-products small{font-size:10px;color:#8a94a1}.sari-order-total{padding-top:10px;border-top:1px solid #e2e8f0;color:#1f2937;font-size:12px;font-weight:700;text-align:right}
.sari-order-details{display:grid;gap:10px;margin:0;padding:0 16px 16px}.sari-order-details>div{display:grid;grid-template-columns:96px minmax(0,1fr);gap:10px;padding:12px 14px;border:1px solid #e7ecf2;border-radius:14px;background:#fff}.sari-order-details dt{font-size:10px;font-weight:700;color:#7b8794;text-transform:uppercase;letter-spacing:.03em}.sari-order-details dd{min-width:0;margin:0;font-size:11px;line-height:1.65;text-align:right;color:#334155;overflow-wrap:anywhere;white-space:pre-wrap}
.sari-order-confirm-button{width:calc(100% - 32px);margin:0 16px;min-height:46px;padding:12px 14px;border:0;border-radius:14px;background:#c99128;color:#fff;font-size:12px;font-weight:750;cursor:pointer;box-shadow:0 10px 20px rgba(201,145,40,.22);transition:background .15s,transform .15s,box-shadow .15s}.sari-order-confirm-button:hover{background:#b9821e;box-shadow:0 12px 24px rgba(185,130,30,.24)}.sari-order-confirm-button:active{transform:translateY(1px)}.sari-order-confirm-button:focus-visible{outline:3px solid #f4dca8;outline-offset:3px}.sari-order-confirm-button:disabled{opacity:.7;cursor:wait}
.sari-order-hint{padding:10px 16px 16px;margin:0;color:#7b8794;font-size:10px;line-height:1.75}.sari-order-confirmed{display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 16px 18px;color:#2f7a43;font-size:11px;font-weight:700}.sari-order-confirmed svg{width:18px;height:18px}
@media(max-width:440px){.sari-order-card-head{flex-wrap:wrap}.sari-order-status{margin-left:52px}.sari-order-details>div{grid-template-columns:1fr;gap:4px}.sari-order-details dd{text-align:left}}
@media(prefers-reduced-motion:reduce){.sari-order-confirm-button{transition:none}}

.sari-order-product img{width:44px;height:44px;flex:0 0 44px;object-fit:cover;background:#fff;border-radius:12px;border:1px solid #e4e8ee}.sari-order-product span{min-width:0;flex:1}

/* Admin Support conversations share the existing compact inbox design. */
.sari-bm-admin-threads[hidden]{display:none!important}
.sari-bm-admin-threads{padding:9px 8px 10px;border-bottom:1px solid #e8edf2;max-height:230px;overflow:auto;scrollbar-width:thin}
.sari-bm-admin-threads .sari-bm-seller{min-height:61px;margin:2px 0;padding:9px 10px}
.sari-bm-admin-threads .sari-bm-avatar{width:39px;height:39px;flex-basis:39px;background:#fff7e7;border-color:#f1dfb8;color:#b77c0f;font-size:15px}
.sari-bm-admin-threads .sari-bm-seller-copy strong{font-size:11.5px}
.sari-bm-admin-threads .sari-bm-seller-copy small{font-size:10px}
@media(max-width:790px){.sari-bm-admin-threads{display:flex;gap:5px;max-height:none;overflow-x:auto;padding:6px 8px}.sari-bm-admin-threads .sari-bm-seller{flex:0 0 205px}}
</style>
@endpush

@section('content')
@include('components.buyer.header')
@if (in_array(request()->query('channel'), ['support', 'report'], true))
    @include('buyer.partials.admin-support-messages')
@else
<div class="sari-bm-page">

    <section id="sariBuyerSellerChat" class="sari-bm-shell"
        data-seller-id="{{ $selectedSeller?->id ?? '' }}"
        data-thread-url="{{ $selectedSeller ? route('buyer.messages.thread', $selectedSeller) : '' }}"
        data-send-url="{{ $selectedSeller ? route('buyer.messages.send', $selectedSeller) : '' }}"
        data-confirm-template="{{ route('buyer.messages.confirm-address', ['order' => '__ORDER__']) }}"
        data-private-channel="sari.buyer-seller.buyer.{{ session('buyer_account_id') ? 'account.'.session('buyer_account_id') : 'social.'.session('buyer_social_account_id') }}">

        {{-- Conversation list: visually aligned with the Seller Center. --}}
        <aside class="sari-bm-sidebar" aria-label="Seller conversations">
            <div class="sari-bm-side-header">
                <h2>Messages</h2>
                <p>Your conversations with SARI stores</p>
                <label class="sari-bm-search-wrap">
                    <span class="sr-only">Search stores</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
                    <input id="sariBuyerSellerSearch" type="search" placeholder="Search messages..." autocomplete="off">
                </label>
            </div>
            <nav id="sariBmAdminThreads" class="sari-bm-admin-threads" aria-label="Admin support conversations"
                 data-inbox-url="{{ route('messaging.api.index') }}"
                 data-messages-url="{{ route('buyer.messages') }}" hidden></nav>
            <nav class="sari-bm-sellers" aria-label="Store messages">
                @forelse ($sellers as $seller)
                    @php
                        $storeInitial = mb_strtoupper(mb_substr((string) ($seller->store_name ?: 'S'), 0, 1));
                        $logo = filled($seller->store_logo_path)
                            ? \Illuminate\Support\Facades\Storage::disk('public')->url($seller->store_logo_path)
                            : null;
                    @endphp
                    <a href="{{ route('buyer.messages', ['seller' => $seller->id]) }}"
                       class="sari-bm-seller {{ $selectedSeller?->id === $seller->id ? 'is-active' : '' }}"
                       data-store-search="{{ strtolower($seller->store_name ?: '') }}"
                       @if ($selectedSeller?->id === $seller->id) aria-current="page" @endif wire:navigate.hover>
                        <span class="sari-bm-avatar" aria-hidden="true">
                            @if ($logo)<img src="{{ $logo }}" alt="" loading="lazy">@else {{ $storeInitial }} @endif
                        </span>
                        <span class="sari-bm-seller-copy">
                            <strong>{{ $seller->store_name ?: 'SARI Seller Store' }}</strong>
                            <small>{{ $selectedSeller?->id === $seller->id && $messages->isNotEmpty() ? \Illuminate\Support\Str::limit((string) $messages->last()?->body, 44) : 'Seller conversation' }}</small>
                        </span>
                        @if ((int) ($unreadCounts[$seller->id] ?? 0) > 0)
                            <span class="sari-bm-unread" aria-label="Unread messages">{{ (int) $unreadCounts[$seller->id] }}</span>
                        @endif
                    </a>
                @empty
                    <p class="sari-bm-no-stores">No seller conversations are available yet.</p>
                @endforelse
            </nav>
        </aside>

        {{-- Active conversation: existing JS IDs and routes remain unchanged. --}}
        <div class="sari-bm-chat">
            @if ($selectedSeller)
                @php
                    $selectedSellerLogo = filled($selectedSeller->store_logo_path)
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($selectedSeller->store_logo_path)
                        : null;
                    $selectedSellerInitial = mb_strtoupper(mb_substr($selectedSeller->store_name ?: 'S', 0, 1));
                    // Only show the seller's published pickup/store location, never a private registration street address.
                    $storeLocation = trim((string) ($selectedSeller->pickup_address ?? ''));
                    if ($storeLocation === '') {
                        $storeLocation = collect([
                            $selectedSeller->barangay_name,
                            $selectedSeller->municipality_name,
                            $selectedSeller->province_name,
                        ])->map(fn ($part) => trim((string) $part))->filter()->implode(', ');
                    }
                    $storePhone = trim((string) ($selectedSeller->store_phone ?? ''));
                    $storeDialNumber = preg_replace('/[^0-9+]/', '', $storePhone);
                    $storePublicEmail = trim((string) ($selectedSeller->store_public_email ?? ''));
                @endphp
                <header class="sari-bm-chat-header">
                    <div class="sari-bm-header-copy">
                        <span class="sari-bm-avatar is-small" aria-hidden="true">
                            @if ($selectedSellerLogo)
                                <img src="{{ $selectedSellerLogo }}" alt="" loading="lazy">
                            @else
                                {{ $selectedSellerInitial }}
                            @endif
                        </span>
                        <div>
                            <h2>{{ $selectedSeller->store_name ?: 'SARI Seller Store' }}</h2>
                            <p>Seller conversation · Order coordination</p>
                        </div>
                    </div>
                    <div class="sari-bm-header-right">
                        <span class="sari-bm-live" id="sariBmStatus" role="status" aria-live="polite">Connecting…</span>
                        <button id="sariBmInfoToggle" type="button" class="sari-bm-header-action" data-sari-info-open
                                aria-controls="sariBmSellerDetails" aria-expanded="false"
                                aria-label="Open seller information" title="Seller information">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5h.01"/></svg>
                        </button>
                    </div>
                </header>

                <div id="sariBuyerSellerMessageList" class="sari-bm-thread"
                     aria-label="Conversation with {{ $selectedSeller->store_name }}"
                     aria-live="polite" aria-relevant="additions">
                    @forelse ($messages as $message)
                        @php $mine = $message->sender_role === 'buyer'; @endphp
                        <div class="sari-bm-message {{ $mine ? 'is-mine' : '' }}" data-message-id="{{ $message->id }}">
                            @unless ($mine)
                                <span class="sari-bm-message-avatar" aria-hidden="true">
                                    @if ($selectedSellerLogo)<img src="{{ $selectedSellerLogo }}" alt="">@else{{ $selectedSellerInitial }}@endif
                                </span>
                            @endunless
                            <div class="sari-bm-message-content">
                                @php
                                    $attachmentUrl = filled($message->attachment_path ?? null)
                                        ? \Illuminate\Support\Facades\Storage::disk($message->attachment_disk ?? 'public')->url($message->attachment_path)
                                        : null;
                                    $attachmentMime = (string) ($message->attachment_mime_type ?? '');
                                    $attachmentName = (string) ($message->attachment_original_name ?? 'Attachment');
                                    $attachmentIsImage = $attachmentUrl && str_starts_with($attachmentMime, 'image/');
                                    $hasBody = filled(trim((string) $message->body));
                                @endphp
                                <span class="sari-bm-sender">{{ $mine ? 'You' : (($message->message_type ?? 'text') === 'order_confirmation' ? 'Automatic order confirmation' : ($selectedSeller->store_name ?: 'Seller')) }}</span>
                                @if ($attachmentUrl)
                                    <div class="sari-bm-attachment-message {{ $hasBody ? '' : 'is-only' }}">
                                        @if ($attachmentIsImage)
                                            <a class="sari-bm-attachment-image" href="{{ $attachmentUrl }}" target="_blank" rel="noopener noreferrer"><img src="{{ $attachmentUrl }}" alt="{{ $attachmentName }}" loading="lazy"></a>
                                        @else
                                            <a class="sari-bm-attachment-link" href="{{ $attachmentUrl }}" target="_blank" rel="noopener noreferrer"><span class="sari-bm-attachment-file-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 3H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9l-4-6Z"/><path d="M14 3v6h4"/></svg></span><span class="sari-bm-attachment-file-copy"><strong>{{ $attachmentName }}</strong><span>{{ strtoupper(pathinfo($attachmentName, PATHINFO_EXTENSION) ?: 'FILE') }}</span></span></a>
                                        @endif
                                    </div>
                                @endif
                                @if ($hasBody)
                                    <p class="sari-bm-bubble">{{ $message->body }}</p>
                                @endif
                                @if (($message->message_type ?? 'text') === 'order_confirmation' && $message->order)
                                    @include('components.messaging.order-confirmation-card', ['order' => $message->order, 'forBuyer' => true])
                                @endif
                                <time class="sari-bm-time" datetime="{{ $message->created_at?->toIso8601String() }}">{{ $message->created_at?->format('M d, h:i A') }}</time>
                            </div>
                        </div>
                    @empty
                        <div class="sari-bm-empty" id="sariBmEmpty">
                            <span class="sari-bm-empty-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4 4v7Z"/></svg>
                            </span>
                            <h3>Start your conversation</h3>
                            <p>Message the seller about your order. Automatic order confirmations appear here after checkout.</p>
                        </div>
                    @endforelse
                </div>

                <div class="sari-bm-composer">
                    <p id="sariBmFeedback" class="sari-bm-feedback" role="alert" hidden></p>
                    <form id="sariBuyerSellerSendForm" action="{{ route('buyer.messages.send', $selectedSeller) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="sari-bm-compose-tools" aria-label="Message attachments">
                            <label class="sari-bm-compose-tool" for="sariBmImageInput" title="Upload image"><span class="sr-only">Upload image</span><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="1.7"/><path d="m21 16-5.3-5.3a1.5 1.5 0 0 0-2.1 0L6 18"/></svg></label>
                            <input id="sariBmImageInput" type="file" accept="image/*" hidden>
                            <label class="sari-bm-compose-tool" for="sariBmFileInput" title="Upload file"><span class="sr-only">Upload file</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9l-4-6Z"/><path d="M14 3v6h4"/></svg></label>
                            <input id="sariBmFileInput" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.7z,.csv,.jpg,.jpeg,.png,.webp" hidden>
                        </div>
                        <div class="sari-bm-compose-main">
                            <div id="sariBmAttachmentPreview" class="sari-bm-attachment-preview" hidden></div>
                            <label for="sariBmMessageInput" class="sr-only">Message</label>
                            <textarea id="sariBmMessageInput" name="body" maxlength="3000" rows="1" placeholder="Write a message to the seller..."></textarea>
                        </div>
                        <button id="sariBmSend" type="submit" aria-label="Send message" title="Send message">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 4 17 8-17 8 3-8-3-8Z"/><path d="M7 12h14"/></svg>
                            <span>Send</span>
                        </button>
                    </form>

                </div>
            @else
                <div class="sari-bm-empty is-page">
                    <span class="sari-bm-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 14a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"/></svg>
                    </span>
                    <h3>Choose a conversation</h3>
                    <p>Select a seller to view messages and order confirmations.</p>
                </div>
            @endif
        </div>

        {{-- Seller information drawer opens only from the (i) button. --}}
        <button type="button" class="sari-bm-context-backdrop" data-sari-info-backdrop
                aria-hidden="true" tabindex="-1" aria-label="Close seller information"></button>
        <aside id="sariBmSellerDetails" class="sari-bm-context" role="dialog" aria-modal="true"
               aria-labelledby="sariBmSellerPanelTitle" aria-describedby="sariBmSellerPanelDescription"
               aria-hidden="true" inert>
            <div class="sari-bm-context-header">
                <div>
                    <h2 id="sariBmSellerPanelTitle">{{ $selectedSeller ? 'Seller profile' : 'Chat details' }}</h2>
                    <p id="sariBmSellerPanelDescription">{{ $selectedSeller ? 'Selected store information' : 'About SARI messages' }}</p>
                </div>
                <button type="button" class="sari-bm-context-close" data-sari-info-close aria-label="Close seller information">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 5 14 14M19 5 5 19"/></svg>
                </button>
            </div>
            <div class="sari-bm-context-body">
                @if ($selectedSeller)
                    <div id="sariBmSellerProfile" class="sari-bm-context-profile">
                        <span class="sari-bm-avatar is-large" aria-hidden="true">@if ($selectedSellerLogo)<img src="{{ $selectedSellerLogo }}" alt="" loading="lazy">@else{{ $selectedSellerInitial }}@endif</span>
                        <h3>{{ $selectedSeller->store_name ?: 'SARI Seller Store' }}</h3><p>SARI Marketplace seller</p><span class="sari-bm-role">Seller</span>
                    </div>
                    <nav class="sari-bm-profile-actions" aria-label="Seller actions"><a class="sari-bm-profile-action" href="{{ route('buyer.shop', ['shop' => 'seller-' . (int) $selectedSeller->id]) }}" title="Visit {{ $selectedSeller->store_name ?: 'SARI Seller Store' }} store"><span class="sari-bm-profile-action-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4 20a8 8 0 0 1 16 0"/></svg></span><span>Profile</span></a>@if ($storeLocation !== '')<a class="sari-bm-profile-action" href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode($storeLocation) }}" target="_blank" rel="noopener noreferrer" title="Locate the seller's store on Google Maps"><span class="sari-bm-profile-action-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span><span>Map</span></a>@endif</nav>
                    <div class="sari-bm-info-list">
                        <details class="sari-bm-info-accordion"><summary><span class="sari-bm-info-summary"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span>Store location</span></span><span class="sari-bm-info-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span></summary><div class="sari-bm-info-body"><p>{{ $storeLocation !== '' ? $storeLocation : 'Location not provided' }}</p>@if ($storeLocation !== '')<div class="sari-bm-info-link-row"><a class="sari-bm-info-link" href="https://www.google.com/maps/search/?api=1&amp;query={{ rawurlencode($storeLocation) }}" target="_blank" rel="noopener noreferrer">View on map <span aria-hidden="true">↗</span></a></div>@endif</div></details>
                        <details class="sari-bm-info-accordion"><summary><span class="sari-bm-info-summary"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg><span>Store rating</span></span><span class="sari-bm-info-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span></summary><div class="sari-bm-info-body">@if (($sellerReviewCount ?? 0) > 0 && $sellerAverageRating !== null)<div class="sari-bm-store-rating" aria-label="{{ number_format($sellerAverageRating, 1) }} out of 5 based on {{ number_format($sellerReviewCount) }} product reviews"><span class="sari-bm-store-rating-star" aria-hidden="true">★</span><strong class="sari-bm-store-rating-value">{{ number_format($sellerAverageRating, 1) }}</strong><span class="sari-bm-store-rating-outof">/ 5</span></div><p>{{ number_format($sellerReviewCount) }} {{ $sellerReviewCount === 1 ? 'verified product review' : 'verified product reviews' }}</p>@else<p class="sari-bm-store-rating-empty">Not rated yet</p><p>Ratings appear after buyers review products.</p>@endif</div></details>
                        <details class="sari-bm-info-accordion"><summary><span class="sari-bm-info-summary"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h3l1.1 4-2 1.9a15.5 15.5 0 0 0 6 6l1.9-2L21 14v3a4 4 0 0 1-4 4C9.3 21 3 14.7 3 7a4 4 0 0 1 4-4Z"/></svg><span>Store contact number</span></span><span class="sari-bm-info-chevron" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span></summary><div class="sari-bm-info-body">@if ($storePhone !== '' && $storeDialNumber !== '')<strong>{{ $storePhone }}</strong><p>Contact number published by this store.</p>@else<p>Not provided</p><p>You can contact this seller through the chat.</p>@endif</div></details>
                    </div>
                @else
                    <div class="sari-bm-context-panel is-help" style="margin-top:0">
                        <h4>Private buyer–seller chat</h4>
                        <p>Select a conversation to contact a seller about products, orders, or deliveries.</p>
                    </div>
                @endif
            </div>
        </aside>
    </section>
</div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/sari-buyer-admin-support.js') }}?v=20261029-hide-report-1009b07e" defer></script>
<script src="{{ asset('js/sari-order-confirmation-card.js') }}?v=20261026-1"></script>
<script src="{{ asset('js/sari-buyer-seller-messages.js') }}?v=20261029-unified-toast"></script>
<script>
/* Buyer seller-info drawer: no anchor hashes, reloads, or backend requests. */
(function () {
    if (window.SariBuyerSellerInfoDrawer) {
        window.SariBuyerSellerInfoDrawer.init();
        return;
    }

    function init() {
        const root = document.getElementById('sariBuyerSellerChat');
        if (!root || root.dataset.infoDrawerReady === '1') return;

        const trigger = root.querySelector('[data-sari-info-open]');
        const panel = root.querySelector('#sariBmSellerDetails');
        const dismiss = root.querySelector('[data-sari-info-close]');
        const backdrop = root.querySelector('[data-sari-info-backdrop]');
        if (!trigger || !panel || !dismiss || !backdrop) return;

        root.dataset.infoDrawerReady = '1';
        const controller = new AbortController();
        const signal = controller.signal;
        let isOpen = false;

        function show(visible, restoreFocus = false) {
            if (isOpen === visible) return;
            isOpen = visible;
            panel.classList.toggle('is-open', visible);
            backdrop.classList.toggle('is-open', visible);
            panel.inert = !visible;
            panel.setAttribute('aria-hidden', String(!visible));
            trigger.setAttribute('aria-expanded', String(visible));
            trigger.setAttribute('aria-label', visible ? 'Close seller information' : 'Open seller information');
            if (visible) {
                dismiss.focus({preventScroll: true});
            } else if (restoreFocus) {
                trigger.focus({preventScroll: true});
            }
        }

        trigger.addEventListener('click', function () {
            show(!isOpen, isOpen);
        }, {signal});
        dismiss.addEventListener('click', function () {
            show(false, true);
        }, {signal});
        backdrop.addEventListener('click', function () {
            show(false, true);
        }, {signal});

        root.addEventListener('keydown', function (event) {
            if (!isOpen) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                show(false, true);
                return;
            }
            if (event.key !== 'Tab') return;
            const focusable = Array.from(panel.querySelectorAll('a[href],button:not([disabled]),summary,[tabindex]:not([tabindex="-1"])'))
                .filter(function (item) { return !item.closest('details:not([open])') && item.getClientRects().length > 0; });
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }, {signal});

        function cleanup() {
            controller.abort();
            root.dataset.infoDrawerReady = '0';
        }
        document.addEventListener('livewire:navigating', cleanup, {once: true, signal});
        window.addEventListener('pagehide', cleanup, {once: true, signal});
    }

    window.SariBuyerSellerInfoDrawer = {init: init};
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, {once: true});
    } else {
        init();
    }
    document.addEventListener('livewire:navigated', init);
})();
</script>
@endpush
