{{-- Edit Product modal — uses the existing Laravel endpoint and seller-products.js hooks. --}}
<style id="sariSellerEditProductPremiumUi">
@verbatim
/* Seller Edit Product — premium, clean, responsive; scoped to this modal only. */
#sellerEditProductModal.sari-ei-overlay{
  padding:16px!important;align-items:center!important;justify-content:center!important;
  background:rgba(17,24,39,.54)!important;backdrop-filter:blur(4px)!important;
}
#sellerEditProductModal .seller-edit-enterprise-dialog.sari-ei-shell{
  display:flex!important;flex-direction:column!important;overflow:hidden!important;
  width:min(1100px,calc(100vw - 32px))!important;max-width:1100px!important;
  max-height:calc(100dvh - 32px)!important;
  border:1px solid #e2e7ed!important;border-radius:20px!important;background:#fff!important;
  box-shadow:0 32px 84px rgba(15,23,42,.20),0 6px 18px rgba(15,23,42,.07)!important;
  font-family:Poppins,ui-sans-serif,system-ui,sans-serif!important;
}
#sellerEditProductModal .seller-edit-enterprise-header.sari-ei-header{
  display:flex!important;flex:0 0 auto!important;align-items:center!important;justify-content:space-between!important;
  gap:16px!important;min-height:80px!important;padding:19px 28px!important;
  background:#fff!important;border-bottom:1px solid #e8ecf0!important;
}
#sellerEditProductModal .sari-ei-eyebrow{margin:0!important;color:#a16b10!important;font-size:11px!important;font-weight:750!important;letter-spacing:.08em!important;text-transform:uppercase!important}
#sellerEditProductModal .seller-edit-enterprise-header.sari-ei-header h3{margin:4px 0 0!important;font-size:25px!important;line-height:1.2!important;font-weight:750!important;color:#252e3a!important;letter-spacing:-.025em!important}
#sellerEditProductModal .seller-edit-header-close.sari-ei-close{
  display:grid!important;place-items:center!important;flex:0 0 38px!important;width:38px!important;min-width:38px!important;height:38px!important;
  border:1px solid #e1e6ed!important;border-radius:10px!important;background:#fff!important;color:#607080!important;box-shadow:none!important;
}
#sellerEditProductModal .seller-edit-header-close.sari-ei-close:hover{background:#f7f9fb!important;color:#26303b!important;border-color:#cfd7df!important}
#sellerEditProductModal #sellerEditProductForm.seller-edit-enterprise-form.sari-ei-form{
  display:block!important;min-height:0!important;flex:1 1 auto!important;overflow-y:auto!important;overflow-x:hidden!important;
  padding:0 28px!important;background:#fff!important;scrollbar-width:thin!important;scrollbar-color:#ccd3dc transparent!important;
}
#sellerEditProductModal #sellerEditProductForm *{box-sizing:border-box}
#sellerEditProductModal .sari-ei-overview{
  display:grid!important;grid-template-columns:minmax(245px,310px) minmax(0,1fr)!important;
  align-items:start!important;gap:26px!important;padding:24px 0!important;
}
#sellerEditProductModal .sari-ei-media-panel{min-width:0!important}
#sellerEditProductModal .sari-ei-image-label{display:block!important;margin:0 0 10px!important;font-size:13px!important;color:#303a47!important;font-weight:700!important}
#sellerEditProductModal .sari-ei-image-frame{
  position:relative!important;display:grid!important;width:100%!important;min-height:280px!important;height:310px!important;
  overflow:hidden!important;place-items:center!important;background:#f5f7f9!important;border:1px solid #e2e7ed!important;
  border-radius:15px!important;
}
#sellerEditProductModal #sellerEditProductForm #editCurrentImageWrap.seller-edit-current-image{
  display:grid!important;width:100%!important;height:100%!important;min-width:0!important;min-height:0!important;
  padding:10px!important;border:0!important;border-radius:0!important;background:transparent!important;
  box-shadow:none!important;place-items:center!important;
}
#sellerEditProductModal #sellerEditProductForm #editCurrentImageWrap.hidden{display:none!important}
#sellerEditProductModal #sellerEditProductForm #editCurrentImage{
  display:block!important;max-width:100%!important;max-height:100%!important;
  width:100%!important;height:100%!important;object-fit:contain!important;object-position:center!important;
  border:0!important;border-radius:0!important;background:transparent!important;
}
#sellerEditProductModal .sari-ei-image-empty{display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;gap:8px!important;color:#8c97a5!important;text-align:center}
#sellerEditProductModal .sari-ei-image-empty svg{width:34px;height:34px}
#sellerEditProductModal .sari-ei-image-empty span{font-size:12px}
#sellerEditProductModal #editCurrentImageWrap:not(.hidden) ~ .sari-ei-image-empty{display:none!important}
#sellerEditProductModal .sari-ei-image-frame[data-orientation="portrait"]{height:360px!important}
#sellerEditProductModal .sari-ei-image-frame[data-orientation="landscape"]{height:265px!important}
#sellerEditProductModal .sari-ei-image-frame[data-orientation="square"]{height:310px!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-upload-box.sari-ei-upload{
  display:block!important;margin-top:12px!important;padding:0!important;min-height:0!important;border:0!important;
  border-radius:0!important;background:transparent!important;box-shadow:none!important;
}
#sellerEditProductModal #sellerEditProductForm #editProductImage{
  display:block!important;width:100%!important;max-width:100%!important;height:auto!important;min-height:44px!important;
  border:1px solid #dce3ea!important;border-radius:10px!important;padding:9px!important;background:#fff!important;
  font-family:inherit!important;font-size:12px!important;color:#4f5e6f!important;
}
#sellerEditProductModal #editProductImage::file-selector-button{border:0;border-radius:7px;background:#f8eed8;color:#97660f;padding:7px 10px;font-weight:700;margin-right:8px}
#sellerEditProductModal .sari-ei-file-hint{margin:7px 0 0!important;font-size:11px!important;color:#8994a1!important;line-height:1.4!important}
#sellerEditProductModal .sari-ei-fields-panel{min-width:0!important}
#sellerEditProductModal .sari-ei-section-name{margin:0 0 16px!important;font-size:16px!important;line-height:1.35!important;color:#2b3541!important;font-weight:750!important}
#sellerEditProductModal .sari-ei-section-name::before{content:"";display:inline-block;vertical-align:-2px;width:3px;height:16px;margin-right:9px;border-radius:3px;background:#c88912}
#sellerEditProductModal #sellerEditProductForm .seller-edit-grid.sari-ei-grid{
  display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;
  gap:16px 17px!important;width:100%!important;min-width:0!important;
}
#sellerEditProductModal #sellerEditProductForm .seller-edit-grid.sari-ei-grid>.seller-edit-field{grid-column:auto!important;min-width:0!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-grid.sari-ei-grid>.seller-edit-field--full{grid-column:1/-1!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-field>label:not(.seller-edit-shipping-card){
  display:block!important;margin:0 0 7px!important;color:#526173!important;
  font-size:12px!important;line-height:1.35!important;font-weight:650!important;
}
#sellerEditProductModal #sellerEditProductForm .seller-edit-field>label>span{color:#b13c3c!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-field input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]),
#sellerEditProductModal #sellerEditProductForm .seller-edit-field select,
#sellerEditProductModal #sellerEditProductForm .seller-edit-field textarea{
  width:100%!important;min-width:0!important;border:1px solid #dae1e8!important;border-radius:10px!important;
  background:#fff!important;color:#2e3b48!important;padding:0 13px!important;font-family:inherit!important;
  font-size:13px!important;font-weight:500!important;line-height:1.45!important;box-shadow:none!important;
}
#sellerEditProductModal #sellerEditProductForm .seller-edit-field input:not([type="hidden"]):not([type="checkbox"]):not([type="file"]),
#sellerEditProductModal #sellerEditProductForm .seller-edit-field select{height:45px!important;min-height:45px!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-field textarea{padding:12px 13px!important;min-height:140px!important;height:auto!important;resize:vertical!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-field input:focus,
#sellerEditProductModal #sellerEditProductForm .seller-edit-field select:focus,
#sellerEditProductModal #sellerEditProductForm .seller-edit-field textarea:focus{
  outline:none!important;border-color:#c88912!important;box-shadow:0 0 0 3px rgba(200,137,18,.10)!important;
}
#sellerEditProductModal #sellerEditProductForm .seller-edit-select-wrap>svg{width:15px!important;height:15px!important;right:12px!important;color:#788797!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-helper{margin:6px 0 0!important;font-size:11px!important;line-height:1.5!important;color:#7e8a98!important;max-width:100%!important}
#sellerEditProductModal .sari-ei-section{padding:23px 0!important;border-top:1px solid #e8ecf0!important;background:#fff!important}
#sellerEditProductModal .sari-ei-section-head{margin-bottom:15px!important}
#sellerEditProductModal .sari-ei-section-head .sari-ei-section-name{margin-bottom:0!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-shipping-card.sari-ei-shipping-link{
  display:flex!important;align-items:center!important;justify-content:space-between!important;gap:12px!important;
  min-height:54px!important;border:1px solid #e4e8ee!important;border-radius:10px!important;
  background:#f9fafc!important;padding:12px 14px!important;box-shadow:none!important;text-decoration:none!important;
}
#sellerEditProductModal .sari-ei-shipping-link strong{font-size:12px!important;color:#344151!important;font-weight:650!important}
#sellerEditProductModal .sari-ei-shipping-link .sari-ei-shipping-action{color:#a77218!important;font-size:12px!important;font-weight:700!important;white-space:nowrap!important}
#sellerEditProductModal .sari-ei-shipping-link:hover{background:#fffaf0!important;border-color:#e8d6b3!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-sensitive-note.sari-ei-notice{
  display:block!important;margin:0 0 21px!important;padding:10px 13px!important;border:1px solid #eee2ce!important;
  border-radius:10px!important;background:#fffdf8!important;color:#80694b!important;box-shadow:none!important;
}
#sellerEditProductModal .sari-ei-notice p{margin:0!important;font-size:11px!important;line-height:1.5!important;color:#80694b!important}
#sellerEditProductModal #sellerEditProductForm .seller-edit-footer.sari-ei-footer{
  position:sticky!important;z-index:5!important;bottom:0!important;display:flex!important;align-items:center!important;
  justify-content:flex-end!important;flex:0 0 auto!important;gap:10px!important;
  min-height:70px!important;margin:0 -28px!important;padding:12px 28px!important;
  border-top:1px solid #e8ecf0!important;background:#fff!important;backdrop-filter:none!important;
}
#sellerEditProductModal #sellerEditProductForm .sari-ei-footer .seller-edit-button{
  display:inline-flex!important;align-items:center!important;justify-content:center!important;
  width:auto!important;min-width:110px!important;height:43px!important;min-height:43px!important;
  border-radius:10px!important;padding:0 19px!important;font-family:inherit!important;
  font-size:13px!important;font-weight:700!important;line-height:1.2!important;box-shadow:none!important;
}
#sellerEditProductModal #sellerEditProductForm .sari-ei-footer .seller-edit-button--secondary{border:1px solid #dbe1e8!important;background:#fff!important;color:#516072!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-footer .seller-edit-button--primary{border:1px solid #c88912!important;background:#c88912!important;color:#fff!important;box-shadow:0 5px 13px rgba(200,137,18,.13)!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-footer .seller-edit-button--primary:hover{background:#b67c12!important;border-color:#b67c12!important}
#sellerEditProductModal :is(button,a,input,select,textarea):focus-visible{outline:2px solid #c88912!important;outline-offset:2px!important}
@media(max-width:900px){
  #sellerEditProductModal .seller-edit-enterprise-dialog.sari-ei-shell{width:calc(100vw - 24px)!important;max-width:calc(100vw - 24px)!important;max-height:calc(100dvh - 24px)!important}
  #sellerEditProductModal .sari-ei-overview{grid-template-columns:minmax(180px,260px) minmax(0,1fr)!important;gap:18px!important}
  #sellerEditProductModal .sari-ei-image-frame{height:275px!important;min-height:240px!important}
  #sellerEditProductModal .sari-ei-image-frame[data-orientation="portrait"]{height:310px!important}
}
@media(max-width:650px){
  #sellerEditProductModal.sari-ei-overlay{padding:7px!important}
  #sellerEditProductModal .seller-edit-enterprise-dialog.sari-ei-shell{width:100%!important;max-width:100%!important;max-height:calc(100dvh - 14px)!important;border-radius:15px!important}
  #sellerEditProductModal .seller-edit-enterprise-header.sari-ei-header{padding:15px 18px!important;min-height:70px!important}
  #sellerEditProductModal .seller-edit-enterprise-header.sari-ei-header h3{font-size:21px!important}
  #sellerEditProductModal #sellerEditProductForm.sari-ei-form{padding:0 18px!important}
  #sellerEditProductModal .sari-ei-overview{grid-template-columns:1fr!important;gap:20px!important;padding:18px 0!important}
  #sellerEditProductModal .sari-ei-image-frame{height:240px!important;min-height:215px!important;max-width:460px!important;margin-inline:auto!important}
  #sellerEditProductModal .sari-ei-image-frame[data-orientation="portrait"]{height:310px!important}
  #sellerEditProductModal .sari-ei-image-frame[data-orientation="landscape"]{height:230px!important}
  #sellerEditProductModal .sari-ei-section{padding:18px 0!important}
  #sellerEditProductModal #sellerEditProductForm .seller-edit-grid.sari-ei-grid{gap:13px!important}
  #sellerEditProductModal #sellerEditProductForm .seller-edit-footer.sari-ei-footer{margin:0 -18px!important;padding:12px 18px!important;min-height:66px!important}
}
@media(max-width:410px){
  #sellerEditProductModal #sellerEditProductForm .seller-edit-grid.sari-ei-grid{grid-template-columns:1fr!important}
  #sellerEditProductModal #sellerEditProductForm .seller-edit-grid.sari-ei-grid>.seller-edit-field{grid-column:1/-1!important}
  #sellerEditProductModal #sellerEditProductForm .sari-ei-footer .seller-edit-button{min-width:0!important;flex:1!important;padding-inline:12px!important}
}
@media(prefers-reduced-motion:reduce){#sellerEditProductModal *{transition:none!important;animation:none!important}}

/* Final clean pass: no vertical gold strokes or unnecessary text ornaments. */
#sellerEditProductModal .seller-edit-select-wrap{position:relative!important;display:block!important}
#sellerEditProductModal .seller-edit-select-wrap>select{width:100%!important;appearance:none!important;padding-right:39px!important}
#sellerEditProductModal .seller-edit-select-wrap>svg{position:absolute!important;right:12px!important;top:50%!important;transform:translateY(-50%)!important;pointer-events:none!important;width:16px!important;height:16px!important}
#sellerEditProductModal .sari-ei-section-name::before,
#sellerEditProductModal .seller-edit-enterprise-header h3::before,
#sellerEditProductModal .sari-ei-image-label::before {content:none!important;display:none!important}
#sellerEditProductModal .sari-ei-section-name{border-left:0!important;padding-left:0!important;margin-bottom:12px!important;font-size:17px!important;font-weight:750!important}
#sellerEditProductModal .sari-ei-overview{grid-template-columns:minmax(230px,330px) minmax(0,1fr)!important;gap:24px!important;padding-bottom:18px!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-section{border-top:0!important;border-bottom:0!important;padding:18px 0 20px!important}
#sellerEditProductModal .sari-ei-image-frame{height:300px!important;min-height:210px!important}
#sellerEditProductModal .sari-ei-image-frame[data-orientation="portrait"]{height:365px!important}
#sellerEditProductModal .sari-ei-image-frame[data-orientation="landscape"]{height:260px!important}
#sellerEditProductModal .sari-ei-image-frame img{object-fit:contain!important;object-position:center!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-grid>.seller-edit-field{min-width:0!important}
#sellerEditProductModal .sari-ei-muted{font-size:12px!important;color:#778492!important;line-height:1.5!important}
#sellerEditProductModal .sari-ei-media-title{margin:17px 0 10px!important;font-size:14px!important;line-height:1.3!important;font-weight:700!important;color:#2c3845!important}
#sellerEditProductModal .sari-ei-gallery{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:9px!important}
#sellerEditProductModal .sari-ei-gallery-item{position:relative!important;min-width:0!important;border:1px solid #e0e5eb!important;border-radius:11px!important;overflow:hidden!important;background:#f8fafc!important}
#sellerEditProductModal .sari-ei-gallery-view{display:block!important;width:100%!important;height:88px!important;border:0!important;background:#f4f6f8!important;cursor:pointer!important;padding:0!important}
#sellerEditProductModal .sari-ei-gallery-view img{display:block!important;width:100%!important;height:100%!important;object-fit:contain!important}
#sellerEditProductModal .sari-ei-gallery-item label{display:flex!important;align-items:center!important;justify-content:center!important;gap:5px!important;min-height:30px!important;margin:0!important;padding:4px!important;background:#fff!important;color:#617181!important;font-size:11px!important;cursor:pointer!important}
#sellerEditProductModal .sari-ei-gallery-item.is-remove{opacity:.56!important;border-color:#d89898!important}
#sellerEditProductModal .sari-ei-gallery-item.is-remove label{color:#b44949!important}
#sellerEditProductModal .sari-ei-new-images{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:9px!important;margin-top:10px!important}
#sellerEditProductModal .sari-ei-new-images img{display:block!important;width:100%!important;height:78px!important;object-fit:contain!important;border:1px solid #dce4ea!important;border-radius:9px!important;background:#f7f9fb!important}
#sellerEditProductModal .sari-ei-file-label{display:block!important;margin:13px 0 7px!important;font-size:12px!important;font-weight:650!important;color:#445363!important}
#sellerEditProductModal .sari-ei-file{display:block!important;max-width:100%!important;width:100%!important;min-height:42px!important;padding:8px!important;font-family:inherit!important;font-size:12px!important;color:#4c5b69!important;border:1px solid #dce3ea!important;border-radius:10px!important;background:#fff!important}
#sellerEditProductModal .sari-ei-file::file-selector-button{border:0;border-radius:7px;background:#f8eed8;color:#97660f;font-weight:700;padding:6px 9px;margin-right:8px}
#sellerEditProductModal .sari-ei-two-cols{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:15px!important}
#sellerEditProductModal .sari-ei-simple-row{display:grid!important;grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:15px!important}
#sellerEditProductModal .sari-ei-controls{display:flex!important;flex-wrap:wrap!important;align-items:center!important;justify-content:space-between!important;gap:10px!important;margin:0 0 13px!important}
#sellerEditProductModal .sari-ei-lite-button{display:inline-flex!important;align-items:center!important;justify-content:center!important;min-height:36px!important;border:1px solid #ddc48d!important;border-radius:9px!important;background:#fffaf0!important;padding:0 13px!important;color:#99630d!important;font-size:12px!important;font-weight:700!important;cursor:pointer!important}
#sellerEditProductModal .sari-ei-lite-button:hover{background:#f8edda!important}
#sellerEditProductModal .sari-ei-remove-row{border:0!important;background:transparent!important;color:#ab5151!important;font-size:12px!important;padding:7px 8px!important;cursor:pointer!important;align-self:center!important}
#sellerEditProductModal .sari-ei-list{display:grid!important;gap:12px!important}
#sellerEditProductModal .sari-ei-edit-card{border:1px solid #e2e7ed!important;border-radius:12px!important;background:#fff!important;padding:13px!important;box-shadow:none!important}
#sellerEditProductModal .sari-ei-edit-card-head{display:flex!important;align-items:center!important;justify-content:space-between!important;gap:12px!important;margin-bottom:12px!important}
#sellerEditProductModal .sari-ei-edit-card-title{font-size:13px!important;font-weight:750!important;color:#324050!important}
#sellerEditProductModal .sari-ei-option-grid{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:10px!important;margin:0 0 12px!important}
#sellerEditProductModal .sari-ei-item-fields{display:grid!important;grid-template-columns:minmax(105px,1.15fr) minmax(95px,.8fr) minmax(95px,.75fr)!important;gap:10px!important}
#sellerEditProductModal .sari-ei-field-label{display:block!important;margin:0 0 5px!important;color:#596778!important;font-weight:650!important;font-size:12px!important}
#sellerEditProductModal .sari-ei-value-input{width:100%!important;min-width:0!important;height:42px!important;padding:0 11px!important;border:1px solid #dae1e8!important;border-radius:9px!important;color:#344253!important;background:#fff!important;font-size:13px!important}
#sellerEditProductModal .sari-ei-value-input:focus{outline:0!important;border-color:#c88912!important;box-shadow:0 0 0 3px rgba(200,137,18,.1)!important}
#sellerEditProductModal .sari-ei-variant-image{display:flex!important;align-items:center!important;gap:10px!important;flex-wrap:wrap!important;margin-top:12px!important}
#sellerEditProductModal .sari-ei-variant-image img{display:block!important;width:61px!important;height:61px!important;object-fit:contain!important;border:1px solid #e0e5eb!important;border-radius:9px!important;background:#f5f7f9!important}
#sellerEditProductModal .sari-ei-variant-image input{flex:1 1 195px!important;min-width:0!important}
#sellerEditProductModal .sari-ei-check-label{display:flex!important;align-items:center!important;gap:10px!important;min-height:42px!important;color:#344253!important;font-size:13px!important;font-weight:650!important;cursor:pointer!important}
#sellerEditProductModal .sari-ei-check-label input{width:17px!important;height:17px!important;accent-color:#c88912!important}
#sellerEditProductModal .sari-ei-hidden{display:none!important}
#sellerEditProductModal .sari-ei-inline-error{min-height:0!important;margin-top:9px!important;color:#b14343!important;font-size:12px!important;font-weight:600!important}
#sellerEditProductModal .sari-ei-note{margin:7px 0 0!important;font-size:11px!important;color:#83909f!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-edit-card .sari-ei-value-input[type="number"]{height:42px!important}
#sellerEditProductModal .sari-ei-basic-fields{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:14px!important}
#sellerEditProductModal .sari-ei-basic-fields>.seller-edit-field:nth-child(1){grid-column:1/-1!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-eform-field{min-width:0!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-eform-field>label{display:block!important;margin-bottom:7px!important;font-size:12px!important;font-weight:650!important;color:#526173!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-eform-field :is(input,select,textarea){display:block!important;width:100%!important;min-width:0!important;height:44px!important;padding:0 12px!important;font-size:13px!important;font-weight:500!important;border:1px solid #dae1e8!important;border-radius:10px!important;background:#fff!important;color:#2e3b48!important;box-shadow:none!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-eform-field textarea{height:auto!important;min-height:130px!important;padding:12px!important;resize:vertical!important}
#sellerEditProductModal #sellerEditProductForm .sari-ei-eform-field :is(input,select,textarea):focus{outline:none!important;border-color:#c88912!important;box-shadow:0 0 0 3px rgba(200,137,18,.1)!important}
@media(max-width:900px){#sellerEditProductModal .sari-ei-overview{grid-template-columns:minmax(205px,275px) minmax(0,1fr)!important;gap:17px!important}}
@media(max-width:690px){
 #sellerEditProductModal .sari-ei-overview{grid-template-columns:1fr!important}
 #sellerEditProductModal .sari-ei-image-frame{height:235px!important;max-width:540px!important;margin-inline:auto!important}
 #sellerEditProductModal .sari-ei-image-frame[data-orientation="portrait"]{height:320px!important}
 #sellerEditProductModal .sari-ei-two-cols,#sellerEditProductModal .sari-ei-basic-fields{grid-template-columns:1fr!important}
 #sellerEditProductModal .sari-ei-simple-row{grid-template-columns:repeat(2,minmax(0,1fr))!important}
 #sellerEditProductModal .sari-ei-item-fields{grid-template-columns:1fr 1fr!important}
 #sellerEditProductModal .sari-ei-item-fields>div:first-child{grid-column:1/-1!important}
}
@media(max-width:390px){#sellerEditProductModal .sari-ei-option-grid,#sellerEditProductModal .sari-ei-simple-row{grid-template-columns:1fr!important}}

/* Seller Edit Product — notebook-style variants, locked category, and image dropdown. */
#sellerEditProductModal #editProductCategory:disabled{
  cursor:not-allowed!important;background:#f6f7f9!important;color:#4c5867!important;
  border-color:#e1e5ea!important;opacity:1!important;-webkit-text-fill-color:#4c5867!important;
}
#sellerEditProductModal #sellerEditProductForm #editProductImage.sari-ei-image-file-hidden,
#sellerEditProductModal #sellerEditProductForm #sellerEditGalleryUpload.sari-ei-image-file-hidden{
  position:absolute!important;width:1px!important;height:1px!important;min-height:0!important;
  padding:0!important;margin:0!important;border:0!important;overflow:hidden!important;
  clip:rect(0,0,0,0)!important;clip-path:inset(50%)!important;white-space:nowrap!important;opacity:0!important;
}
#sellerEditProductModal .sari-ei-image-menu{margin-top:12px!important;border:1px solid #e2e7ed!important;border-radius:12px!important;background:#fff!important;overflow:hidden!important}
#sellerEditProductModal .sari-ei-image-menu-trigger{display:flex!important;align-items:center!important;justify-content:space-between!important;gap:10px!important;min-height:47px!important;list-style:none!important;cursor:pointer!important;padding:10px 14px!important;color:#293748!important;font-size:13px!important;font-weight:700!important}
#sellerEditProductModal .sari-ei-image-menu-trigger::-webkit-details-marker{display:none}
#sellerEditProductModal .sari-ei-image-menu-trigger svg{width:17px!important;height:17px!important;flex-shrink:0!important;color:#778493!important;transition:transform .16s ease!important}
#sellerEditProductModal .sari-ei-image-menu[open] .sari-ei-image-menu-trigger svg{transform:rotate(180deg)!important}
#sellerEditProductModal .sari-ei-image-menu[open] .sari-ei-image-menu-trigger{border-bottom:1px solid #e7ebf0!important}
#sellerEditProductModal .sari-ei-image-count{display:inline-block!important;min-width:22px!important;margin-left:5px!important;color:#a77218!important;font-size:11px!important;font-weight:750!important}
#sellerEditProductModal .sari-ei-image-menu-content{padding:13px!important;background:#fff!important}
#sellerEditProductModal .sari-ei-image-tools{display:flex!important;flex-wrap:wrap!important;gap:9px!important;margin-bottom:14px!important}
#sellerEditProductModal .sari-ei-photo-action{display:inline-flex!important;align-items:center!important;justify-content:center!important;min-height:37px!important;padding:0 13px!important;border:1px solid #d7dfe6!important;border-radius:9px!important;background:#fff!important;color:#435366!important;font-size:12px!important;font-weight:700!important;cursor:pointer!important}
#sellerEditProductModal .sari-ei-photo-action:hover{border-color:#d6b26d!important;background:#fffaf1!important;color:#96650e!important}
#sellerEditProductModal .sari-ei-variant-actions{margin:12px 0 8px!important}
#sellerEditProductModal .sari-ei-variant-table{width:100%!important;min-width:0!important;overflow-x:auto!important;overflow-y:visible!important;border:1px solid #e4e8ee!important;border-radius:12px!important;background:#fff!important;scrollbar-width:thin!important}
#sellerEditProductModal .sari-ei-variant-table-head,
#sellerEditProductModal .sari-ei-variant-line{display:grid!important;grid-template-columns:minmax(180px,2fr) minmax(115px,1.1fr) minmax(90px,.9fr) minmax(55px,.55fr) 56px 94px!important;gap:12px!important;align-items:center!important;min-width:720px!important}
#sellerEditProductModal .sari-ei-variant-table-head{min-height:40px!important;padding:9px 14px!important;border-bottom:1px solid #e5e9ee!important;background:#f8f9fb!important;color:#718091!important;font-size:11px!important;font-weight:750!important}
#sellerEditProductModal .sari-ei-table-actions-head{text-align:right!important}
#sellerEditProductModal .sari-ei-variant-lines{display:block!important;min-width:720px!important}
#sellerEditProductModal .sari-ei-variant-row{min-width:720px!important;border:0!important;border-bottom:1px solid #e8ecf0!important;border-radius:0!important;background:#fff!important;padding:0!important;margin:0!important;box-shadow:none!important}
#sellerEditProductModal .sari-ei-variant-row:last-child{border-bottom:0!important}
#sellerEditProductModal .sari-ei-variant-line{min-height:65px!important;padding:9px 14px!important;background:transparent!important}
#sellerEditProductModal .sari-ei-variant-row:hover>.sari-ei-variant-line{background:#fcfdff!important}
#sellerEditProductModal .sari-ei-variant-row.is-editing>.sari-ei-variant-line{background:#fffcf5!important}
#sellerEditProductModal .sari-ei-variant-identity{display:flex!important;flex-direction:column!important;gap:4px!important;min-width:0!important}
#sellerEditProductModal .sari-ei-variant-title{color:#2d3846!important;font-size:13px!important;line-height:1.2!important;font-weight:750!important}
#sellerEditProductModal .sari-ei-variant-description{color:#7a8795!important;font-size:11px!important;line-height:1.3!important;overflow-wrap:anywhere!important}
#sellerEditProductModal .sari-ei-variant-cell{overflow:hidden!important;min-width:0!important;color:#425162!important;font-size:12px!important;line-height:1.4!important;font-weight:550!important;overflow-wrap:anywhere!important}
#sellerEditProductModal .sari-ei-variant-thumb-cell{display:flex!important;align-items:center!important;justify-content:center!important;min-width:0!important}
#sellerEditProductModal .sari-ei-variant-thumb{display:block!important;max-width:42px!important;width:42px!important;height:42px!important;object-fit:contain!important;border:1px solid #e5e9ee!important;border-radius:8px!important;background:#f9fafb!important}
#sellerEditProductModal .sari-ei-variant-thumb[hidden],
#sellerEditProductModal .sari-ei-variant-no-image[hidden]{display:none!important}
#sellerEditProductModal .sari-ei-variant-no-image{color:#a6afba!important;font-size:14px!important}
#sellerEditProductModal .sari-ei-variant-row-actions{display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:5px!important}
#sellerEditProductModal .sari-ei-variant-icon{display:grid!important;place-items:center!important;width:35px!important;height:35px!important;min-height:35px!important;border:1px solid transparent!important;border-radius:9px!important;background:#fff!important;padding:0!important;color:#a26c13!important;cursor:pointer!important}
#sellerEditProductModal .sari-ei-variant-icon:hover,#sellerEditProductModal .sari-ei-variant-icon.is-editing{background:#fff5e3!important;border-color:#f0dfbd!important}
#sellerEditProductModal .sari-ei-variant-icon--delete{color:#b44f4f!important}
#sellerEditProductModal .sari-ei-variant-icon--delete:hover{border-color:#f2d7d7!important;background:#fff0f0!important}
#sellerEditProductModal .sari-ei-variant-icon svg{width:18px!important;height:18px!important;stroke-width:1.7!important;pointer-events:none!important}
#sellerEditProductModal .sari-ei-variant-editor{padding:17px 18px 19px!important;border-top:1px dashed #e8e1d5!important;background:#fffdf9!important}
#sellerEditProductModal .sari-ei-variant-editor[hidden]{display:none!important}
#sellerEditProductModal .sari-ei-variant-editor .sari-ei-option-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}
#sellerEditProductModal .sari-ei-variant-editor .sari-ei-variant-image{margin-top:13px!important}
#sellerEditProductModal .sari-ei-variant-editor .sari-ei-variant-image .sari-ei-field-label{margin:0!important}
@media(max-width:690px){
 #sellerEditProductModal .sari-ei-variant-table{max-width:100%!important}
 #sellerEditProductModal .sari-ei-variant-editor .sari-ei-option-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}
}


/* Size Guide in Edit Product uses the same drag-and-drop language as Add Product. */
#sellerEditProductModal .sari-ei-size-guide-header{display:flex!important;align-items:start!important;justify-content:space-between!important;gap:15px!important;flex-wrap:wrap!important;margin-bottom:12px!important}
#sellerEditProductModal .sari-ei-size-guide-header .sari-ei-section-name{margin:0 0 4px!important}
#sellerEditProductModal .sari-ei-optional{color:#8793a0!important;font-size:11px!important;font-weight:500!important}
#sellerEditProductModal .sari-ei-size-guide-zone{display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;gap:8px!important;min-height:150px!important;max-width:100%!important;padding:17px!important;overflow:hidden!important;cursor:pointer!important;border:1px dashed #cbd3dd!important;border-radius:12px!important;background:#fafbfc!important;transition:background-color .16s,border-color .16s!important}
#sellerEditProductModal .sari-ei-size-guide-zone:hover,
#sellerEditProductModal .sari-ei-size-guide-zone.is-dragover{background:#fffbf2!important;border-color:#d59617!important}
#sellerEditProductModal .sari-ei-size-guide-zone.has-image{border-style:solid!important;background:#fff!important}
#sellerEditProductModal .sari-ei-size-guide-zone:focus-visible{outline:3px solid rgba(213,150,23,.27)!important;outline-offset:2px!important}
#sellerEditProductModal .sari-ei-size-guide-placeholder{display:flex!important;flex-direction:column!important;align-items:center!important;gap:5px!important;text-align:center!important;color:#788595!important;font-size:11px!important;line-height:1.45!important}
#sellerEditProductModal .sari-ei-size-guide-placeholder svg{width:27px!important;height:27px!important;color:#bd8730!important}
#sellerEditProductModal .sari-ei-size-guide-placeholder strong{font-size:13px!important;color:#2d3947!important}
#sellerEditProductModal .sari-ei-size-guide-zone img{display:block!important;max-width:100%!important;max-height:370px!important;width:auto!important;height:auto!important;object-fit:contain!important;border-radius:7px!important}
#sellerEditProductModal .sari-ei-size-guide-zone .sari-ei-size-guide-replace{color:#7e8997!important;font-size:11px!important;font-weight:650!important}
#sellerEditProductModal .sari-ei-size-guide-zone [hidden]{display:none!important}
#sellerEditProductModal .sari-ei-size-guide-status{margin:9px 0 0!important;font-size:11px!important;color:#788596!important;line-height:1.4!important}
#sellerEditProductModal .sari-ei-size-guide-error{margin:6px 0 0!important;font-size:11px!important;color:#b14343!important;line-height:1.4!important}
#sellerEditProductModal #sellerEditSizeGuideRemoveButton{min-height:33px!important;border-color:#e1c5c5!important;background:#fff8f8!important;color:#a54343!important}
#sellerEditProductModal #sellerEditSizeGuideRemoveButton:hover{border-color:#d89f9f!important;background:#fff0f0!important}

@endverbatim
</style>

<div id="sellerEditProductModal" class="fixed inset-0 z-[130] hidden items-center justify-center sari-ei-overlay">
  <div class="seller-edit-enterprise-dialog sari-ei-shell" role="dialog" aria-modal="true" aria-labelledby="sellerEditModalTitle">
    <header class="seller-edit-enterprise-header sari-ei-header">
      <div class="seller-edit-header-copy"><h3 id="sellerEditModalTitle">Edit Product</h3><p id="sellerEditFormError" class="sari-ei-inline-error" role="alert" aria-live="polite"></p></div>
      <button type="button" data-close-edit class="seller-edit-header-close sari-ei-close" aria-label="Close Edit Product">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"></path></svg>
      </button>
    </header>
    <form id="sellerEditProductForm" method="POST" action="" enctype="multipart/form-data" class="seller-edit-enterprise-form sari-ei-form">
      @csrf
      @method('PUT')
      <div class="sari-ei-overview">
        <div class="sari-ei-media-panel">
          <h4 class="sari-ei-section-name">Product Images</h4>
          <div class="sari-ei-image-frame" id="sellerEditImageFrame">
            <div id="editCurrentImageWrap" class="hidden seller-edit-current-image"><img id="editCurrentImage" alt="Product cover preview" src=""></div>
            <div class="sari-ei-image-empty"><span>No product image</span></div>
          </div>
          {{-- Native file inputs remain available for the existing Laravel upload endpoint. --}}
          <input id="editProductImage" name="image" type="file" class="sari-ei-file sari-ei-image-file-hidden" accept="image/jpeg,image/png,image/webp" tabindex="-1" aria-label="Choose product cover image">
          <input type="file" id="sellerEditGalleryUpload" name="gallery_images[]" multiple class="sari-ei-file sari-ei-image-file-hidden" accept="image/jpeg,image/png,image/webp" tabindex="-1" aria-label="Add gallery images">
          <details class="sari-ei-image-menu" id="sellerEditImagesMenu">
            <summary class="sari-ei-image-menu-trigger">
              <span>Images &amp; Gallery <span id="sellerEditGalleryCount" class="sari-ei-image-count"></span></span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>
            </summary>
            <div class="sari-ei-image-menu-content">
              <div class="sari-ei-image-tools">
                <button type="button" id="sellerEditChangeCover" class="sari-ei-photo-action">Change cover</button>
                <button type="button" id="sellerEditAddGallery" class="sari-ei-photo-action">Add images</button>
              </div>
              <div id="sellerEditGallery" class="sari-ei-gallery" aria-label="Current product gallery"></div>
              <p id="sellerEditGalleryEmpty" class="sari-ei-muted">No gallery images</p>
              <div id="sellerEditGalleryNew" class="sari-ei-new-images" aria-label="Images ready to upload"></div>
            </div>
          </details>
        </div>
        <section class="sari-ei-fields-panel" aria-label="Listing details">
          <h4 class="sari-ei-section-name">Listing Details</h4>
          <div class="seller-edit-grid sari-ei-grid">
            <div class="seller-edit-field seller-edit-field--full"><label for="editProductName">Product Name <span>*</span></label><input id="editProductName" name="name" maxlength="150" required></div>
            <div class="seller-edit-field"><label for="editProductCategory">Category</label><div class="seller-edit-select-wrap">
              <select id="editProductCategory" disabled aria-disabled="true" title="Category is fixed by your seller registration">
                <option value="">Select category</option><option>Electronics</option><option>Fashion</option><option>Fashion &amp; Apparel</option><option>Shoes &amp; Footwear</option><option>Home &amp; Living</option><option>Furniture &amp; Office</option><option>Beauty</option><option>Beauty &amp; Personal Care</option><option>Books</option><option>Books &amp; Stationery</option><option>Food &amp; Beverage</option><option>Food &amp; Gourmet</option><option>Jewelry &amp; Watches</option><option>Sports &amp; Outdoors</option><option>Automotive &amp; Motorcycle</option><option>Baby &amp; Kids</option><option>Pet Supplies</option><option>Health &amp; Wellness</option><option>Toys &amp; Collectibles</option><option>Appliances</option><option>Others</option>
              </select><input type="hidden" id="sellerEditLockedCategory" name="category" value="">
            </div></div>
            <div class="seller-edit-field"><label for="editProductBrand">Brand</label><input id="editProductBrand" name="brand" maxlength="120"></div>
            <div class="seller-edit-field seller-edit-field--full"><label for="editProductSku">SKU</label><input id="editProductSku" name="sku" maxlength="100"></div>
            <div class="seller-edit-field"><label for="sellerEditCondition">Condition</label><select id="sellerEditCondition" name="condition"><option value="">Not specified</option><option value="new">New</option><option value="like_new">Like new</option><option value="used">Used</option></select></div>
            <div class="seller-edit-field"><label for="sellerEditLowStock">Low Stock Alert</label><input id="sellerEditLowStock" name="low_stock_threshold" type="number" min="0" max="1000000"></div>
          </div>
        </section>
      </div>

      <section class="sari-ei-section" aria-label="Inventory and price">
        <h4 class="sari-ei-section-name" id="sellerEditPricingHeading">Pricing &amp; Inventory</h4>
        <div class="seller-edit-grid sari-ei-grid">
          <div class="seller-edit-field"><label for="editProductPrice">Price <span>*</span></label><input id="editProductPrice" name="price" type="number" min="0" step="0.01" required></div>
          <div class="seller-edit-field"><label for="editProductStock">Stock <span>*</span></label><input id="editProductStock" name="stock" type="number" min="0" step="1" required></div>
        </div>
        <p id="sellerEditVariantInventoryHint" class="sari-ei-note sari-ei-hidden">For products with variants, total stock and displayed price are calculated from the variant rows below.</p>
      </section>

      <section class="sari-ei-section" aria-label="Promotions">
        <h4 class="sari-ei-section-name" id="sellerEditPromoHeading">Promotions</h4>
        <div class="seller-edit-grid sari-ei-grid">
          <div class="seller-edit-field"><label for="editProductDiscount">Discount %</label><input id="editProductDiscount" name="discount" type="number" min="0" max="100" step="0.01"><p id="editProductSalePreview" class="seller-edit-helper" aria-live="polite"></p></div>
          <div class="seller-edit-field"><label for="editProductFlashSaleStartsAtLocal">Flash Sale Starts</label><input id="editProductFlashSaleStartsAtLocal" type="datetime-local"><input id="editProductFlashSaleStartsAt" name="flash_sale_starts_at" type="hidden"></div>
          <div class="seller-edit-field"><label for="editProductFlashSaleEndsAtLocal">Flash Sale Ends</label><input id="editProductFlashSaleEndsAtLocal" type="datetime-local"><input id="editProductFlashSaleEndsAt" name="flash_sale_ends_at" type="hidden"></div>
          <div class="seller-edit-field"><label for="editProductVoucher">Voucher Code</label><input id="editProductVoucher" name="voucher_code" maxlength="80"></div>
          <div class="seller-edit-field"><label for="sellerEditCod">Payment</label><label for="sellerEditCod" class="sari-ei-check-label"><input id="sellerEditCod" type="checkbox" name="cash_on_delivery" value="1"> Cash on Delivery (COD)</label></div>
        </div>
      </section>

      <section class="sari-ei-section" aria-label="Package and preparation">
        <h4 class="sari-ei-section-name">Shipping &amp; Package</h4>
        <div class="seller-edit-grid sari-ei-grid">
          <div class="seller-edit-field"><label for="sellerEditWeight">Weight (kg)</label><input id="sellerEditWeight" name="package_weight" type="number" min="0" max="999999.999" step="0.001"></div>
          <div class="seller-edit-field"><label for="sellerEditPrep">Preparation (days)</label><input id="sellerEditPrep" name="preparation_days" type="number" min="0" max="365" step="1"></div>
          <div class="seller-edit-field"><label for="sellerEditLength">Length (cm)</label><input id="sellerEditLength" name="package_length" type="number" min="0" step="0.01"></div>
          <div class="seller-edit-field"><label for="sellerEditWidth">Width (cm)</label><input id="sellerEditWidth" name="package_width" type="number" min="0" step="0.01"></div>
          <div class="seller-edit-field"><label for="sellerEditHeight">Height (cm)</label><input id="sellerEditHeight" name="package_height" type="number" min="0" step="0.01"></div>
          <div class="seller-edit-field seller-edit-field--full"><a class="seller-edit-shipping-card sari-ei-shipping-link" href="{{ route('seller.vouchers.index') }}"><strong>Free Shipping Promotions</strong><span class="sari-ei-shipping-action">Manage →</span></a></div>
        </div>
      </section>

      <section class="sari-ei-section" aria-label="Description">
        <h4 class="sari-ei-section-name" id="sellerEditDescriptionHeading">Product Description</h4>
        <div class="seller-edit-grid sari-ei-grid"><div class="seller-edit-field seller-edit-field--full"><label for="editProductDescription">Description</label><textarea id="editProductDescription" name="description" rows="4" maxlength="5000"></textarea></div></div>
      </section>

      <section class="sari-ei-section" aria-labelledby="sellerEditSizeGuideHeading">
        <div class="sari-ei-size-guide-header">
          <div>
            <h4 class="sari-ei-section-name" id="sellerEditSizeGuideHeading">Size Guide <span class="sari-ei-optional">(optional)</span></h4>
            <p class="sari-ei-muted">One size chart per product, separate from the gallery. Buyers can enlarge it on the product page.</p>
          </div>
          <button type="button" id="sellerEditSizeGuideRemoveButton" class="sari-ei-lite-button sari-ei-hidden">Remove chart</button>
        </div>
        <input id="sellerEditSizeGuideFile" name="size_guide_image" type="file" accept="image/jpeg,image/png,image/webp" class="sari-ei-hidden" aria-describedby="sellerEditSizeGuideHelp sellerEditSizeGuideError">
        <input id="sellerEditRemoveSizeGuide" name="remove_size_guide_image" type="hidden" value="0">
        <div id="sellerEditSizeGuideDrop" class="sari-ei-size-guide-zone" tabindex="0" role="button" aria-label="Choose or drop a size guide image" aria-describedby="sellerEditSizeGuideHelp sellerEditSizeGuideStatus sellerEditSizeGuideError">
          <div id="sellerEditSizeGuideEmpty" class="sari-ei-size-guide-placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V3m-5 5 5-5 5 5M3 16v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3"/></svg>
            <strong>Drag &amp; drop size guide image</strong>
            <span id="sellerEditSizeGuideHelp">or click to browse · JPG, PNG or WEBP · up to 4 MB</span>
          </div>
          <img id="sellerEditSizeGuidePreview" alt="Current or newly selected size chart" decoding="async" hidden>
          <span id="sellerEditSizeGuideReplace" class="sari-ei-size-guide-replace" hidden>Click or drop another image to replace</span>
        </div>
        <p id="sellerEditSizeGuideStatus" class="sari-ei-size-guide-status" aria-live="polite"></p>
        <p id="sellerEditSizeGuideError" class="sari-ei-size-guide-error" role="alert" aria-live="polite"></p>
      </section>

      <section class="sari-ei-section" aria-label="Specifications">
        <div class="sari-ei-controls"><h4 class="sari-ei-section-name" style="margin:0!important">Specifications</h4><button type="button" id="sellerEditAddSpec" class="sari-ei-lite-button">+ Add Specification</button></div>
        <div id="sellerEditSpecs" class="sari-ei-list"></div>
        <input type="hidden" id="sellerEditEmptySpecMarker" name="specifications[0][name]" value="" disabled>
        <p id="sellerEditSpecEmpty" class="sari-ei-muted">No specifications</p>
      </section>

      <section class="sari-ei-section" aria-label="Product variations">
        <div class="sari-ei-controls"><h4 class="sari-ei-section-name" style="margin:0!important">Product Variants</h4><div class="sari-ei-eform-field"><label for="sellerEditVariantMode">Listing type</label><select id="sellerEditVariantMode"><option value="0">Simple Product</option><option value="1">With Variants</option></select></div></div>
        <input type="hidden" id="sellerEditHasVariants" name="has_variants" value="0">
        <div id="sellerEditVariantEditor" class="sari-ei-hidden">
          <div class="sari-ei-controls sari-ei-variant-actions"><span id="sellerEditVariantCount" class="sari-ei-muted"></span><button type="button" id="sellerEditAddVariant" class="sari-ei-lite-button">+ Add Variant</button></div>
          <div class="sari-ei-variant-table" role="table" aria-label="Editable product variants">
            <div class="sari-ei-variant-table-head" role="row"><span role="columnheader">Variant</span><span role="columnheader">SKU</span><span role="columnheader">Price</span><span role="columnheader">Stock</span><span role="columnheader">Image</span><span role="columnheader" class="sari-ei-table-actions-head">Actions</span></div>
            <div id="sellerEditVariants" class="sari-ei-variant-lines" role="rowgroup"></div>
          </div>
        </div>
      </section>
      <footer class="seller-edit-footer sari-ei-footer">
        <button type="button" data-close-edit class="seller-edit-button seller-edit-button--secondary">Cancel</button>
        <button type="submit" class="seller-edit-button seller-edit-button--primary">Save Changes</button>
      </footer>
    </form>
  </div>
</div>

<script>
/* Single-Blade editor enhancements. Existing seller-products.js continues to open/save. */
(() => {
  const modal = document.getElementById('sellerEditProductModal');
  const form = document.getElementById('sellerEditProductForm');
  if (!modal || !form || modal.dataset.completeEditBound === '1') return;
  modal.dataset.completeEditBound = '1';
  const $ = id => document.getElementById(id);
  const image = $('editCurrentImage'), imageWrap = $('editCurrentImageWrap');
  const frame = $('sellerEditImageFrame'), coverPicker = $('editProductImage');
  const gallery = $('sellerEditGallery'), galleryPicker = $('sellerEditGalleryUpload');
  const galleryNew = $('sellerEditGalleryNew'), specList = $('sellerEditSpecs');
  const variantList = $('sellerEditVariants'), variantMode = $('sellerEditVariantMode');
  const errorNode = $('sellerEditFormError');
  const sizeGuideFile = $('sellerEditSizeGuideFile');
  const sizeGuideDrop = $('sellerEditSizeGuideDrop');
  const sizeGuideImage = $('sellerEditSizeGuidePreview');
  const sizeGuideEmpty = $('sellerEditSizeGuideEmpty');
  const sizeGuideReplace = $('sellerEditSizeGuideReplace');
  const sizeGuideStatus = $('sellerEditSizeGuideStatus');
  const sizeGuideError = $('sellerEditSizeGuideError');
  const sizeGuideRemoveButton = $('sellerEditSizeGuideRemoveButton');
  const sizeGuideRemoveField = $('sellerEditRemoveSizeGuide');
  let sizeGuideObjectUrl = null;

  function clearSizeGuideObjectUrl() {
    if (!sizeGuideObjectUrl) return;
    URL.revokeObjectURL(sizeGuideObjectUrl);
    sizeGuideObjectUrl = null;
  }
  function editSizeGuidePreview(url, status = '') {
    const hasImage = Boolean(url);
    if (hasImage) sizeGuideImage.src = url;
    else sizeGuideImage.removeAttribute('src');
    sizeGuideImage.hidden = !hasImage;
    sizeGuideEmpty.hidden = hasImage;
    sizeGuideReplace.hidden = !hasImage;
    sizeGuideDrop.classList.toggle('has-image', hasImage);
    sizeGuideRemoveButton.classList.toggle('sari-ei-hidden', !hasImage);
    sizeGuideStatus.textContent = status;
  }
  function selectSizeGuideFile(file) {
    if (!file) return;
    if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 4 * 1024 * 1024) {
      sizeGuideFile.value = '';
      sizeGuideError.textContent = 'Choose a JPG, PNG or WEBP image no larger than 4 MB.';
      return;
    }
    sizeGuideError.textContent = '';
    sizeGuideRemoveField.value = '0';
    clearSizeGuideObjectUrl();
    sizeGuideObjectUrl = URL.createObjectURL(file);
    editSizeGuidePreview(sizeGuideObjectUrl, 'New size chart selected. Save Changes to apply it.');
  }
  sizeGuideDrop.addEventListener('click', () => sizeGuideFile.click());
  sizeGuideDrop.addEventListener('keydown', event => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      sizeGuideFile.click();
    }
  });
  sizeGuideFile.addEventListener('change', () => selectSizeGuideFile(sizeGuideFile.files?.[0]));
  ['dragover', 'dragenter'].forEach(type => sizeGuideDrop.addEventListener(type, event => {
    event.preventDefault();
    sizeGuideDrop.classList.add('is-dragover');
  }));
  ['dragleave', 'drop'].forEach(type => sizeGuideDrop.addEventListener(type, event => {
    event.preventDefault();
    sizeGuideDrop.classList.remove('is-dragover');
  }));
  sizeGuideDrop.addEventListener('drop', event => {
    const file = event.dataTransfer?.files?.[0];
    if (!file) return;
    if (typeof DataTransfer === 'undefined') {
      sizeGuideError.textContent = 'Drag-and-drop is unavailable in this browser. Click to browse instead.';
      return;
    }
    const transfer = new DataTransfer();
    transfer.items.add(file);
    sizeGuideFile.files = transfer.files;
    selectSizeGuideFile(sizeGuideFile.files?.[0]);
  });
  sizeGuideRemoveButton.addEventListener('click', () => {
    clearSizeGuideObjectUrl();
    sizeGuideFile.value = '';
    sizeGuideRemoveField.value = '1';
    sizeGuideError.textContent = '';
    editSizeGuidePreview('', 'Size chart will be removed when you save your changes.');
  });
  $('sellerEditChangeCover').addEventListener('click', () => coverPicker.click());
  $('sellerEditAddGallery').addEventListener('click', () => galleryPicker.click());
  const maxBytes = 4 * 1024 * 1024;
  const allowedTypes = new Set(['image/jpeg', 'image/png', 'image/webp']);
  const urls = new Set();
  let loadedId = null, current = null;
  let initialVariantMode = false;
  let previewOriginalUrl = '';
  const make = (tag, className = '', text = null) => {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== null) node.textContent = text;
    return node;
  };
  function objectUrl(file) { const url = URL.createObjectURL(file); urls.add(url); return url; }
  function freeUrls() { urls.forEach(url => URL.revokeObjectURL(url)); urls.clear(); }
  function showError(message) { errorNode.textContent = message || ''; }
  function validateFiles(files) {
    for (const file of Array.from(files || [])) {
      if (!allowedTypes.has(file.type)) return `Only JPG, PNG and WEBP images are accepted: ${file.name}`;
      if (file.size > maxBytes) return `${file.name} exceeds the 4 MB limit.`;
    }
    return '';
  }
  function fileChange(input, action) {
    input.addEventListener('change', () => {
      const message = validateFiles(input.files);
      if (message) { input.value = ''; showError(message); return; }
      showError('');
      action();
    });
  }
  function adapt() {
    const ratio = image.naturalHeight ? image.naturalWidth / image.naturalHeight : 1;
    frame.dataset.orientation = ratio > 1.2 ? 'landscape' : ratio < .82 ? 'portrait' : 'square';
  }
  image.addEventListener('load', adapt);
  function previewImage(url) {
    if (!url) return;
    image.src = url;
    imageWrap.classList.remove('hidden');
    if (image.complete && image.naturalWidth) adapt();
  }
  fileChange(coverPicker, () => {
    const file = coverPicker.files?.[0];
    previewImage(file ? objectUrl(file) : previewOriginalUrl);
  });
  function inputField(label, value, type = 'text') {
    const cell = make('div');
    const caption = make('label', 'sari-ei-field-label', label);
    const input = make('input', 'sari-ei-value-input');
    input.type = type; input.value = value ?? '';
    if (type === 'number') { input.min = '0'; input.step = '1'; }
    cell.append(caption, input);
    return { cell, input };
  }
  function renderGallery(product) {
    gallery.replaceChildren(); galleryNew.replaceChildren();
    galleryPicker.value = '';
    const items = Array.isArray(product.gallery) ? product.gallery : [];
    $('sellerEditGalleryCount').textContent = items.length ? `${items.length}` : '0';
    $('sellerEditGalleryEmpty').classList.toggle('sari-ei-hidden', items.length > 0);
    items.forEach((item, index) => {
      const card = make('div', 'sari-ei-gallery-item');
      const button = make('button', 'sari-ei-gallery-view');
      button.type = 'button'; button.title = `Preview gallery image ${index + 1}`;
      const img = make('img'); img.src = item.url || ''; img.alt = `Gallery image ${index + 1}`;
      img.loading = 'lazy'; button.append(img);
      button.addEventListener('click', () => { if (item.url) previewImage(item.url); });
      const label = make('label');
      const checkbox = make('input'); checkbox.type = 'checkbox';
      checkbox.name = 'remove_gallery_image_ids[]'; checkbox.value = String(item.id);
      checkbox.addEventListener('change', () => card.classList.toggle('is-remove', checkbox.checked));
      label.append(checkbox, document.createTextNode(' Remove'));
      card.append(button, label); gallery.append(card);
    });
  }
  fileChange(galleryPicker, () => {
    galleryNew.replaceChildren();
    Array.from(galleryPicker.files || []).forEach((file, index) => {
      const img = make('img'); img.src = objectUrl(file);
      img.alt = `New gallery image ${index + 1}`;
      galleryNew.append(img);
    });
  });
  function renumberSpecs() {
    Array.from(specList.children).forEach((row, index) => {
      row.querySelectorAll('[data-spec]').forEach(input => {
        input.name = `specifications[${index}][${input.dataset.spec}]`;
      });
    });
    $('sellerEditSpecEmpty').classList.toggle('sari-ei-hidden', specList.children.length > 0);
    $('sellerEditEmptySpecMarker').disabled = specList.children.length > 0;
  }
  function addSpec(item = {}) {
    if (specList.children.length >= 30) { showError('Maximum 30 specifications.'); return; }
    const card = make('div', 'sari-ei-edit-card');
    const cells = make('div', 'sari-ei-simple-row');
    [['name', 'Name'], ['value', 'Value'], ['unit', 'Unit']].forEach(([key, label]) => {
      const { cell, input } = inputField(label, item[key] || '');
      input.dataset.spec = key;
      cells.append(cell);
    });
    const remove = make('button', 'sari-ei-remove-row', 'Remove');
    remove.type = 'button'; remove.addEventListener('click', () => { card.remove(); renumberSpecs(); });
    card.append(cells, remove); specList.append(card); renumberSpecs();
  }
  $('sellerEditAddSpec').addEventListener('click', () => addSpec());
  function buildOptions(card, data) {
    const optionGrid = card.querySelector('[data-variant-options]');
    optionGrid.replaceChildren();
    const entries = Object.entries(data || {}).filter(([key]) => key.trim());
    (entries.length ? entries : [['Option', '']]).forEach(([key, value]) => {
      const pair = make('div');
      const caption = make('label', 'sari-ei-field-label', key);
      const input = make('input', 'sari-ei-value-input');
      input.value = value ?? ''; input.dataset.optionKey = key; input.maxLength = 120; input.required = false;
      pair.append(caption, input); optionGrid.append(pair);
    });
  }
  function renumberVariants() {
    Array.from(variantList.children).forEach((card, index) => {
      card.querySelectorAll('[data-variant-field]').forEach(input => {
        input.name = `variants[${index}][${input.dataset.variantField}]`;
      });
      card.querySelector('[data-variant-hidden-options]').name = `variants[${index}][options]`;
      card.querySelector('[data-variant-photo]').name = `variants[${index}][image]`;
      card.querySelector('[data-variant-title]').textContent = `Variant ${index + 1}`;
      card.querySelector('[data-variant-edit]').setAttribute('aria-label', `Edit variant ${index + 1}`);
      card.querySelector('.sari-ei-variant-icon--delete').setAttribute('aria-label', `Delete variant ${index + 1}`);
    });
    const count = variantList.children.length;
    $('sellerEditVariantCount').textContent = `${count} variant${count === 1 ? '' : 's'}`;
  }
  const pencilSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m16 5 3 3M4 20l4-.8L19 8a2.12 2.12 0 0 0-3-3L5 16z"></path></svg>`;
  const trashSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2M8 10v9m4-9v9m4-9v9M6 7l1 14h10l1-14"></path></svg>`;
  function money(value) {
    const n = Number(value);
    return Number.isFinite(n) && value !== '' ? `₱${n.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}` : '—';
  }
  function editVariantRow(row, open, focusFirst = false) {
    const editor = row.querySelector('[data-variant-editor]');
    const button = row.querySelector('[data-variant-edit]');
    editor.hidden = !open;
    button.setAttribute('aria-expanded', String(open));
    button.classList.toggle('is-editing', open);
    row.classList.toggle('is-editing', open);
    button.title = open ? 'Done editing' : 'Edit variant';
    button.setAttribute('aria-label', (open ? 'Done editing ' : 'Edit ') + row.querySelector('[data-variant-title]').textContent);
    if (open && focusFirst) editor.querySelector('[data-option-key], [data-variant-field]')?.focus();
  }
  function syncVariantSummary(row) {
    const optionNames = Array.from(row.querySelectorAll('[data-option-key]'))
      .map(input => `${input.dataset.optionKey}: ${input.value.trim() || '—'}`);
    row.querySelector('[data-variant-description]').textContent = optionNames.join(' · ') || 'No options';
    row.querySelector('[data-variant-sku]').textContent = row.querySelector('[data-variant-field="sku"]').value.trim() || '—';
    row.querySelector('[data-variant-price]').textContent = money(row.querySelector('[data-variant-field="price"]').value);
    row.querySelector('[data-variant-stock]').textContent = row.querySelector('[data-variant-field="stock"]').value || '—';
  }
  function addVariant(item = {}) {
    if (variantList.children.length >= 100) { showError('Maximum 100 variants.'); return; }
    const row = make('div', 'sari-ei-variant-row');
    row.setAttribute('role', 'row');
    const line = make('div', 'sari-ei-variant-line');
    const name = make('div', 'sari-ei-variant-identity');
    name.setAttribute('role', 'cell');
    const title = make('span', 'sari-ei-variant-title'); title.dataset.variantTitle = '';
    const description = make('span', 'sari-ei-variant-description'); description.dataset.variantDescription = '';
    name.append(title, description);
    const skuText = make('span', 'sari-ei-variant-cell'); skuText.dataset.variantSku = ''; skuText.setAttribute('role', 'cell');
    const priceText = make('span', 'sari-ei-variant-cell'); priceText.dataset.variantPrice = ''; priceText.setAttribute('role', 'cell');
    const stockText = make('span', 'sari-ei-variant-cell'); stockText.dataset.variantStock = ''; stockText.setAttribute('role', 'cell');
    const imageCell = make('div', 'sari-ei-variant-thumb-cell'); imageCell.setAttribute('role', 'cell');
    const img = make('img', 'sari-ei-variant-thumb'); img.alt = 'Variant image'; img.loading = 'lazy';
    img.hidden = !item.image_url; if (item.image_url) img.src = item.image_url;
    const imageEmpty = make('span', 'sari-ei-variant-no-image', '—'); imageEmpty.hidden = !!item.image_url;
    imageCell.append(img, imageEmpty);
    const actionCell = make('div', 'sari-ei-variant-row-actions'); actionCell.setAttribute('role', 'cell');
    const edit = make('button', 'sari-ei-variant-icon'); edit.type = 'button'; edit.dataset.variantEdit = '';
    edit.innerHTML = pencilSvg; edit.title = 'Edit variant'; edit.setAttribute('aria-expanded', 'false');
    const remove = make('button', 'sari-ei-variant-icon sari-ei-variant-icon--delete'); remove.type = 'button';
    remove.innerHTML = trashSvg; remove.title = 'Delete variant';
    remove.setAttribute('aria-label', 'Delete variant');
    edit.addEventListener('click', () => editVariantRow(row, row.querySelector('[data-variant-editor]').hidden));
    remove.addEventListener('click', () => {
      if (item.id || item.image_url) {
        if (!window.confirm('Remove this variant from the product when you save?')) return;
      }
      row.remove(); renumberVariants();
    });
    actionCell.append(edit, remove);
    line.append(name, skuText, priceText, stockText, imageCell, actionCell);

    const editor = make('div', 'sari-ei-variant-editor'); editor.dataset.variantEditor = '';
    const optionGrid = make('div', 'sari-ei-option-grid'); optionGrid.dataset.variantOptions = '';
    const optionsJson = make('input'); optionsJson.type = 'hidden'; optionsJson.dataset.variantHiddenOptions = '';
    const fields = make('div', 'sari-ei-item-fields');
    [['sku', 'SKU', 'text'], ['price', 'Price', 'number'], ['stock', 'Stock', 'number']].forEach(([key, label, type]) => {
      const {cell, input} = inputField(label, item[key] ?? '', type);
      input.dataset.variantField = key;
      if (key === 'sku') input.maxLength = 120;
      if (key === 'price') input.step = '.01';
      if (key === 'stock') input.step = '1';
      // Collapsed variant editors must not contain hidden browser-required controls.
      // Server validation and checkVariantFields() still enforce valid values.
      input.required = false;
      fields.append(cell);
    });
    const photoRow = make('div', 'sari-ei-variant-image');
    const editPhotoLabel = make('label', 'sari-ei-field-label', 'Image');
    const photo = make('input', 'sari-ei-file'); photo.type = 'file';
    photo.accept = 'image/jpeg,image/png,image/webp'; photo.dataset.variantPhoto = '';
    fileChange(photo, () => {
      const file = photo.files?.[0];
      const url = file ? objectUrl(file) : (item.image_url || '');
      if (url) { img.src = url; img.hidden = false; imageEmpty.hidden = true; }
      else { img.removeAttribute('src'); img.hidden = true; imageEmpty.hidden = false; }
    });
    photoRow.append(editPhotoLabel, photo);
    editor.append(optionGrid, optionsJson, fields, photoRow);
    row.append(line, editor); variantList.append(row);
    row.dataset.originalOptions = JSON.stringify(item.options || {});
    row.dataset.hadPhoto = item.image_url ? '1' : '0';
    buildOptions(row, item.options || { Option: '' });
    row.querySelectorAll('[data-variant-field], [data-option-key]').forEach(input => {
      input.required = false;
      input.addEventListener('input', () => syncVariantSummary(row));
    });
    renumberVariants(); syncVariantSummary(row);
    editVariantRow(row, !item.id && !item.image_url && !item.sku && !item.price);
  }
  $('sellerEditAddVariant').addEventListener('click', () => {
    const template = variantList.firstElementChild;
    const options = {};
    template?.querySelectorAll('[data-option-key]').forEach(input => { options[input.dataset.optionKey] = ''; });
    addVariant({ options: Object.keys(options).length ? options : {Option:''} });
  });
  function syncMode() {
    const active = variantMode.value === '1';
    $('sellerEditHasVariants').value = active ? '1' : '0';
    $('sellerEditVariantEditor').classList.toggle('sari-ei-hidden', !active);
    const p = $('editProductPrice'), s = $('editProductStock');
    p.readOnly = active; s.readOnly = active;
    $('sellerEditVariantInventoryHint').classList.toggle('sari-ei-hidden', !active);
    variantList.querySelectorAll('input').forEach(input => { input.disabled = !active; });
    if (active && !variantList.children.length) addVariant();
  }
  variantMode.addEventListener('change', syncMode);
  function resetEditor() {
    loadedId = null; current = null; initialVariantMode = false;
    showError(''); freeUrls();
    coverPicker.value = ''; galleryPicker.value = '';
    clearSizeGuideObjectUrl();
    sizeGuideFile.value = ''; sizeGuideRemoveField.value = '0';
    sizeGuideError.textContent = ''; editSizeGuidePreview('');
    $('sellerEditImagesMenu').open = false;
    frame.removeAttribute('data-orientation');
    gallery.replaceChildren(); galleryNew.replaceChildren();
    specList.replaceChildren(); variantList.replaceChildren();
  }
  function activeProduct() {
    const match = new URL(form.action, document.baseURI).pathname.match(/\/(\d+)\/?$/);
    const api = window.__SARI_PRODUCTS_PAGE_API__;
    return match && api && typeof api.getProductById === 'function'
      ? api.getProductById(match[1]) : null;
  }
  function loadEditor(product) {
    resetEditor(); current = product; loadedId = String(product.id);
    previewOriginalUrl = product.image_url || '';
    let currentSizeGuideUrl = product.size_guide_image_url || '';
    if (currentSizeGuideUrl && product.updated_at) {
      const chartUrl = new URL(currentSizeGuideUrl, window.location.href);
      chartUrl.searchParams.set('v', product.updated_at);
      currentSizeGuideUrl = chartUrl.toString();
    }
    editSizeGuidePreview(currentSizeGuideUrl, currentSizeGuideUrl ? 'Current size chart. Drop a new image to replace it.' : 'No size chart uploaded yet.');
    if (previewOriginalUrl) previewImage(previewOriginalUrl);
    const categorySelect = $('editProductCategory');
    const categoryName = String(product.category || categorySelect.value || '').trim();
    if (categoryName && !Array.from(categorySelect.options).some(option => option.value === categoryName)) {
      categorySelect.add(new Option(categoryName, categoryName));
    }
    categorySelect.value = categoryName;
    $('sellerEditLockedCategory').value = categoryName;
    renderGallery(product);
    $('sellerEditCondition').value = product.condition || '';
    $('sellerEditLowStock').value = product.low_stock_threshold ?? 5;
    $('sellerEditCod').checked = Boolean(product.cash_on_delivery);
    [['sellerEditWeight','package_weight'],['sellerEditLength','package_length'],
     ['sellerEditWidth','package_width'],['sellerEditHeight','package_height'],['sellerEditPrep','preparation_days']]
      .forEach(([id, key]) => { $(id).value = product[key] ?? ''; });
    const specs = Array.isArray(product.specifications) ? product.specifications : [];
    specs.forEach(spec => addSpec(spec)); renumberSpecs();
    const variants = Array.isArray(product.variants) ? product.variants : [];
    variants.forEach(item => addVariant(item));
    initialVariantMode = Boolean(product.has_variants || variants.length);
    variantMode.value = initialVariantMode ? '1' : '0';
    syncMode();
  }
  function checkVariantFields() {
    if (variantMode.value !== '1') return '';
    if (!variantList.children.length) return 'Add at least one variant.';
    const seen = new Set();
    for (const card of variantList.children) {
      const options = {};
      card.querySelectorAll('[data-option-key]').forEach(input => {
        const key = input.dataset.optionKey;
        if (input.value.trim()) options[key] = input.value.trim();
      });
      if (!Object.keys(options).length || card.querySelectorAll('[data-option-key]').length !== Object.keys(options).length) {
        editVariantRow(card, true); return 'Each variant needs an option value.';
      }
      const signature = JSON.stringify(Object.entries(options).sort((a,b) => a[0].localeCompare(b[0])));
      if (seen.has(signature)) { editVariantRow(card, true); return 'Two variants have identical options. Give them unique combinations.'; }
      seen.add(signature);
      const price = card.querySelector('[data-variant-field="price"]').value;
      const stock = card.querySelector('[data-variant-field="stock"]').value;
      if (price === '' || !Number.isFinite(Number(price)) || Number(price) < 0) {
        editVariantRow(card, true); return 'Each variant needs a valid non-negative price.';
      }
      if (stock === '' || !Number.isInteger(Number(stock)) || Number(stock) < 0) {
        editVariantRow(card, true); return 'Each variant needs a valid whole-number stock.';
      }
      card.querySelector('[data-variant-hidden-options]').value = JSON.stringify(options);
      const imageIsNew = Boolean(card.querySelector('[data-variant-photo]').files?.length);
      if (card.dataset.hadPhoto === '1' && !imageIsNew && card.dataset.originalOptions !== JSON.stringify(options)) {
        editVariantRow(card, true);
        return 'Changing variant options with an existing photo requires selecting a new variant image to avoid losing the original photo.';
      }
    }
    return '';
  }
  function onSubmit(event) {
    showError('');
    const photos = [coverPicker, galleryPicker, sizeGuideFile, ...variantList.querySelectorAll('[data-variant-photo]')];
    for (const input of photos) {
      const message = validateFiles(input.files);
      if (message) {
        event.preventDefault(); event.stopImmediatePropagation(); showError(message); input.focus(); return;
      }
    }
    if (variantMode.value === '1') {
      const message = checkVariantFields();
      if (message) { event.preventDefault(); event.stopImmediatePropagation(); showError(message); return; }
    } else if (initialVariantMode && variantList.children.length) {
      if (!window.confirm('Convert this product to simple? Existing variants and their images will be removed.')) {
        event.preventDefault(); event.stopImmediatePropagation(); return;
      }
    }
  }
  form.addEventListener('submit', onSubmit, true);
  /* Modal opens synchronously in existing seller-products.js; class mutation runs after form fields are populated. */
  const observer = new MutationObserver(() => {
    if (!modal.classList.contains('flex') || modal.classList.contains('hidden')) {
      if (loadedId !== null) resetEditor();
      return;
    }
    const product = activeProduct();
    if (product && String(product.id) !== loadedId) loadEditor(product);
  });
  observer.observe(modal, {attributes:true, attributeFilter:['class']});
  document.addEventListener('livewire:navigating', () => { observer.disconnect(); clearSizeGuideObjectUrl(); freeUrls(); }, { once:true });
})();
</script>
