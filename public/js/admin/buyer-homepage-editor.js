/* SARI Homepage Studio v6 (buyer-size preview).
 * Six fixed banner slots; only their images, copy, button and overlay coordinates are editable.
 * Saved through the existing admin buyer-homepage POST route.
 */
(function () {
    'use strict';

    const root = document.getElementById('buyerHomepageEditor');
    if (!root || !window.SARI_HOMEPAGE_BANNERS) return;

    const $ = (id) => document.getElementById(id);
    const originals = window.SARI_HOMEPAGE_BANNERS;
    const banners = JSON.parse(JSON.stringify(originals));
    const fonts = window.SARI_HOMEPAGE_FONTS || {};
    const fields = {
        kicker: 'bhmKicker', title: 'bhmTitle', description: 'bhmDescription',
        button_text: 'bhmButtonText', button_url: 'bhmButtonUrl', font_family: 'bhmFont',
        title_size: 'bhmTitleSize', description_size: 'bhmDescriptionSize',
        button_size: 'bhmButtonSize', text_color: 'bhmTextColor',
        button_bg: 'bhmButtonBg', button_color: 'bhmButtonColor',
        text_x: 'bhmTextX', text_y: 'bhmTextY', button_x: 'bhmButtonX', button_y: 'bhmButtonY',
    };
    const numeric = new Set(['title_size', 'description_size', 'button_size', 'text_x', 'text_y', 'button_x', 'button_y']);
    const textLimits = { kicker: 100, title: 160, description: 250, button_text: 60 };
    const slotNames = Object.keys(banners);
    let selected = slotNames.includes($('bhmSlot').value) ? $('bhmSlot').value : slotNames[0];
    let activeHero = selected.startsWith('hero_') ? selected : 'hero_1';
    let objectUrl = null;
    let fileRequest = 0;

    const cardFor = (slot) => slot.startsWith('hero_')
        ? $('bhmHeroImage').closest('.bhm-stage-card')
        : root.querySelector('[data-preview-slot="' + slot + '"]');

    function isDirty(slot) {
        const current = { ...banners[slot] };
        const original = { ...originals[slot] };
        delete current.preview_url;
        delete original.preview_url;
        return JSON.stringify(current) !== JSON.stringify(original)
            || (slot === selected && $('bhmImage').files.length > 0);
    }

    function message(text) { $('bhmFileInfo').textContent = text; }

    function resetSelectedFile() {
        fileRequest += 1;
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = null;
        $('bhmImage').value = '';
        message('Drop JPG, PNG or WebP (max 5 MB, min 400 × 200) over a banner to replace its image.');
    }

    function selectSlot(slot) {
        if (!banners[slot]) return false;
        if (slot === selected) return true;
        if (isDirty(selected) && !window.confirm('Discard unsaved changes to ' + originals[selected].label + '?')) return false;
        resetSelectedFile();
        banners[selected] = JSON.parse(JSON.stringify(originals[selected]));
        selected = slot;
        if (slot.startsWith('hero_')) activeHero = slot;
        $('bhmSlot').value = slot;
        populateFields();
        render();
        return true;
    }

    function populateFields() {
        for (const [key, id] of Object.entries(fields)) $(id).value = banners[selected][key] ?? '';
        root.querySelectorAll('[data-hex-for]').forEach((input) => {
            input.value = $(input.dataset.hexFor).value.toUpperCase();
        });
        loadFont(banners[selected].font_family);
    }

    function putText(element, value) {
        if (document.activeElement !== element && element.textContent !== (value || '')) {
            element.textContent = value || '';
        }
        element.hidden = !value && document.activeElement !== element;
    }

    function paint(slot) {
        const b = banners[slot];
        const card = cardFor(slot);
        const hero = slot.startsWith('hero_');
        const image = hero ? $('bhmHeroImage') : card.querySelector('img');
        const copy = hero ? $('bhmHeroCopy') : card.querySelector('[data-drag="text"]');
        const button = hero ? $('bhmHeroButton') : card.querySelector('[data-drag="button"]');
        const kicker = hero ? $('bhmHeroKicker') : card.querySelector('[data-side-kicker]');
        const title = hero ? $('bhmHeroTitle') : card.querySelector('[data-side-title]');
        const description = hero ? $('bhmHeroDescription') : card.querySelector('[data-side-description]');
        const buttonLabel = button.querySelector('[data-content-field="button_text"]');
        const url = b.preview_url || b.image_url;
        if (image.getAttribute('src') !== url) image.src = url;
        image.alt = b.title || b.label;
        putText(kicker, b.kicker);
        putText(title, b.title);
        putText(description, b.description);
        putText(buttonLabel, b.button_text);
        copy.style.left = b.text_x + '%';
        copy.style.top = b.text_y + '%';
        copy.style.color = b.text_color;
        copy.style.fontFamily = fontCss(b.font_family);
        title.style.fontSize = b.title_size + 'px';
        description.style.fontSize = b.description_size + 'px';
        button.style.left = b.button_x + '%';
        button.style.top = b.button_y + '%';
        button.style.fontSize = b.button_size + 'px';
        button.style.color = b.button_color;
        button.style.backgroundColor = b.button_bg;
        button.style.fontFamily = copy.style.fontFamily;
        // The buyer page uses the same title/body/button sizes as stored in the banner.
        // Important overrides ensure editor breakpoints never downsize a saved font.
        title.style.setProperty('font-size', b.title_size + 'px', 'important');
        description.style.setProperty('font-size', b.description_size + 'px', 'important');
        button.style.setProperty('font-size', b.button_size + 'px', 'important');
        // Match the buyer's dark/light copy styling without adding editing boxes.
        const hex = String(b.text_color || '').replace('#', '');
        const dark = /^[0-9a-f]{6}$/i.test(hex) && (
            parseInt(hex.slice(0, 2), 16) + parseInt(hex.slice(2, 4), 16) + parseInt(hex.slice(4, 6), 16)
        ) < 420;
        card.classList.toggle('is-dark-copy', hero && dark);
        const active = selected === slot;
        card.classList.toggle('is-active', active);
        copy.classList.toggle('is-selected', active);
        button.classList.toggle('is-selected', active && !!b.button_text);
        button.hidden = !b.button_text && document.activeElement !== buttonLabel;
    }

    function render() {
        $('bhmSlot').value = selected;
        $('bhmCurrentLabel').textContent = banners[selected].label;
        root.querySelectorAll('.bhm-slot').forEach((tab) => {
            const slot = tab.dataset.slot;
            tab.setAttribute('aria-pressed', String(slot === selected));
            const url = banners[slot].preview_url || banners[slot].image_url;
            if (tab.querySelector('img').getAttribute('src') !== url) tab.querySelector('img').src = url;
        });
        const heroCard = $('bhmHeroImage').closest('.bhm-stage-card');
        heroCard.dataset.previewSlot = activeHero;
        paint(activeHero);
        paint('side_beauty');
        paint('side_flash');
        const dirty = isDirty(selected);
        const status = $('bhmUnsaved');
        status.textContent = dirty ? '● Unsaved changes in selected banner' : '✓ Current banner published';
        status.classList.toggle('is-dirty', dirty);
    }

    function clearDirty() {
        if (!isDirty(selected)) return;
        if (!window.confirm('Discard your unsaved changes to this banner?')) return;
        resetSelectedFile();
        banners[selected] = JSON.parse(JSON.stringify(originals[selected]));
        populateFields();
        render();
    }

    root.querySelectorAll('.bhm-slot').forEach((tab) => {
        tab.addEventListener('click', () => selectSlot(tab.dataset.slot));
    });

    $('bhmReset').addEventListener('click', clearDirty);

    // Canvas-first editing: optional elements can be added without a content sidebar.
    [
        ['bhmAddKicker', 'kicker', 'Featured collection'],
        ['bhmAddDescription', 'description', 'Add a description'],
        ['bhmAddButton', 'button_text', 'Shop now'],
    ].forEach(([buttonId, key, fallback]) => {
        $(buttonId).addEventListener('click', () => {
            if (!banners[selected][key]) banners[selected][key] = fallback;
            $(fields[key]).value = banners[selected][key];
            render();
            const card = cardFor(selected);
            const editable = card.querySelector('[data-content-field="' + key + '"]') ||
                (key === 'button_text' ? card.querySelector('[data-drag="button"] [data-content-field="button_text"]') : null);
            if (editable && editable.focus) editable.focus();
        });
    });

    // Native color palette, editable HEX and the browser's screen eyedropper.
    const validHex = /^#[0-9a-fA-F]{6}$/;
    const colorIds = ['bhmTextColor', 'bhmButtonBg', 'bhmButtonColor'];
    function setColor(id, hex) {
        if (!validHex.test(hex)) return false;
        $(id).value = hex.toUpperCase();
        $(id).dispatchEvent(new Event('input', { bubbles: true }));
        const hexField = root.querySelector('[data-hex-for="' + id + '"]');
        if (hexField) hexField.value = hex.toUpperCase();
        return true;
    }
    colorIds.forEach((id) => {
        const hexField = root.querySelector('[data-hex-for="' + id + '"]');
        const native = $(id);
        const sync = () => { if (hexField) hexField.value = native.value.toUpperCase(); };
        native.addEventListener('input', sync);
        native.addEventListener('change', sync);
        if (hexField) {
            hexField.addEventListener('change', () => {
                const raw = hexField.value.trim();
                const value = raw.startsWith('#') ? raw : '#' + raw;
                if (!setColor(id, value)) {
                    sync();
                    message('Use a 6-digit HEX color, such as #C88912.');
                }
            });
            hexField.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') { event.preventDefault(); hexField.blur(); }
            });
        }
    });
    root.querySelectorAll('[data-pick-for]').forEach((button) => {
        button.addEventListener('click', async () => {
            const id = button.dataset.pickFor;
            if (!window.EyeDropper) {
                message('Screen eyedropper needs a supported browser (Chrome or Edge over HTTPS). You can still pick any color or enter a HEX value.');
                $(id).click();
                return;
            }
            try {
                const picked = await new window.EyeDropper().open();
                if (picked && setColor(id, picked.sRGBHex)) {
                    message('Sampled ' + picked.sRGBHex.toUpperCase() + ' from your screen. Save & Publish to apply it.');
                }
            } catch (error) {
                if (error.name !== 'AbortError') message('Unable to sample this screen. Choose a color using the swatch instead.');
            }
        });
    });

    // Font names are not limited to a dropdown. Valid names use locally installed
    // fonts or try Google Fonts; unsupported names use a readable fallback.
    const systemFonts = new Set(['arial', 'georgia', 'times', 'times new roman', 'verdana', 'tahoma', 'trebuchet ms', 'courier new', 'serif', 'sans-serif', 'monospace']);
    const loadedFonts = new Set();
    const validFont = /^[\p{L}\p{N}][\p{L}\p{N} -]{0,31}$/u;
    let fontTimer;
    function fontCss(value) {
        const name = String(value || '').trim();
        const known = Object.keys(fonts).find((key) => key.toLowerCase() === name.toLowerCase());
        if (known) return fonts[known];
        return validFont.test(name) ? "'" + name + "', sans-serif" : "'Poppins', sans-serif";
    }
    function loadFont(value) {
        const name = String(value || '').trim();
        if (!validFont.test(name) || systemFonts.has(name.toLowerCase()) || loadedFonts.has(name.toLowerCase())) return;
        loadedFonts.add(name.toLowerCase());
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(name).replace(/%20/g, '+') + '&display=swap';
        document.head.appendChild(link);
    }
    $('bhmFont').addEventListener('input', () => {
        clearTimeout(fontTimer);
        fontTimer = setTimeout(() => loadFont($('bhmFont').value), 300);
    });
    $('bhmFont').addEventListener('change', () => {
        loadFont($('bhmFont').value);
        if (!validFont.test($('bhmFont').value.trim())) {
            message('Font name must be 1–32 letters/numbers, spaces or hyphens.');
        }
    });

    for (const [key, id] of Object.entries(fields)) {
        const input = $(id);
        const update = () => {
            banners[selected][key] = numeric.has(key) ? (Number(input.value) || 0) : input.value;
            render();
        };
        input.addEventListener('input', update);
        input.addEventListener('change', update);
    }

    // Direct editing on the image. The move handles, not the text spans, start dragging.
    root.querySelectorAll('[data-content-field]').forEach((element) => {
        element.addEventListener('click', (event) => event.stopPropagation());
        element.addEventListener('focus', () => {
            const slot = element.closest('[data-preview-slot]').dataset.previewSlot;
            if (selectSlot(slot)) render();
            else element.blur();
        });
        element.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') { event.preventDefault(); element.blur(); }
            if (event.key === 'Escape') { event.preventDefault(); element.blur(); }
        });
        element.addEventListener('paste', (event) => {
            const content = (event.clipboardData || window.clipboardData).getData('text/plain').replace(/\s*\n\s*/g, ' ');
            event.preventDefault();
            const selection = window.getSelection();
            if (!selection || !selection.rangeCount) return;
            const range = selection.getRangeAt(0);
            range.deleteContents();
            const node = document.createTextNode(content);
            range.insertNode(node);
            range.setStartAfter(node);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
            element.dispatchEvent(new Event('input', { bubbles: false }));
        });
        element.addEventListener('input', () => {
            const slot = element.closest('[data-preview-slot]').dataset.previewSlot;
            if (slot !== selected) return;
            const key = element.dataset.contentField;
            let value = (element.textContent || '').replace(/[\r\n]+/g, ' ');
            if (value.length > textLimits[key]) {
                value = value.slice(0, textLimits[key]);
                element.textContent = value;
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(element);
                range.collapse(false);
                selection.removeAllRanges();
                selection.addRange(range);
            }
            banners[selected][key] = value;
            $(fields[key]).value = value;
            // Avoid re-rendering the active editable node: doing so moves the caret.
            render();
        });
    });

    // Clicking a different card selects its banner; the underlying image never moves.
    root.querySelectorAll('.bhm-stage-card').forEach((card) => {
        const slot = () => card.dataset.previewSlot;
        card.addEventListener('click', (event) => {
            if (event.target.closest('[data-replace-image], [data-move], [data-content-field]')) return;
            selectSlot(slot());
        });
        card.addEventListener('keydown', (event) => {
            if (event.target !== card) return;
            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); selectSlot(slot()); }
        });
    });

    // Image replacement is attached to the canvas, with the ribbon button as a fallback.
    const fileInput = $('bhmImage');
    $('bhmReplaceBtn').addEventListener('click', () => fileInput.click());
    root.querySelectorAll('[data-replace-image]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault(); event.stopPropagation();
            const slot = button.closest('[data-preview-slot]').dataset.previewSlot;
            if (selectSlot(slot)) fileInput.click();
        });
    });

    function rejectPendingImage(reason) {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = null;
        delete banners[selected].preview_url;
        fileInput.value = '';
        message(reason);
        render();
    }

    async function acceptFile(file) {
        if (!file) return;
        const request = ++fileRequest;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            rejectPendingImage('Invalid image. Use JPG, PNG or WebP, maximum 5 MB.');
            return;
        }
        const nextUrl = URL.createObjectURL(file);
        const dimensions = await new Promise((resolve) => {
            const image = new Image();
            image.onload = () => resolve({ width: image.naturalWidth, height: image.naturalHeight });
            image.onerror = () => resolve(null);
            image.src = nextUrl;
        });
        if (request !== fileRequest) { URL.revokeObjectURL(nextUrl); return; }
        if (!dimensions || dimensions.width < 400 || dimensions.height < 200) {
            URL.revokeObjectURL(nextUrl);
            rejectPendingImage('Image must be at least 400 × 200 pixels. Current image was not changed.');
            return;
        }
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = nextUrl;
        banners[selected].preview_url = objectUrl;
        message('Image selected: ' + file.name + ' (' + dimensions.width + ' × ' + dimensions.height + '). Save & Publish to apply it.');
        render();
    }
    fileInput.addEventListener('change', () => { void acceptFile(fileInput.files[0]); });

    root.querySelectorAll('.bhm-stage-card').forEach((card) => {
        const isFileDrag = (event) => event.dataTransfer && Array.from(event.dataTransfer.types || []).includes('Files');
        card.addEventListener('dragenter', (event) => {
            if (!isFileDrag(event)) return;
            event.preventDefault();
            card.classList.add('is-drag-over');
        });
        card.addEventListener('dragover', (event) => {
            if (!isFileDrag(event)) return;
            event.preventDefault();
            event.dataTransfer.dropEffect = 'copy';
            card.classList.add('is-drag-over');
        });
        card.addEventListener('dragleave', (event) => {
            if (!card.contains(event.relatedTarget)) card.classList.remove('is-drag-over');
        });
        card.addEventListener('drop', (event) => {
            if (!isFileDrag(event)) return;
            event.preventDefault(); event.stopPropagation();
            card.classList.remove('is-drag-over');
            const file = event.dataTransfer.files[0];
            if (!file || !selectSlot(card.dataset.previewSlot)) return;
            try {
                const transfer = new DataTransfer();
                transfer.items.add(file);
                fileInput.files = transfer.files;
            } catch (error) {
                fileInput.value = '';
                message('Your browser does not support dropped file upload here. Use Replace image instead.');
                return;
            }
            void acceptFile(file);
        });
    });

    function moveOverlay(slot, kind, x, y) {
        const keyX = kind === 'button' ? 'button_x' : 'text_x';
        const keyY = kind === 'button' ? 'button_y' : 'text_y';
        banners[slot][keyX] = Math.max(0, Math.min(90, Math.round(x)));
        banners[slot][keyY] = Math.max(5, Math.min(95, Math.round(y)));
        $(fields[keyX]).value = banners[slot][keyX];
        $(fields[keyY]).value = banners[slot][keyY];
        render();
    }

    root.querySelectorAll('.bhm-move-handle').forEach((handle) => {
        const slotFor = () => handle.closest('[data-preview-slot]').dataset.previewSlot;
        const kind = handle.dataset.move;
        handle.addEventListener('click', (event) => event.stopPropagation());
        handle.addEventListener('pointerdown', (event) => {
            if (event.button !== 0 || !selectSlot(slotFor())) return;
            event.preventDefault(); event.stopPropagation();
            const slot = slotFor();
            const card = handle.closest('[data-preview-slot]');
            const keyX = kind === 'button' ? 'button_x' : 'text_x';
            const keyY = kind === 'button' ? 'button_y' : 'text_y';
            const startX = Number(banners[slot][keyX]);
            const startY = Number(banners[slot][keyY]);
            const originX = event.clientX;
            const originY = event.clientY;
            const rect = card.getBoundingClientRect();
            handle.setPointerCapture(event.pointerId);
            handle.setAttribute('aria-grabbed', 'true');
            const move = (e) => {
                moveOverlay(slot, kind,
                    startX + (e.clientX - originX) * 100 / rect.width,
                    startY + (e.clientY - originY) * 100 / rect.height);
            };
            const finish = () => {
                handle.removeEventListener('pointermove', move);
                handle.removeEventListener('pointerup', finish);
                handle.removeEventListener('pointercancel', finish);
                handle.removeAttribute('aria-grabbed');
            };
            handle.addEventListener('pointermove', move);
            handle.addEventListener('pointerup', finish);
            handle.addEventListener('pointercancel', finish);
        });
        handle.addEventListener('keydown', (event) => {
            const key = event.key;
            if (!['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(key)) return;
            if (!selectSlot(slotFor())) return;
            event.preventDefault(); event.stopPropagation();
            const slot = slotFor();
            const step = event.shiftKey ? 5 : 1;
            const xKey = kind === 'button' ? 'button_x' : 'text_x';
            const yKey = kind === 'button' ? 'button_y' : 'text_y';
            moveOverlay(slot, kind,
                Number(banners[slot][xKey]) + (key === 'ArrowLeft' ? -step : key === 'ArrowRight' ? step : 0),
                Number(banners[slot][yKey]) + (key === 'ArrowUp' ? -step : key === 'ArrowDown' ? step : 0));
        });
    });

    // Validation for direct-canvas content that is mirrored to visually hidden fields.
    $('bhmForm').addEventListener('submit', (event) => {
        if (!banners[selected].title.trim()) {
            event.preventDefault();
            message('A main title is required. Click the title on the image to edit it.');
            const title = cardFor(selected).querySelector('[data-content-field="title"]');
            if (title && title.focus) title.focus();
            return;
        }
        if (!validFont.test(String(banners[selected].font_family || '').trim())) {
            event.preventDefault();
            message('Enter a valid font family name (up to 32 letters, numbers, spaces or hyphens).');
            $('bhmFont').focus();
        }
    });
    $('bhmForm').addEventListener('submit', (event) => {
        if (event.defaultPrevented) return;
        $('bhmPublish').disabled = true;
        $('bhmPublish').textContent = 'Publishing…';
    });

    // The exact buyer content rectangle at the same viewport:
    // buyer .sari-home-shell max-width:1500px, Tailwind horizontal paddings
    // xl:40px / lg:32px / sm:24px / base:16px per side.
    // Use intrinsic buyer CSS pixels, then zoom the whole composition as one unit.
    // This avoids changing font sizes, image crops, or percentage positioning.
    const previewScroller = $('bhmPreviewScroller');
    const previewFrame = $('bhmStageFrame');
    const stage = $('bhmStage');
    let zoomMode = 'fit';
    function buyerCanvasMetrics() {
        const vw = window.innerWidth;
        const sidePadding = vw >= 1280 ? 40 : vw >= 1024 ? 32 : vw >= 640 ? 24 : 16;
        const width = Math.max(280, Math.min(vw, 1500) - (sidePadding * 2));
        if (vw <= 639) return { width, height: 220 + 10 + 132 + 10 + 132, layout: 'phone' };
        if (vw <= 767) return { width, height: 238 + 10 + 112, layout: 'tablet' };
        if (vw <= 1023) return { width, height: 270 + 10 + 132, layout: 'tablet' };
        return { width, height: Math.min(390, Math.max(350, vw * .29)), layout: 'desktop' };
    }
    function syncBuyerCanvas() {
        const { width, height, layout } = buyerCanvasMetrics();
        stage.dataset.buyerLayout = layout;
        stage.style.width = width + 'px';
        stage.style.height = height + 'px';
        const available = previewScroller.clientWidth;
        const scale = zoomMode === 'fit' && available > 0 ? Math.min(1, available / width) : 1;
        stage.style.transform = 'scale(' + scale + ')';
        previewFrame.style.width = (width * scale) + 'px';
        previewFrame.style.height = (height * scale) + 'px';
        $('bhmBuyerDimensions').textContent = 'Buyer: ' + Math.round(width) + ' × ' + Math.round(height) + ' px';
        $('bhmZoomReadout').textContent = Math.round(scale * 100) + '%';
        $('bhmFitView').setAttribute('aria-pressed', String(zoomMode === 'fit'));
        $('bhmActualView').setAttribute('aria-pressed', String(zoomMode === 'actual'));
    }
    $('bhmFitView').addEventListener('click', () => { zoomMode = 'fit'; syncBuyerCanvas(); });
    $('bhmActualView').addEventListener('click', () => { zoomMode = 'actual'; syncBuyerCanvas(); });
    window.addEventListener('resize', syncBuyerCanvas);
    if (window.ResizeObserver) new ResizeObserver(syncBuyerCanvas).observe(previewScroller);

    populateFields();
    render();
    syncBuyerCanvas();
})();
