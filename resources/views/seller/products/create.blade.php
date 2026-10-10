@extends('layouts.seller')

@section('title', 'Add Product — SARI')
@section('page-title', 'Add Product')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/product-create.css') }}?v=20261007-1">
{{-- Cacheable Add Product stylesheet; visual rules remain unchanged. --}}



















@endpush

@section('content')
@php
    $categories = [
        'Electronics',
        'Fashion & Apparel',
        'Beauty & Personal Care',
        'Home & Living',
        'Food & Beverages',
        'Sports & Outdoors',
        'Books & Stationery',
        'Automotive',
        'Baby & Kids',
        'Pet Supplies',
        'Health & Wellness',
        'Toys & Collectibles',
        'Appliances',
        'Others',
    ];
@endphp



<div class="seller-create-page mx-auto w-full max-w-[1440px]">
    <div class="seller-create-topbar">
        <div class="seller-create-heading">
            <div class="seller-create-heading-copy">
                <p class="seller-create-heading-eyebrow">Seller Catalog</p>
                <h1 class="seller-create-heading-title">Add <span>Product</span></h1>
                <a href="{{ route('seller.products.index') }}" class="seller-create-heading-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                    Back to product catalog
                </a>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <div id="productDraftManager" class="seller-draft-manager">
                <button id="productDraftMenuButton" type="button" class="seller-create-button" aria-haspopup="menu" aria-expanded="false">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="M5 5h14v14H5z"></path>
                        <path d="M8 9h8"></path>
                        <path d="M8 13h5"></path>
                    </svg>
                    Drafts
                    <span id="productDraftCount" class="seller-draft-count">0</span>
                    <svg viewBox="0 0 24 24" class="seller-draft-chevron" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path d="m7 10 5 5 5-5"></path>
                    </svg>
                </button>

                <div id="productDraftMenu" class="seller-draft-menu hidden" role="menu">
                    <div class="seller-draft-menu-head">
                        <div>
                            <strong>Saved drafts</strong>
                            <span>Open any saved product and continue editing.</span>
                        </div>
                        <button id="newBlankProduct" type="button" class="seller-draft-new-button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>
                            New
                        </button>
                    </div>

                    <div id="productDraftList" class="seller-draft-list">
                        <div class="seller-draft-empty">No saved drafts yet.</div>
                    </div>
                </div>
            </div>

            <button id="saveProductDraft" type="button" class="seller-create-button">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <path d="M5 4h12l2 2v14H5z"></path>
                    <path d="M8 4v6h8V4"></path>
                    <path d="M8 16h8"></path>
                </svg>
                <span data-draft-button-label>Save as draft</span>
            </button>

            <button id="submitProductReview" type="submit" form="sellerCreateProductForm" class="seller-create-button seller-create-button--primary">
                Submit for review
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m5 12 14-7-5 14-2-5-7-2Z"></path></svg>
            </button>
        </div>
    </div>

    <div
        id="draftSaveFeedback"
        role="status"
        aria-live="polite"
        aria-atomic="true"
        class="seller-draft-feedback"
        aria-hidden="true"
    >
        <div class="seller-draft-feedback-card">
            <div id="draftGoldLoader" class="seller-draft-feedback-loader" aria-hidden="true">
                <span class="seller-draft-feedback-spinner"></span>
            </div>

            <iframe
                id="draftSaveLottie"
                src="about:blank"
                title="Draft saved animation"
                loading="eager"
                scrolling="no"
                allow="autoplay"
                tabindex="-1"
                class="seller-draft-feedback-lottie"
                aria-hidden="true"
            ></iframe>

            <div id="draftSaveFallback" class="seller-draft-feedback-fallback" aria-hidden="true">
                <svg viewBox="0 0 72 72" fill="none" aria-hidden="true">
                    <circle class="seller-draft-fallback-ring" cx="36" cy="36" r="27"></circle>
                    <path class="seller-draft-fallback-check" d="M23 36.5 32 45l18-19"></path>
                </svg>
            </div>

            <div class="seller-draft-feedback-copy">
                <strong id="draftSaveFeedbackTitle">Saving draft</strong>
                <span id="draftSaveFeedbackText">Please wait while your changes are saved.</span>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="seller-inline-alert">
            <strong class="block font-bold">Please check the product information.</strong>
            <ul class="mt-1 list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="seller-create-grid">
        <aside class="seller-create-side">
            <div class="seller-create-side-card">
                <div class="seller-create-side-title">
                    <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#c88912]" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 12h3l2-3 3 7 2-4h6"></path></svg>
                    Suggestions
                </div>
                <p class="px-2 pt-2 text-[8px] leading-4 text-[#818995]">Complete each section to improve listing quality.</p>
                <nav class="seller-create-nav" aria-label="Add product sections">
                    <button type="button" data-create-nav="basic">Basic Information</button>
                    <button type="button" data-create-nav="details">Product Details</button>
                    <button type="button" data-create-nav="sales">Sales Information</button>
                    <button type="button" data-create-nav="shipping">Shipping</button>
                </nav>
            </div>
        </aside>

        <form id="sellerCreateProductForm" method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="seller-create-workspace">
            @csrf
            <input id="sellerCreateDraftId" type="hidden" name="draft_id" value="{{ old('draft_id') }}">
            <input id="sellerCreateHasVariants" type="hidden" name="has_variants" value="{{ old('has_variants', 0) }}">

            <section id="create-basic" data-create-section="basic" class="seller-create-section">
                <div class="seller-create-section-head">
                    <div class="seller-create-section-copy">
                        <h2>Basic information</h2>
                        <p>Core information buyers use to identify and discover your product.</p>
                    </div>
                    <span class="seller-create-rule"></span>
                </div>

                <div class="seller-create-field-grid">
                    <div class="seller-create-span-2">
                        <label class="seller-create-label">Product name <span class="text-[#dc4c4c]">*</span></label>
                        <input id="createProductName" name="name" value="{{ old('name') }}" required maxlength="150" class="seller-create-input" placeholder="e.g. Nordic Solid Wood Dining Chair">
                    </div>
                    <div>
                        <label class="seller-create-label">Category <span class="text-[#dc4c4c]">*</span></label>

                        <div id="createCategoryPicker" class="seller-category-picker">
                            <select id="createProductCategory" name="category" required class="sr-only" tabindex="-1" aria-hidden="true">
                                <option value="">Choose product category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>

                            <button
                                id="createCategoryTrigger"
                                type="button"
                                class="seller-category-trigger"
                                aria-haspopup="listbox"
                                aria-expanded="false"
                                aria-controls="createCategoryMenu"
                            >
                                <span class="seller-category-trigger-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M20 13 11 22l-8-8 9-9h7v7Z"></path>
                                        <circle cx="16" cy="9" r="1.4"></circle>
                                    </svg>
                                </span>
                                <span id="createCategoryTriggerText" class="seller-category-trigger-text">Choose product category</span>
                                <svg class="seller-category-trigger-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                    <path d="m7 10 5 5 5-5"></path>
                                </svg>
                            </button>

                            <div id="createCategoryMenu" class="seller-category-menu hidden" role="listbox">
                                @foreach ($categories as $category)
                                    <button
                                        type="button"
                                        class="seller-category-option"
                                        data-category-option="{{ $category }}"
                                        role="option"
                                        aria-selected="{{ old('category') === $category ? 'true' : 'false' }}"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M20 13 11 22l-8-8 9-9h7v7Z"></path>
                                            <circle cx="16" cy="9" r="1.2"></circle>
                                        </svg>
                                        <span>{{ $category }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="seller-create-label">Brand</label>
                        <input id="createProductBrand" name="brand" value="{{ old('brand') }}" maxlength="120" class="seller-create-input" placeholder="Brand name or No Brand">
                    </div>
                    <div id="createCustomCategoryWrap" class="seller-create-span-2 hidden">
                        <label class="seller-create-label">Custom category</label>
                        <input id="createCustomCategory" name="custom_category" value="{{ old('custom_category') }}" maxlength="100" class="seller-create-input" placeholder="Enter your product category">
                    </div>
                </div>

                <details class="mt-4 rounded-[11px] border border-[#e4e8ed] bg-[#fafbfc]">
                    <summary class="cursor-pointer list-none px-3 py-3 text-[9px] font-semibold text-[#49515b]">More listing information <span class="ml-1 font-normal text-[#8a929c]">SKU, voucher and condition</span></summary>
                    <div class="seller-create-field-grid border-t border-[#e8ebef] p-3">
                        <div>
                            <label class="seller-create-label">Seller SKU</label>
                            <input name="sku" value="{{ old('sku') }}" maxlength="100" class="seller-create-input" placeholder="Optional">
                        </div>
                        <div>
                            <label class="seller-create-label">Voucher code</label>
                            <input name="voucher_code" value="{{ old('voucher_code') }}" maxlength="80" class="seller-create-input" placeholder="Optional">
                        </div>
                        <div>
                            <label class="seller-create-label">Condition</label>
                            <select name="condition" class="seller-create-select">
                                <option value="">Not specified</option>
                                <option value="new" @selected(old('condition') === 'new')>New</option>
                                <option value="like_new" @selected(old('condition') === 'like_new')>Like New</option>
                                <option value="used" @selected(old('condition') === 'used')>Used</option>
                            </select>
                        </div>
                    </div>
                </details>
            </section>

            <section id="create-details" data-create-section="details" class="seller-create-section">
                <div class="seller-create-section-head">
                    <div class="seller-create-section-copy">
                        <h2>Product details</h2>
                        <p>Add clear photos and an accurate description of the product.</p>
                    </div>
                    <span class="seller-create-rule"></span>
                </div>

                <div class="seller-create-field-grid md:grid-cols-[190px_minmax(0,1fr)]">
                    <div>
                        <label class="seller-create-label">Cover image</label>
                        <label
                            id="createCoverDropzone"
                            class="seller-create-upload seller-create-upload--dropzone"
                            tabindex="0"
                            aria-describedby="createCoverDropHelp"
                        >
                            <input id="createCoverImage" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">

                            <span id="createCoverPlaceholder" class="seller-cover-drop-copy">
                                <span class="mx-auto grid h-11 w-11 place-items-center rounded-[11px] border border-[#dce1e7] bg-white text-[#65707c]">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path d="M12 4v10"></path>
                                        <path d="m8.5 7.5 3.5-3.5 3.5 3.5"></path>
                                        <path d="M5 13.5v4A2.5 2.5 0 0 0 7.5 20h9a2.5 2.5 0 0 0 2.5-2.5v-4"></path>
                                    </svg>
                                </span>
                                <strong>Drag &amp; drop cover image</strong>
                                <span class="seller-cover-drop-secondary">or click to browse</span>
                                <span id="createCoverDropHelp" class="seller-cover-drop-meta">JPG, PNG or WEBP · up to 4 MB</span>
                            </span>

                            <img id="createCoverPreview" class="hidden" alt="Cover preview" decoding="async">

                            <span class="seller-cover-change-hint" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                    <path d="M12 4v10"></path>
                                    <path d="m8.5 7.5 3.5-3.5 3.5 3.5"></path>
                                    <path d="M5 13.5v4A2.5 2.5 0 0 0 7.5 20h9a2.5 2.5 0 0 0 2.5-2.5v-4"></path>
                                </svg>
                                Drop or click to change
                            </span>
                        </label>
                    </div>

                    <div>
                        <label class="seller-create-label">Product description</label>
                        <textarea id="createProductDescription" name="description" maxlength="5000" class="seller-create-textarea" placeholder="Describe the product clearly: features, materials, measurements, condition, package inclusions, warranty, or other important information...">{{ old('description') }}</textarea>
                        <div class="mt-1 text-right text-[7px] text-[#949ca6]"><span id="createDescriptionCount">0</span>/5000</div>
                    </div>
                </div>

                <div class="mt-4 border-t border-[#edf0f3] pt-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-[10px] font-semibold text-[#333840]">Product gallery</p>
                            <span id="createGalleryCount" class="seller-gallery-count">0 / 12 images</span>
                        </div>
                        <p class="mt-1 text-[8px] text-[#818995]">Drag images into the gallery first. After that, use the Add images tile whenever you need more.</p>
                    </div>

                    <input id="createGalleryImages" name="gallery_images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden">

                    <div id="createGalleryDropzone" class="seller-gallery-dropzone">
                        <button
                            id="createGalleryEmpty"
                            type="button"
                            class="seller-gallery-drop-empty"
                            aria-label="Choose or drop product gallery images"
                        >
                            <span class="seller-gallery-drop-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M12 4v10"></path>
                                    <path d="m8.5 7.5 3.5-3.5 3.5 3.5"></path>
                                    <path d="M5 13.5v4A2.5 2.5 0 0 0 7.5 20h9a2.5 2.5 0 0 0 2.5-2.5v-4"></path>
                                </svg>
                            </span>
                            <span class="seller-gallery-drop-copy">
                                <strong>Drag &amp; drop gallery images here</strong>
                                <span>or click to browse · JPG, PNG or WEBP · up to 12 images</span>
                            </span>
                        </button>

                        <div id="createGalleryPreview" class="seller-create-gallery hidden"></div>
                    </div>
                </div>
            </section>

            <section id="create-sales" data-create-section="sales" class="seller-create-section">
                <div class="seller-create-section-head">
                    <div class="seller-create-section-copy">
                        <h2>Sales information</h2>
                        <p>Set price, stock, variants and buyer-facing promotions.</p>
                    </div>
                    <span class="seller-create-rule"></span>
                </div>

                <div id="createSimpleInventory" class="seller-sales-base-grid">
                    <div class="seller-sales-base-field">
                        <label class="seller-create-label">Base Price <span class="text-[#dc4c4c]">*</span></label>
                        <div class="seller-create-money-field">
                            <span class="seller-create-money-prefix" aria-hidden="true">₱</span>
                            <input
                                id="createProductPrice"
                                name="price"
                                type="number"
                                min="0"
                                step="0.01"
                                value="{{ old('price') }}"
                                required
                                class="seller-create-money-input"
                                placeholder="0.00"
                                inputmode="decimal"
                            >
                        </div>
                        <p class="seller-create-help">Used as the default price when new variant combinations are generated.</p>
                    </div>

                    <div class="seller-sales-base-field">
                        <label class="seller-create-label">Base Stock <span class="text-[#dc4c4c]">*</span></label>
                        <input
                            id="createProductStock"
                            name="stock"
                            type="number"
                            min="0"
                            value="{{ old('stock') }}"
                            required
                            class="seller-create-input"
                            placeholder="0"
                        >
                        <p class="seller-create-help">Used as the default stock when new variant combinations are generated.</p>
                    </div>

                    <div class="seller-variant-mode-card">
                        <div class="seller-variant-mode-title">
                            <span>Has different options?</span>
                            <span
                                class="seller-variant-info-dot"
                                title="Use variants when buyers need to choose size, color, design or another option."
                                aria-label="Variant option help"
                            >?</span>
                        </div>

                        <div class="seller-variant-mode-actions" role="group" aria-label="Product variant mode">
                            <button
                                id="createVariantModeNo"
                                type="button"
                                class="seller-variant-mode-choice"
                                data-variant-mode-value="0"
                                aria-pressed="{{ old('has_variants') ? 'false' : 'true' }}"
                            >
                                <span class="seller-variant-mode-radio" aria-hidden="true"></span>
                                No
                            </button>

                            <button
                                id="createVariantModeYes"
                                type="button"
                                class="seller-variant-mode-choice"
                                data-variant-mode-value="1"
                                aria-pressed="{{ old('has_variants') ? 'true' : 'false' }}"
                            >
                                <span class="seller-variant-mode-radio" aria-hidden="true"></span>
                                Yes
                            </button>
                        </div>

                        <p class="seller-create-help">Use variants for sizes, colors, designs or other buyer choices.</p>

                        <input
                            id="createVariantsToggle"
                            type="checkbox"
                            class="sr-only"
                            @checked(old('has_variants'))
                        >
                    </div>
                </div>

                <div id="createVariantsArea" class="seller-smart-variants hidden" hidden aria-hidden="true">
                    <section class="seller-variant-options-panel" aria-labelledby="createVariantOptionsTitle">
                        <div class="seller-variant-subhead">
                            <div>
                                <h3 id="createVariantOptionsTitle">Variant Options</h3>
                                <p>Select the choices that apply to your product. Combinations are generated automatically.</p>
                            </div>
                        </div>

                        <div class="seller-variant-option-grid">
                            <div class="seller-variant-option-card" data-variant-option-card="0">
                                <label class="seller-variant-option-name-label" for="createVariantOptionName1">Option 1</label>
                                <input
                                    id="createVariantOptionName1"
                                    type="text"
                                    value="Size"
                                    maxlength="30"
                                    class="seller-variant-option-name"
                                    data-variant-option-name="0"
                                    aria-label="First variant option name"
                                >

                                <div
                                    id="createVariantOptionValues1"
                                    class="seller-variant-option-values"
                                    data-variant-option-values="0"
                                    aria-live="polite"
                                ></div>

                                <div class="seller-variant-option-add">
                                    <input
                                        id="createVariantOptionInput1"
                                        type="text"
                                        maxlength="60"
                                        class="seller-variant-option-value-input"
                                        data-variant-option-input="0"
                                        placeholder="e.g. Small"
                                        aria-label="Add first option value"
                                    >
                                    <button type="button" class="seller-variant-add-value" data-add-variant-option="0">
                                        + Add option
                                    </button>
                                </div>
                            </div>

                            <div class="seller-variant-option-card" data-variant-option-card="1">
                                <label class="seller-variant-option-name-label" for="createVariantOptionName2">Option 2</label>
                                <input
                                    id="createVariantOptionName2"
                                    type="text"
                                    value="Color"
                                    maxlength="30"
                                    class="seller-variant-option-name"
                                    data-variant-option-name="1"
                                    aria-label="Second variant option name"
                                >

                                <div
                                    id="createVariantOptionValues2"
                                    class="seller-variant-option-values"
                                    data-variant-option-values="1"
                                    aria-live="polite"
                                ></div>

                                <div class="seller-variant-option-add">
                                    <input
                                        id="createVariantOptionInput2"
                                        type="text"
                                        maxlength="60"
                                        class="seller-variant-option-value-input"
                                        data-variant-option-input="1"
                                        placeholder="e.g. Black"
                                        aria-label="Add second option value"
                                    >
                                    <button type="button" class="seller-variant-add-value" data-add-variant-option="1">
                                        + Add option
                                    </button>
                                </div>
                            </div>

                            <aside class="seller-variant-combination-summary" aria-live="polite">
                                <div class="seller-variant-summary-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <path d="M12 10v6"></path>
                                        <path d="M12 7.2h.01"></path>
                                    </svg>
                                </div>

                                <div>
                                    <span id="createVariantSummaryEyebrow">Add option values</span>
                                    <strong id="createVariantCombinationSummary">No combinations yet</strong>
                                    <p id="createVariantCombinationHelp">Add a size, color, design or another option to generate variants.</p>
                                </div>
                            </aside>
                        </div>
                    </section>

                    <section class="seller-variants-panel" aria-labelledby="createVariantsTableTitle">
                        <div class="seller-variants-panel-head">
                            <div>
                                <h3 id="createVariantsTableTitle">
                                    Variants
                                    <span id="createVariantCount">0 variants</span>
                                </h3>
                                <p>Each combination can have its own image, SKU, price and stock.</p>
                            </div>

                            <div class="seller-variant-bulk-actions">
                                <button id="applyBasePriceToVariants" type="button" class="seller-variant-bulk-button">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <path d="M12 3 5 10l7 7 7-7-7-7Z"></path>
                                        <path d="M12 8v4"></path>
                                    </svg>
                                    Set price for all variants
                                </button>

                                <button id="applyBaseStockToVariants" type="button" class="seller-variant-bulk-button">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <circle cx="12" cy="12" r="8"></circle>
                                        <path d="M12 8v8"></path>
                                        <path d="M8 12h8"></path>
                                    </svg>
                                    Set stock for all variants
                                </button>
                            </div>
                        </div>

                        <div id="createVariantEmpty" class="seller-variant-empty">
                            Add option values above to generate your variant combinations.
                        </div>

                        <div id="createVariantTableWrap" class="seller-variant-table-wrap hidden">
                            <table class="seller-variant-table seller-smart-variant-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th id="createVariantHeader1">Size</th>
                                        <th id="createVariantHeader2">Color</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="createVariantRows"></tbody>
                            </table>
                        </div>
                    </section>
                </div>


                <div class="seller-create-mobile-preview">
                    <div class="seller-create-mobile-preview-copy">
                        <span class="seller-create-mobile-preview-label">Buyer preview</span>
                        <strong id="mobilePreviewTitle">Product title</strong>
                        <span id="mobilePreviewMeta">Category · ₱0.00</span>
                    </div>
                    <div class="seller-create-mobile-preview-badges">
                        <span id="mobilePreviewDiscount" class="seller-preview-badge seller-preview-badge--gold hidden">0% OFF</span>
                        <span id="mobilePreviewShipping" class="seller-preview-badge seller-preview-badge--green hidden">Free Shipping</span>
                        <span id="mobilePreviewFlash" class="seller-preview-badge seller-preview-badge--blue hidden">Flash Sale</span>
                        <span id="mobilePreviewCOD" class="seller-preview-badge seller-preview-badge--blue hidden">COD</span>
                    </div>
                </div>

                <div class="mt-5 border-t border-[#edf0f3] pt-4">
                    <div class="seller-create-field-grid">
                        <div>
                            <label class="seller-create-label">Discount %</label>
                            <input id="createDiscount" name="discount" type="number" min="0" max="100" step="0.01" value="{{ old('discount', 0) }}" class="seller-create-input" placeholder="0">
                            <p class="seller-create-help">Discount is applied automatically to the buyer price.</p>
                        </div>
                        <div>
                            <label class="seller-create-label-with-icon">
                                <span class="seller-create-plain-icon seller-create-plain-icon--flash" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M13 2 5 14h6l-1 8 8-12h-6l1-8Z"></path>
                                    </svg>
                                </span>
                                <span>Flash Sale end date & time</span>
                            </label>
                            <input id="createFlashSale" name="flash_sale_ends_at" type="datetime-local" value="{{ old('flash_sale_ends_at') }}" class="seller-create-input">
                            <p class="seller-create-help">Countdown begins only after administrator approval.</p>
                        </div>
                    </div>

                    <div class="seller-create-setting-grid">
                        <label class="seller-create-setting-card cursor-pointer">
                            <span class="seller-create-setting-copy">
                                <span class="seller-create-plain-icon seller-create-plain-icon--shipping" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M3 7h11v9H3z"></path>
                                        <path d="M14 10h4l3 3v3h-7z"></path>
                                        <circle cx="7" cy="18" r="1.5"></circle>
                                        <circle cx="18" cy="18" r="1.5"></circle>
                                    </svg>
                                </span>
                                <span>
                                    <strong>Free Shipping</strong>
                                    <span>Show the Free Shipping badge and waive the standard delivery fee when eligible.</span>
                                </span>
                            </span>
                            <span class="shrink-0">
                                <input id="createFreeShipping" name="free_shipping" type="checkbox" value="1" class="peer sr-only" @checked(old('free_shipping'))>
                                <span class="seller-create-toggle"></span>
                            </span>
                        </label>

                        <label class="seller-create-setting-card cursor-pointer">
                            <span class="seller-create-setting-copy">
                                <span class="seller-create-plain-icon seller-create-plain-icon--cod" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <rect x="3.5" y="6" width="17" height="12" rx="2.5"></rect>
                                        <path d="M8 12h8"></path>
                                        <path d="M9 9.5v5"></path>
                                    </svg>
                                </span>
                                <span>
                                    <strong>Cash on Delivery</strong>
                                    <span>Let buyers pay when the order is delivered, if COD is available in checkout.</span>
                                </span>
                            </span>
                            <span class="shrink-0">
                                <input id="createCOD" name="cash_on_delivery" type="checkbox" value="1" class="peer sr-only" @checked(old('cash_on_delivery'))>
                                <span class="seller-create-toggle"></span>
                            </span>
                        </label>
                    </div>

                    <div class="seller-promo-preview">
                        <div class="seller-promo-preview-head">
                            <strong>Live sale preview</strong>
                            <span id="promoPreviewState">No discount</span>
                        </div>
                        <div class="seller-promo-preview-price">
                            <strong id="promoPreviewPrice">₱0.00</strong>
                            <span id="promoPreviewOldPrice" class="hidden">₱0.00</span>
                        </div>
                        <p id="promoPreviewNote" class="seller-promo-preview-note">Variant prices will be shown at their original values.</p>
                    </div>
                </div>
            </section>

            <section id="create-shipping" data-create-section="shipping" class="seller-create-section">
                <div class="seller-create-section-head">
                    <div class="seller-create-section-copy">
                        <h2>Shipping</h2>
                        <p>Provide packaged weight, dimensions and preparation details used for fulfillment.</p>
                    </div>
                    <span class="seller-create-rule"></span>
                </div>

                <div class="seller-create-field-grid seller-create-field-grid--4">
                    <div>
                        <label class="seller-create-label">Package weight</label>
                        <input name="package_weight" type="number" min="0" step="0.001" value="{{ old('package_weight') }}" class="seller-create-input" placeholder="kg">
                    </div>
                    <div>
                        <label class="seller-create-label">Preparation time</label>
                        <input name="preparation_days" type="number" min="0" max="365" value="{{ old('preparation_days') }}" class="seller-create-input" placeholder="days">
                    </div>
                    <div>
                        <label class="seller-create-label">Low stock alert</label>
                        <input name="low_stock_threshold" type="number" min="0" value="{{ old('low_stock_threshold', 5) }}" class="seller-create-input" placeholder="5">
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-[9px] font-semibold text-[#3e4650]">Package dimensions <span class="font-normal text-[#8a929c]">(cm)</span></p>
                    <div class="seller-create-field-grid seller-create-field-grid--3 mt-2">
                        <div><label class="seller-create-label">Length</label><input name="package_length" type="number" min="0" step="0.01" value="{{ old('package_length') }}" class="seller-create-input" placeholder="0"></div>
                        <div><label class="seller-create-label">Width</label><input name="package_width" type="number" min="0" step="0.01" value="{{ old('package_width') }}" class="seller-create-input" placeholder="0"></div>
                        <div><label class="seller-create-label">Height</label><input name="package_height" type="number" min="0" step="0.01" value="{{ old('package_height') }}" class="seller-create-input" placeholder="0"></div>
                    </div>
                </div>

                <div class="mt-5 border-t border-[#edf0f3] pt-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-semibold text-[#333840]">Product specifications</p>
                            <p class="mt-1 text-[8px] text-[#818995]">Add material, model, warranty, capacity or other searchable details.</p>
                        </div>
                        <button id="addSpecificationRow" type="button" class="seller-create-button">+ Add specification</button>
                    </div>
                    <div id="createSpecificationRows"></div>
                    <p id="createSpecificationsEmpty" class="mt-3 rounded-[10px] border border-dashed border-[#d9dee5] bg-[#fafbfc] px-3 py-3 text-center text-[8px] text-[#8b939d]">No specifications added yet.</p>
                </div>
            </section>
        </form>

        <aside class="seller-create-preview">
            <div class="seller-create-preview-card">
                <div class="seller-preview-head">
                    <h3>Preview</h3>
                    <span class="text-[7px] font-semibold text-[#9aa1a9]">BUYER VIEW</span>
                </div>
                <div class="seller-preview-body">
                    <div id="previewImage" class="seller-preview-image">
                        <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="m5 17 5-5 4 4 2-2 3 3"></path></svg>
                    </div>
                    <p id="previewTitle" class="seller-preview-title">Product title</p>
                    <div class="mt-1 text-[8px] text-[#8c949e]"><span id="previewCategory">Category</span></div>
                    <div>
                        <span id="previewPrice" class="seller-preview-price">₱0.00</span>
                        <span id="previewOldPrice" class="seller-preview-old-price hidden">₱0.00</span>
                    </div>
                    <div class="seller-preview-badges">
                        <span id="previewDiscountBadge" class="seller-preview-badge seller-preview-badge--gold hidden">0% OFF</span>
                        <span id="previewShippingBadge" class="seller-preview-badge seller-preview-badge--green hidden">Free Shipping</span>
                        <span id="previewFlashBadge" class="seller-preview-badge seller-preview-badge--blue hidden">Flash Sale</span>
                        <span id="previewCODBadge" class="seller-preview-badge seller-preview-badge--blue hidden">COD</span>
                    </div>
                    <p class="seller-preview-note">For reference only. The final buyer view may vary based on category, promotions and checkout eligibility.</p>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const bootSellerCreateProductPage = function () {
    const form = document.getElementById('sellerCreateProductForm');
    if (!form || form.dataset.bound === '1') return;
    form.dataset.bound = '1';

    window.__SARI_SELLER_CREATE_PRODUCT_ABORT__?.abort();
    const createProductLifecycle = new AbortController();
    window.__SARI_SELLER_CREATE_PRODUCT_ABORT__ = createProductLifecycle;
    const createProductSignal = createProductLifecycle.signal;

    const csrf = form.querySelector('input[name="_token"]')?.value || '';
    const category = document.getElementById('createProductCategory');
    const categoryPicker = document.getElementById('createCategoryPicker');
    const categoryTrigger = document.getElementById('createCategoryTrigger');
    const categoryTriggerText = document.getElementById('createCategoryTriggerText');
    const categoryMenu = document.getElementById('createCategoryMenu');
    const customCategoryWrap = document.getElementById('createCustomCategoryWrap');
    const customCategory = document.getElementById('createCustomCategory');
    const nameInput = document.getElementById('createProductName');
    const description = document.getElementById('createProductDescription');
    const descriptionCount = document.getElementById('createDescriptionCount');
    const coverInput = document.getElementById('createCoverImage');
    const coverPreview = document.getElementById('createCoverPreview');
    const coverPlaceholder = document.getElementById('createCoverPlaceholder');
    const coverDropzone = document.getElementById('createCoverDropzone');
    const galleryInput = document.getElementById('createGalleryImages');
    const galleryPreview = document.getElementById('createGalleryPreview');
    const galleryEmpty = document.getElementById('createGalleryEmpty');
    const galleryDropzone = document.getElementById('createGalleryDropzone');
    const galleryCount = document.getElementById('createGalleryCount');
    const priceInput = document.getElementById('createProductPrice');
    const stockInput = document.getElementById('createProductStock');
    const discountInput = document.getElementById('createDiscount');
    const flashInput = document.getElementById('createFlashSale');
    const freeShipping = document.getElementById('createFreeShipping');
    const codToggle = document.getElementById('createCOD');
    const variantsToggle = document.getElementById('createVariantsToggle');
    const hasVariants = document.getElementById('sellerCreateHasVariants');
    const variantsArea = document.getElementById('createVariantsArea');
    const variantRows = document.getElementById('createVariantRows');
    const variantWrap = document.getElementById('createVariantTableWrap');
    const variantEmpty = document.getElementById('createVariantEmpty');
    const variantCount = document.getElementById('createVariantCount');
    const variantModeNo = document.getElementById('createVariantModeNo');
    const variantModeYes = document.getElementById('createVariantModeYes');
    const variantOptionNameInputs = Array.from(document.querySelectorAll('[data-variant-option-name]'));
    const variantOptionValueContainers = Array.from(document.querySelectorAll('[data-variant-option-values]'));
    const variantOptionValueInputs = Array.from(document.querySelectorAll('[data-variant-option-input]'));
    const variantOptionAddButtons = Array.from(document.querySelectorAll('[data-add-variant-option]'));
    const variantSummaryEyebrow = document.getElementById('createVariantSummaryEyebrow');
    const variantCombinationSummary = document.getElementById('createVariantCombinationSummary');
    const variantCombinationHelp = document.getElementById('createVariantCombinationHelp');
    const variantHeader1 = document.getElementById('createVariantHeader1');
    const variantHeader2 = document.getElementById('createVariantHeader2');
    const applyBasePriceToVariants = document.getElementById('applyBasePriceToVariants');
    const applyBaseStockToVariants = document.getElementById('applyBaseStockToVariants');
    const specRows = document.getElementById('createSpecificationRows');
    const specsEmpty = document.getElementById('createSpecificationsEmpty');
    const submitProductReview = document.getElementById('submitProductReview');
    const saveDraft = document.getElementById('saveProductDraft');
    const draftButtonLabel = saveDraft?.querySelector('[data-draft-button-label]');
    const draftFeedback = document.getElementById('draftSaveFeedback');
    const draftFeedbackTitle = document.getElementById('draftSaveFeedbackTitle');
    const draftFeedbackText = document.getElementById('draftSaveFeedbackText');
    const draftGoldLoader = document.getElementById('draftGoldLoader');
    const draftSaveLottie = document.getElementById('draftSaveLottie');
    const draftSaveFallback = document.getElementById('draftSaveFallback');
    const draftId = document.getElementById('sellerCreateDraftId');
    const draftManager = document.getElementById('productDraftManager');
    const draftMenuButton = document.getElementById('productDraftMenuButton');
    const draftMenu = document.getElementById('productDraftMenu');
    const draftCount = document.getElementById('productDraftCount');
    const draftList = document.getElementById('productDraftList');
    const newBlankProduct = document.getElementById('newBlankProduct');

    const previewTitle = document.getElementById('previewTitle');
    const previewCategory = document.getElementById('previewCategory');
    const previewPrice = document.getElementById('previewPrice');
    const previewOldPrice = document.getElementById('previewOldPrice');
    const previewImage = document.getElementById('previewImage');
    const previewDiscountBadge = document.getElementById('previewDiscountBadge');
    const previewShippingBadge = document.getElementById('previewShippingBadge');
    const previewFlashBadge = document.getElementById('previewFlashBadge');
    const previewCODBadge = document.getElementById('previewCODBadge');
    const promoPreviewState = document.getElementById('promoPreviewState');
    const promoPreviewPrice = document.getElementById('promoPreviewPrice');
    const promoPreviewOldPrice = document.getElementById('promoPreviewOldPrice');
    const promoPreviewNote = document.getElementById('promoPreviewNote');
    const mobilePreviewTitle = document.getElementById('mobilePreviewTitle');
    const mobilePreviewMeta = document.getElementById('mobilePreviewMeta');
    const mobilePreviewDiscount = document.getElementById('mobilePreviewDiscount');
    const mobilePreviewShipping = document.getElementById('mobilePreviewShipping');
    const mobilePreviewFlash = document.getElementById('mobilePreviewFlash');
    const mobilePreviewCOD = document.getElementById('mobilePreviewCOD');

    let variantIndex = 0;
    let specIndex = 0;
    let previewFrame = 0;
    let productSubmitLocked = false;

    const variantOptionGroups = [
        { name: 'Size', values: [] },
        { name: 'Color', values: [] },
    ];

    const suppressedVariantKeys = new Set();
    const initialOldVariants = @json(old('variants', []));

    const queuePreviewUpdate = () => {
        if (previewFrame) return;

        previewFrame = window.requestAnimationFrame(() => {
            previewFrame = 0;
            updatePreview();
        });
    };

    const GALLERY_LIMIT = 12;
    const ACCEPTED_IMAGE_TYPES = new Set([
        'image/jpeg',
        'image/png',
        'image/webp',
    ]);

    let pendingGalleryFiles = [];
    let savedDraftGallery = [];
    let galleryObjectUrls = [];
    let coverObjectUrl = null;
    const variantObjectUrls = new Set();

    const acceptedImageFiles = (files) =>
        Array.from(files || []).filter(file =>
            file instanceof File
            && ACCEPTED_IMAGE_TYPES.has(String(file.type || '').toLowerCase())
        );

    function assignCoverFile(file) {
        if (!coverInput || !file || typeof DataTransfer === 'undefined') return;

        const transfer = new DataTransfer();
        transfer.items.add(file);
        coverInput.files = transfer.files;
        coverInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    const money = (value) => '₱' + Math.max(0, Number(value || 0)).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function syncCategoryPicker() {
        const selected = category?.value || '';
        if (categoryTriggerText) {
            categoryTriggerText.textContent = selected || 'Choose product category';
        }

        categoryMenu?.querySelectorAll('[data-category-option]').forEach(option => {
            const active = option.dataset.categoryOption === selected;
            option.classList.toggle('is-selected', active);
            option.setAttribute('aria-selected', active ? 'true' : 'false');
        });
    }

    function closeCategoryMenu() {
        categoryMenu?.classList.add('hidden');
        categoryTrigger?.setAttribute('aria-expanded', 'false');
    }

    function toggleCategoryMenu() {
        if (!categoryMenu || !categoryTrigger) return;
        const opening = categoryMenu.classList.contains('hidden');
        categoryMenu.classList.toggle('hidden', !opening);
        categoryTrigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
    }

    function updateCustomCategory() {
        const show = category?.value === 'Others';
        customCategoryWrap?.classList.toggle('hidden', !show);
        if (customCategory) customCategory.required = show;
        updatePreview();
    }

    function updateDescriptionCount() {
        if (descriptionCount) descriptionCount.textContent = String(description?.value?.length || 0);
    }

    function updatePreview() {
        const title = (nameInput?.value || '').trim() || 'Product title';
        const categoryText = category?.value === 'Others'
            ? ((customCategory?.value || '').trim() || 'Other category')
            : (category?.value || 'Category');
        const discount = Math.min(100, Math.max(0, Number(discountInput?.value || 0)));
        const hasFlash = Boolean(flashInput?.value);
        const hasFreeShipping = Boolean(freeShipping?.checked);
        const hasCOD = Boolean(codToggle?.checked);
        const variantMode = Boolean(variantsToggle?.checked);

        let base = Number(priceInput?.value || 0);
        if (variantMode) {
            const variantPrices = Array.from(variantRows?.querySelectorAll('input[data-variant-price]') || [])
                .map(input => Number(input.value))
                .filter(value => Number.isFinite(value) && value >= 0);
            if (variantPrices.length) base = Math.min(...variantPrices);
        }

        const finalPrice = discount > 0 ? base * (1 - discount / 100) : base;

        previewTitle.textContent = title;
        previewCategory.textContent = categoryText;
        previewPrice.textContent = money(finalPrice);

        if (discount > 0 && base > 0) {
            previewOldPrice.textContent = money(base);
            previewOldPrice.classList.remove('hidden');
            previewDiscountBadge.textContent = `${discount}% OFF`;
            previewDiscountBadge.classList.remove('hidden');
            if (promoPreviewState) promoPreviewState.textContent = `${discount}% OFF`;
            if (promoPreviewOldPrice) {
                promoPreviewOldPrice.textContent = money(base);
                promoPreviewOldPrice.classList.remove('hidden');
            }
            if (promoPreviewPrice) promoPreviewPrice.textContent = money(finalPrice);
            if (promoPreviewNote) {
                promoPreviewNote.textContent = variantMode
                    ? 'This discount will be applied to the buyer-facing variant prices.'
                    : 'The buyer-facing price is discounted automatically from the original price.';
            }
        } else {
            previewOldPrice.classList.add('hidden');
            previewDiscountBadge.classList.add('hidden');
            if (promoPreviewState) promoPreviewState.textContent = 'No discount';
            if (promoPreviewOldPrice) promoPreviewOldPrice.classList.add('hidden');
            if (promoPreviewPrice) promoPreviewPrice.textContent = money(base);
            if (promoPreviewNote) {
                promoPreviewNote.textContent = variantMode
                    ? 'Variant prices will be shown at their original values.'
                    : 'No sale discount is currently applied to this product.';
            }
        }

        previewShippingBadge.classList.toggle('hidden', !hasFreeShipping);
        previewFlashBadge.classList.toggle('hidden', !hasFlash);
        if (previewCODBadge) previewCODBadge.classList.toggle('hidden', !hasCOD);

        if (mobilePreviewTitle) mobilePreviewTitle.textContent = title;
        if (mobilePreviewMeta) mobilePreviewMeta.textContent = `${categoryText} · ${money(finalPrice)}`;
        if (mobilePreviewDiscount) {
            mobilePreviewDiscount.textContent = `${discount}% OFF`;
            mobilePreviewDiscount.classList.toggle('hidden', !(discount > 0 && base > 0));
        }
        mobilePreviewShipping?.classList.toggle('hidden', !hasFreeShipping);
        mobilePreviewFlash?.classList.toggle('hidden', !hasFlash);
        mobilePreviewCOD?.classList.toggle('hidden', !hasCOD);
    }

    function setCoverPreview(file) {
        if (!file || !previewImage || !coverPreview || !coverPlaceholder) return;

        if (coverObjectUrl) {
            URL.revokeObjectURL(coverObjectUrl);
        }

        coverObjectUrl = URL.createObjectURL(file);
        coverPreview.src = coverObjectUrl;
        coverPreview.classList.remove('hidden');
        coverPlaceholder.classList.add('hidden');
        coverDropzone?.classList.add('has-image');
        previewImage.innerHTML = `<img src="${coverObjectUrl}" alt="Product preview">`;
    }

    function clearGalleryObjectUrls() {
        galleryObjectUrls.forEach(url => URL.revokeObjectURL(url));
        galleryObjectUrls = [];
    }

    function galleryFileKey(file) {
        return [file?.name || '', file?.size || 0, file?.lastModified || 0].join('::');
    }

    function syncGalleryInputFiles() {
        if (!galleryInput || typeof DataTransfer === 'undefined') return;

        const transfer = new DataTransfer();
        pendingGalleryFiles.forEach(file => transfer.items.add(file));
        galleryInput.files = transfer.files;
    }

    const DRAFT_LOTTIE_URL = 'https://lottie.host/embed/909177ca-eabe-470c-8426-ef5b814b2f8c/r2Xp54jI2x.lottie';
    let draftFeedbackTimer = null;
    let draftLottiePreloaded = false;

    function clearDraftFeedbackTimers() {
        window.clearTimeout(draftFeedbackTimer);
        draftFeedbackTimer = null;
    }

    function resetDraftLottie() {
        if (draftSaveLottie) {
            draftSaveLottie.classList.remove('is-playing');
            draftSaveLottie.style.display = 'none';
            draftSaveLottie.setAttribute('aria-hidden', 'true');
        }

        if (draftSaveFallback) {
            draftSaveFallback.classList.remove('is-playing');
            draftSaveFallback.style.display = 'none';
            draftSaveFallback.setAttribute('aria-hidden', 'true');
        }
    }

    function playDraftFallback() {
        if (!draftSaveFallback) return;

        draftSaveFallback.classList.remove('is-playing');
        draftSaveFallback.style.display = 'grid';
        draftSaveFallback.setAttribute('aria-hidden', 'false');

        void draftSaveFallback.offsetWidth;

        window.requestAnimationFrame(() => {
            draftSaveFallback.classList.add('is-playing');
        });
    }

    function hideDraftFallback() {
        if (!draftSaveFallback) return;
        draftSaveFallback.classList.remove('is-playing');
        draftSaveFallback.style.display = 'none';
        draftSaveFallback.setAttribute('aria-hidden', 'true');
    }

    function preloadDraftLottie() {
        if (!draftSaveLottie || draftLottiePreloaded) return;

        draftLottiePreloaded = true;
        draftSaveLottie.classList.remove('is-playing');
        draftSaveLottie.style.display = 'block';
        draftSaveLottie.style.position = 'fixed';
        draftSaveLottie.style.left = '-9999px';
        draftSaveLottie.style.top = '-9999px';
        draftSaveLottie.style.opacity = '0';
        draftSaveLottie.style.pointerEvents = 'none';
        draftSaveLottie.setAttribute('aria-hidden', 'true');

        draftSaveLottie.src = DRAFT_LOTTIE_URL;
    }

    function playDraftLottie(onReady = null) {
        if (!draftSaveLottie) {
            if (typeof onReady === 'function') onReady(false);
            return;
        }

        let settled = false;
        let fallbackTimer = null;

        const finish = (loaded) => {
            if (settled) return;
            settled = true;

            if (fallbackTimer) {
                window.clearTimeout(fallbackTimer);
                fallbackTimer = null;
            }

            draftSaveLottie.removeEventListener('load', handleLoad);

            if (typeof onReady === 'function') {
                onReady(Boolean(loaded));
            }
        };

        const revealLottie = () => {
            draftSaveLottie.style.position = 'static';
            draftSaveLottie.style.left = '';
            draftSaveLottie.style.top = '';
            draftSaveLottie.style.pointerEvents = 'none';
            draftSaveLottie.style.display = 'block';
            draftSaveLottie.style.opacity = '';
            draftSaveLottie.setAttribute('aria-hidden', 'false');

            window.requestAnimationFrame(() => {
                draftSaveLottie.classList.add('is-playing');
            });
        };

        const handleLoad = () => {
            revealLottie();
            finish(true);
        };

        draftSaveLottie.classList.remove('is-playing');
        draftSaveLottie.addEventListener('load', handleLoad, { once: true });

        // Restart the already-warmed Lottie document.
        draftSaveLottie.src = 'about:blank';

        window.requestAnimationFrame(() => {
            draftSaveLottie.src = DRAFT_LOTTIE_URL;
        });

        // If the remote host is blocked/slow, the inline fallback stays visible.
        fallbackTimer = window.setTimeout(() => {
            finish(false);
        }, 900);
    }

    function hideDraftFeedback() {
        clearDraftFeedbackTimers();
        if (!draftFeedback) return;

        draftFeedback.classList.remove('is-visible', 'is-success', 'is-error');
        draftFeedback.setAttribute('aria-hidden', 'true');

        if (draftGoldLoader) draftGoldLoader.style.display = 'grid';
        resetDraftLottie();
    }

    function showDraftSaving(title = 'Saving draft', message = 'Please wait while your changes are saved.') {
        if (!draftFeedback || !draftFeedbackTitle || !draftFeedbackText) return;

        clearDraftFeedbackTimers();
        resetDraftLottie();

        draftFeedback.classList.remove('is-success', 'is-error');
        draftFeedback.classList.add('is-visible');
        draftFeedback.setAttribute('aria-hidden', 'false');

        if (draftGoldLoader) draftGoldLoader.style.display = 'grid';

        draftFeedbackTitle.textContent = title;
        draftFeedbackText.textContent = message;
    }

    function showDraftSaved(title = 'Draft saved', message = 'Your product draft has been saved.', autoHideMs = 2000) {
        if (!draftFeedback || !draftFeedbackTitle || !draftFeedbackText) return;

        clearDraftFeedbackTimers();

        draftFeedback.classList.remove('is-error');
        draftFeedback.classList.add('is-visible', 'is-success');
        draftFeedback.setAttribute('aria-hidden', 'false');

        if (draftGoldLoader) {
            draftGoldLoader.style.display = 'none';
        }

        draftFeedbackTitle.textContent = title;
        draftFeedbackText.textContent = message;

        // Always show a smooth success animation immediately.
        playDraftFallback();

        // Swap to the real Lottie only when it genuinely finishes loading.
        playDraftLottie((loaded) => {
            if (loaded) {
                hideDraftFallback();
            }
        });

        // Success feedback stays on screen for 2 seconds.
        if (autoHideMs > 0) {
            draftFeedbackTimer = window.setTimeout(hideDraftFeedback, autoHideMs);
        }
    }

    function showDraftError(title = 'Draft not saved', message = 'Please try again.', autoHideMs = 1600) {
        if (!draftFeedback || !draftFeedbackTitle || !draftFeedbackText) return;

        clearDraftFeedbackTimers();
        resetDraftLottie();

        draftFeedback.classList.remove('is-success');
        draftFeedback.classList.add('is-visible', 'is-error');
        draftFeedback.setAttribute('aria-hidden', 'false');

        if (draftGoldLoader) draftGoldLoader.style.display = 'none';

        draftFeedbackTitle.textContent = title;
        draftFeedbackText.textContent = message;

        if (autoHideMs > 0) {
            draftFeedbackTimer = window.setTimeout(hideDraftFeedback, autoHideMs);
        }
    }

    function showDraftFeedback(state = 'saving', title = '', message = '', autoHideMs = 0) {
        if (state === 'saved') {
            showDraftSaved(
                title || 'Draft saved',
                message || 'Your product draft has been saved.',
                autoHideMs || 2000
            );
            return;
        }

        if (state === 'error') {
            showDraftError(
                title || 'Draft not saved',
                message || 'Please try again.',
                autoHideMs || 1600
            );
            return;
        }

        showDraftSaving(
            title || 'Saving draft',
            message || 'Please wait while your changes are saved.'
        );
    }

    function setDraftStatus(message = '', state = '') {
        if (!message) {
            hideDraftFeedback();
            return;
        }

        if (state === 'loading') {
            showDraftSaving('Saving draft', message);
            return;
        }

        if (state === 'success') {
            showDraftSaved('Draft saved', message, 2000);
            return;
        }

        if (state === 'error') {
            showDraftError('Draft not saved', message, 1600);
        }
    }

    function showReviewSubmitSuccess() {
        return new Promise(resolve => {
            if (!draftFeedback || !draftFeedbackTitle || !draftFeedbackText) {
                resolve();
                return;
            }

            clearDraftFeedbackTimers();

            draftFeedback.classList.remove('is-error');
            draftFeedback.classList.add('is-visible', 'is-success');
            draftFeedback.setAttribute('aria-hidden', 'false');

            if (draftGoldLoader) {
                draftGoldLoader.style.display = 'none';
            }

            draftFeedbackTitle.textContent = 'Submitted for review';
            draftFeedbackText.textContent = 'Your product was received and is ready for SARI screening.';

            playDraftLottie();

            draftFeedbackTimer = window.setTimeout(() => {
                hideDraftFeedback();
                resolve();
            }, 1400);
        });
    }

    window.__SARI_PRODUCT_REVIEW_FEEDBACK__ = {
        start() {
            showDraftSaving(
                'Submitting for review',
                'Please wait while your product is securely submitted.'
            );
        },

        success() {
            return showReviewSubmitSuccess();
        },

        hide() {
            hideDraftFeedback();
        }
    };


    function renderGallery() {
        if (!galleryPreview || !galleryEmpty) return;

        clearGalleryObjectUrls();
        galleryPreview.innerHTML = '';

        const total = savedDraftGallery.length + pendingGalleryFiles.length;
        const hasImages = total > 0;

        galleryEmpty.classList.toggle('hidden', hasImages);
        galleryPreview.classList.toggle('hidden', !hasImages);
        galleryDropzone?.setAttribute('data-has-images', hasImages ? '1' : '0');

        if (galleryCount) {
            galleryCount.textContent = `${total} / ${GALLERY_LIMIT} images`;
        }

        savedDraftGallery.forEach((item, index) => {
            if (!item?.url) return;

            const node = document.createElement('div');
            node.className = 'seller-create-gallery-thumb';
            node.innerHTML = `
                <img src="${item.url}" alt="Saved gallery image ${index + 1}">
                <span class="seller-gallery-saved-badge">Saved draft</span>
            `;
            galleryPreview.appendChild(node);
        });

        pendingGalleryFiles.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            galleryObjectUrls.push(url);

            const node = document.createElement('div');
            node.className = 'seller-create-gallery-thumb';
            node.innerHTML = `
                <img src="${url}" alt="Selected gallery image ${index + 1}">
                <button type="button" class="seller-gallery-remove" data-remove-pending-gallery="${index}" aria-label="Remove selected image">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M6 6l12 12"></path>
                        <path d="M18 6 6 18"></path>
                    </svg>
                </button>
            `;
            galleryPreview.appendChild(node);
        });

        if (hasImages && total < GALLERY_LIMIT) {
            const addTile = document.createElement('button');
            addTile.type = 'button';
            addTile.className = 'seller-gallery-add-tile';
            addTile.setAttribute('data-add-gallery-images', '');
            addTile.setAttribute('aria-label', 'Add more product gallery images');
            addTile.innerHTML = `
                <span class="seller-gallery-add-tile-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                </span>
                <strong>Add images</strong>
                <span>${GALLERY_LIMIT - total} slots left</span>
            `;
            galleryPreview.appendChild(addTile);
        }
    }

    function addGalleryFiles(files) {
        const incoming = acceptedImageFiles(files);
        if (!incoming.length) return;

        const existingKeys = new Set(pendingGalleryFiles.map(galleryFileKey));
        const availableSlots = Math.max(0, GALLERY_LIMIT - savedDraftGallery.length - pendingGalleryFiles.length);
        let added = 0;

        for (const file of incoming) {
            if (added >= availableSlots) break;

            const key = galleryFileKey(file);
            if (existingKeys.has(key)) continue;

            pendingGalleryFiles.push(file);
            existingKeys.add(key);
            added++;
        }

        syncGalleryInputFiles();
        renderGallery();

        if (incoming.length > added) {
            setDraftStatus(`Gallery limit is ${GALLERY_LIMIT} images.`, 'error');
            window.setTimeout(() => {
                if (draftStatus?.textContent?.includes('Gallery limit')) setDraftStatus('');
            }, 2600);
        }
    }

    galleryPreview?.addEventListener('click', event => {
        const addButton = event.target.closest('[data-add-gallery-images]');
        if (addButton) {
            galleryInput?.click();
            return;
        }

        const button = event.target.closest('[data-remove-pending-gallery]');
        if (!button) return;

        const index = Number(button.dataset.removePendingGallery);
        if (!Number.isInteger(index) || index < 0 || index >= pendingGalleryFiles.length) return;

        pendingGalleryFiles.splice(index, 1);
        syncGalleryInputFiles();
        renderGallery();
    });

    galleryEmpty?.addEventListener('click', () => {
        galleryInput?.click();
    });

    async function savedGalleryAsFiles() {
        const files = [];

        for (let index = 0; index < savedDraftGallery.length; index++) {
            const item = savedDraftGallery[index];
            if (!item?.url) continue;

            const response = await fetch(item.url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store',
            });

            if (!response.ok) {
                throw new Error('Unable to preserve one of the saved draft images.');
            }

            const blob = await response.blob();
            const extension = blob.type === 'image/png'
                ? 'png'
                : (blob.type === 'image/webp' ? 'webp' : 'jpg');

            files.push(new File(
                [blob],
                `saved-gallery-${index + 1}.${extension}`,
                { type: blob.type || 'image/jpeg' }
            ));
        }

        return files;
    }

    function normalizeVariantText(value) {
        return String(value ?? '')
            .trim()
            .replace(/\s+/g, ' ');
    }

    function escapeVariantHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function isColorVariantGroup(groupIndex) {
        const name =
            normalizeVariantText(
                variantOptionGroups[groupIndex]?.name
            ).toLowerCase();

        return [
            'color',
            'colour',
            'colors',
            'colours',
        ].includes(name);
    }

    function variantColorHex(value) {
        const normalized =
            normalizeVariantText(value)
                .toLowerCase();

        const colors = {
            'black': '#202124',
            'white': '#F8F8F6',
            'blue': '#315FC7',
            'navy': '#1F3A68',
            'navy blue': '#1F3A68',
            'royal blue': '#2F5BD3',
            'sky blue': '#66B7E8',
            'light blue': '#9FD3F2',
            'dark blue': '#244A8F',
            'red': '#D64545',
            'dark red': '#A92D2D',
            'green': '#2E9B57',
            'emerald': '#1F9D68',
            'forest green': '#237447',
            'dark green': '#24633F',
            'light green': '#8FCF9E',
            'mint': '#8FD7B5',
            'mint green': '#8FD7B5',
            'olive': '#808B45',
            'lime': '#9ACB45',
            'yellow': '#E8C547',
            'gold': '#D5A52D',
            'orange': '#E58A32',
            'purple': '#7155B7',
            'violet': '#7D59C6',
            'lavender': '#B9A7E7',
            'pink': '#E58AAA',
            'hot pink': '#E35C98',
            'magenta': '#C34A9A',
            'brown': '#8A5B3F',
            'beige': '#D7C4A1',
            'cream': '#F2E6C9',
            'ivory': '#F5F1DF',
            'gray': '#98A2B3',
            'grey': '#98A2B3',
            'silver': '#BFC5CC',
            'charcoal': '#4A5058',
            'teal': '#3A9692',
            'cyan': '#45B8C8',
            'turquoise': '#42B9B3',
            'maroon': '#7E3140',
            'burgundy': '#7A2E3B',
            'peach': '#EBA783',
            'coral': '#E77E72',
            'khaki': '#B7A77A',
        };

        return colors[normalized] || '#D0D5DD';
    }

    function isLightVariantColor(value) {
        return [
            'white',
            'cream',
            'ivory',
            'beige',
            'yellow',
            'light blue',
            'silver',
        ].includes(
            normalizeVariantText(value).toLowerCase()
        );
    }

    function variantColorDotMarkup(value, extraClass = '') {
        const color =
            variantColorHex(value);

        const light =
            isLightVariantColor(value)
                ? 'true'
                : 'false';

        return `
            <span
                class="seller-variant-color-dot ${extraClass}"
                style="--variant-swatch:${color}"
                data-light-swatch="${light}"
                aria-hidden="true"
            ></span>
        `;
    }

    function refreshVariantColorSwatches(scope = document) {
        scope
            .querySelectorAll(
                '[data-variant-color-select-wrap]'
            )
            .forEach(wrapper => {
                const select =
                    wrapper.querySelector(
                        '[data-variant-option-select]'
                    );

                const dot =
                    wrapper.querySelector(
                        '[data-variant-color-dot]'
                    );

                const label =
                    wrapper.querySelector(
                        '[data-variant-color-label]'
                    );

                if (!select || !dot) return;

                const value =
                    normalizeVariantText(
                        select.value
                    );

                dot.style.setProperty(
                    '--variant-swatch',
                    variantColorHex(value)
                );

                dot.dataset.lightSwatch =
                    isLightVariantColor(value)
                        ? 'true'
                        : 'false';

                if (label) {
                    label.textContent =
                        value || 'Select color';
                }

                wrapper
                    .querySelectorAll(
                        '[data-variant-color-choice]'
                    )
                    .forEach(choice => {
                        const selected =
                            normalizeVariantText(
                                choice.dataset.variantColorChoice
                            ).toLowerCase()
                            === value.toLowerCase();

                        choice.setAttribute(
                            'aria-selected',
                            selected ? 'true' : 'false'
                        );
                    });
            });
    }

    function restoreVariantColorMenu(menu) {
        if (!menu) return;

        const owner =
            menu.__variantColorOwner;

        menu.hidden = true;
        menu.classList.remove(
            'seller-variant-color-menu--portal'
        );

        menu.style.removeProperty('top');
        menu.style.removeProperty('left');
        menu.style.removeProperty('right');
        menu.style.removeProperty('bottom');
        menu.style.removeProperty('width');
        menu.style.removeProperty('max-height');

        if (owner?.isConnected) {
            owner.appendChild(menu);

            owner
                .querySelector(
                    '[data-variant-color-trigger]'
                )
                ?.setAttribute(
                    'aria-expanded',
                    'false'
                );
        }

        menu.__variantColorOwner = null;
    }

    function closeVariantColorMenus(except = null) {
        document
            .querySelectorAll(
                '[data-variant-color-menu]'
            )
            .forEach(menu => {
                if (menu === except) return;
                restoreVariantColorMenu(menu);
            });
    }

    function positionVariantColorMenu(
        trigger,
        menu
    ) {
        if (!trigger || !menu) return;

        const rect =
            trigger.getBoundingClientRect();

        const viewportGap = 10;
        const menuGap = 5;
        const preferredHeight = 210;

        const roomBelow =
            window.innerHeight
            - rect.bottom
            - viewportGap;

        const roomAbove =
            rect.top
            - viewportGap;

        const openAbove =
            roomBelow < 150
            && roomAbove > roomBelow;

        const availableHeight =
            Math.max(
                96,
                Math.min(
                    preferredHeight,
                    openAbove
                        ? roomAbove - menuGap
                        : roomBelow - menuGap
                )
            );

        const width =
            Math.max(
                150,
                rect.width
            );

        const left =
            Math.min(
                Math.max(
                    viewportGap,
                    rect.left
                ),
                Math.max(
                    viewportGap,
                    window.innerWidth
                    - width
                    - viewportGap
                )
            );

        menu.style.setProperty(
            'width',
            `${width}px`,
            'important'
        );

        menu.style.setProperty(
            'left',
            `${left}px`,
            'important'
        );

        menu.style.setProperty(
            'max-height',
            `${availableHeight}px`,
            'important'
        );

        if (openAbove) {
            menu.style.setProperty(
                'top',
                'auto',
                'important'
            );

            menu.style.setProperty(
                'bottom',
                `${Math.max(
                    viewportGap,
                    window.innerHeight
                    - rect.top
                    + menuGap
                )}px`,
                'important'
            );
        } else {
            menu.style.setProperty(
                'bottom',
                'auto',
                'important'
            );

            menu.style.setProperty(
                'top',
                `${Math.min(
                    window.innerHeight
                    - viewportGap
                    - availableHeight,
                    rect.bottom + menuGap
                )}px`,
                'important'
            );
        }
    }

    function openVariantColorMenu(
        wrapper,
        trigger,
        menu
    ) {
        if (!wrapper || !trigger || !menu) {
            return;
        }

        closeVariantColorMenus(menu);

        menu.__variantColorOwner = wrapper;
        document.body.appendChild(menu);

        menu.classList.add(
            'seller-variant-color-menu--portal'
        );

        menu.hidden = false;

        trigger.setAttribute(
            'aria-expanded',
            'true'
        );

        positionVariantColorMenu(
            trigger,
            menu
        );
    }


    function variantKey(option1 = '', option2 = '') {
        return [
            normalizeVariantText(option1).toLowerCase(),
            normalizeVariantText(option2).toLowerCase(),
        ].join('|||');
    }

    function activeVariantGroups() {
        return variantOptionGroups
            .map((group, index) => ({
                index,
                name: normalizeVariantText(group.name) || `Option ${index + 1}`,
                values: Array.from(new Set(
                    (group.values || [])
                        .map(normalizeVariantText)
                        .filter(Boolean)
                )),
            }))
            .filter(group => group.values.length > 0);
    }

    function syncVariantModeButtons() {
        const active = Boolean(variantsToggle?.checked);
        variantModeNo?.setAttribute('aria-pressed', active ? 'false' : 'true');
        variantModeYes?.setAttribute('aria-pressed', active ? 'true' : 'false');
    }

    function syncVariantMode() {
        const active = Boolean(variantsToggle?.checked);

        if (hasVariants) {
            hasVariants.value = active ? '1' : '0';
        }

        if (variantsArea) {
            /*
             * Strict visibility rule:
             * Variant Options + Variants are visible ONLY when Yes is active.
             * Inline !important prevents older page CSS from accidentally
             * forcing the smart variant area open.
             */
            variantsArea.classList.toggle('hidden', !active);
            variantsArea.hidden = !active;
            variantsArea.setAttribute(
                'aria-hidden',
                active ? 'false' : 'true'
            );

            variantsArea.style.setProperty(
                'display',
                active ? 'grid' : 'none',
                'important'
            );

            variantsArea
                .querySelectorAll('[name^="variants["]')
                .forEach(field => {
                    field.disabled = !active;
                });
        }

        /*
         * Keep Base Price / Base Stock editable in variant mode because they
         * are now the defaults used when combinations are generated.
         * They are still valid existing Product form fields.
         */
        if (priceInput) {
            priceInput.disabled = false;
            priceInput.required = true;
        }

        if (stockInput) {
            stockInput.disabled = false;
            stockInput.required = true;
        }

        syncVariantModeButtons();

        if (active) {
            renderVariantOptionBuilder();

            if (!variantRows?.children.length) {
                regenerateVariantCombinations();
            }
        }

        updateVariantCount();
        updatePreview();
    }

    function syncVariantOptionNames() {
        variantOptionNameInputs.forEach((input, index) => {
            if (!variantOptionGroups[index]) return;

            const value = normalizeVariantText(input.value);
            variantOptionGroups[index].name =
                value || `Option ${index + 1}`;
        });

        if (variantHeader1) {
            variantHeader1.textContent =
                variantOptionGroups[0]?.name || 'Option 1';
        }

        if (variantHeader2) {
            variantHeader2.textContent =
                variantOptionGroups[1]?.name || 'Option 2';
        }

        variantRows?.querySelectorAll('tr').forEach(syncVariantOptions);
        updateVariantCombinationSummary();

        /*
         * If an option is renamed to/from Color, rebuild combination rows so
         * the appropriate swatch control is applied.
         */
        if (variantsToggle?.checked) {
            regenerateVariantCombinations(true);
        }
    }

    function renderVariantOptionBuilder() {
        variantOptionNameInputs.forEach((input, index) => {
            const group = variantOptionGroups[index];
            if (!group) return;

            if (document.activeElement !== input) {
                input.value =
                    normalizeVariantText(group.name)
                    || `Option ${index + 1}`;
            }
        });

        variantOptionValueContainers.forEach((container, index) => {
            const group = variantOptionGroups[index];
            if (!group) return;

            container.innerHTML = '';

            if (!group.values.length) {
                const empty = document.createElement('span');
                empty.className = 'text-[7.5px] text-[#a0a8b3]';
                empty.textContent = 'No values added yet.';
                container.appendChild(empty);
                return;
            }

            group.values.forEach(value => {
                const chip = document.createElement('span');

                const colorGroup =
                    isColorVariantGroup(index);

                chip.className =
                    colorGroup
                        ? 'seller-variant-value-chip seller-variant-value-chip--color'
                        : 'seller-variant-value-chip';

                chip.innerHTML = `
                    ${colorGroup ? variantColorDotMarkup(value) : ''}
                    <span>${escapeVariantHtml(value)}</span>
                    <button
                        type="button"
                        data-remove-variant-option="${index}"
                        data-variant-option-value="${escapeVariantHtml(value)}"
                        aria-label="Remove ${escapeVariantHtml(value)}"
                    >×</button>
                `;
                container.appendChild(chip);
            });
        });

        syncVariantOptionNames();
        updateVariantCombinationSummary();
    }

    function variantColorChoicesMarkup(values, currentValue = '') {
        const current =
            normalizeVariantText(currentValue);

        return values.map(value => {
            const safe =
                escapeVariantHtml(value);

            const isSelected =
                normalizeVariantText(value).toLowerCase()
                === current.toLowerCase();

            return `
                <button
                    type="button"
                    class="seller-variant-color-choice"
                    data-variant-color-choice="${safe}"
                    role="option"
                    aria-selected="${isSelected ? 'true' : 'false'}"
                >
                    ${variantColorDotMarkup(value)}
                    <span>${safe}</span>

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                        aria-hidden="true"
                    >
                        <path d="m7 12 3 3 7-7"></path>
                    </svg>
                </button>
            `;
        }).join('');
    }

    function refreshVariantColorControl(row) {
        const wrapper =
            row?.querySelector(
                '[data-variant-color-select-wrap]'
            );

        if (!wrapper) {
            return;
        }

        const select =
            wrapper.querySelector(
                '[data-variant-option-select="1"]'
            );

        const menu =
            wrapper.querySelector(
                '[data-variant-color-menu]'
            );

        const trigger =
            wrapper.querySelector(
                '[data-variant-color-trigger]'
            );

        if (!select || !menu || !trigger) {
            return;
        }

        const colors =
            variantOptionGroups[1]?.values || [];

        const previous =
            normalizeVariantText(
                select.value
            );

        const nextValue =
            colors.some(
                color =>
                    normalizeVariantText(color).toLowerCase()
                    === previous.toLowerCase()
            )
                ? previous
                : normalizeVariantText(
                    colors[0] || ''
                );

        select.innerHTML =
            colors.length
                ? colors.map(color => {
                    const safe =
                        escapeVariantHtml(color);

                    return `
                        <option
                            value="${safe}"
                            ${normalizeVariantText(color) === nextValue ? 'selected' : ''}
                        >${safe}</option>
                    `;
                }).join('')
                : '<option value="">—</option>';

        select.disabled = !colors.length;
        select.value = nextValue;

        menu.innerHTML =
            variantColorChoicesMarkup(
                colors,
                nextValue
            );

        trigger.disabled = !colors.length;

        if (!colors.length) {
            menu.hidden = true;
            trigger.setAttribute(
                'aria-expanded',
                'false'
            );
        }

        refreshVariantColorSwatches(row);
        syncVariantOptions(row);
    }

    function refreshAllVariantColorControls() {
        variantRows
            ?.querySelectorAll('tr')
            .forEach(row => {
                refreshVariantColorControl(row);
            });

        updateVariantCombinationSummary();
        updatePreview();
    }

    function addVariantOptionValue(groupIndex, rawValue) {
        const group = variantOptionGroups[groupIndex];
        if (!group) return false;

        const value = normalizeVariantText(rawValue);
        if (!value) return false;

        const exists = group.values.some(
            current =>
                normalizeVariantText(current).toLowerCase()
                === value.toLowerCase()
        );

        if (exists) return false;

        group.values.push(value);
        suppressedVariantKeys.clear();
        renderVariantOptionBuilder();

        /*
         * Variant rows use a true Cartesian product:
         * Size × Color. Adding either option group recalculates the rows.
         */
        regenerateVariantCombinations();

        const input = variantOptionValueInputs[groupIndex];
        if (input) input.value = '';

        return true;
    }

    function removeVariantOptionValue(groupIndex, rawValue) {
        const group = variantOptionGroups[groupIndex];
        if (!group) return;

        const target = normalizeVariantText(rawValue).toLowerCase();

        group.values = group.values.filter(
            value =>
                normalizeVariantText(value).toLowerCase()
                !== target
        );

        suppressedVariantKeys.clear();
        renderVariantOptionBuilder();
        regenerateVariantCombinations();
    }

    function variantCombinations() {
        const sizes =
            (variantOptionGroups[0]?.values || [])
                .map(normalizeVariantText)
                .filter(Boolean);

        const colors =
            (variantOptionGroups[1]?.values || [])
                .map(normalizeVariantText)
                .filter(Boolean);

        /*
         * Size is required before variant rows exist.
         * Once Size exists:
         * - no Color yet -> one row per Size
         * - one or more Colors -> Size × Color combinations
         */
        if (!sizes.length) {
            return [];
        }

        const colorValues =
            colors.length
                ? colors
                : [''];

        const combinations = [];

        sizes.forEach(size => {
            colorValues.forEach(color => {
                combinations.push({
                    option1: size,
                    option2: color,
                });
            });
        });

        return combinations;
    }


    function updateVariantCombinationSummary() {
        const sizeCount =
            variantOptionGroups[0]?.values?.length || 0;

        const colorCount =
            variantOptionGroups[1]?.values?.length || 0;

        const combinationCount =
            variantCombinations().length;

        if (
            !variantSummaryEyebrow
            || !variantCombinationSummary
            || !variantCombinationHelp
        ) {
            return;
        }

        if (sizeCount < 1) {
            variantSummaryEyebrow.textContent =
                colorCount > 0
                    ? `${colorCount} color ${colorCount === 1 ? 'option' : 'options'} saved`
                    : 'Add option values';

            variantCombinationSummary.textContent =
                'Add at least one Size to create variants';

            variantCombinationHelp.textContent =
                colorCount > 0
                    ? 'Your colors are saved. Add a Size to generate Size × Color combinations.'
                    : 'Add Size values first, then add Colors to generate every combination.';

            return;
        }

        if (colorCount > 0) {
            variantSummaryEyebrow.textContent =
                `You've configured 2 option groups`;

            variantCombinationSummary.textContent =
                `${sizeCount} ${sizeCount === 1 ? 'size' : 'sizes'} × `
                + `${colorCount} ${colorCount === 1 ? 'color' : 'colors'} = `
                + `${combinationCount} ${combinationCount === 1 ? 'variant' : 'variants'}`;

            variantCombinationHelp.textContent =
                'Every Size is combined with every Color automatically. Each row can keep its own image, SKU, price and stock.';

            return;
        }

        variantSummaryEyebrow.textContent =
            `${sizeCount} ${sizeCount === 1 ? 'size' : 'sizes'} configured`;

        variantCombinationSummary.textContent =
            `${combinationCount} ${combinationCount === 1 ? 'variant' : 'variants'} will be created`;

        variantCombinationHelp.textContent =
            'Add Colors anytime to expand these into Size × Color combinations.';
    }


    function generateVariantSku(option1 = '', option2 = '', sequence = 1) {
        const productSku =
            normalizeVariantText(
                form?.elements?.namedItem('sku')?.value
            );

        const productName =
            normalizeVariantText(nameInput?.value);

        const base =
            productSku
            || productName
                .replace(/[^a-z0-9]+/gi, '-')
                .replace(/^-|-$/g, '')
                .slice(0, 12)
                .toUpperCase()
            || 'VAR';

        const suffix = [option1, option2]
            .filter(Boolean)
            .map(value =>
                normalizeVariantText(value)
                    .replace(/[^a-z0-9]+/gi, '')
                    .slice(0, 3)
                    .toUpperCase()
            )
            .filter(Boolean)
            .join('-');

        return `${base}${suffix ? '-' + suffix : ''}-${String(sequence).padStart(2, '0')}`;
    }

    function optionSelectMarkup(groupIndex, selectedValue = '') {
        const group = variantOptionGroups[groupIndex];
        const values = group?.values || [];
        const selected = normalizeVariantText(selectedValue);
        const colorGroup = isColorVariantGroup(groupIndex);

        if (!values.length) {
            if (colorGroup) {
                return `
                    <div
                        class="seller-variant-color-dropdown"
                        data-variant-color-select-wrap
                    >
                        <select
                            class="seller-variant-color-native-select"
                            data-variant-option-select="${groupIndex}"
                            disabled
                            aria-hidden="true"
                            tabindex="-1"
                        >
                            <option value="">—</option>
                        </select>

                        <button
                            type="button"
                            class="seller-variant-color-trigger"
                            data-variant-color-trigger
                            aria-expanded="false"
                            disabled
                        >
                            <span
                                class="seller-variant-color-dot"
                                data-variant-color-dot
                                style="--variant-swatch:#D0D5DD"
                                data-light-swatch="false"
                                aria-hidden="true"
                            ></span>

                            <span
                                class="seller-variant-color-label"
                                data-variant-color-label
                            >Select color</span>

                            <svg
                                class="seller-variant-color-chevron"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                aria-hidden="true"
                            >
                                <path d="m7 10 5 5 5-5"></path>
                            </svg>
                        </button>
                    </div>
                `;
            }

            return `
                <div class="seller-variant-plain-select">
                    <select
                        data-variant-option-select="${groupIndex}"
                        disabled
                    >
                        <option value="">—</option>
                    </select>
                </div>
            `;
        }

        const current =
            selected
            || normalizeVariantText(values[0]);

        const nativeOptions =
            values.map(value => {
                const safe =
                    escapeVariantHtml(value);

                return `
                    <option
                        value="${safe}"
                        ${normalizeVariantText(value) === current ? 'selected' : ''}
                    >${safe}</option>
                `;
            }).join('');

        if (colorGroup) {
            const colorChoices =
                values.map(value => {
                    const safe =
                        escapeVariantHtml(value);

                    const isSelected =
                        normalizeVariantText(value).toLowerCase()
                        === current.toLowerCase();

                    return `
                        <button
                            type="button"
                            class="seller-variant-color-choice"
                            data-variant-color-choice="${safe}"
                            role="option"
                            aria-selected="${isSelected ? 'true' : 'false'}"
                        >
                            ${variantColorDotMarkup(value)}
                            <span>${safe}</span>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.9"
                                aria-hidden="true"
                            >
                                <path d="m7 12 3 3 7-7"></path>
                            </svg>
                        </button>
                    `;
                }).join('');

            return `
                <div
                    class="seller-variant-color-dropdown"
                    data-variant-color-select-wrap
                >
                    <select
                        class="seller-variant-color-native-select"
                        data-variant-option-select="${groupIndex}"
                        aria-hidden="true"
                        tabindex="-1"
                    >
                        ${nativeOptions}
                    </select>

                    <button
                        type="button"
                        class="seller-variant-color-trigger"
                        data-variant-color-trigger
                        aria-haspopup="listbox"
                        aria-expanded="false"
                    >
                        <span
                            class="seller-variant-color-dot"
                            data-variant-color-dot
                            style="--variant-swatch:${variantColorHex(current)}"
                            data-light-swatch="${isLightVariantColor(current) ? 'true' : 'false'}"
                            aria-hidden="true"
                        ></span>

                        <span
                            class="seller-variant-color-label"
                            data-variant-color-label
                        >${escapeVariantHtml(current)}</span>

                        <svg
                            class="seller-variant-color-chevron"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <path d="m7 10 5 5 5-5"></path>
                        </svg>
                    </button>

                    <div
                        class="seller-variant-color-menu"
                        data-variant-color-menu
                        role="listbox"
                        hidden
                    >
                        ${colorChoices}
                    </div>
                </div>
            `;
        }

        return `
            <div class="seller-variant-plain-select">
                <select
                    data-variant-option-select="${groupIndex}"
                    aria-label="${escapeVariantHtml(group?.name || `Option ${groupIndex + 1}`)}"
                >
                    ${nativeOptions}
                </select>
            </div>
        `;
    }


    function syncVariantOptions(row) {
        const hidden = row.querySelector('[data-variant-options]');
        if (!hidden) return;

        const options = {};

        variantOptionGroups.forEach((group, index) => {
            const select = row.querySelector(
                `[data-variant-option-select="${index}"]`
            );

            const value =
                normalizeVariantText(select?.value || '');

            const name =
                normalizeVariantText(group?.name)
                || `Option ${index + 1}`;

            if (value) {
                options[name] = value;
            }
        });

        hidden.value = JSON.stringify(options);

        const option1 =
            normalizeVariantText(
                row.querySelector(
                    '[data-variant-option-select="0"]'
                )?.value
            );

        const option2 =
            normalizeVariantText(
                row.querySelector(
                    '[data-variant-option-select="1"]'
                )?.value
            );

        row.dataset.variantKey =
            variantKey(option1, option2);
    }

    function updateVariantCount() {
        const count = variantRows?.children.length || 0;

        if (variantCount) {
            variantCount.textContent =
                `${count} ${count === 1 ? 'variant' : 'variants'}`;
        }

        variantWrap?.classList.toggle('hidden', count === 0);
        variantEmpty?.classList.toggle('hidden', count > 0);

        updateVariantCombinationSummary();
        updatePreview();
    }

    function addVariant(data = {}) {
        if (!variantRows) return null;

        const index = variantIndex++;
        const option1 =
            normalizeVariantText(
                data.option1
                ?? data.size
                ?? ''
            );

        const option2 =
            normalizeVariantText(
                data.option2
                ?? data.color
                ?? ''
            );

        const key = variantKey(option1, option2);
        const row = document.createElement('tr');

        row.dataset.variantRow = String(index);
        row.dataset.variantKey = key;

        const defaultPrice =
            data.price
            ?? priceInput?.value
            ?? '';

        const defaultStock =
            data.stock
            ?? stockInput?.value
            ?? '';

        const defaultSku =
            data.sku
            || generateVariantSku(
                option1,
                option2,
                variantRows.children.length + 1
            );

        row.innerHTML = `
            <td>
                <label class="seller-variant-image-label">
                    <input
                        name="variants[${index}][image]"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden"
                        data-variant-image
                    >
                    <span data-variant-image-preview>
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7">
                            <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                            <path d="m5 17 5-5 4 4 2-2 3 3"></path>
                        </svg>
                    </span>
                </label>
            </td>
            <td>${optionSelectMarkup(0, option1)}</td>
            <td>
                ${optionSelectMarkup(1, option2)}
                <input
                    data-variant-options
                    name="variants[${index}][options]"
                    type="hidden"
                    value=""
                >
                <input
                    data-variant-draft-image-key
                    name="variants[${index}][draft_image_key]"
                    type="hidden"
                    value="${escapeVariantHtml(data.draftImageKey ?? data.draft_image_key ?? '')}"
                >
            </td>
            <td>
                <input
                    name="variants[${index}][sku]"
                    type="text"
                    value="${escapeVariantHtml(defaultSku)}"
                    placeholder="SKU"
                >
            </td>
            <td>
                <div class="seller-variant-money-cell">
                    <input
                        data-variant-price
                        name="variants[${index}][price]"
                        type="number"
                        min="0"
                        step="0.01"
                        value="${escapeVariantHtml(defaultPrice)}"
                        placeholder="0.00"
                        required
                    >
                </div>
            </td>
            <td>
                <input
                    data-variant-stock
                    name="variants[${index}][stock]"
                    type="number"
                    min="0"
                    value="${escapeVariantHtml(defaultStock)}"
                    placeholder="0"
                    required
                >
            </td>
            <td>
                <button
                    type="button"
                    data-remove-variant
                    class="seller-variant-remove"
                    aria-label="Remove variant"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M5 7h14"></path>
                        <path d="M9 7V5h6v2"></path>
                        <path d="M8 7l1 12h6l1-12"></path>
                    </svg>
                </button>
            </td>
        `;

        variantRows.appendChild(row);
        syncVariantOptions(row);

        row.querySelectorAll('[data-variant-option-select]').forEach(select => {
            select.addEventListener('change', () => {
                syncVariantOptions(row);
                refreshVariantColorSwatches(row);
                updatePreview();
            });
        });

        row.querySelectorAll('[data-variant-color-trigger]').forEach(trigger => {
            trigger.addEventListener('click', event => {
                event.stopPropagation();

                const wrapper =
                    trigger.closest(
                        '[data-variant-color-select-wrap]'
                    );

                const nestedMenu =
                    wrapper?.querySelector(
                        '[data-variant-color-menu]'
                    );

                const portalMenu =
                    Array.from(
                        document.querySelectorAll(
                            'body > [data-variant-color-menu]'
                        )
                    ).find(
                        menu =>
                            menu.__variantColorOwner
                            === wrapper
                    );

                const menu =
                    nestedMenu || portalMenu;

                if (!wrapper || !menu) return;

                const alreadyOpen =
                    !menu.hidden
                    && menu.__variantColorOwner
                        === wrapper;

                if (alreadyOpen) {
                    restoreVariantColorMenu(menu);
                    return;
                }

                openVariantColorMenu(
                    wrapper,
                    trigger,
                    menu
                );
            });
        });

        row.querySelectorAll('[data-variant-color-menu]').forEach(menu => {
            menu.addEventListener('click', event => {
                const choice =
                    event.target.closest(
                        '[data-variant-color-choice]'
                    );

                if (!choice) return;

                event.stopPropagation();

                const wrapper =
                    menu.__variantColorOwner
                    || menu.closest(
                        '[data-variant-color-select-wrap]'
                    );

                const select =
                    wrapper?.querySelector(
                        '[data-variant-option-select]'
                    );

                if (!select) return;

                select.value =
                    choice.dataset.variantColorChoice
                    || '';

                select.dispatchEvent(
                    new Event(
                        'change',
                        { bubbles: true }
                    )
                );

                restoreVariantColorMenu(menu);
            });
        });

        refreshVariantColorSwatches(row);

        const restoredVariantImageUrl =
            normalizeVariantText(
                data.draftImageUrl
                ?? data.draft_image_url
                ?? ''
            );

        if (restoredVariantImageUrl) {
            const target =
                row.querySelector(
                    '[data-variant-image-preview]'
                );

            if (target) {
                target.innerHTML =
                    `<img src="${escapeVariantHtml(restoredVariantImageUrl)}" alt="Saved variant image">`;
            }
        }

        row.querySelector('[data-variant-price]')?.addEventListener(
            'input',
            queuePreviewUpdate
        );

        row.querySelector('[data-remove-variant]')?.addEventListener(
            'click',
            () => {
                closeVariantColorMenus();

                suppressedVariantKeys.add(
                    row.dataset.variantKey || key
                );

                const preview = row.querySelector('[data-variant-image-preview]');
                const objectUrl = preview?.dataset.objectUrl || '';
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    variantObjectUrls.delete(objectUrl);
                }

                row.remove();
                updateVariantCount();
            }
        );

        row.querySelector('[data-variant-image]')?.addEventListener(
            'change',
            event => {
                const file = event.target.files?.[0];
                if (!file) return;

                const target =
                    row.querySelector(
                        '[data-variant-image-preview]'
                    );

                const previousObjectUrl = target?.dataset.objectUrl || '';
                if (previousObjectUrl) {
                    URL.revokeObjectURL(previousObjectUrl);
                    variantObjectUrls.delete(previousObjectUrl);
                }

                const url = URL.createObjectURL(file);
                variantObjectUrls.add(url);
                target.dataset.objectUrl = url;
                target.innerHTML =
                    `<img src="${url}" alt="Variant image">`;
            }
        );

        updateVariantCount();
        return row;
    }

    function regenerateVariantCombinations(forceRebuild = false) {
        if (!variantRows) return;

        const combinations =
            variantCombinations();

        const snapshots = new Map();

        if (forceRebuild) {
            Array.from(
                variantRows.querySelectorAll('tr')
            ).forEach(row => {
                const key =
                    row.dataset.variantKey || '';

                snapshots.set(key, {
                    sku:
                        row.querySelector(
                            'input[name$="[sku]"]'
                        )?.value || '',
                    price:
                        row.querySelector(
                            '[data-variant-price]'
                        )?.value || '',
                    stock:
                        row.querySelector(
                            '[data-variant-stock]'
                        )?.value || '',
                    draftImageKey:
                        row.querySelector(
                            '[data-variant-draft-image-key]'
                        )?.value || '',
                    draftImageUrl:
                        row.querySelector(
                            '[data-variant-image-preview] img'
                        )?.src || '',
                });
            });

            variantRows.innerHTML = '';
        }

        const desiredKeys =
            new Set(
                combinations.map(
                    combination =>
                        variantKey(
                            combination.option1,
                            combination.option2
                        )
                )
            );

        /*
         * Preserve existing rows (including selected images and edited SKU,
         * price and stock) whenever their combination still exists.
         */
        Array.from(
            variantRows.querySelectorAll('tr')
        ).forEach(row => {
            const key = row.dataset.variantKey || '';

            if (!desiredKeys.has(key)) {
                row.remove();
            }
        });

        const currentRows =
            new Map(
                Array.from(
                    variantRows.querySelectorAll('tr')
                ).map(row => [
                    row.dataset.variantKey || '',
                    row,
                ])
            );

        combinations.forEach((combination, sequence) => {
            const key =
                variantKey(
                    combination.option1,
                    combination.option2
                );

            if (suppressedVariantKeys.has(key)) {
                return;
            }

            const existing = currentRows.get(key);

            if (existing) {
                /*
                 * Preserve edited image/SKU/price/stock for combinations that
                 * still exist. Only resync the hidden options JSON.
                 */
                syncVariantOptions(existing);
                return;
            }

            const snapshot =
                snapshots.get(key);

            addVariant({
                option1: combination.option1,
                option2: combination.option2,
                price:
                    snapshot?.price
                    ?? priceInput?.value
                    ?? '',
                stock:
                    snapshot?.stock
                    ?? stockInput?.value
                    ?? '',
                sku:
                    snapshot?.sku
                    || generateVariantSku(
                        combination.option1,
                        combination.option2,
                        sequence + 1
                    ),
                draftImageKey:
                    snapshot?.draftImageKey || '',
                draftImageUrl:
                    snapshot?.draftImageUrl || '',
            });
        });

        updateVariantCount();
    }

    function normalizeDraftVariantOptionGroups(rawGroups) {
        let groups = rawGroups;

        if (typeof groups === 'string') {
            try {
                groups = JSON.parse(groups);
            } catch (_) {
                groups = [];
            }
        }

        if (!Array.isArray(groups)) {
            return [];
        }

        return groups
            .slice(0, 2)
            .map((group, index) => ({
                name:
                    normalizeVariantText(
                        group?.name
                    )
                    || (index === 0 ? 'Size' : 'Color'),
                values:
                    Array.from(
                        new Set(
                            (
                                Array.isArray(group?.values)
                                    ? group.values
                                    : []
                            )
                                .map(normalizeVariantText)
                                .filter(Boolean)
                        )
                    ),
            }));
    }

    function variantOptionGroupsDraftState() {
        return variantOptionGroups
            .slice(0, 2)
            .map((group, index) => ({
                name:
                    normalizeVariantText(
                        group?.name
                    )
                    || (index === 0 ? 'Size' : 'Color'),
                values:
                    Array.from(
                        new Set(
                            (
                                Array.isArray(group?.values)
                                    ? group.values
                                    : []
                            )
                                .map(normalizeVariantText)
                                .filter(Boolean)
                        )
                    ),
            }));
    }

    function hydrateVariantBuilder(
        savedVariants = [],
        savedOptionGroups = null
    ) {
        const variants =
            Array.isArray(savedVariants)
                ? savedVariants
                : [];

        const explicitGroups =
            normalizeDraftVariantOptionGroups(
                savedOptionGroups
            );

        let keys = [];

        if (explicitGroups.length) {
            variantOptionGroups[0] = {
                name:
                    explicitGroups[0]?.name
                    || 'Size',
                values:
                    explicitGroups[0]?.values
                    || [],
            };

            variantOptionGroups[1] = {
                name:
                    explicitGroups[1]?.name
                    || 'Color',
                values:
                    explicitGroups[1]?.values
                    || [],
            };

            keys = [
                variantOptionGroups[0].name,
                variantOptionGroups[1].name,
            ];
        } else {
            variantOptionGroups[0] = {
                name: 'Size',
                values: [],
            };

            variantOptionGroups[1] = {
                name: 'Color',
                values: [],
            };

            const parsedForKeys =
                variants.map(variant => ({
                    variant,
                    options:
                        parseVariantOptions(
                            variant?.options
                        ),
                }));

            parsedForKeys.forEach(item => {
                Object.keys(item.options || {}).forEach(key => {
                    if (!keys.includes(key)) {
                        keys.push(key);
                    }
                });
            });

            keys.slice(0, 2).forEach((key, index) => {
                variantOptionGroups[index].name =
                    normalizeVariantText(key)
                    || `Option ${index + 1}`;

                variantOptionGroups[index].values =
                    Array.from(
                        new Set(
                            parsedForKeys
                                .map(item =>
                                    normalizeVariantText(
                                        item.options?.[key]
                                    )
                                )
                                .filter(Boolean)
                        )
                    );
            });
        }

        renderVariantOptionBuilder();

        if (variantRows) {
            variantRows.innerHTML = '';
            variantIndex = 0;
        }

        suppressedVariantKeys.clear();

        const parsed =
            variants.map(variant => ({
                variant,
                options:
                    parseVariantOptions(
                        variant?.options
                    ),
            }));

        const key1 =
            keys[0]
            || variantOptionGroups[0]?.name
            || 'Size';

        const key2 =
            keys[1]
            || variantOptionGroups[1]?.name
            || 'Color';

        parsed.forEach(item => {
            const size =
                normalizeVariantText(
                    item.options?.[key1]
                    ?? item.options?.Size
                    ?? item.options?.size
                    ?? ''
                );

            const color =
                normalizeVariantText(
                    item.options?.[key2]
                    ?? item.options?.Color
                    ?? item.options?.color
                    ?? ''
                );

            if (!size) {
                return;
            }

            addVariant({
                option1: size,
                option2: color,
                sku: item.variant?.sku || '',
                price:
                    item.variant?.price
                    ?? '',
                stock:
                    item.variant?.stock
                    ?? '',
                draftImageKey:
                    item.variant?.draft_image_key
                    ?? '',
                draftImageUrl:
                    item.variant?.draft_image_url
                    ?? '',
            });
        });


        /*
         * A draft may contain option-group values before any Size row exists
         * (for example: Colors were prepared first). Keep those values even
         * when there are no variants yet.
         */
        renderVariantOptionBuilder();
        refreshAllVariantColorControls();
        updateVariantCount();
    }

    function appendVariantDraftState(formData) {
        if (!formData) return;

        /*
         * FormData omits disabled fields. Variant rows are intentionally
         * disabled while "Has different options?" is No, but a draft must
         * still preserve the Seller's work. Rebuild the draft variant payload
         * manually from the live UI.
         */
        Array.from(formData.keys())
            .filter(key =>
                String(key).startsWith(
                    'variants['
                )
            )
            .forEach(key => {
                formData.delete(key);
            });

        const rows =
            Array.from(
                variantRows?.querySelectorAll('tr')
                || []
            );

        rows.forEach((row, draftIndex) => {
            syncVariantOptions(row);

            const options =
                row.querySelector(
                    '[data-variant-options]'
                )?.value || '';

            const sku =
                row.querySelector(
                    'input[name$="[sku]"]'
                )?.value || '';

            const price =
                row.querySelector(
                    '[data-variant-price]'
                )?.value || '';

            const stock =
                row.querySelector(
                    '[data-variant-stock]'
                )?.value || '';

            const draftImageKey =
                row.querySelector(
                    '[data-variant-draft-image-key]'
                )?.value || '';

            const imageFile =
                row.querySelector(
                    '[data-variant-image]'
                )?.files?.[0];

            formData.append(
                `variants[${draftIndex}][options]`,
                options
            );

            formData.append(
                `variants[${draftIndex}][sku]`,
                sku
            );

            formData.append(
                `variants[${draftIndex}][price]`,
                price
            );

            formData.append(
                `variants[${draftIndex}][stock]`,
                stock
            );

            if (draftImageKey) {
                formData.append(
                    `variants[${draftIndex}][draft_image_key]`,
                    draftImageKey
                );
            }

            if (imageFile instanceof File) {
                formData.append(
                    `variants[${draftIndex}][image]`,
                    imageFile,
                    imageFile.name
                );
            }
        });

        formData.set(
            'variant_option_groups',
            JSON.stringify(
                variantOptionGroupsDraftState()
            )
        );

        /*
         * Lets the server safely prune variant draft images for rows the
         * Seller actually removed, while remaining backward-compatible with
         * older clients.
         */
        formData.set(
            'variant_state_present',
            '1'
        );

        formData.set(
            'has_variants',
            variantsToggle?.checked
                ? '1'
                : '0'
        );
    }


    function applyBaseValueToVariants(kind) {
        if (!variantRows?.children.length) {
            const target =
                kind === 'price'
                    ? priceInput
                    : stockInput;

            target?.focus();
            return;
        }

        const source =
            kind === 'price'
                ? priceInput
                : stockInput;

        const value =
            normalizeVariantText(
                source?.value
            );

        if (!value) {
            source?.focus();
            return;
        }

        const selector =
            kind === 'price'
                ? '[data-variant-price]'
                : '[data-variant-stock]';

        variantRows
            .querySelectorAll(selector)
            .forEach(input => {
                input.value = value;
                input.dispatchEvent(
                    new Event(
                        'input',
                        { bubbles: true }
                    )
                );
            });

        updatePreview();
    }

    function addSpecification(data = {}) {
        const index = specIndex++;
        const row = document.createElement('div');
        row.className = 'seller-spec-row';
        row.innerHTML = `
            <input name="specifications[${index}][name]" value="${data.name || ''}" class="seller-create-input" placeholder="Attribute e.g. Material">
            <input name="specifications[${index}][value]" value="${data.value || ''}" class="seller-create-input" placeholder="Value e.g. Solid Wood">
            <input name="specifications[${index}][unit]" value="${data.unit || ''}" class="seller-create-input" placeholder="Unit">
            <button type="button" data-remove-spec class="grid h-[42px] w-9 place-items-center rounded-[9px] border border-[#e1e5ea] bg-white text-[#7d858f]" aria-label="Remove specification">×</button>
        `;
        specRows.appendChild(row);
        specsEmpty.classList.add('hidden');
        row.querySelector('[data-remove-spec]')?.addEventListener('click', () => {
            row.remove();
            specsEmpty.classList.toggle('hidden', specRows.children.length > 0);
        });
    }

    function draftBoolean(value) {
        return value === true
            || value === 1
            || value === '1'
            || value === 'true'
            || value === 'on';
    }

    function parseVariantOptions(value) {
        if (value && typeof value === 'object' && !Array.isArray(value)) return value;

        try {
            const parsed = JSON.parse(String(value || '{}'));
            return parsed && typeof parsed === 'object' ? parsed : {};
        } catch (_) {
            return {};
        }
    }

    function assignDraftField(name, value) {
        const field = form?.elements?.namedItem(name);
        if (!field || value === undefined || value === null) return;

        if (field instanceof RadioNodeList) return;

        if (field.type === 'checkbox') {
            field.checked = draftBoolean(value);
            return;
        }

        if (field.type !== 'file') {
            field.value = value;
        }
    }

    function restoreDraftPayload(draft) {
        if (!draft?.payload || !form) return false;

        const payload = draft.payload || {};

        if (draftId) draftId.value = draft.id || '';

        [
            'name',
            'brand',
            'sku',
            'voucher_code',
            'condition',
            'price',
            'stock',
            'discount',
            'flash_sale_ends_at',
            'package_weight',
            'package_length',
            'package_width',
            'package_height',
            'preparation_days',
            'low_stock_threshold',
            'description',
            'custom_category',
        ].forEach(name => assignDraftField(name, payload[name] ?? ''));

        if (category) {
            category.value = payload.category || '';
        }

        if (freeShipping) {
            freeShipping.checked = draftBoolean(payload.free_shipping);
        }

        if (codToggle) {
            codToggle.checked = draftBoolean(payload.cash_on_delivery);
        }

        const variantMode = draftBoolean(payload.has_variants);
        if (variantsToggle) variantsToggle.checked = variantMode;
        if (hasVariants) hasVariants.value = variantMode ? '1' : '0';

        const savedVariants =
            Array.isArray(payload.variants)
                ? payload.variants
                : [];

        const savedVariantOptionGroups =
            payload.variant_option_groups
            ?? [];

        /*
         * Restore the complete smart-variant builder even when the Seller
         * saved the draft with "No" selected. syncVariantMode() below will
         * keep it hidden, but clicking Yes again brings the saved work back.
         */
        hydrateVariantBuilder(
            savedVariants,
            savedVariantOptionGroups
        );

        if (specRows) {
            specRows.innerHTML = '';
            specIndex = 0;
        }

        const savedSpecs = Array.isArray(payload.specifications) ? payload.specifications : [];
        savedSpecs.forEach(spec => {
            addSpecification({
                name: spec?.name || '',
                value: spec?.value || '',
                unit: spec?.unit || '',
            });
        });

        if (specsEmpty) {
            specsEmpty.classList.toggle('hidden', savedSpecs.length > 0);
        }

        if (draft.cover_image_url && coverPreview && coverPlaceholder) {
            coverPreview.src = draft.cover_image_url;
            coverPreview.classList.remove('hidden');
            coverPlaceholder.classList.add('hidden');
            coverDropzone?.classList.add('has-image');

            if (previewImage) {
                previewImage.innerHTML = `<img src="${draft.cover_image_url}" alt="Product preview">`;
            }
        }

        savedDraftGallery = Array.isArray(draft.gallery_images)
            ? draft.gallery_images.slice(0, GALLERY_LIMIT)
            : [];

        pendingGalleryFiles = [];
        if (galleryInput) galleryInput.value = '';

        syncCategoryPicker();
        updateCustomCategory();
        updateDescriptionCount();
        syncVariantMode();
        updateVariantCount();
        renderGallery();
        updatePreview();

        // Restoring an existing draft is intentionally silent so the header
        // stays clean. The active draft remains visible in the Drafts menu.
        hideDraftFeedback();

        return true;
    }

    function escapeDraftHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function currentDraftIdFromUrl() {
        const params = new URLSearchParams(window.location.search);

        // Explicit New Draft mode must never reuse an old hidden draft id.
        if (params.get('new') === '1') {
            return 0;
        }

        const value = Number(params.get('draft') || draftId?.value || 0);
        return Number.isInteger(value) && value > 0 ? value : 0;
    }

    function isNewDraftMode() {
        return new URLSearchParams(window.location.search).get('new') === '1';
    }

    function setDraftUrl(id = 0) {
        const url = new URL(window.location.href);

        url.searchParams.delete('new');
        url.searchParams.delete('fresh');

        if (id > 0) {
            url.searchParams.set('draft', String(id));
        } else {
            url.searchParams.delete('draft');
        }

        window.history.replaceState({}, '', url);
    }

    function closeDraftMenu() {
        draftManager?.classList.remove('is-open');
        draftMenu?.classList.add('hidden');
        draftMenuButton?.setAttribute('aria-expanded', 'false');
    }

    function toggleDraftMenu() {
        if (!draftMenu || !draftManager) return;
        const opening = draftMenu.classList.contains('hidden');
        draftMenu.classList.toggle('hidden', !opening);
        draftManager.classList.toggle('is-open', opening);
        draftMenuButton?.setAttribute('aria-expanded', opening ? 'true' : 'false');
    }

    function renderDraftList(drafts = []) {
        const list = Array.isArray(drafts) ? drafts : [];
        const activeId = currentDraftIdFromUrl();

        if (draftCount) draftCount.textContent = String(list.length);
        if (!draftList) return;

        if (!list.length) {
            draftList.innerHTML = '<div class="seller-draft-empty">No saved drafts yet.</div>';
            return;
        }

        draftList.innerHTML = list.map(draft => {
            const savedAt = draft?.saved_at ? new Date(draft.saved_at) : null;
            const time = savedAt && !Number.isNaN(savedAt.getTime())
                ? savedAt.toLocaleString([], { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' })
                : 'Saved draft';

            const mediaText = Number(draft?.gallery_count || 0) > 0
                ? ` · ${Number(draft.gallery_count)} gallery image${Number(draft.gallery_count) === 1 ? '' : 's'}`
                : '';

            const thumbnail = draft?.cover_image_url
                ? `<img src="${escapeDraftHtml(draft.cover_image_url)}" alt="">`
                : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2"></rect><path d="m7 16 3.5-4 2.5 3 2-2 2 3"></path><circle cx="15.5" cy="9.5" r="1.2"></circle></svg>`;

            return `
                <div class="seller-draft-item ${Number(draft?.id) === activeId ? 'is-current' : ''}" data-draft-row="${Number(draft?.id || 0)}">
                    <button type="button" class="seller-draft-open" data-open-draft="${Number(draft?.id || 0)}">
                        <span class="seller-draft-thumb">${thumbnail}</span>
                        <span class="seller-draft-open-copy">
                            <strong>${escapeDraftHtml(draft?.name || 'Untitled product')}</strong>
                            <span>${escapeDraftHtml(draft?.category || 'No category')} · ${escapeDraftHtml(time)}${mediaText}</span>
                        </span>
                    </button>

                    <button type="button" class="seller-draft-delete" data-delete-draft="${Number(draft?.id || 0)}" aria-label="Delete draft">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4 7h16"></path>
                            <path d="M9 7V5h6v2"></path>
                            <path d="M7 7l1 12h8l1-12"></path>
                            <path d="M10 11v5"></path>
                            <path d="M14 11v5"></path>
                        </svg>
                    </button>
                </div>
            `;
        }).join('');
    }

    async function deleteServerDraft(id) {
        if (!id) return;

        try {
            const body = new FormData();
            body.append('draft_id', String(id));

            const response = await fetch(@json(route('seller.products.draft.delete')), {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body,
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'Unable to delete draft.');
            }

            renderDraftList(data.drafts || []);

            if (currentDraftIdFromUrl() === Number(id)) {
                window.location.href = @json(route('seller.products.create'));
                return;
            }

            hideDraftFeedback();
        } catch (error) {
            setDraftStatus(error?.message || 'Unable to delete draft.', 'error');
        }
    }

    async function fetchDraftState(draftIdToOpen = 0) {
        const url = new URL(@json(route('seller.products.draft')), window.location.origin);

        if (Number(draftIdToOpen) > 0) {
            url.searchParams.set('draft_id', String(Number(draftIdToOpen)));
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok || !data?.ok) {
            throw new Error(data?.message || 'Unable to load saved drafts.');
        }

        renderDraftList(data?.drafts || []);
        return data;
    }

    async function refreshDraftList() {
        try {
            return await fetchDraftState(0);
        } catch (error) {
            console.debug('Draft list refresh failed.', error);
            return null;
        }
    }

    async function openDraftById(id) {
        const draftNumber = Number(id || 0);
        if (!Number.isInteger(draftNumber) || draftNumber <= 0) return;

        closeDraftMenu();
        showDraftSaving('Loading draft', 'Restoring your saved product details.');

        try {
            const data = await fetchDraftState(draftNumber);

            if (!data?.draft) {
                throw new Error('That draft is no longer available.');
            }

            setDraftUrl(draftNumber);
            restoreDraftPayload(data.draft);
            renderDraftList(data.drafts || []);

            hideDraftFeedback();

            window.scrollTo({
                top: Math.max(0, document.querySelector('.seller-create-page')?.offsetTop || 0),
                behavior: 'smooth',
            });
        } catch (error) {
            showDraftFeedback(
                'error',
                'Draft could not open',
                error?.message || 'Please try again.',
                1500
            );
        }
    }

    async function loadServerDraft() {
        try {
            if (isNewDraftMode()) {
                if (draftId) draftId.value = '';
                await fetchDraftState(0);
                return;
            }

            const requestedDraftId = currentDraftIdFromUrl();
            const data = await fetchDraftState(requestedDraftId);

            if (requestedDraftId > 0 && data?.draft) {
                restoreDraftPayload(data.draft);
            }
        } catch (error) {
            console.debug('Saved product drafts could not be loaded.', error);
        }
    }

    async function saveDraftNow() {
        if (!form || !saveDraft) return;

        saveDraft.disabled = true;
        showDraftSaving('Saving draft', 'Please wait while your changes are saved.');

        try {
            const formData = new FormData(form);

            /*
             * Persist the complete smart-variant editor, including option
             * names, every Size/Color value, selected row colors, SKU,
             * price, stock, and variant images.
             */
            appendVariantDraftState(formData);

            // New Draft mode is explicit. Never allow a stale hidden draft_id
            // to overwrite the previously saved product draft.
            if (isNewDraftMode()) {
                formData.delete('draft_id');
                formData.append('force_new_draft', '1');
            }

            // The file input only contains newly selected local files. When a
            // previous server draft already has gallery media, re-attach those
            // files too so the server can safely replace its gallery set
            // without losing previously saved images.
            formData.delete('gallery_images[]');

            const savedFiles = await savedGalleryAsFiles();
            const completeGallery = [...savedFiles, ...pendingGalleryFiles].slice(0, GALLERY_LIMIT);

            completeGallery.forEach(file => {
                formData.append('gallery_images[]', file, file.name);
            });

            const response = await fetch(@json(route('seller.products.draft.save')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: formData,
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.ok) {
                const firstError = Object.values(data?.errors || {}).flat().find(Boolean);
                throw new Error(firstError || data.message || 'Unable to save draft.');
            }

            if (data.draft?.id && draftId) {
                draftId.value = data.draft.id;
                setDraftUrl(Number(data.draft.id));
            }

            if (Array.isArray(data.drafts)) {
                renderDraftList(data.drafts);
            } else {
                await refreshDraftList();
            }

            savedDraftGallery = Array.isArray(data.draft?.gallery_images)
                ? data.draft.gallery_images.slice(0, GALLERY_LIMIT)
                : savedDraftGallery;

            const savedDraftVariants =
                Array.isArray(data.draft?.payload?.variants)
                    ? data.draft.payload.variants
                    : [];

            const savedBySize =
                new Map();

            savedDraftVariants.forEach(variant => {
                const options =
                    parseVariantOptions(
                        variant?.options
                    );

                const size =
                    normalizeVariantText(
                        options?.[
                            variantOptionGroups[0]?.name
                            || 'Size'
                        ]
                        ?? options?.Size
                        ?? options?.size
                        ?? ''
                    ).toLowerCase();

                if (size) {
                    savedBySize.set(
                        size,
                        variant
                    );
                }
            });

            variantRows
                ?.querySelectorAll('tr')
                .forEach(row => {
                    const size =
                        normalizeVariantText(
                            row.querySelector(
                                '[data-variant-option-select="0"]'
                            )?.value
                        ).toLowerCase();

                    const saved =
                        savedBySize.get(size);

                    if (!saved) return;

                    const keyInput =
                        row.querySelector(
                            '[data-variant-draft-image-key]'
                        );

                    if (keyInput) {
                        keyInput.value =
                            saved.draft_image_key
                            || '';
                    }

                    if (
                        saved.draft_image_url
                        && !row.querySelector(
                            '[data-variant-image]'
                        )?.files?.length
                    ) {
                        const preview =
                            row.querySelector(
                                '[data-variant-image-preview]'
                            );

                        if (preview) {
                            preview.innerHTML =
                                `<img src="${escapeVariantHtml(saved.draft_image_url)}" alt="Saved variant image">`;
                        }
                    }
                });

            pendingGalleryFiles = [];
            if (galleryInput) galleryInput.value = '';
            renderGallery();

            showDraftSaved(
                'Draft saved',
                'Your product draft has been saved.',
                2000
            );
        } catch (error) {
            showDraftError(
                'Draft not saved',
                error?.message || 'Please try again.',
                1600
            );
        } finally {
            saveDraft.disabled = false;
        }
    }

    draftMenuButton?.addEventListener('click', async event => {
        event.stopPropagation();

        const opening = draftMenu?.classList.contains('hidden');
        toggleDraftMenu();

        if (opening) {
            // Drafts were already loaded on page boot. Refresh silently in background.
            window.setTimeout(() => refreshDraftList(), 0);
        }
    });

    draftMenu?.addEventListener('click', event => {
        event.stopPropagation();

        const openButton = event.target.closest('[data-open-draft]');
        if (openButton) {
            const id = Number(openButton.dataset.openDraft || 0);
            if (id > 0) {
                openDraftById(id);
            }
            return;
        }

        const deleteButton = event.target.closest('[data-delete-draft]');
        if (deleteButton) {
            const id = Number(deleteButton.dataset.deleteDraft || 0);
            if (id > 0 && window.confirm('Delete this saved draft?')) {
                deleteServerDraft(id);
            }
        }
    });

    newBlankProduct?.addEventListener('click', () => {
        closeDraftMenu();

        if (draftId) {
            draftId.value = '';
        }

        const url = new URL(@json(route('seller.products.create')), window.location.origin);
        url.searchParams.set('new', '1');
        url.searchParams.set('fresh', String(Date.now()));

        // Full navigation is intentional here: it guarantees a completely
        // blank form and prevents an old draft_id from being reused.
        window.location.assign(url.toString());
    });

    document.addEventListener('click', event => {
        if (draftManager && !draftManager.contains(event.target)) {
            closeDraftMenu();
        }
    }, { signal: createProductSignal });

    categoryTrigger?.addEventListener('click', event => {
        event.stopPropagation();
        toggleCategoryMenu();
    });

    categoryMenu?.querySelectorAll('[data-category-option]').forEach(option => {
        option.addEventListener('click', () => {
            if (!category) return;
            category.value = option.dataset.categoryOption || '';
            category.dispatchEvent(new Event('change', { bubbles: true }));
            syncCategoryPicker();
            closeCategoryMenu();
        });
    });

    document.addEventListener('click', event => {
        if (categoryPicker && !categoryPicker.contains(event.target)) {
            closeCategoryMenu();
        }
    }, { signal: createProductSignal });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeCategoryMenu();
            closeVariantColorMenus();
        }
    }, { signal: createProductSignal });

    document.addEventListener('click', event => {
        const clickedInsideTrigger =
            event.target.closest(
                '[data-variant-color-select-wrap]'
            );

        const clickedInsidePortal =
            event.target.closest(
                '.seller-variant-color-menu--portal'
            );

        if (
            !clickedInsideTrigger
            && !clickedInsidePortal
        ) {
            closeVariantColorMenus();
        }
    }, { signal: createProductSignal });

    const repositionOpenVariantColorMenus = () => {
        document
            .querySelectorAll(
                'body > .seller-variant-color-menu--portal'
            )
            .forEach(menu => {
                const owner =
                    menu.__variantColorOwner;

                const trigger =
                    owner?.querySelector(
                        '[data-variant-color-trigger]'
                    );

                if (
                    trigger
                    && !menu.hidden
                ) {
                    positionVariantColorMenu(
                        trigger,
                        menu
                    );
                }
            });
    };

    let variantMenuRepositionFrame = 0;

    const scheduleVariantColorMenuReposition = () => {
        if (variantMenuRepositionFrame) return;

        variantMenuRepositionFrame = window.requestAnimationFrame(() => {
            variantMenuRepositionFrame = 0;
            repositionOpenVariantColorMenus();
        });
    };

    window.addEventListener(
        'resize',
        scheduleVariantColorMenuReposition,
        { passive: true, signal: createProductSignal }
    );

    window.addEventListener(
        'scroll',
        scheduleVariantColorMenuReposition,
        { capture: true, passive: true, signal: createProductSignal }
    );

        document.querySelectorAll('[data-create-nav]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById(`create-${button.dataset.createNav}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            document.querySelectorAll('[data-create-nav]').forEach(button => button.classList.toggle('is-active', button.dataset.createNav === entry.target.dataset.createSection));
        });
    }, { rootMargin: '-25% 0px -60% 0px', threshold: 0.01 });
    document.querySelectorAll('[data-create-section]').forEach(section => observer.observe(section));

    category?.addEventListener('change', updateCustomCategory);
    customCategory?.addEventListener('input', queuePreviewUpdate);
    nameInput?.addEventListener('input', queuePreviewUpdate);
    priceInput?.addEventListener('input', queuePreviewUpdate);
    discountInput?.addEventListener('input', queuePreviewUpdate);
    flashInput?.addEventListener('change', queuePreviewUpdate);
    freeShipping?.addEventListener('change', queuePreviewUpdate);
    codToggle?.addEventListener('change', queuePreviewUpdate);
    description?.addEventListener('input', updateDescriptionCount);

    coverInput?.addEventListener('change', event => {
        const file = acceptedImageFiles(event.target.files)[0];
        if (file) setCoverPreview(file);
    });

    coverDropzone?.addEventListener('keydown', event => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        coverInput?.click();
    });

    ['dragenter', 'dragover'].forEach(type => {
        coverDropzone?.addEventListener(type, event => {
            event.preventDefault();
            event.stopPropagation();
            if (event.dataTransfer) event.dataTransfer.dropEffect = 'copy';
            coverDropzone.classList.add('is-dragover');
        });

        galleryDropzone?.addEventListener(type, event => {
            event.preventDefault();
            event.stopPropagation();
            if (event.dataTransfer) event.dataTransfer.dropEffect = 'copy';
            galleryDropzone.classList.add('is-dragover');
        });
    });

    ['dragleave', 'drop'].forEach(type => {
        coverDropzone?.addEventListener(type, event => {
            event.preventDefault();
            event.stopPropagation();
            coverDropzone.classList.remove('is-dragover');
        });

        galleryDropzone?.addEventListener(type, event => {
            event.preventDefault();
            event.stopPropagation();
            galleryDropzone.classList.remove('is-dragover');
        });
    });

    coverDropzone?.addEventListener('drop', event => {
        const file = acceptedImageFiles(event.dataTransfer?.files)[0];
        if (file) assignCoverFile(file);
    });

    galleryDropzone?.addEventListener('drop', event => {
        addGalleryFiles(event.dataTransfer?.files);
    });

    galleryInput?.addEventListener('change', event => {
        addGalleryFiles(event.target.files);
    });

    variantsToggle?.addEventListener('change', () => {
        syncVariantMode();
    });

    document.querySelectorAll('[data-variant-mode-value]').forEach(button => {
        button.addEventListener('click', () => {
            if (!variantsToggle) return;

            variantsToggle.checked =
                button.dataset.variantModeValue === '1';

            variantsToggle.dispatchEvent(
                new Event('change', { bubbles: true })
            );
        });
    });

    variantOptionNameInputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            variantOptionGroups[index].name =
                normalizeVariantText(input.value)
                || `Option ${index + 1}`;

            syncVariantOptionNames();
        });

        input.addEventListener('change', () => {
            syncVariantOptionNames();
        });
    });

    variantOptionAddButtons.forEach(button => {
        button.addEventListener('click', () => {
            const index =
                Number(
                    button.dataset.addVariantOption
                );

            addVariantOptionValue(
                index,
                variantOptionValueInputs[index]?.value
            );
        });
    });

    variantOptionValueInputs.forEach((input, index) => {
        input.addEventListener('keydown', event => {
            if (event.key !== 'Enter') return;

            event.preventDefault();

            addVariantOptionValue(
                index,
                input.value
            );
        });
    });

    variantOptionValueContainers.forEach(container => {
        container.addEventListener('click', event => {
            const button =
                event.target.closest(
                    '[data-remove-variant-option]'
                );

            if (!button) return;

            removeVariantOptionValue(
                Number(
                    button.dataset.removeVariantOption
                ),
                button.dataset.variantOptionValue || ''
            );
        });
    });

    applyBasePriceToVariants?.addEventListener(
        'click',
        () => applyBaseValueToVariants('price')
    );

    applyBaseStockToVariants?.addEventListener(
        'click',
        () => applyBaseValueToVariants('stock')
    );

    document.getElementById('addSpecificationRow')?.addEventListener('click', () => addSpecification());
    saveDraft?.addEventListener('click', saveDraftNow);

    form.addEventListener('submit', event => {
        if (variantsToggle?.checked) {
            const rows =
                Array.from(
                    variantRows.querySelectorAll('tr')
                );

            const sizeValues =
                variantOptionGroups[0]?.values || [];

            if (!sizeValues.length || !rows.length) {
                event.preventDefault();

                alert(
                    'Add at least one Size before submitting variants. Colors alone do not create variant rows.'
                );

                variantOptionValueInputs[0]?.focus();
                return;
            }

            const invalidOption =
                rows.find(row => {
                    syncVariantOptions(row);

                    const hidden =
                        row.querySelector(
                            '[data-variant-options]'
                        );

                    const options =
                        parseVariantOptions(
                            hidden?.value
                        );

                    return !Object.keys(options).length;
                });

            if (invalidOption) {
                event.preventDefault();

                alert(
                    'Every variant needs at least one option value.'
                );

                invalidOption
                    .querySelector(
                        '[data-variant-option-select]'
                    )
                    ?.focus();

                return;
            }
        }

        /*
         * Base Price / Base Stock are defaults in variant mode.
         * Keep them visible/editable, but preserve the original backend
         * behavior by omitting them from final variant-mode submission.
         * Draft saves still keep these values because FormData is created
         * before this submit-only step.
         */
        if (
            productSubmitLocked
            || form.dataset.submitting === '1'
        ) {
            event.preventDefault();
            return;
        }

        /*
         * A successful create changes the Product Library. Invalidate only
         * this seller-session frontend cache before the POST so the redirect
         * to Product Management cannot reuse a stale catalog.
         */
        try {
            const productLibraryUrl = @json(route('seller.products.library'));
            const sellerId = @json((int) ($sellerAccount->id ?? 0));
            window.sessionStorage.removeItem(
                'sari:seller-products-library:v6:' + sellerId + ':' + productLibraryUrl
            );
        } catch (_) {}

        window.__SARI_PRODUCTS_LIBRARY_CACHED__ = null;
        window.__SARI_PRODUCTS_LIBRARY_CACHE_IS_COMPLETE__ = false;
        window.__SARI_PRODUCTS_LIBRARY_CACHE_SAVED_AT__ = 0;
        window.__SARI_SELLER_PRODUCT_LIBRARY_PREFETCH__ = null;

        /*
         * One click = one product POST.
         * Every added size/color is nested in variants[] under the same
         * product form. Repeated clicks while this request is in-flight
         * are ignored.
         */
        productSubmitLocked = true;
        form.dataset.submitting = '1';

        if (submitProductReview) {
            submitProductReview.disabled = true;
            submitProductReview.setAttribute(
                'aria-disabled',
                'true'
            );
            submitProductReview.setAttribute(
                'aria-busy',
                'true'
            );
        }

        if (variantsToggle?.checked) {
            if (priceInput) priceInput.disabled = true;
            if (stockInput) stockInput.disabled = true;
        }
    });

    window.addEventListener('pageshow', event => {
        if (!event.persisted) return;

        productSubmitLocked = false;
        delete form.dataset.submitting;

        if (submitProductReview) {
            submitProductReview.disabled = false;
            submitProductReview.removeAttribute(
                'aria-disabled'
            );
            submitProductReview.removeAttribute(
                'aria-busy'
            );
        }
    }, { signal: createProductSignal });

    syncCategoryPicker();
    updateCustomCategory();
    updateDescriptionCount();

    if (
        variantsToggle?.checked
        && Array.isArray(initialOldVariants)
        && initialOldVariants.length
    ) {
        hydrateVariantBuilder(initialOldVariants);
    } else {
        renderVariantOptionBuilder();
    }

    syncVariantMode();
    renderGallery();
    updatePreview();

    hideDraftFeedback();
    preloadDraftLottie();

    const cleanupCreateProductPage = () => {
        if (previewFrame) {
            window.cancelAnimationFrame(previewFrame);
            previewFrame = 0;
        }

        if (variantMenuRepositionFrame) {
            window.cancelAnimationFrame(variantMenuRepositionFrame);
            variantMenuRepositionFrame = 0;
        }

        observer.disconnect();
        clearDraftFeedbackTimers();
        clearGalleryObjectUrls();

        if (coverObjectUrl) {
            URL.revokeObjectURL(coverObjectUrl);
            coverObjectUrl = null;
        }

        variantObjectUrls.forEach(url => URL.revokeObjectURL(url));
        variantObjectUrls.clear();

        closeVariantColorMenus();
        document
            .querySelectorAll('body > .seller-variant-color-menu--portal')
            .forEach(menu => menu.remove());

        createProductLifecycle.abort();
        form.dataset.bound = '0';
    };

    document.addEventListener(
        'livewire:navigating',
        cleanupCreateProductPage,
        { once: true, signal: createProductSignal }
    );

    window.addEventListener(
        'beforeunload',
        cleanupCreateProductPage,
        { once: true, signal: createProductSignal }
    );

    const shouldRestoreServerDraft = @json(!$errors->any());
    if (shouldRestoreServerDraft) {
        // Load draft state immediately on Add Product boot.
        loadServerDraft();
    }
    };

    if (window.__SARI_SELLER_AFTER_PAINT__) {
        window.__SARI_SELLER_AFTER_PAINT__(bootSellerCreateProductPage);
    } else {
        window.requestAnimationFrame(() => window.requestAnimationFrame(bootSellerCreateProductPage));
    }
})();
</script>
@endpush
