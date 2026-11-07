{{-- Seller Product Inspector: premium reference layout, original JS IDs preserved. --}}
<style id="sariProductInspectorCleanResponsiveV3">
@verbatim
/* SARI Product Inspector — simplified, readable and orientation-aware. */
#sellerViewProductModal.sari-pi-overlay {
  padding:18px!important;align-items:center!important;justify-content:center!important;
  overflow:hidden!important;background:rgba(18,25,36,.52)!important;
  backdrop-filter:blur(4px)!important;-webkit-backdrop-filter:blur(4px)!important;
  color:#25303c;font-family:"Poppins",ui-sans-serif,system-ui,sans-serif;
}
#sellerViewProductModal.sari-pi-overlay *,#sellerViewProductModal.sari-pi-overlay *::before,#sellerViewProductModal.sari-pi-overlay *::after{box-sizing:border-box}
#sellerViewProductModal.sari-pi-overlay .sari-pi-shell{
  display:flex!important;flex-direction:column!important;width:min(1120px,100%)!important;
  max-width:1120px!important;max-height:calc(100dvh - 36px)!important;min-height:0!important;
  overflow:hidden!important;border:1px solid #e4e8ed!important;border-radius:20px!important;
  background:#fff!important;box-shadow:0 28px 85px rgba(12,22,36,.24)!important;
  transform:none!important;
}
#sellerViewProductModal .sari-pi-header{
  display:flex;flex:0 0 auto;align-items:center;justify-content:space-between;gap:20px;
  padding:20px 28px;border-bottom:1px solid #e9edf1;background:#fff;
}
#sellerViewProductModal .sari-pi-header h2{margin:0;color:#202631;font-size:26px;font-weight:750;line-height:1.2;letter-spacing:-.035em}
#sellerViewProductModal .sari-pi-close{
  display:grid;flex:0 0 38px;width:38px;height:38px;place-items:center;
  border:1px solid #e4e8ed;border-radius:10px;background:#fff;color:#697687;cursor:pointer;
}
#sellerViewProductModal .sari-pi-close:hover{border-color:#d6dce3;background:#f7f9fb;color:#283341}
#sellerViewProductModal .sari-pi-close svg{width:19px;height:19px}
#sellerViewProductModal .sari-pi-body{
  min-height:0;overflow-y:auto;overscroll-behavior:contain;scrollbar-width:thin;
  scrollbar-color:#c8d1da transparent;padding:0 28px;
}
#sellerViewProductModal .sari-pi-overview{
  display:grid;grid-template-columns:minmax(275px,330px) minmax(0,1fr);
  align-items:start;gap:24px;padding:24px 0;
}
/* Aspect ratio is populated from the real image dimensions (portrait / landscape). */
#sellerViewProductModal #viewProductImageWrap.sari-pi-image-card{
  --sari-pi-image-aspect:1.18;display:grid!important;place-items:center!important;
  position:relative!important;width:100%!important;height:auto!important;
  min-height:235px!important;max-height:410px!important;
  aspect-ratio:var(--sari-pi-image-aspect)!important;
  overflow:hidden!important;border:1px solid #e5e9ee!important;border-radius:14px!important;
  background:#f6f8fa!important;box-shadow:none!important;
}
#sellerViewProductModal #viewProductImage{
  display:block!important;width:100%!important;height:100%!important;
  min-width:0!important;min-height:0!important;object-fit:contain!important;
  object-position:center!important;background:transparent!important;
}
#sellerViewProductModal #viewProductImage.hidden{display:none!important}
#sellerViewProductModal .sari-pi-image-placeholder{
  display:grid;width:100%;height:100%;place-items:center;padding:20px;
  background:#f7f9fb;color:#8e99a6;text-align:center;
}
#sellerViewProductModal .sari-pi-image-placeholder.hidden{display:none!important}
#sellerViewProductModal .sari-pi-image-placeholder svg{width:35px;height:35px;display:block;margin:0 auto 8px}
#sellerViewProductModal .sari-pi-image-placeholder p{margin:0;font-size:13px}
#sellerViewProductModal .sari-pi-summary{display:flex;flex-direction:column;min-width:0}
#sellerViewProductModal .sari-pi-chips{display:flex;align-items:center;flex-wrap:wrap;gap:7px}
#sellerViewProductModal .sari-pi-chips>span,#sellerViewProductModal #viewProductStatusBadge{
  display:inline-flex!important;position:static!important;inset:auto!important;
  min-height:29px!important;align-items:center!important;gap:5px!important;
  padding:5px 11px!important;border:1px solid #eadfc9!important;border-radius:999px!important;
  background:#fffbf2!important;color:#946516!important;font-size:11px!important;
  font-weight:650!important;line-height:1.25!important;white-space:nowrap!important;
  box-shadow:none!important;backdrop-filter:none!important;
}
#sellerViewProductModal .sari-pi-chips>span.hidden{display:none!important}
#sellerViewProductModal #viewProductStatusBadge{border-color:#dce2e8!important;background:#f6f8fa!important;color:#576577!important}
#sellerViewProductModal #viewProductStatusBadge[class*="cce2d4"]{border-color:#cfe6d7!important;background:#eaf8ef!important;color:#247348!important}
#sellerViewProductModal #viewProductStatusBadge[class*="cedfec"]{border-color:#cadff1!important;background:#eef6fd!important;color:#386d96!important}
#sellerViewProductModal #viewProductStatusBadge[class*="efd1d1"],#sellerViewProductModal #viewProductStatusBadge[class*="edcccc"]{border-color:#f0d0d0!important;background:#fff1f1!important;color:#b64d54!important}
#sellerViewProductModal #viewProductName{
  margin:14px 0 0!important;color:#202630!important;font-size:24px!important;
  font-weight:750!important;line-height:1.25!important;letter-spacing:-.025em!important;overflow-wrap:anywhere;
}
#sellerViewProductModal #viewProductBrandLine{
  margin:6px 0 0!important;color:#748191!important;font-size:13px!important;
  line-height:1.5!important;overflow-wrap:anywhere;
}
#sellerViewProductModal .sari-pi-price{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-top:14px}
#sellerViewProductModal #viewProductSalePrice{color:#b3790e!important;font-size:25px!important;line-height:1.2!important;font-weight:800!important}
#sellerViewProductModal #viewProductOriginalPrice{color:#93a0ac!important;font-size:13px!important;text-decoration:line-through!important}
#sellerViewProductModal #viewProductDiscountBadge{display:inline-flex;align-items:center;border-radius:999px;background:#c88912;color:#fff;padding:5px 10px;font-size:11px;font-weight:750}
#sellerViewProductModal #viewProductOriginalPrice.hidden,#sellerViewProductModal #viewProductDiscountBadge.hidden{display:none!important}
/* One quiet metrics surface, no decorative mini-cards or unnecessary icons. */
#sellerViewProductModal .sari-pi-stats{
  display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0;
  margin-top:18px;overflow:hidden;border:1px solid #e5e9ee;border-radius:12px;background:#fff;
}
#sellerViewProductModal .sari-pi-stat{
  display:flex;min-width:0;min-height:68px;align-items:center;
  border:0;border-right:1px solid #eef1f4;border-bottom:1px solid #eef1f4;
  border-radius:0;background:#fff;padding:11px 13px;box-shadow:none;
}
#sellerViewProductModal .sari-pi-stat:nth-child(3n){border-right:0}
#sellerViewProductModal .sari-pi-stat-icon{display:none!important}
#sellerViewProductModal .sari-pi-stat-copy{min-width:0;width:100%}
#sellerViewProductModal .sari-pi-stat-copy span{display:block;color:#7c8998;font-size:11.5px;line-height:1.4;font-weight:500}
#sellerViewProductModal .sari-pi-stat-copy strong{
  display:block;margin-top:4px;color:#2d3744;font-size:13px;line-height:1.45;
  font-weight:700;overflow-wrap:anywhere;white-space:normal;text-overflow:clip;
}
#sellerViewProductModal .sari-pi-stat--status{grid-column:1/-1;min-height:55px;border-bottom:0;border-right:0}
#sellerViewProductModal .sari-pi-stat--status strong{color:#26764f}
#sellerViewProductModal .sari-pi-section{
  display:grid;grid-template-columns:178px minmax(0,1fr);align-items:start;gap:24px;
  min-width:0;padding:20px 0;border-top:1px solid #e9edf1;background:#fff;
}
#sellerViewProductModal .sari-pi-section.hidden{display:none!important}
#sellerViewProductModal .sari-pi-section-head h3{
  margin:0;color:#27313e;font-size:16px;line-height:1.4;font-weight:750;letter-spacing:-.01em;
}
#sellerViewProductModal .sari-pi-section-head p{display:none!important}
#sellerViewProductModal .sari-pi-section-content{min-width:0}
#sellerViewProductModal .sari-pi-section-count{
  display:inline-flex;margin-top:8px;border:1px solid #eddfc2;border-radius:999px;
  background:#fffaf0;padding:5px 9px;color:#97640f;font-size:11px;font-weight:650;
}
#sellerViewProductModal #viewProductGallery{display:flex!important;flex-wrap:wrap!important;gap:10px!important}
#sellerViewProductModal #viewProductGallery>div{
  display:grid!important;width:114px!important;height:94px!important;place-items:center!important;
  aspect-ratio:auto!important;overflow:hidden!important;border:1px solid #e3e8ee!important;
  border-radius:10px!important;background:#f6f8fa!important;box-shadow:none!important;
}
#sellerViewProductModal #viewProductGallery img{display:block!important;width:100%!important;height:100%!important;object-fit:contain!important}
#sellerViewProductModal .sari-pi-description{
  min-height:44px;max-height:none!important;overflow:visible!important;white-space:pre-wrap;
  overflow-wrap:anywhere;border:0;border-radius:0;background:#fff;padding:0;
  color:#485564;font-size:13.5px;line-height:1.75;box-shadow:none;
}
#sellerViewProductModal .sari-pi-information{
  display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 22px;
  border:0;border-radius:0;background:#fff;padding:0;
}
#sellerViewProductModal .sari-pi-information>div{
  display:grid;grid-template-columns:minmax(110px,.78fr) minmax(0,1fr);
  align-items:start;gap:8px;min-width:0;padding:10px 0;border-bottom:1px solid #edf0f3;
}
#sellerViewProductModal .sari-pi-information dt{color:#7b8795;font-size:12px;font-weight:500;line-height:1.5}
#sellerViewProductModal .sari-pi-information dd{
  margin:0;color:#2d3945;font-size:12.5px;font-weight:650;line-height:1.5;overflow-wrap:anywhere;
}
#sellerViewProductModal #viewProductDiscountText{color:#a87313}
#sellerViewProductModal .sari-pi-spec-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
#sellerViewProductModal #viewProductSpecifications [data-view-spec-card]{border:1px solid #e6eaf0!important;border-radius:10px!important;padding:12px!important}
#sellerViewProductModal #viewProductSpecifications [data-view-spec-card] p:first-child{font-size:11.5px!important;color:#748190!important}
#sellerViewProductModal #viewProductSpecifications [data-view-spec-card] p:last-child{font-size:13px!important;color:#303b49!important}
#sellerViewProductModal .sari-pi-table-wrap{
  overflow-x:auto;overflow-y:visible;border:1px solid #e5e9ee;border-radius:12px;background:#fff;scrollbar-width:thin;
}
#sellerViewProductModal .sari-pi-table{width:100%;min-width:670px;border-collapse:collapse;text-align:left}
#sellerViewProductModal .sari-pi-table th{background:#f6f8fa;color:#4d5c6e;font-size:12px;font-weight:700;white-space:nowrap;padding:13px 14px}
#sellerViewProductModal .sari-pi-table td{border-top:1px solid #edf0f3;padding:11px 14px;color:#475362;font-size:12.5px;line-height:1.45}
#sellerViewProductModal .sari-pi-table td:first-child{font-weight:650;color:#303b48}
#sellerViewProductModal .sari-pi-table td:nth-child(4){font-weight:700;color:#a87313}
#sellerViewProductModal .sari-pi-table img{display:block;width:44px;height:44px;object-fit:contain!important;border-radius:7px;background:#f7f8fa}
#sellerViewProductModal .sari-pi-footer{
  display:flex;flex:0 0 auto;align-items:center;justify-content:flex-end;
  min-height:68px;border-top:1px solid #e9edf1;background:#fff;padding:12px 28px;
}
#sellerViewProductModal .sari-pi-footer-note{display:none!important}
#sellerViewProductModal .sari-pi-footer-actions{display:flex;align-items:center;justify-content:flex-end;gap:10px}
#sellerViewProductModal .sari-pi-btn{
  display:inline-flex;min-height:42px;align-items:center;justify-content:center;gap:7px;
  border:1px solid #dce2e8;border-radius:10px;background:#fff;padding:0 19px;
  color:#495465;font-family:inherit;font-size:13px;font-weight:700;cursor:pointer;
  transition:background .15s ease,border-color .15s ease;
}
#sellerViewProductModal .sari-pi-btn:hover{background:#f7f9fb;border-color:#cbd2da}
#sellerViewProductModal .sari-pi-btn--gold{border-color:#c88912;background:#c88912;color:#fff;box-shadow:0 5px 12px rgba(200,137,18,.13)}
#sellerViewProductModal .sari-pi-btn--gold:hover{background:#b97d11;border-color:#b97d11}
#sellerViewProductModal.sari-pi-overlay :is(button,a):focus-visible{outline:2px solid #c88912;outline-offset:3px}
@media(max-width:940px){
  #sellerViewProductModal.sari-pi-overlay{padding:10px!important}
  #sellerViewProductModal.sari-pi-overlay .sari-pi-shell{max-height:calc(100dvh - 20px)!important}
  #sellerViewProductModal .sari-pi-overview{grid-template-columns:minmax(215px,280px) minmax(0,1fr);gap:18px}
  #sellerViewProductModal .sari-pi-stats{grid-template-columns:repeat(2,minmax(0,1fr))}
  #sellerViewProductModal .sari-pi-stat:nth-child(3n){border-right:1px solid #eef1f4}
  #sellerViewProductModal .sari-pi-stat:nth-child(2n){border-right:0}
  #sellerViewProductModal .sari-pi-section{grid-template-columns:150px minmax(0,1fr);gap:16px}
}
@media(max-width:700px){
  #sellerViewProductModal .sari-pi-header{padding:16px 18px}
  #sellerViewProductModal .sari-pi-header h2{font-size:23px}
  #sellerViewProductModal .sari-pi-body{padding:0 18px}
  #sellerViewProductModal .sari-pi-overview{grid-template-columns:1fr;gap:17px;padding:18px 0}
  #sellerViewProductModal #viewProductImageWrap.sari-pi-image-card{width:min(100%,440px)!important;min-height:215px!important;max-height:min(400px,50dvh)!important;margin-inline:auto}
  #sellerViewProductModal .sari-pi-section{grid-template-columns:1fr;gap:12px;padding:18px 0}
  #sellerViewProductModal .sari-pi-section-head h3{font-size:16px}
  #sellerViewProductModal .sari-pi-information{grid-template-columns:1fr}
  #sellerViewProductModal .sari-pi-spec-grid{grid-template-columns:1fr}
  #sellerViewProductModal .sari-pi-footer{padding:11px 18px}
}
@media(max-width:400px){
  #sellerViewProductModal .sari-pi-stats{grid-template-columns:repeat(2,minmax(0,1fr))}
  #sellerViewProductModal .sari-pi-stat{padding:10px}
  #sellerViewProductModal .sari-pi-stat-copy strong{font-size:12px}
  #sellerViewProductModal .sari-pi-footer-actions{width:100%}
  #sellerViewProductModal .sari-pi-btn{flex:1}
  #sellerViewProductModal #viewProductGallery>div{width:94px!important;height:84px!important}
}
@media(prefers-reduced-motion:reduce){#sellerViewProductModal .sari-pi-btn{transition:none}}
@endverbatim
</style>

<div id="sellerViewProductModal" class="sari-pi-overlay fixed inset-0 z-[130] hidden items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="sariPiDialogTitle">
  <div class="sari-pi-shell">
    <header class="sari-pi-header">
      <div>
        <h2 id="sariPiDialogTitle">Product Inspector</h2>
      </div>
      <button type="button" data-close-view class="sari-pi-close" aria-label="Close product inspector">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
      </button>
    </header>

    <div class="sari-pi-body">
      <div class="sari-pi-overview">
        <div id="viewProductImageWrap" class="sari-pi-image-card">
          <img id="viewProductImage" src="" alt="" class="hidden" loading="lazy" decoding="async">
          <div id="viewProductImagePlaceholder" class="sari-pi-image-placeholder">
            <div>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="9" cy="9" r="2"/><path d="m4 19 6-6 4 3 3-4 3 4"/></svg>
              <p>No product image</p>
            </div>
          </div>
        </div>

        <div class="sari-pi-summary">
          <div class="sari-pi-chips">
            <span id="viewProductStatusBadge">Status</span>
            <span id="viewProductCategoryChip">Category</span>
            <span id="viewProductTypeChip">Listing type</span>
            <span id="viewProductShippingChip" class="hidden">
              Free Shipping
            </span>
          </div>
          <h3 id="viewProductName">Product</h3>
          <p id="viewProductBrandLine">No Brand</p>
          <div class="sari-pi-price">
            <strong id="viewProductSalePrice">₱0.00</strong>
            <span id="viewProductOriginalPrice" class="hidden">₱0.00</span>
            <span id="viewProductDiscountBadge" class="hidden">0% OFF</span>
          </div>

          <div class="sari-pi-stats">
            <div class="sari-pi-stat">
              <div class="sari-pi-stat-copy"><span>Created</span><strong id="viewProductCreated">—</strong></div>
            </div>
            <div class="sari-pi-stat">
              <div class="sari-pi-stat-copy"><span>Rating</span><strong id="viewProductRating">—</strong></div>
            </div>
            <div class="sari-pi-stat">
              <div class="sari-pi-stat-copy"><span>Total Stock</span><strong id="viewProductStock">0</strong></div>
            </div>
            <div class="sari-pi-stat">
              <div class="sari-pi-stat-copy"><span>SKU</span><strong id="viewProductSku">—</strong></div>
            </div>
            <div class="sari-pi-stat">
              <div class="sari-pi-stat-copy"><span>Brand</span><strong id="viewProductBrand">—</strong></div>
            </div>
            <div class="sari-pi-stat">
              <div class="sari-pi-stat-copy"><span>Listing Type</span><strong id="viewProductType">—</strong></div>
            </div>
            <div class="sari-pi-stat sari-pi-stat--status">
              <div class="sari-pi-stat-copy"><span>Moderation Status</span><strong id="viewProductStatus">Pending</strong></div>
            </div>
          </div>
        </div>
      </div>

      <section id="viewProductGallerySection" class="sari-pi-section hidden">
        <div class="sari-pi-section-head"><h3>Gallery</h3><p id="viewProductGalleryCount"></p></div>
        <div class="sari-pi-section-content sari-pi-gallery-list"><div id="viewProductGallery"></div></div>
      </section>

      <section class="sari-pi-section">
        <div class="sari-pi-section-head"><h3>Product Description</h3></div>
        <div id="viewProductDescription" class="sari-pi-section-content sari-pi-description">No description has been added for this product yet.</div>
      </section>

      <section class="sari-pi-section">
        <div class="sari-pi-section-head"><h3>Listing Information</h3></div>
        <dl class="sari-pi-section-content sari-pi-information">
          <div><dt>Category</dt><dd id="viewProductCategory">—</dd></div>
          <div><dt>Condition</dt><dd id="viewProductCondition">—</dd></div>
          <div><dt>Package</dt><dd id="viewProductPackage">—</dd></div>
          <div><dt>Preparation</dt><dd id="viewProductPreparation">—</dd></div>
          <div><dt>Low Stock Alert</dt><dd id="viewProductLowStock">—</dd></div>
          <div><dt>Discount</dt><dd id="viewProductDiscountText">—</dd></div>
          <div><dt>Voucher</dt><dd id="viewProductVoucher">—</dd></div>
          <div><dt>Shipping</dt><dd id="viewProductShippingText">—</dd></div>
        </dl>
      </section>

      <section id="viewProductSpecificationsSection" class="sari-pi-section hidden">
        <div class="sari-pi-section-head"><h3>Specifications</h3><span id="viewProductSpecificationCount" class="sari-pi-section-count"></span></div>
        <div id="viewProductSpecifications" class="sari-pi-section-content sari-pi-spec-grid"></div>
      </section>

      <section id="viewProductVariantsSection" class="sari-pi-section hidden">
        <div class="sari-pi-section-head"><h3>Product Variants</h3><span id="viewProductVariantCount" class="sari-pi-section-count"></span></div>
        <div class="sari-pi-section-content sari-pi-table-wrap">
          <table class="sari-pi-table">
            <thead><tr><th>Variant</th><th>Image</th><th>SKU</th><th>Price</th><th>Stock</th></tr></thead>
            <tbody id="viewProductVariants"></tbody>
          </table>
        </div>
      </section>
    </div>

    <footer class="sari-pi-footer">
          <div class="sari-pi-footer-actions">
        <button type="button" data-close-view class="sari-pi-btn">Close</button>
        <button type="button" id="viewProductEditButton" class="sari-pi-btn sari-pi-btn--gold">
          Edit Product
        </button>
      </div>
    </footer>
  </div>
</div>
<script>
(() => {
  const modal = document.getElementById('sellerViewProductModal');
  if (!modal || modal.dataset.sariPiEnhancement === '1') return;
  modal.dataset.sariPiEnhancement = '1';

  const image = modal.querySelector('#viewProductImage');
  const frame = modal.querySelector('#viewProductImageWrap');
  const placeholder = modal.querySelector('#viewProductImagePlaceholder');
  if (!image || !frame) return;

  // Use the real photo dimensions so portrait and landscape images fit without cropping.
  const adjustImage = () => {
    if (!image.naturalWidth || !image.naturalHeight || image.classList.contains('hidden')) {
      frame.style.setProperty('--sari-pi-image-aspect', '1.18');
      return;
    }
    const naturalRatio = image.naturalWidth / image.naturalHeight;
    const displayRatio = Math.min(1.85, Math.max(0.72, naturalRatio));
    frame.style.setProperty('--sari-pi-image-aspect', displayRatio.toFixed(4));
  };

  image.addEventListener('load', adjustImage);
  image.addEventListener('error', adjustImage);
  new MutationObserver(adjustImage).observe(image, {attributes:true,attributeFilter:['src','class']});

  const onShown = () => {
    if (!modal.classList.contains('flex')) return;
    const firstGallery = modal.querySelector('#viewProductGallery img');
    if (placeholder && firstGallery && !image.getAttribute('src')) {
      image.src = firstGallery.getAttribute('src') || '';
      image.alt = firstGallery.alt || 'Product gallery image';
      image.classList.remove('hidden');
      placeholder.classList.add('hidden');
    }
    adjustImage();
    const status = modal.querySelector('#viewProductStatus');
    const label = (status?.textContent || '').trim().toLowerCase();
    if (status) status.style.color = label.includes('approved') ? '#26764f'
      : /rejected|flagged/.test(label) ? '#b64d54' : '#536171';
  };

  new MutationObserver(onShown).observe(modal, {attributes:true,attributeFilter:['class']});
  adjustImage();
})();
</script>
