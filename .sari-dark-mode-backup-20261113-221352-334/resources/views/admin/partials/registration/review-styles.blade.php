<style>
/* Registration modal styles live here, independently of the page/table. */
body.reg-review-open { overflow: hidden; }
#reviewBackdrop.registration-modal-backdrop { position: fixed; inset: 0; z-index: 1000; background: rgb(17 24 39 / 43%); opacity: 0; visibility: hidden; pointer-events: none; }
#reviewBackdrop.registration-modal-backdrop.is-open { opacity: 1; visibility: visible; pointer-events: auto; }
#reviewDrawer.registration-modal { position: fixed; inset: 50% auto auto 50%; z-index: 1010; width: min(780px, calc(100vw - 40px)); height: min(650px, calc(100dvh - 32px)); max-height: calc(100dvh - 32px); transform: translate(-50%, -50%); border: 1px solid #e8e9ed; border-radius: 18px; background: #fff; color: #202124; font: 13px/1.45 'Poppins', system-ui, sans-serif; box-shadow: 0 24px 80px rgb(15 23 42 / 18%); overflow: hidden; visibility: hidden; opacity: 0; pointer-events: none; }
#reviewDrawer.registration-modal.is-open { visibility: visible; opacity: 1; pointer-events: auto; }
.registration-modal *, .registration-modal *::before, .registration-modal *::after { box-sizing: border-box; }
.registration-modal [hidden] { display: none !important; }
.registration-modal button, .registration-modal textarea { font: inherit; }
.registration-modal button { cursor: pointer; }
.registration-modal-detail { height: 100%; min-height: 0; display: flex; flex-direction: column; }
.registration-modal-header { flex: none; display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 18px 24px 14px; }
.registration-modal-header h2 { margin: 0; font-size: 21px; font-weight: 600; letter-spacing: -.5px; line-height: 1.3; }
.registration-modal-header p { margin: 4px 0 0; color: #73757e; font-size: 11px; }
.registration-modal-close { display: grid; place-items: center; flex: none; width: 32px; height: 32px; border: 0; border-radius: 10px; background: #f5f6f8; color: #62656f; }
.registration-modal-close svg { width: 20px; height: 20px; }
.registration-modal-close:hover { background: #eceef2; }
.registration-modal-tabs { flex: none; display: flex; gap: 22px; padding: 0 24px; border-bottom: 1px solid #eceef2; }
.registration-modal-tabs button { min-height: 38px; padding: 0 0 9px; border: 0; border-bottom: 2px solid transparent; background: none; color: #73757e; font-size: 12px; font-weight: 500; white-space: nowrap; }
.registration-modal-tabs button.is-active { border-bottom-color: #bc830f; color: #986a0b; }
.registration-modal-body { flex: 1; min-height: 0; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: none; -ms-overflow-style: none; overflow-x: hidden; padding: 17px 24px 20px; }
.registration-modal-summary { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
.registration-modal-summary > p { margin: 0; font-size: 16px; font-weight: 600; overflow-wrap: anywhere; }
.registration-modal-status { display: inline-flex; flex: none; align-items: center; padding: 4px 9px; border-radius: 6px; background: #fff5dd; color: #90670a; font-size: 11px; font-weight: 500; }
.registration-modal-status[data-status="approved"] { background: #edf7ef; color: #32704a; }
.registration-modal-status[data-status="rejected"] { background: #fff0ef; color: #a84943; }
.registration-modal-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: 18px; row-gap: 11px; margin: 0; }
.registration-modal-field { min-width: 0; }
.registration-modal-field--wide { grid-column: 1 / -1; }
.registration-modal-field dt { margin: 0 0 5px; color: #6c707a; font-size: 11px; font-weight: 400; }
.registration-modal-field dd { margin: 0; min-height: 36px; padding: 8px 11px; border: 1px solid #e6e8ed; border-radius: 7px; background: #fafbfc; font-size: 13px; line-height: 19px; overflow-wrap: anywhere; }
.registration-modal-section-title { margin: 18px 0 11px; font-size: 13px; font-weight: 600; }
.registration-modal-section-title--first { margin-top: 0; margin-bottom: 7px; }
.registration-modal-copy { margin: 0 0 14px; color: #747680; font-size: 13px; line-height: 1.7; }
.registration-modal-note { margin: 12px 0 0; padding: 11px 12px; border: 1px solid #e3e5eb; border-radius: 8px; color: #555861; font-size: 13px; white-space: pre-wrap; overflow-wrap: anywhere; }
.registration-modal-documents { display: grid; gap: 9px; }
.registration-modal-document { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 12px 14px; border: 1px solid #e6e8ed; border-radius: 9px; }
.registration-modal-document h4 { margin: 0; font-size: 13px; font-weight: 500; }
.registration-modal-document p { margin: 4px 0 0; color: #7b7e87; font-size: 11px; }
.registration-modal-missing { font-size: 12px; color: #b5554f; }
.registration-modal-button { display: inline-flex; align-items: center; justify-content: center; gap: 9px; min-height: 36px; padding: 7px 13px; border: 1px solid transparent; border-radius: 8px; font-size: 12px; font-weight: 500; text-decoration: none; line-height: 20px; }
.registration-modal-button--secondary { border-color: #dfe1e8; background: #fff; color: #555861; }
.registration-modal-button--secondary:hover { background: #f7f8fa; }
.registration-modal-button--primary { border-color: #b47d12; background: #b47d12; color: #fff; }
.registration-modal-button--primary:hover { background: #99690b; }
.registration-modal-footer { flex: none; display: flex; align-items: center; justify-content: flex-end; gap: 9px; padding: 12px 24px; border-top: 1px solid #eceef2; background: #fff; }
.registration-modal-footer-note { font-size: 12px; color: #858892; }
.registration-modal-warning { padding: 13px 15px; margin: 0 0 18px; border: 1px solid #efdfb6; border-radius: 10px; color: #8c6c26; background: #fffaf0; font-size: 12px; }
.registration-modal-decisions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
.registration-modal-decisions form { display: flex; flex-direction: column; min-width: 0; padding: 18px; border: 1px solid #e4e7ec; border-radius: 13px; background: #fff; box-shadow: 0 2px 5px rgb(16 24 40 / 3%); }
.registration-modal-decisions h4 { margin: 0; color: #292d35; font-size: 13px; font-weight: 600; line-height: 1.4; }
.registration-modal-decisions label { display: block; margin-bottom: 7px; font-size: 12px; color: #62646e; }
.registration-modal-decisions label span { color: #8b8e97; }
.registration-modal-decisions textarea { display: block; width: 100%; min-height: 112px; padding: 11px 12px; border: 1px solid #dfe1e8; border-radius: 10px; font-size: 13px; line-height: 1.7; color: #33363e; background: #fff; resize: vertical; }
.registration-modal-decisions textarea::placeholder { color: #92959e; }
.registration-modal-decisions .registration-modal-button { margin-top: 16px; width: 100%; min-height: 39px; font-size: 12px; font-weight: 600; transition: background-color .15s ease, border-color .15s ease; }
.registration-modal-button--approve { border-color: #35704f; background: #35704f; color: #fff; }
.registration-modal-button--approve:hover { border-color: #285c3e; background: #285c3e; }
.registration-modal-button--decline { border-color: #b74444; background: #b74444; color: #fff; }
.registration-modal-button--decline:hover { border-color: #9d3535; background: #9d3535; }
.registration-modal :is(button, a, textarea):focus-visible { outline: 3px solid rgb(64 140 255 / 28%); outline-offset: 3px; }
.registration-modal textarea:focus { border-color: #428eff; outline: 3px solid rgb(64 140 255 / 13%); outline-offset: 0; }
/* Short windows retain readable type and allow the body to scroll. */
@media (max-height: 650px) and (min-width: 601px) {
    .registration-modal-header { padding-top: 14px; padding-bottom: 10px; }
    .registration-modal-body { padding-top: 13px; padding-bottom: 15px; }
    .registration-modal-summary { margin-bottom: 12px; }
}
@media (max-width: 600px) {
    #reviewDrawer.registration-modal { width: calc(100vw - 24px); height: calc(100dvh - 24px); max-height: calc(100dvh - 24px); border-radius: 18px; }
    .registration-modal-header { padding: 16px 18px 12px; }
    .registration-modal-header h2 { font-size: 19px; }
    .registration-modal-tabs { padding: 0 18px; gap: 18px; }
    .registration-modal-tabs button { font-size: 12px; }
    .registration-modal-body { padding: 16px 18px; }
    .registration-modal-fields, .registration-modal-decisions { grid-template-columns: minmax(0, 1fr); }
    .registration-modal-summary { align-items: flex-start; }
    .registration-modal-summary > p { font-size: 15px; }
    .registration-modal-footer { padding: 12px 18px; }
    .registration-modal-document { flex-wrap: wrap; }
}
/* Keep wheel/touch/keyboard scrolling available without a visible track. */
.registration-modal-body::-webkit-scrollbar { display: none; width: 0; height: 0; }
.registration-modal-identity { display: flex; align-items: center; gap: 12px; min-width: 0; }
.registration-modal-identity p { margin: 0; font-size: 15px; font-weight: 600; overflow-wrap: anywhere; }
.registration-modal-identity-caption { display: block; margin-top: 2px; color: #858892; font-size: 11px; }
.registration-modal-avatar { position: relative; display: grid; place-items: center; flex: none; width: 56px; height: 56px; border: 1px solid #e5e7eb; border-radius: 50%; overflow: hidden; background: #f4f5f7; color: #6b7079; font-size: 14px; font-weight: 600; }
.registration-modal-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; background: #f4f5f7; }
.registration-modal-documents { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.registration-modal-document { display: flex; flex-direction: column; align-items: stretch; gap: 9px; min-width: 0; padding: 12px; }
.registration-modal-document-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
.registration-modal-document h4 { font-size: 12px; }
.registration-modal-file-status { color: #397353; font-size: 10px; white-space: nowrap; }
.registration-modal-preview { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; height: 122px; overflow: hidden; border: 1px solid #eceef2; border-radius: 7px; background: #f7f8fa; text-decoration: none; color: #737782; }
.registration-modal-preview img { width: 100%; height: 100%; object-fit: contain; }
.registration-modal-preview--pdf span { font-size: 13px; font-weight: 500; }
.registration-modal-preview--pdf p { margin: 0; padding: 0 12px; text-align: center; font-size: 10px; }
.registration-modal-preview--empty { font-size: 12px; }
.registration-modal-file-link { color: #94690f; font-size: 11px; font-weight: 500; text-decoration: underline; text-underline-offset: 3px; }
@media (max-width: 600px) {
    .registration-modal-tabs { gap: 14px; flex-wrap: wrap; }
    .registration-modal-tabs button { font-size: 11px; }
    .registration-modal-documents { grid-template-columns: minmax(0, 1fr); }
}
.registration-decision-heading { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 20px; }
.registration-decision-heading p { margin: 5px 0 0; color: #828792; font-size: 11px; line-height: 1.6; }
.registration-decision-symbol { display: grid; place-items: center; flex: none; width: 32px; height: 32px; border-radius: 9px; }
.registration-decision-symbol svg { width: 19px; height: 19px; }
.registration-decision-symbol--approve { background: #eef6f0; color: #35704f; }
.registration-decision-symbol--decline { background: #fbefef; color: #b74444; }
.registration-modal-decisions label { font-size: 11px; font-weight: 500; color: #606773; }
.registration-modal-decisions label span { font-weight: 400; }
.registration-modal-decisions textarea { border-color: #e1e5eb; border-radius: 9px; font-size: 12px; }
.registration-modal-button:disabled { opacity: .6; cursor: wait; }
@media (prefers-reduced-motion: reduce) {
    .registration-modal-decisions .registration-modal-button { transition: none; }
}
</style>
