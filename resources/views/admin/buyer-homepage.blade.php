@extends('layouts.admin')

@section('title', 'Buyer Homepage Studio — SARI Admin')
@section('page-title', 'Buyer Homepage Studio')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/buyer-homepage.css') }}?v=6">
@endpush

@section('content')
<div class="bhm-page" id="buyerHomepageEditor">
    <form id="bhmForm" class="bhm-workspace" action="{{ route('admin.buyer-homepage.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="slot" id="bhmSlot" value="{{ old('slot', request('slot', 'hero_1')) }}">
        <input type="file" name="image" id="bhmImage" accept="image/jpeg,image/png,image/webp" class="bhm-visually-hidden" tabindex="-1">
        {{-- Form data is edited directly on the canvas, not inside visible side panels. --}}
        <div class="bhm-visually-hidden" aria-hidden="true">
            <input name="kicker" id="bhmKicker" type="hidden">
            <input name="title" id="bhmTitle" type="hidden">
            <textarea name="description" id="bhmDescription" maxlength="250" tabindex="-1"></textarea>
            <input name="button_text" id="bhmButtonText" type="hidden">
            <input name="text_x" id="bhmTextX" type="hidden">
            <input name="text_y" id="bhmTextY" type="hidden">
            <input name="button_x" id="bhmButtonX" type="hidden">
            <input name="button_y" id="bhmButtonY" type="hidden">
        </div>

        {{-- A compact editor status row inside the admin content area.
             The original admin sidebar and top header are provided by layouts.admin. --}}
        <div class="bhm-appbar">
            <div class="bhm-appbar-left">
                <div class="bhm-brandline">
                    <strong>Buyer Homepage Studio</strong>
                    <span>Marketing <span aria-hidden="true">/</span> <span id="bhmCurrentLabel">Main banner · Slide 1</span></span>
                </div>
            </div>
            <div class="bhm-appbar-right">
                <span class="bhm-save-status" id="bhmUnsaved" role="status" aria-live="polite">All changes saved</span>
                <a href="{{ route('buyer.home') }}" class="bhm-view-link" target="_blank" rel="noopener noreferrer">View buyer page ↗</a>
                <button type="submit" class="bhm-publish" id="bhmPublish">Save &amp; Publish</button>
            </div>
        </div>

        @if (session('success'))
            <div class="bhm-alert bhm-alert--success" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bhm-alert bhm-alert--error" role="alert"><strong>Unable to publish.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <section class="bhm-ribbon" aria-label="Text and banner editing tools">
            <div class="bhm-ribbon-group">
                <span class="bhm-caption">IMAGE</span>
                <button type="button" class="bhm-tool-btn" id="bhmReplaceBtn" title="Replace selected banner image">▧ <span>Replace</span></button>
                <span class="bhm-keyhint">or drop directly</span>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--font">
                <span class="bhm-caption">FONT FAMILY</span>
                <input list="bhmFontOptions" type="text" name="font_family" id="bhmFont" maxlength="32" spellcheck="false" autocomplete="off" placeholder="Search or type any font" aria-label="Font family — search or type a custom font" required>
                <datalist id="bhmFontOptions">
                    @foreach ($fonts as $value => $css)<option value="{{ ucwords($value) }}"></option>@endforeach
                </datalist>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--sizes">
                <span class="bhm-caption">TEXT SIZE</span>
                <div class="bhm-inline-numbers">
                    <label>Title <input name="title_size" id="bhmTitleSize" type="number" min="12" max="52" required aria-label="Title font size in pixels"></label>
                    <label>Body <input name="description_size" id="bhmDescriptionSize" type="number" min="8" max="26" required aria-label="Description font size in pixels"></label>
                    <label>Button <input name="button_size" id="bhmButtonSize" type="number" min="8" max="24" required aria-label="Button font size in pixels"></label>
                </div>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--colors">
                <span class="bhm-caption">COLOR &amp; PICK FROM SCREEN</span>
                <div class="bhm-color-tools">
                    @foreach (['text_color' => ['bhmTextColor', 'Text'], 'button_bg' => ['bhmButtonBg', 'Button'], 'button_color' => ['bhmButtonColor', 'CTA text']] as $key => $color)
                        <div class="bhm-color-control">
                            <label class="bhm-color-swatch" title="Choose {{ $color[1] }} color"><input type="color" name="{{ $key }}" id="{{ $color[0] }}" aria-label="Choose {{ $color[1] }} color" required></label>
                            <div class="bhm-color-meta"><span>{{ $color[1] }}</span><input type="text" data-hex-for="{{ $color[0] }}" maxlength="7" spellcheck="false" aria-label="{{ $color[1] }} hex color" placeholder="#FFFFFF"></div>
                            <button type="button" class="bhm-eyedropper" data-pick-for="{{ $color[0] }}" aria-label="Pick {{ $color[1] }} color from screen" title="Eyedropper: pick color from screen">⌖</button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bhm-ribbon-group bhm-ribbon-group--more">
                <span class="bhm-caption">ACTION</span>
                <div class="bhm-more-actions">
                    <button type="button" class="bhm-tool-btn" id="bhmAddKicker">+ Label</button>
                    <button type="button" class="bhm-tool-btn" id="bhmAddDescription">+ Description</button>
                    <button type="button" class="bhm-tool-btn" id="bhmAddButton">+ Button</button>
                    <label class="bhm-link-edit" title="Internal destination for the selected banner button">Link <input name="button_url" id="bhmButtonUrl" type="text" maxlength="255" required placeholder="/buyer/products" aria-label="Button link (internal path)"></label>
                    <button type="button" class="bhm-tool-btn bhm-reset" id="bhmReset">Reset</button>
                </div>
            </div>
        </section>

        <nav class="bhm-filmstrip" id="bhmSlots" aria-label="Choose a homepage banner">
            <span class="bhm-filmstrip-caption">Banners <strong>06</strong></span>
            @foreach ($banners as $slot => $banner)
                <button type="button" class="bhm-slot" data-slot="{{ $slot }}" aria-pressed="false" aria-label="Edit {{ $banner['label'] }}">
                    <img src="{{ $banner['image_url'] }}" alt="" loading="lazy">
                    <span>{{ $banner['label'] }}</span>
                </button>
            @endforeach
        </nav>

        <section class="bhm-canvas-wrap" aria-label="Full-width visual homepage banner editor">
            <div class="bhm-canvas-topbar">
                <div><span class="bhm-canvas-heading">Buyer homepage <span aria-hidden="true">/</span> Banner composition</span><span class="bhm-canvas-subtitle">Click text to type · drag Move handle to position · drag image onto a banner to replace</span></div>
                <div class="bhm-view-options" aria-label="Buyer canvas zoom">
                    <span class="bhm-preview-size" id="bhmBuyerDimensions">Buyer-sized canvas</span>
                    <button type="button" class="bhm-zoom-button" id="bhmFitView" aria-pressed="true" title="Fit the exact buyer-sized canvas within the admin content width">Fit</button>
                    <button type="button" class="bhm-zoom-button" id="bhmActualView" aria-pressed="false" title="Show real CSS pixels at 100%; scroll horizontally if necessary">100%</button>
                    <span class="bhm-zoom-readout" id="bhmZoomReadout" aria-live="polite">100%</span>
                </div>
            </div>
                <div class="bhm-preview-scroller" id="bhmPreviewScroller" aria-label="Buyer-size visual preview; horizontally scroll at 100% zoom">
                  <div class="bhm-stage-frame" id="bhmStageFrame">
                    <div class="bhm-stage" id="bhmStage">
                    <div class="bhm-stage-main bhm-stage-card" data-preview-slot="hero_1" tabindex="0" aria-label="Main carousel banner canvas">
                        <img id="bhmHeroImage" alt="Main promotional banner" draggable="false">
                        <div class="bhm-stage-shade"></div>
                        <div class="bhm-stage-copy" id="bhmHeroCopy" data-drag="text">
                            <button type="button" class="bhm-move-handle" data-move="text" aria-label="Move main banner text using drag or arrow keys" title="Drag to move text">✥ <span>Move text</span></button>
                            <span class="bhm-stage-kicker bhm-editable" id="bhmHeroKicker" data-content-field="kicker" contenteditable="true" role="textbox" aria-label="Edit small heading" spellcheck="true"></span>
                            <strong class="bhm-stage-title bhm-editable" id="bhmHeroTitle" data-content-field="title" contenteditable="true" role="textbox" aria-label="Edit main title" spellcheck="true"></strong>
                            <span class="bhm-stage-description bhm-editable" id="bhmHeroDescription" data-content-field="description" contenteditable="true" role="textbox" aria-label="Edit description" spellcheck="true"></span>
                        </div>
                        <div class="bhm-stage-button" id="bhmHeroButton" data-drag="button"><button type="button" class="bhm-move-handle" data-move="button" aria-label="Move main banner button using drag or arrow keys" title="Drag to move button">✥</button><span class="bhm-editable bhm-button-label" data-content-field="button_text" contenteditable="true" role="textbox" aria-label="Edit button text"></span><span class="bhm-button-arrow" aria-hidden="true">→</span></div>
                        <button type="button" class="bhm-image-trigger" data-replace-image aria-label="Replace main banner image">▧ <span>Replace image</span></button>
                        <div class="bhm-stage-drop-indicator" aria-hidden="true"><span>↓</span> Drop image to replace</div>
                        <div class="bhm-carousel-previous" aria-hidden="true">‹</div>
                        <div class="bhm-carousel-next" aria-hidden="true">›</div>
                        <div class="bhm-carousel-dots" aria-hidden="true"><i class="is-active"></i><i></i><i></i><i></i></div>
                    </div>
                    <div class="bhm-stage-side">
                        @foreach (['side_beauty', 'side_flash'] as $slot)
                            <div class="bhm-stage-card bhm-stage-side-card" data-preview-slot="{{ $slot }}" tabindex="0" aria-label="{{ $banners[$slot]['label'] }} canvas">
                                <img data-side-image="{{ $slot }}" alt="" draggable="false">
                                <div class="bhm-stage-shade"></div>
                                <div class="bhm-stage-copy bhm-stage-side-copy" data-side-copy="{{ $slot }}" data-drag="text">
                                    <button type="button" class="bhm-move-handle" data-move="text" aria-label="Move side banner text using drag or arrow keys">✥</button>
                                    <span class="bhm-stage-kicker bhm-editable" data-side-kicker="{{ $slot }}" data-content-field="kicker" contenteditable="true" role="textbox" aria-label="Edit small heading"></span>
                                    <strong class="bhm-stage-title bhm-editable" data-side-title="{{ $slot }}" data-content-field="title" contenteditable="true" role="textbox" aria-label="Edit title"></strong>
                                    <span class="bhm-stage-description bhm-editable" data-side-description="{{ $slot }}" data-content-field="description" contenteditable="true" role="textbox" aria-label="Edit description"></span>
                                </div>
                                <div class="bhm-stage-button bhm-stage-side-button" data-side-button="{{ $slot }}" data-drag="button"><button type="button" class="bhm-move-handle" data-move="button" aria-label="Move side banner button using drag or arrow keys">✥</button><span class="bhm-editable bhm-button-label" data-content-field="button_text" contenteditable="true" role="textbox" aria-label="Edit button text"></span><span class="bhm-button-arrow" aria-hidden="true">→</span></div>
                                <button type="button" class="bhm-image-trigger" data-replace-image aria-label="Replace {{ $banners[$slot]['label'] }} image">▧ <span>Replace image</span></button>
                                <div class="bhm-stage-drop-indicator" aria-hidden="true"><span>↓</span> Drop image to replace</div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                  </div>
                </div>

            <footer class="bhm-stage-footer">
                <span class="bhm-file-info" id="bhmFileInfo" role="status" aria-live="polite">Drop JPG, PNG or WebP (max 5 MB, min 400 × 200) over a banner to replace its image.</span>
                <span>4 main slides · 2 fixed right banners · positions of images remain locked</span>
            </footer>
        </section>
    </form>
</div>
@endsection

@push('scripts')
<script>
    window.SARI_HOMEPAGE_BANNERS = @json($banners, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    window.SARI_HOMEPAGE_FONTS = @json($fonts);
</script>
<script src="{{ asset('js/admin/buyer-homepage-editor.js') }}?v=6" defer></script>
@endpush
