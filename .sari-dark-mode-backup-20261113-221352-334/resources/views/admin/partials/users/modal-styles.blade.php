<style>
/* Account modal: compact, neutral surface shared with registration review. */
#profileBackdrop { background: rgb(15 23 42 / 35%) !important; backdrop-filter: blur(2px); }
#profileDrawer { inset: 50% auto auto 50% !important; transform: translate(-50%, -50%) !important; width: min(780px, calc(100vw - 28px)) !important; height: min(690px, calc(100dvh - 36px)) !important; max-height: calc(100dvh - 36px) !important; border: 1px solid #e4e7ec !important; border-radius: 18px !important; background: #fff !important; box-shadow: 0 24px 80px rgb(16 24 40 / 20%) !important; color: #344054; }
#profileDrawer:not(.is-open), #profileBackdrop:not(.is-open) { visibility: hidden !important; pointer-events: none !important; }
#profileDrawer.is-open, #profileBackdrop.is-open { visibility: visible !important; opacity: 1 !important; pointer-events: auto !important; }
#profileDrawer .enterprise-user-modal { height: 100% !important; max-height: 100% !important; display: flex; flex-direction: column; min-height: 0; }
#profileDrawer .enterprise-user-modal-header { padding: 20px 24px !important; background: #fff !important; border-bottom: 1px solid #edf0f3 !important; flex-shrink: 0; }
#profileDrawer .enterprise-user-modal-eyebrow, #profileDrawer .enterprise-section-eyebrow { color: #7b8493 !important; font-size: 10px !important; font-weight: 600 !important; letter-spacing: .09em !important; }
#profileDrawer #drawerName { color: #1d2939 !important; font-size: 18px !important; font-weight: 600 !important; line-height: 1.4 !important; }
#profileDrawer .enterprise-user-modal-context { display: none !important; }
#profileDrawer .enterprise-user-modal-header-icon { width: 38px !important; height: 38px !important; background: #f3f5f7 !important; border: 1px solid #e9edf1 !important; color: #667085 !important; }
#profileDrawer .enterprise-modal-close { width: 32px !important; height: 32px !important; background: #fff !important; border: 1px solid #e4e7ec !important; color: #667085 !important; border-radius: 8px !important; }
#profileDrawer .enterprise-user-modal-tabs { padding: 0 24px !important; gap: 22px !important; background: #fff !important; border-bottom: 1px solid #edf0f3 !important; flex-shrink: 0; }
#profileDrawer .drawer-tab { padding: 14px 0 !important; border-radius: 0 !important; border: 0 !important; border-bottom: 2px solid transparent !important; font-size: 12px !important; color: #7b8493 !important; background: transparent !important; box-shadow: none !important; white-space: normal; }
#profileDrawer .drawer-tab.is-active { color: #283647 !important; border-bottom-color: #283647 !important; }
#profileDrawer .drawer-tab svg { width: 15px !important; height: 15px !important; }
#profileDrawer .enterprise-user-modal-scroll { padding: 20px 24px !important; min-height: 0; max-height: none !important; overflow-y: auto !important; overflow-x: hidden !important; scrollbar-width: none; background: #fff !important; }
#profileDrawer .enterprise-user-modal-scroll::-webkit-scrollbar { display: none; }
#profileDrawer .enterprise-user-panel { padding: 0 !important; }
#profileDrawer .enterprise-overview-grid { display: grid !important; grid-template-columns: 1fr !important; gap: 16px !important; }
#profileDrawer .enterprise-manage-grid { display: grid !important; grid-template-columns: minmax(0,1fr) minmax(0,1fr) !important; gap: 16px !important; }
#profileDrawer .enterprise-card { padding: 18px !important; background: #fff !important; border: 1px solid #e7ebef !important; border-radius: 12px !important; box-shadow: none !important; min-width: 0; }
#profileDrawer .enterprise-section-heading h4, #profileDrawer .enterprise-access-card h4 { color: #344054 !important; font-size: 13px !important; font-weight: 600 !important; }
#profileDrawer .enterprise-section-copy { font-size: 11px !important; line-height: 1.7 !important; color: #7b8493 !important; }
#profileDrawer .enterprise-profile-hero { margin-top: 16px !important; gap: 18px !important; align-items: center !important; }
#profileDrawer #drawerInitials { position: relative !important; display: grid !important; place-items: center !important; width: 88px !important; height: 88px !important; border-radius: 14px !important; background: #f3f5f7 !important; border: 1px solid #e4e7ec !important; color: #7b8493 !important; font-size: 24px !important; overflow: hidden !important; }
#profileDrawer #drawerInitials img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
#profileDrawer #drawerInitials img[hidden] { display: none !important; }
#profileDrawer .user-photo-block { display: grid; gap: 6px; justify-items: center; flex-shrink: 0; }
#profileDrawer .user-photo-state { max-width: 96px; font-size: 10px; line-height: 1.5; text-align: center; color: #7b8493; }
#profileDrawer .user-photo-state:empty { display: none; }
#profileDrawer .enterprise-contact-row { font-size: 12px !important; line-height: 1.6 !important; color: #586476 !important; }
#profileDrawer .enterprise-contact-icon { background: #f6f8fa !important; color: #7b8493 !important; }
#profileDrawer .enterprise-meta-tile { padding: 12px !important; background: #f8f9fb !important; border: 0 !important; border-radius: 8px !important; }
#profileDrawer .enterprise-meta-tile p { color: #8a93a1 !important; font-size: 10px !important; }
#profileDrawer .enterprise-meta-tile strong { font-size: 12px !important; line-height: 1.6 !important; }
#profileDrawer .enterprise-quick-actions { display: grid !important; grid-template-columns: 1fr 1fr; gap: 12px !important; }
#profileDrawer .enterprise-quick-action { padding: 12px !important; background: #f8f9fb !important; border: 1px solid #edf0f3 !important; color: #536175 !important; border-radius: 9px !important; }
#profileDrawer .enterprise-quick-action strong { font-size: 12px !important; color: #344054 !important; }
#profileDrawer .enterprise-quick-action small { font-size: 10px !important; line-height: 1.6 !important; color: #7b8493 !important; }
#profileDrawer .enterprise-quick-action-icon { background: #eef1f5 !important; color: #667085 !important; }
#profileDrawer .enterprise-quick-action-arrow { display: none !important; }
#profileDrawer .enterprise-field-label { font-size: 11px !important; color: #667085 !important; }
#profileDrawer :is(.enterprise-field-control,.enterprise-field-textarea) { padding: 9px 11px !important; font-size: 12px !important; border: 1px solid #dfe4ea !important; border-radius: 8px !important; background: #fff !important; box-shadow: none !important; }
#profileDrawer .enterprise-button { display: inline-flex !important; justify-content: center !important; align-items: center !important; gap: 7px; min-height: 36px !important; padding: 9px 14px !important; font-size: 12px !important; line-height: 1.4 !important; font-weight: 500 !important; border-radius: 8px !important; box-shadow: none !important; transform: none !important; }
#profileDrawer .enterprise-button--primary { background: #29394d !important; border: 1px solid #29394d !important; color: #fff !important; }
#profileDrawer .enterprise-button--secondary { background: #fff !important; border: 1px solid #dfe4ea !important; color: #586476 !important; }
#profileDrawer .enterprise-button--danger { background: #b74444 !important; border: 1px solid #b74444 !important; color: #fff !important; }
#profileDrawer .enterprise-button--success { background: #35704f !important; border: 1px solid #35704f !important; color: #fff !important; }
#profileDrawer .enterprise-button svg { width: 15px !important; height: 15px !important; }
#profileDrawer .enterprise-full-profile-card { padding: 18px !important; }
#profileDrawer .enterprise-profile-sections { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 16px; }
#profileDrawer .enterprise-detail-section { min-width: 0; overflow: hidden; border: 1px solid #e7ebef; border-radius: 10px; background: #fbfcfd; }
#profileDrawer .enterprise-detail-section-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 12px 14px; border-bottom: 1px solid #edf0f3; }
#profileDrawer .enterprise-detail-section-heading p { margin: 0; color: #344054; font-size: 11px; font-weight: 650; }
#profileDrawer .enterprise-detail-section-heading span { display: block; margin-top: 2px; color: #8a93a1; font-size: 9px; line-height: 1.5; }
#profileDrawer .enterprise-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
#profileDrawer .enterprise-detail-item { min-width: 0; padding: 11px 14px; border-top: 1px solid #f0f2f5; }
#profileDrawer .enterprise-detail-item:nth-child(-n+2) { border-top: 0; }
#profileDrawer .enterprise-detail-item:nth-child(odd) { border-right: 1px solid #f0f2f5; }
#profileDrawer .enterprise-detail-label { margin: 0; color: #98a2b3; font-size: 9px; font-weight: 600; letter-spacing: .02em; }
#profileDrawer .enterprise-detail-value { margin-top: 4px; color: #475467; font-size: 11px; font-weight: 550; line-height: 1.55; overflow-wrap: anywhere; white-space: pre-wrap; }
#profileDrawer .enterprise-detail-value.is-empty { color: #b0b7c2; font-weight: 450; }
#profileDrawer .enterprise-documents-wrap { margin-top: 14px; overflow: hidden; border: 1px solid #e7ebef; border-radius: 10px; background: #fbfcfd; }
#profileDrawer .enterprise-document-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; padding: 12px; }
#profileDrawer .enterprise-document-link { display: flex; align-items: center; justify-content: space-between; gap: 10px; min-width: 0; padding: 10px 12px; border: 1px solid #e2e7ec; border-radius: 8px; background: #fff; color: #475467; font-size: 10px; font-weight: 600; transition: border-color .15s ease, background .15s ease; }
#profileDrawer .enterprise-document-link:hover { border-color: #cbd3dc; background: #f8fafc; }
#profileDrawer .enterprise-document-link span { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
#profileDrawer .enterprise-document-link svg { width: 14px; height: 14px; flex-shrink: 0; color: #667085; }
#profileDrawer .user-modal-footer { display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding: 14px 24px; border-top: 1px solid #edf0f3; background: #fff; flex-shrink: 0; }
#profileDrawer :is(button,a,input,textarea):focus-visible { outline: 2px solid #667a94 !important; outline-offset: 3px; }
@media(max-width: 600px) { #profileDrawer .enterprise-manage-grid, #profileDrawer .enterprise-quick-actions, #profileDrawer .enterprise-profile-sections, #profileDrawer .enterprise-document-grid { grid-template-columns: 1fr !important; } #profileDrawer .enterprise-detail-grid { grid-template-columns: 1fr !important; } #profileDrawer .enterprise-detail-item { border-right: 0 !important; border-top: 1px solid #f0f2f5 !important; } #profileDrawer .enterprise-detail-item:first-child { border-top: 0 !important; } #profileDrawer .enterprise-user-modal-header, #profileDrawer .enterprise-user-modal-scroll { padding: 16px !important; } #profileDrawer .enterprise-user-modal-tabs { padding: 0 16px !important; gap: 14px !important; } #profileDrawer .drawer-tab { font-size: 11px !important; } #profileDrawer .drawer-tab svg { display: none; } }
@media(prefers-reduced-motion: reduce) { #profileDrawer, #profileBackdrop { transition: none !important; } }


/* Account review refinement. Scoped to this modal; page/table styles stay intact. */
#profileDrawer { width: min(1040px, calc(100vw - 40px)) !important; max-width: 1040px !important; height: min(820px, calc(100dvh - 40px)) !important; max-height: calc(100dvh - 40px) !important; border-radius: 20px !important; color: #272a30; }
#profileDrawer .enterprise-user-modal { max-height: 100% !important; background: #fff !important; }
#profileDrawer .enterprise-user-modal-header { display: flex !important; align-items: center !important; justify-content: space-between !important; gap: 16px; padding: 22px 28px !important; }
#profileDrawer .enterprise-user-modal-header-icon { display: grid; place-items: center; border-radius: 12px; color: #a36c08 !important; background: #fff !important; }
#profileDrawer .enterprise-user-modal-header-icon svg { width: 21px; height: 21px; }
#profileDrawer .enterprise-user-modal-eyebrow { text-transform: uppercase; font-size: 10px !important; letter-spacing: .12em !important; color: #777d87 !important; }
#profileDrawer #drawerName { max-width: min(720px, 65vw); font-size: 21px !important; font-weight: 650 !important; letter-spacing: -.025em; }
#profileDrawer .enterprise-modal-close { display: grid; place-items: center; flex-shrink: 0; width: 36px !important; height: 36px !important; }
#profileDrawer .enterprise-modal-close svg { width: 18px; height: 18px; }
#profileDrawer .enterprise-user-modal-tabs { display: flex !important; padding: 0 28px !important; gap: 28px !important; overflow-x: auto; }
#profileDrawer .drawer-tab { display: inline-flex !important; align-items: center; gap: 8px; padding: 15px 0 !important; font-size: 13px !important; font-weight: 600; white-space: nowrap; }
#profileDrawer .drawer-tab.is-active { color: #916008 !important; border-bottom-color: #bd861a !important; }
#profileDrawer .enterprise-user-modal-scroll { padding: 24px 28px !important; background: #f6f7f9 !important; scrollbar-width: thin; scrollbar-color: #cbd0d8 transparent; overscroll-behavior: contain; }
#profileDrawer .enterprise-user-modal-scroll::-webkit-scrollbar { display: block; width: 6px; }
#profileDrawer .enterprise-user-modal-scroll::-webkit-scrollbar-thumb { background: #cbd0d8; border-radius: 6px; }
#profileDrawer .enterprise-overview-grid { grid-template-columns: minmax(0, 1.6fr) minmax(240px, 1fr) !important; align-items: stretch; gap: 16px !important; }
#profileDrawer .enterprise-card { padding: 22px !important; border-color: #e3e6eb !important; border-radius: 14px !important; box-shadow: 0 2px 5px rgb(16 24 40 / 2%) !important; }
#profileDrawer .enterprise-section-eyebrow { font-size: 10px !important; text-transform: uppercase; letter-spacing: .09em !important; color: #7a818d !important; }
#profileDrawer .enterprise-section-heading h4, #profileDrawer .enterprise-access-card h4 { margin-top: 5px; color: #242830 !important; font-size: 15px !important; letter-spacing: -.015em; }
#profileDrawer .enterprise-section-copy { margin-top: 6px; color: #727985 !important; font-size: 12px !important; line-height: 1.65 !important; }
#profileDrawer .enterprise-profile-hero { display: flex !important; gap: 18px !important; margin-top: 20px !important; }
#profileDrawer #drawerInitials { width: 76px !important; height: 76px !important; flex: 0 0 76px; font-size: 23px !important; border-radius: 18px !important; background: #f3f4f6 !important; color: #59616e !important; }
#profileDrawer .enterprise-contact-list { display: grid; gap: 6px; margin-top: 12px; }
#profileDrawer .enterprise-contact-row { display: flex; align-items: center; gap: 8px; font-size: 12px !important; color: #4e5663 !important; }
#profileDrawer .enterprise-contact-icon { display: grid; place-items: center; width: 16px; height: 16px; flex: 0 0 16px; background: transparent !important; color: #8b929e !important; }
#profileDrawer .enterprise-contact-icon svg { width: 15px; height: 15px; }
#profileDrawer .enterprise-account-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 20px; padding-top: 16px; border-top: 1px solid #edf0f3; }
#profileDrawer .enterprise-meta-tile { padding: 0 !important; background: transparent !important; }
#profileDrawer .enterprise-meta-tile p { font-size: 11px !important; color: #858c98 !important; }
#profileDrawer .enterprise-meta-tile strong { display: block; margin-top: 5px; font-size: 12px !important; }
#profileDrawer #drawerStatusBadge, #profileDrawer #drawerRole { display: inline-flex; align-items: center; min-height: 25px; padding: 3px 9px; font-size: 11px !important; line-height: 1.5; font-weight: 600; border-radius: 6px; }
#profileDrawer #drawerRole { background: #f4f5f7 !important; border: 1px solid #e5e7eb !important; color: #59616e !important; }
#profileDrawer .enterprise-quick-actions { grid-template-columns: 1fr !important; gap: 10px !important; margin-top: 18px; }
#profileDrawer .enterprise-quick-action { display: flex; align-items: center; gap: 12px; text-align: left; padding: 13px 14px !important; background: #fff !important; border-color: #e3e6eb !important; transition: border-color .15s, background .15s; }
#profileDrawer .enterprise-quick-action:hover { border-color: #be8b2b !important; background: #f9fafb !important; }
#profileDrawer .enterprise-quick-action-icon { background: transparent !important; border: 0 !important; color: #ad7915 !important; width: 22px; height: 22px; flex-shrink: 0; }
#profileDrawer .enterprise-quick-action strong { display: block; font-size: 12px !important; }
#profileDrawer .enterprise-quick-action small { display: block; margin-top: 3px; font-size: 11px !important; }
#profileDrawer .enterprise-full-profile-card { margin-top: 20px !important; padding: 0 !important; overflow: hidden; }
#profileDrawer .enterprise-full-profile-card > .enterprise-section-heading { padding: 22px 24px; margin: 0; border-bottom: 1px solid #e8ebef; }
#profileDrawer .enterprise-section-icon { background: transparent !important; border: 0 !important; color: #9ca3af !important; }
#profileDrawer .enterprise-profile-sections { display: block; margin-top: 0; }
#profileDrawer .enterprise-detail-section { border: 0; border-radius: 0; background: #fff; }
#profileDrawer .enterprise-detail-section + .enterprise-detail-section { border-top: 1px solid #e3e6eb; }
#profileDrawer .enterprise-detail-section-heading { padding: 17px 24px 8px; border: 0; }
#profileDrawer .enterprise-detail-section-heading p { font-size: 13px; color: #303641; font-weight: 650; }
#profileDrawer .enterprise-detail-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0 24px; margin: 0; padding: 0 24px 16px; }
#profileDrawer .enterprise-detail-item { border: 0 !important; padding: 12px 0; min-width: 0; }
#profileDrawer .enterprise-detail-label { color: #7c8490; font-size: 11px; font-weight: 450; letter-spacing: 0; line-height: 1.5; }
#profileDrawer .enterprise-detail-value { margin: 5px 0 0; color: #303641; font-size: 13px; font-weight: 550; line-height: 1.6; overflow-wrap: anywhere; }
#profileDrawer .enterprise-detail-value.is-empty { color: #a4aab3; }
#profileDrawer .enterprise-documents-wrap { margin: 0; border: 0; border-top: 1px solid #e3e6eb; border-radius: 0; background: #fff; }
#profileDrawer .enterprise-document-grid { padding: 10px 24px 24px; }
#profileDrawer .enterprise-document-link { padding: 13px; font-size: 12px; }
#profileDrawer .enterprise-field-label { display: block; font-size: 12px !important; margin-bottom: 7px; }
#profileDrawer :is(.enterprise-field-control,.enterprise-field-textarea) { width: 100%; min-height: 42px; font-size: 13px !important; color: #272a30 !important; }
#profileDrawer .enterprise-form-grid { gap: 16px; margin-top: 20px; }
#profileDrawer .enterprise-button { min-height: 40px !important; font-size: 12px !important; font-weight: 600 !important; }
#profileDrawer .enterprise-button--primary { background: #bd861a !important; border-color: #bd861a !important; color: #fff !important; }
#profileDrawer .enterprise-button--primary:hover { background: #9d6c10 !important; border-color: #9d6c10 !important; }
#profileDrawer .enterprise-button--secondary:hover { background: #f3f4f6 !important; }
#profileDrawer .user-modal-footer { padding: 16px 28px; }
#profileDrawer :is(button,a,input,textarea):focus-visible { outline-color: #ad7915 !important; }
#profileDrawer [hidden] { display: none !important; }
#profileDrawer .enterprise-activity-timeline p { font-size: 12px !important; line-height: 1.6; }
#profileDrawer .enterprise-activity-timeline span { font-size: 11px !important; }
#profileDrawer .enterprise-activity-timeline .bg-\[\#fcfbf8\] { background: #fff !important; }
@media (max-width: 760px) {
 #profileDrawer { width: calc(100vw - 24px) !important; height: calc(100dvh - 24px) !important; max-height: calc(100dvh - 24px) !important; border-radius: 16px !important; }
 #profileDrawer .enterprise-overview-grid, #profileDrawer .enterprise-manage-grid { grid-template-columns: 1fr !important; }
 #profileDrawer .enterprise-user-modal-header, #profileDrawer .enterprise-user-modal-scroll { padding: 18px !important; }
 #profileDrawer .enterprise-user-modal-tabs { padding: 0 18px !important; gap: 22px !important; }
 #profileDrawer #drawerName { font-size: 18px !important; }
 #profileDrawer .enterprise-detail-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; gap: 0 18px; padding: 0 18px 14px; }
 #profileDrawer .enterprise-detail-section-heading, #profileDrawer .enterprise-full-profile-card > .enterprise-section-heading { padding-left: 18px; padding-right: 18px; }
 #profileDrawer .enterprise-detail-item { padding: 11px 0; }
 #profileDrawer .enterprise-detail-value { font-size: 12px; }
 #profileDrawer .user-modal-footer { padding: 14px 18px; }
}
@media (max-width: 380px) {
 #profileDrawer .enterprise-detail-grid { grid-template-columns: 1fr !important; }
 #profileDrawer .enterprise-user-modal-header-icon { display: none; }
 #profileDrawer .drawer-tab { font-size: 12px !important; }
 #profileDrawer .enterprise-user-modal-tabs { gap: 16px !important; }
}


/* Registration-review reference: a single white surface and boxed read-only fields. */
#profileBackdrop { background: rgb(20 25 34 / 44%) !important; backdrop-filter: none !important; }
#profileDrawer { width: min(980px, calc(100vw - 40px)) !important; max-width: 980px !important; height: min(820px, calc(100dvh - 40px)) !important; border: 1px solid #dfe2e8 !important; border-radius: 22px !important; box-shadow: 0 24px 80px rgb(16 24 40 / 20%) !important; }
#profileDrawer .enterprise-user-modal-header { padding: 26px 30px 24px !important; border-bottom: 0 !important; align-items: flex-start !important; }
#profileDrawer .enterprise-user-modal-header-icon { display: none !important; }
#profileDrawer .enterprise-user-modal-eyebrow { font-size: 12px !important; color: #828693 !important; letter-spacing: 0 !important; text-transform: none !important; }
#profileDrawer #drawerName { font-size: 25px !important; line-height: 1.3 !important; font-weight: 650 !important; color: #25262b !important; }
#profileDrawer .enterprise-user-modal-header .min-w-0 > .enterprise-user-modal-eyebrow { display: none; }
#profileDrawer .enterprise-user-modal-context { display: block !important; font-size: 13px !important; color: #808491 !important; }
#profileDrawer .enterprise-user-modal-header .mt-1 { margin-top: 0 !important; display: grid; gap: 7px; }
#profileDrawer .enterprise-modal-close { border: 0 !important; width: 40px !important; height: 40px !important; background: #f5f6f8 !important; border-radius: 14px !important; }
#profileDrawer .enterprise-user-modal-tabs { padding: 12px 30px 0 !important; gap: 28px !important; border-bottom: 1px solid #eceef2 !important; }
#profileDrawer .drawer-tab { font-size: 14px !important; padding: 16px 0 !important; color: #7a7e8b !important; }
#profileDrawer .drawer-tab svg { display: none !important; }
#profileDrawer .drawer-tab.is-active { color: #a66e00 !important; border-bottom-color: #ba820e !important; }
#profileDrawer .enterprise-user-modal-scroll { background: #fff !important; padding: 22px 30px 28px !important; }
#profileDrawer .account-review-summary { padding-bottom: 8px; }
#profileDrawer .account-review-identity { display: flex; align-items: center; gap: 16px; }
#profileDrawer #drawerInitials { width: 68px !important; height: 68px !important; flex: 0 0 68px; border-radius: 50% !important; border-color: #e4e6eb !important; font-size: 19px !important; }
#profileDrawer .account-review-identity-text { flex: 1; min-width: 0; }
#profileDrawer #drawerProfileName { font-size: 18px; line-height: 1.5; font-weight: 650; color: #25262b; overflow-wrap: anywhere; }
#profileDrawer #drawerRole { padding: 0; min-height: 0; margin-top: 3px; border: 0 !important; background: transparent !important; font-size: 13px !important; font-weight: 400; color: #858995 !important; }
#profileDrawer #drawerStatusBadge { flex-shrink: 0; font-size: 12px !important; padding: 5px 11px; border-radius: 7px; }
#profileDrawer .user-photo-state { max-width: 68px; font-size: 9px; }
#profileDrawer .account-review-meta { display: flex; flex-wrap: wrap; gap: 8px 18px; margin: 14px 0 22px; font-size: 11px; line-height: 1.6; color: #818693; }
#profileDrawer #drawerAccess { margin: 0 !important; font-size: 11px !important; }
#profileDrawer .account-review-contacts { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 22px; }
#profileDrawer .account-review-value { margin: 7px 0 0; padding: 11px 14px; min-height: 45px; border: 1px solid #e1e5ed; border-radius: 8px; background: #f8f9fb; color: #303139; font-size: 14px; line-height: 1.6; overflow-wrap: anywhere; }
#profileDrawer .enterprise-full-profile-card { margin-top: 18px !important; border: 0 !important; box-shadow: none !important; border-radius: 0 !important; }
#profileDrawer .enterprise-detail-section + .enterprise-detail-section { border-top: 0; margin-top: 14px; }
#profileDrawer .enterprise-detail-section-heading { padding: 10px 0 14px; }
#profileDrawer .enterprise-detail-section-heading p { font-size: 15px; color: #292b32; font-weight: 600; }
#profileDrawer .enterprise-detail-grid { grid-template-columns: repeat(2,minmax(0,1fr)) !important; gap: 16px 22px; padding: 0 0 14px !important; }
#profileDrawer .enterprise-detail-item { padding: 0 !important; }
#profileDrawer .enterprise-detail-label { font-size: 12px; color: #797f8c; font-weight: 400; }
#profileDrawer .enterprise-detail-value { min-height: 45px; padding: 11px 14px; margin-top: 7px; border: 1px solid #e1e5ed; border-radius: 8px; background: #f8f9fb; font-size: 14px; line-height: 1.6; font-weight: 400; color: #303139; }
#profileDrawer .enterprise-detail-value.is-empty { color: #a0a5af; }
#profileDrawer .enterprise-documents-wrap { margin-top: 16px; border-top: 1px solid #eceef2; }
#profileDrawer .enterprise-document-grid { padding: 0 0 14px; }
#profileDrawer .user-modal-footer { padding: 15px 30px; background: #fff; border-top: 1px solid #eceef2; }
#profileDrawer .enterprise-button { min-height: 43px !important; font-size: 13px !important; padding: 10px 17px !important; border-radius: 10px !important; }
#profileDrawer .enterprise-button--primary { background: #b68010 !important; border-color: #b68010 !important; }
#profileDrawer .enterprise-field-control, #profileDrawer .enterprise-field-textarea { background: #f8f9fb !important; font-size: 14px !important; }
@media (max-width: 600px) {
 #profileDrawer { width: calc(100vw - 20px) !important; height: calc(100dvh - 20px) !important; border-radius: 16px !important; }
 #profileDrawer .enterprise-user-modal-header { padding: 20px !important; }
 #profileDrawer #drawerName { font-size: 21px !important; }
 #profileDrawer .enterprise-user-modal-tabs { padding: 4px 20px 0 !important; gap: 20px !important; }
 #profileDrawer .drawer-tab { font-size: 12px !important; }
 #profileDrawer .enterprise-user-modal-scroll { padding: 20px !important; }
 #profileDrawer .enterprise-detail-grid, #profileDrawer .account-review-contacts { grid-template-columns: 1fr !important; gap: 14px; }
 #profileDrawer #drawerProfileName { font-size: 15px; }
 #profileDrawer .account-review-identity { gap: 12px; flex-wrap: wrap; }
 #profileDrawer .user-modal-footer { padding: 14px 20px; }
}


/* Plain activity rows and a focused administration layout. */
#profileDrawer .account-activity-section { padding: 0; background: transparent; border: 0; box-shadow: none; }
#profileDrawer .account-activity-section .enterprise-section-icon { display: none; }
#profileDrawer .account-activity-section .enterprise-section-heading { margin-bottom: 18px; }
#profileDrawer .account-activity-row { display: flex; align-items: flex-start; gap: 14px; padding: 18px 0; border-bottom: 1px solid #eceef2; background: transparent; }
#profileDrawer .account-activity-row:last-child { border-bottom: 0; }
#profileDrawer .account-activity-marker { display: grid; place-items: center; width: 36px; height: 36px; border-radius: 50%; background: #f4f5f7; color: #a87917; flex: 0 0 36px; }
#profileDrawer .account-activity-marker svg { width: 18px; height: 18px; }
#profileDrawer .account-activity-content { min-width: 0; flex: 1; }
#profileDrawer .account-activity-description { color: #303641; font-size: 13px !important; font-weight: 550; line-height: 1.6; overflow-wrap: anywhere; }
#profileDrawer .account-activity-meta { color: #858b97; font-size: 11px !important; margin-top: 5px; line-height: 1.6; text-transform: none; }
#profileDrawer .account-activity-relative { color: #858b97; font-size: 11px !important; padding-top: 3px; white-space: nowrap; }
#profileDrawer .account-activity-empty { color: #818895; padding: 28px 0; font-size: 13px; }
#profileDrawer .enterprise-manage-grid { display: flex !important; flex-direction: column; gap: 24px !important; }
#profileDrawer .enterprise-manage-grid > .space-y-3 { display: contents; }
#profileDrawer .enterprise-manage-grid .enterprise-card { border: 0 !important; border-radius: 0 !important; padding: 0 !important; margin: 0 !important; box-shadow: none !important; }
#profileDrawer #sellerMessageLink { order: 3; padding: 16px 0 !important; border-top: 1px solid #eceef2 !important; }
#profileDrawer #noteForm { order: 0; }
#profileDrawer #noteForm .enterprise-section-heading { margin-bottom: 18px; }
#profileDrawer #adminNoteInput { min-height: 110px; resize: vertical; }
#profileDrawer #noteForm > button { width: auto !important; margin-top: 12px; background: #b68010 !important; border-color: #b68010 !important; color: white !important; }
#profileDrawer .enterprise-manage-grid .enterprise-access-card { order: 1; display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 20px 24px; border-top: 1px solid #eceef2 !important; padding-top: 24px !important; }
#profileDrawer .enterprise-access-card > .flex { grid-column: 1 / -1; }
#profileDrawer .enterprise-access-icon { display: none; }
#profileDrawer .account-access-action { margin: 0 !important; padding: 0 !important; min-width: 0; }
#profileDrawer .enterprise-access-card h5 { color: #303641; font-size: 13px; line-height: 1.5; font-weight: 600; margin: 0; }
#profileDrawer .account-access-action .enterprise-section-copy { min-height: 38px; margin-bottom: 14px; }
#profileDrawer .account-access-action textarea { min-height: 92px; resize: vertical; }
#profileDrawer .enterprise-button--danger { color: #b34040 !important; background: #fff !important; border-color: #e5bcbc !important; }
#profileDrawer .enterprise-button--danger:hover { background: #fff5f5 !important; border-color: #c96b6b !important; }
#profileDrawer #restoreForm, #profileDrawer #unbanForm { grid-column: 1 / -1; margin-top: 0 !important; padding-top: 0 !important; border-top: 0 !important; }
#profileDrawer #restoreForm button, #profileDrawer #unbanForm button { width: auto !important; }
#profileDrawer .account-delete-action { grid-column: 1 / -1; display: flex; align-items: center; justify-content: space-between; gap: 20px; border-top: 1px solid #eceef2; padding-top: 20px; margin-top: 2px; }
#profileDrawer .account-delete-action button { flex-shrink: 0; width: auto !important; margin: 0 !important; }
#profileDrawer .account-delete-action h5 { color: #a33a3a; }
@media (max-width:600px) {
 #profileDrawer .enterprise-access-card { grid-template-columns: 1fr; }
 #profileDrawer .account-delete-action { align-items: flex-start; flex-direction: column; gap: 12px; }
 #profileDrawer .account-activity-row { gap: 10px; flex-wrap: wrap; }
 #profileDrawer .account-activity-relative { width: 100%; padding-left: 46px; margin-top: -8px; }
}


/* Compact review: four fields per row on desktop, short management forms. */
#profileDrawer .enterprise-user-modal-header { padding: 18px 24px 14px !important; }
#profileDrawer #drawerName { font-size: 21px !important; }
#profileDrawer .enterprise-user-modal-tabs { padding: 0 24px !important; }
#profileDrawer .drawer-tab { padding: 12px 0 !important; font-size: 13px !important; }
#profileDrawer .enterprise-user-modal-scroll { padding: 18px 24px !important; }
#profileDrawer .account-review-identity { gap: 12px; }
#profileDrawer #drawerInitials { width: 48px !important; height: 48px !important; flex-basis: 48px; font-size: 16px !important; }
#profileDrawer #drawerProfileName { font-size: 16px; }
#profileDrawer .account-review-meta { margin: 8px 0 12px; }
#profileDrawer .account-review-contacts { gap: 14px; }
#profileDrawer .account-review-value { min-height: 32px; padding: 6px 10px; margin-top: 4px; font-size: 12px; line-height: 1.5; }
#profileDrawer .enterprise-full-profile-card { margin-top: 10px !important; }
#profileDrawer .enterprise-detail-section + .enterprise-detail-section { margin-top: 6px; border-top: 1px solid #edf0f3; }
#profileDrawer .enterprise-detail-section-heading { padding: 12px 0 8px; }
#profileDrawer .enterprise-detail-section-heading p { font-size: 12px; font-weight: 650; }
#profileDrawer .enterprise-detail-grid { grid-template-columns: repeat(4,minmax(0,1fr)) !important; gap: 9px 14px; padding-bottom: 12px !important; }
#profileDrawer .enterprise-detail-label { font-size: 10px; line-height: 1.5; }
#profileDrawer .enterprise-detail-value { min-height: 31px; padding: 6px 9px; margin-top: 3px; font-size: 11px; line-height: 1.55; border-radius: 5px; }
#profileDrawer .enterprise-detail-item:has(.enterprise-detail-label) { align-self: start; }
#profileDrawer .enterprise-manage-grid { display: grid !important; grid-template-columns: minmax(220px,.8fr) minmax(0,1.6fr) !important; gap: 24px !important; align-items: start; }
#profileDrawer .enterprise-manage-grid #noteForm { border: 1px solid #e5e7eb !important; border-radius: 12px !important; padding: 18px !important; }
#profileDrawer .enterprise-manage-grid .enterprise-access-card { display: flex; flex-direction: column; gap: 0; border-top: 0 !important; border-left: 1px solid #e5e7eb !important; padding: 0 0 0 24px !important; }
#profileDrawer .enterprise-section-heading h4, #profileDrawer .enterprise-access-card h4 { margin: 0; font-size: 14px !important; }
#profileDrawer .enterprise-section-copy { font-size: 11px !important; margin-top: 4px; line-height: 1.6 !important; }
#profileDrawer #noteForm .enterprise-section-heading { margin-bottom: 14px; }
#profileDrawer .enterprise-field-label { font-size: 11px !important; margin-bottom: 6px; }
#profileDrawer #noteForm .enterprise-field-label { margin-top: 0 !important; }
#profileDrawer #adminNoteInput { min-height: 100px; }
#profileDrawer .enterprise-field-textarea { padding: 9px 11px !important; font-size: 12px !important; min-height: 68px; border-radius: 8px !important; }
#profileDrawer .account-access-action { display: grid; grid-template-columns: minmax(0,1fr) auto; gap: 0 14px; padding: 16px 0 !important; border-bottom: 1px solid #edf0f3; }
#profileDrawer .account-access-action h5, #profileDrawer .account-access-action > .enterprise-section-copy, #profileDrawer .account-access-action label { grid-column: 1 / -1; }
#profileDrawer .account-access-action .enterprise-section-copy { min-height: 0; margin-bottom: 10px; }
#profileDrawer .account-access-action textarea { min-width: 0; min-height: 68px; }
#profileDrawer .account-access-action > button { align-self: end; width: auto !important; max-width: 170px; min-height: 36px !important; font-size: 11px !important; margin: 0 !important; padding: 9px 12px !important; }
#profileDrawer .account-access-action > button svg { display: none; }
#profileDrawer .account-delete-action { padding-top: 16px; border-top: 0; gap: 14px; }
#profileDrawer .account-delete-action button { min-height: 36px !important; font-size: 11px !important; }
#profileDrawer #restoreForm, #profileDrawer #unbanForm { padding: 14px 0 !important; }
#profileDrawer #sellerMessageLink { grid-column: 1 / -1; }
#profileDrawer .user-modal-footer { padding: 12px 24px; }
#profileDrawer .account-activity-avatar { position: relative; width: 38px; height: 38px; flex: 0 0 38px; display: grid; place-items: center; overflow: hidden; border-radius: 50%; border: 1px solid #e4e7ec; background: #f3f4f6; color: #687387; font-size: 12px; font-weight: 600; }
#profileDrawer .account-activity-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
#profileDrawer .account-activity-user { display: flex; align-items: baseline; flex-wrap: wrap; gap: 5px 10px; margin-bottom: 4px; }
#profileDrawer .account-activity-user strong { font-size: 12px; color: #293241; }
#profileDrawer .account-activity-user span { font-size: 10px !important; color: #8b93a1; }
#profileDrawer .account-activity-row { padding: 14px 0; }
#profileDrawer .account-activity-description { font-size: 12px !important; font-weight: 400; }
@media (max-width:760px) {
 #profileDrawer .enterprise-detail-grid { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
 #profileDrawer .enterprise-manage-grid { grid-template-columns: 1fr !important; gap: 20px !important; }
 #profileDrawer .enterprise-manage-grid .enterprise-access-card { border-left: 0 !important; border-top: 1px solid #e5e7eb !important; padding: 18px 0 0 !important; }
}
@media (max-width:430px) {
 #profileDrawer .enterprise-user-modal-scroll { padding: 16px !important; }
 #profileDrawer .account-access-action { grid-template-columns: 1fr; }
 #profileDrawer .account-access-action > button { margin-top: 10px !important; max-width: none; }
 #profileDrawer .enterprise-detail-grid { gap: 8px 10px; }
}



/* Preserve the approved side-by-side layout; keep long forms inside the scroll area. */
#profileDrawer .enterprise-user-modal { height: 100% !important; min-height: 0 !important; overflow: hidden !important; }
#profileDrawer .enterprise-user-modal-header, #profileDrawer .enterprise-user-modal-tabs, #profileDrawer .user-modal-footer { flex: 0 0 auto !important; }
#profileDrawer .enterprise-user-modal-scroll { flex: 1 1 0% !important; min-height: 0 !important; height: auto !important; overflow-y: auto !important; overflow-x: hidden !important; scrollbar-gutter: stable; padding-bottom: 28px !important; scroll-padding-block: 18px 28px; }
#profileDrawer .enterprise-user-panel { min-width: 0; height: auto !important; max-height: none !important; overflow: visible !important; }
#profileDrawer .enterprise-manage-grid { display: grid !important; grid-template-columns: minmax(0,.8fr) minmax(0,1.6fr) !important; gap: 24px !important; align-items: start !important; min-width: 0; height: auto !important; max-height: none !important; overflow: visible !important; }
#profileDrawer .enterprise-manage-grid > .space-y-3 { min-width: 0; }
#profileDrawer .enterprise-manage-grid #noteForm { min-width: 0; overflow: visible !important; }
#profileDrawer .enterprise-manage-grid .enterprise-access-card { display: flex !important; flex-direction: column !important; min-width: 0 !important; width: 100% !important; max-height: none !important; height: auto !important; overflow: visible !important; padding: 0 0 0 24px !important; border: 0 !important; border-left: 1px solid #e5e7eb !important; }
#profileDrawer .account-access-action { grid-template-columns: minmax(0,1fr) minmax(140px, 170px) !important; gap: 0 14px !important; min-width: 0; padding: 12px 0 !important; }
#profileDrawer .account-access-action textarea { width: 100% !important; max-width: 100% !important; min-width: 0 !important; height: 60px; min-height: 60px !important; box-sizing: border-box; }
#profileDrawer .account-access-action .enterprise-section-copy { margin-bottom: 8px !important; }
#profileDrawer .account-access-action > button { width: 100% !important; max-width: none !important; min-width: 0 !important; min-height: 38px !important; font-size: 11px !important; line-height: 1.5 !important; white-space: normal !important; overflow-wrap: anywhere; }
#profileDrawer #suspendForm > button, #profileDrawer #banAccessButton, #profileDrawer #deleteAccountButton { background: #c73535 !important; border: 1px solid #c73535 !important; color: #fff !important; box-shadow: none !important; }
#profileDrawer #suspendForm > button:hover, #profileDrawer #banAccessButton:hover, #profileDrawer #deleteAccountButton:hover { background: #a92727 !important; border-color: #a92727 !important; }
#profileDrawer #suspendForm > button:focus-visible, #profileDrawer #banAccessButton:focus-visible, #profileDrawer #deleteAccountButton:focus-visible { outline: 2px solid #a92727 !important; outline-offset: 3px; }
#profileDrawer .account-delete-action { align-items: center !important; min-width: 0; gap: 14px !important; margin: 0 !important; padding: 14px 0 4px !important; }
#profileDrawer .account-delete-action > div { min-width: 0; flex: 1; }
#profileDrawer #deleteAccountButton { flex: 0 0 170px; width: 170px !important; min-height: 38px !important; font-size: 11px !important; }
#profileDrawer .enterprise-user-modal-context { border: 0 !important; background: transparent !important; border-radius: 0 !important; padding: 0 !important; }
@media (max-width:760px) {
 #profileDrawer .enterprise-manage-grid { grid-template-columns: minmax(0,1fr) !important; }
 #profileDrawer .enterprise-manage-grid .enterprise-access-card { padding: 18px 0 0 !important; border-left: 0 !important; border-top: 1px solid #e5e7eb !important; }
}
@media (max-width:430px) {
 #profileDrawer .account-access-action { grid-template-columns: minmax(0,1fr) !important; }
 #profileDrawer .account-access-action > button { width: auto !important; margin-top: 10px !important; }
 #profileDrawer .account-delete-action { align-items: stretch !important; flex-direction: column !important; }
 #profileDrawer #deleteAccountButton { flex-basis: auto; align-self: flex-end; }
}
</style>
