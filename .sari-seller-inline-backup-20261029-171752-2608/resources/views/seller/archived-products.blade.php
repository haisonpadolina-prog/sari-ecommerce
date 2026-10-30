@extends('layouts.seller')

@section('title', 'Archived Products — SARI Seller')

@section('page-title', 'Archived Products')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/archived-products.css') }}?v=20261007-1">
@endpush

@section('content')

@php

    $totalHistoryCount = $products->count();

    $archivedCount = $products->where('archive_reason', '!=', 'deleted')->count();

    $deletedCount = $products->where('archive_reason', 'deleted')->count();

    $approvedHistoryCount = $products->where('moderation_status', 'approved')->count();

@endphp



<div class="archive-shell">

    @if (session('success'))

        <div class="mb-4 rounded-[14px] border border-[#d6e6dc] bg-[#f7fbf8] px-4 py-3 text-[9px] font-medium text-[#4f7d63]">

            {{ session('success') }}

        </div>

    @endif

    @if ($errors->any())

        <div class="mb-4 rounded-[14px] border border-[#ecd5d5] bg-[#fff8f8] px-4 py-3 text-[9px] font-medium text-[#9f5f5f]">

            {{ $errors->first() }}

        </div>

    @endif

    <header class="archive-header">

        <p class="archive-eyebrow">Archived Products</p>

        <h1 class="archive-title"><span>Archived</span><span>Products</span></h1>

        <p class="archive-subtitle">Search, review, restore, or permanently remove inactive product records from your catalog.</p>

    </header>

    <section class="archive-filter-panel" aria-label="Archive filters">

        <label class="archive-search-field">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>

            <input id="archiveProductSearch" type="search" placeholder="Search archived products..." autocomplete="off">

        </label>

        <div class="archive-history-dropdown" data-history-dropdown>

            <select id="archiveProductFilter" class="archive-native-select" aria-hidden="true" tabindex="-1">

                <option value="">All History</option>

                <option value="archived">Archived</option>

                <option value="deleted">Deleted</option>

            </select>

            <button

                id="archiveHistoryButton"

                type="button"

                class="archive-history-button"

                aria-haspopup="listbox"

                aria-expanded="false"

            >

                <span class="archive-history-button-left">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">

                        <path d="M4 7h16"></path>

                        <path d="M6 7v12h12V7"></path>

                        <path d="M9 11h6"></path>

                    </svg>

                    <span id="archiveHistoryLabel">All History</span>

                </span>

                <svg class="archive-history-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                    <path d="m7 10 5 5 5-5"></path>

                </svg>

            </button>

            <div id="archiveHistoryMenu" class="archive-history-menu" role="listbox" hidden>

                <button type="button" class="archive-history-option is-selected" data-history-value="">

                    <span>

                        <strong>All History</strong>

                        <small>Show every inactive product</small>

                    </span>

                    <svg class="archive-history-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                        <path d="m6 12 4 4 8-8"></path>

                    </svg>

                </button>

                <button type="button" class="archive-history-option" data-history-value="archived">

                    <span>

                        <strong>Archived</strong>

                        <small>Products available to restore</small>

                    </span>

                    <svg class="archive-history-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                        <path d="m6 12 4 4 8-8"></path>

                    </svg>

                </button>

                <button type="button" class="archive-history-option" data-history-value="deleted">

                    <span>

                        <strong>Deleted</strong>

                        <small>Recoverable deleted records</small>

                    </span>

                    <svg class="archive-history-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                        <path d="m6 12 4 4 8-8"></path>

                    </svg>

                </button>

            </div>

        </div>

        <button id="archiveClearFilters" type="button">Clear</button>

    </section>

    <div class="archive-result-row">

        <span id="archiveResultCount">{{ $products->count() }} archived product{{ $products->count() === 1 ? '' : 's' }}</span>

    </div>

    <section>

        <div id="archiveProductGrid" class="archive-card-grid">

            @forelse ($products as $product)

                @php

                    $archiveType = $product->archive_reason === 'deleted' ? 'deleted' : 'archived';

                    $moderationState = in_array($product->moderation_status, ['pending', 'approved', 'flagged', 'rejected'], true)

                        ? $product->moderation_status

                        : 'default';

                    $moderationLabel = match ($product->moderation_status) {

                        'pending' => 'Pending Review',

                        'approved' => 'Approved',

                        'flagged' => 'Flagged',

                        'rejected' => 'Rejected',

                        default => ucfirst((string) ($product->moderation_status ?: 'Inactive')),

                    };

                @endphp

                <article

                    class="archive-product-card"

                    data-archive-product

                    data-archive-type="{{ $archiveType }}"

                    data-moderation-status="{{ strtolower((string) $product->moderation_status) }}"

                    data-archive-search="{{ strtolower(trim(($product->name ?? '') . ' ' . ($product->category ?? '') . ' ' . ($product->sku ?? '') . ' ' . ($product->brand ?? ''))) }}"

                >

                    <div class="archive-card-media">

                        @if ($product->image_path)

                            <img src="{{ route('seller.products.image', $product) }}" alt="{{ $product->name }}" loading="lazy" decoding="async">

                        @else

                            <div class="archive-image-placeholder">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="4" width="16" height="16" rx="3"></rect><path d="m6.5 16 3.5-3.5 2.5 2.5 2-2 3 3"></path></svg>

                            </div>

                        @endif

                        <div class="archive-card-badge-row">

                            <span class="archive-badge {{ $moderationState }}">{{ $moderationLabel }}</span>

                            <span class="archive-type-badge {{ $archiveType === 'deleted' ? 'deleted' : '' }}">{{ ucfirst($archiveType) }}</span>

                        </div>

                    </div>

                    <div class="archive-card-body">

                        <div class="archive-card-mainline">

                            <h2 class="archive-card-name">{{ $product->name }}</h2>

                            <span class="archive-card-price">₱{{ number_format((float) $product->price, 2) }}</span>

                        </div>

                        <p class="archive-card-meta">

                            {{ $product->category ?: 'Uncategorized' }}

                            @if($product->brand)

                                · {{ $product->brand }}

                            @endif

                        </p>

                        <div class="archive-card-divider"></div>

                        <div class="archive-card-footer">

                            <div>

                                <div class="archive-card-stock">{{ number_format((int) ($product->stock ?? 0)) }} in stock</div>

                                <div class="archive-card-date">

                                    {{ $product->archived_at?->diffForHumans() ?: 'Archived recently' }}

                                    @if($product->sku)

                                        · {{ $product->sku }}

                                    @endif

                                </div>

                            </div>

                            <div class="archive-card-actions">

                                <form

                                    method="POST"

                                    action="{{ route('seller.products.restore', $product) }}"

                                    class="archive-restore-form"

                                >

                                    @csrf

                                    <button

                                        type="submit"

                                        class="archive-icon-button restore"

                                        title="Restore product"

                                        aria-label="Restore product"

                                        @if ($seller->isSuspended()) disabled @endif

                                    >

                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7"></path><path d="M3 3v6h6"></path></svg>

                                    </button>

                                </form>

                                <button

                                    type="button"

                                    class="archive-icon-button delete"

                                    title="Delete permanently"

                                    aria-label="Delete permanently"

                                    data-archive-delete-open

                                    data-product-name="{{ $product->name }}"

                                    data-delete-url="{{ route('seller.products.delete', $product) }}"

                                    @if ($seller->isSuspended()) disabled @endif

                                >

                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg>

                                </button>

                            </div>

                        </div>

                    </div>

                </article>

            @empty

                <div class="archive-empty-state" data-archive-base-empty>

                    <div>

                        <span class="archive-empty-icon">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M6 7v12h12V7"></path><path d="M9 11h6"></path></svg>

                        </span>

                        <div class="archive-empty-title">No archived products</div>

                        <div class="archive-empty-copy">Products you archive or remove from your active catalog will appear here.</div>

                    </div>

                </div>

            @endforelse

        </div>

    </section>

    <div id="archiveDeleteModal" class="archive-delete-modal" aria-hidden="true">

        <div class="archive-delete-dialog" role="dialog" aria-modal="true" aria-labelledby="archiveDeleteTitle">

            <span class="archive-delete-warning-icon">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path></svg>

            </span>

            <div class="archive-delete-kicker">Permanent action</div>

            <h2 id="archiveDeleteTitle" class="archive-delete-title">Are you sure you want to delete this?</h2>

            <p class="archive-delete-copy">This permanently removes the product catalog record. This action cannot be undone. Products linked to protected transaction history may be blocked by the system.</p>

            <div id="archiveDeleteProductName" class="archive-delete-product">Product</div>

            <form id="archivePermanentDeleteForm" method="POST" action="">

                @csrf

                <input type="hidden" name="permanent" value="1">

                <div class="archive-delete-actions">

                    <button id="archiveDeleteCancel" type="button">Cancel</button>

                    <button id="archiveDeleteConfirm" type="submit">Delete permanently</button>

                </div>

            </form>

        </div>

    </div>

    <div id="archiveToastContainer" class="pointer-events-none fixed bottom-5 right-5 z-[260] flex w-[min(92vw,360px)] flex-col gap-3"></div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    const initArchivePage = () => {
        const shell = document.querySelector('.archive-shell');
        if (!shell || shell.dataset.archiveInitialized === '1') return;
        shell.dataset.archiveInitialized = '1';

        window.__SARI_SELLER_ARCHIVE_LIFECYCLE__?.abort();

        const lifecycle = new AbortController();
        const { signal } = lifecycle;

        window.__SARI_SELLER_ARCHIVE_LIFECYCLE__ = lifecycle;

        const searchInput = document.getElementById('archiveProductSearch');
        const typeFilter = document.getElementById('archiveProductFilter');
        const historyButton = document.getElementById('archiveHistoryButton');
        const historyLabel = document.getElementById('archiveHistoryLabel');
        const historyMenu = document.getElementById('archiveHistoryMenu');
        const historyOptions = Array.from(document.querySelectorAll('[data-history-value]'));
        const clearButton = document.getElementById('archiveClearFilters');
        const resultCount = document.getElementById('archiveResultCount');
        const grid = document.getElementById('archiveProductGrid');
        const toastContainer = document.getElementById('archiveToastContainer');

        const deleteModal = document.getElementById('archiveDeleteModal');
        const deleteDialog = deleteModal?.querySelector('.archive-delete-dialog');
        const deleteForm = document.getElementById('archivePermanentDeleteForm');
        const deleteCancel = document.getElementById('archiveDeleteCancel');
        const deleteConfirm = document.getElementById('archiveDeleteConfirm');
        const deleteProductName = document.getElementById('archiveDeleteProductName');

        const totalCountNode = document.querySelector('[data-archive-total-count]');
        const archivedCountNode = document.querySelector('[data-archive-archived-count]');
        const deletedCountNode = document.querySelector('[data-archive-deleted-count]');
        const approvedCountNode = document.querySelector('[data-archive-approved-count]');

        let cards = Array.from(document.querySelectorAll('[data-archive-product]'));
        let pendingDeleteCard = null;
        let deleteTrigger = null;

        /* --------------------------------------------------------------
           Feedback
           -------------------------------------------------------------- */
        const parseResponse = async (response) => {
            const contentType = response.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                return response.json().catch(() => ({}));
            }

            return {};
        };

        const showToast = (type, message) => {
            if (!toastContainer) return;

            const isError = type === 'error';
            const toast = document.createElement('div');

            toast.className =
                'pointer-events-auto translate-y-2 opacity-0 rounded-[14px] border px-4 py-3 shadow-[0_14px_30px_rgba(15,23,42,.10)] transition duration-300';

            toast.style.borderColor = isError ? '#ECD5D5' : '#D8E6DD';
            toast.style.backgroundColor = isError ? '#FFF9F9' : '#F8FBF9';

            const title = document.createElement('p');
            title.className = 'text-[10px] font-bold';
            title.style.color = isError ? '#9F5F5F' : '#507560';
            title.textContent = isError ? 'Action required' : 'Success';

            const copy = document.createElement('p');
            copy.className = 'mt-1 text-[9px] leading-5';
            copy.style.color = isError ? '#866767' : '#5D7566';
            copy.textContent = String(message || '');

            toast.append(title, copy);
            toastContainer.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            window.setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                window.setTimeout(() => toast.remove(), 260);
            }, 3000);
        };

        /* --------------------------------------------------------------
           Search + History dropdown
           -------------------------------------------------------------- */
        const getVisibleCards = () => cards.filter((card) => !card.hidden);

        let filteredEmpty = document.querySelector('[data-archive-filter-empty]');

        if (!filteredEmpty && grid) {
            filteredEmpty = document.createElement('div');
            filteredEmpty.className = 'archive-empty-state archive-filter-empty';
            filteredEmpty.dataset.archiveFilterEmpty = '';
            filteredEmpty.hidden = true;
            filteredEmpty.innerHTML = `
                <div>
                    <span class="archive-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="6"></circle>
                            <path d="m16 16 4 4"></path>
                        </svg>
                    </span>
                    <div class="archive-empty-title">No matching products</div>
                    <div class="archive-empty-copy">Try another search term or change the History filter.</div>
                </div>
            `;
            grid.appendChild(filteredEmpty);
        }

        const updateResultCount = () => {
            if (!resultCount) return;

            const visible = getVisibleCards().length;

            if (cards.length === 0) {
                resultCount.textContent = '0 archived products';
                return;
            }

            if (visible === cards.length) {
                resultCount.textContent =
                    `${cards.length} archived product${cards.length === 1 ? '' : 's'}`;
                return;
            }

            resultCount.textContent =
                `${visible} product${visible === 1 ? '' : 's'} found`;
        };

        const filterArchive = () => {
            const query = String(searchInput?.value || '').trim().toLowerCase();
            const type = String(typeFilter?.value || '').trim().toLowerCase();

            let visible = 0;

            cards.forEach((card) => {
                const searchable = String(card.dataset.archiveSearch || '').toLowerCase();
                const archiveType = String(card.dataset.archiveType || '').toLowerCase();

                const matchesSearch = !query || searchable.includes(query);
                const matchesType = !type || archiveType === type;
                const matches = matchesSearch && matchesType;

                card.hidden = !matches;
                if (matches) visible += 1;
            });

            if (filteredEmpty) {
                filteredEmpty.hidden = !(cards.length > 0 && visible === 0);
            }

            updateResultCount();
        };

        const historyLabelFor = (value) => {
            if (value === 'archived') return 'Archived';
            if (value === 'deleted') return 'Deleted';
            return 'All History';
        };

        const syncHistoryDropdown = (value = '') => {
            const normalized = ['archived', 'deleted'].includes(value) ? value : '';

            if (typeFilter) typeFilter.value = normalized;
            if (historyLabel) historyLabel.textContent = historyLabelFor(normalized);

            historyOptions.forEach((option) => {
                const selected = (option.dataset.historyValue || '') === normalized;
                option.classList.toggle('is-selected', selected);
                option.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
        };

        const closeHistoryMenu = () => {
            if (!historyMenu || !historyButton) return;
            historyMenu.hidden = true;
            historyButton.setAttribute('aria-expanded', 'false');
        };

        const openHistoryMenu = () => {
            if (!historyMenu || !historyButton) return;
            historyMenu.hidden = false;
            historyButton.setAttribute('aria-expanded', 'true');

            const selected =
                historyOptions.find((option) => option.classList.contains('is-selected')) ||
                historyOptions[0];

            selected?.focus();
        };

        const toggleHistoryMenu = () => {
            if (!historyMenu || !historyButton) return;

            if (historyMenu.hidden) {
                openHistoryMenu();
            } else {
                closeHistoryMenu();
            }
        };

        const clearFilters = () => {
            if (searchInput) searchInput.value = '';
            syncHistoryDropdown('');
            closeHistoryMenu();
            filterArchive();
            searchInput?.focus();
        };

        searchInput?.addEventListener('input', filterArchive);

        historyButton?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            toggleHistoryMenu();
        });

        historyOptions.forEach((option, index) => {
            option.setAttribute('role', 'option');

            option.addEventListener('click', () => {
                syncHistoryDropdown(option.dataset.historyValue || '');
                closeHistoryMenu();
                filterArchive();
                historyButton?.focus();
            });

            option.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();

                    const direction = event.key === 'ArrowDown' ? 1 : -1;
                    const nextIndex =
                        (index + direction + historyOptions.length) % historyOptions.length;

                    historyOptions[nextIndex]?.focus();
                    return;
                }

                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeHistoryMenu();
                    historyButton?.focus();
                }
            });
        });

        clearButton?.addEventListener('click', clearFilters);

        document.addEventListener('click', (event) => {
            if (!event.target.closest('[data-history-dropdown]')) {
                closeHistoryMenu();
            }
        }, { signal });

        /* --------------------------------------------------------------
           Card removal after successful restore / permanent delete
           -------------------------------------------------------------- */
        const numberFrom = (node) => {
            return Number(String(node?.textContent || '0').replace(/,/g, '')) || 0;
        };

        const setNumber = (node, value) => {
            if (!node) return;
            node.textContent = Math.max(0, Number(value || 0)).toLocaleString('en-PH');
        };

        const decrementSummary = (card) => {
            if (!card) return;

            setNumber(totalCountNode, numberFrom(totalCountNode) - 1);

            if ((card.dataset.archiveType || '') === 'deleted') {
                setNumber(deletedCountNode, numberFrom(deletedCountNode) - 1);
            } else {
                setNumber(archivedCountNode, numberFrom(archivedCountNode) - 1);
            }

            if ((card.dataset.moderationStatus || '') === 'approved') {
                setNumber(approvedCountNode, numberFrom(approvedCountNode) - 1);
            }
        };

        const renderBaseEmptyIfNeeded = () => {
            if (!grid || cards.length !== 0) return;

            filteredEmpty?.remove();
            filteredEmpty = null;

            if (grid.querySelector('[data-archive-base-empty]')) return;

            const empty = document.createElement('div');
            empty.className = 'archive-empty-state';
            empty.dataset.archiveBaseEmpty = '';
            empty.innerHTML = `
                <div>
                    <span class="archive-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16"></path>
                            <path d="M6 7v12h12V7"></path>
                            <path d="M9 11h6"></path>
                        </svg>
                    </span>
                    <div class="archive-empty-title">No archived products</div>
                    <div class="archive-empty-copy">
                        Products you archive or remove from your active catalog will appear here.
                    </div>
                </div>
            `;

            grid.appendChild(empty);
        };

        const removeCard = (card) => {
            if (!card) return;

            decrementSummary(card);

            card.style.transition = 'opacity .18s ease';
            card.style.opacity = '0';

            window.setTimeout(() => {
                card.remove();
                cards = cards.filter((item) => item !== card);
                renderBaseEmptyIfNeeded();
                filterArchive();
            }, 190);
        };

        /* --------------------------------------------------------------
           Restore
           -------------------------------------------------------------- */
        const handleRestoreSubmit = async (event) => {
            event.preventDefault();

            const form = event.currentTarget;
            const button = form.querySelector('button[type="submit"]');
            const card = form.closest('[data-archive-product]');

            if (!form || !button || !card || button.disabled) return;

            const originalHTML = button.innerHTML;

            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            button.innerHTML = `
                <svg viewBox="0 0 24 24" class="animate-spin" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 12a9 9 0 1 1-2.64-6.36"></path>
                </svg>
            `;

            try {
                const response = await fetch(form.action, {
                    method: form.method || 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(form),
                    credentials: 'same-origin',
                });

                const payload = await parseResponse(response);

                if (!response.ok) {
                    const firstError = payload?.errors
                        ? Object.values(payload.errors)?.[0]?.[0]
                        : null;

                    throw new Error(
                        firstError ||
                        payload?.message ||
                        'Unable to restore this product right now.'
                    );
                }

                removeCard(card);
                showToast('success', payload?.message || 'Product restored successfully.');
            } catch (error) {
                button.disabled = false;
                button.removeAttribute('aria-busy');
                button.innerHTML = originalHTML;

                showToast(
                    'error',
                    error?.message || 'Unable to restore this product.'
                );
            }
        };

        document.querySelectorAll('.archive-restore-form').forEach((form) => {
            form.addEventListener('submit', handleRestoreSubmit);
        });

        /* --------------------------------------------------------------
           Permanent delete confirmation modal
           -------------------------------------------------------------- */
        const openDeleteModal = (button) => {
            if (
                !deleteModal ||
                !deleteForm ||
                !deleteConfirm ||
                !button ||
                button.disabled
            ) {
                return;
            }

            pendingDeleteCard = button.closest('[data-archive-product]');
            deleteTrigger = button;

            const deleteUrl = button.dataset.deleteUrl || '';
            const productName = button.dataset.productName || 'Product';

            if (!pendingDeleteCard || !deleteUrl) {
                showToast('error', 'The delete action is not available for this product.');
                return;
            }

            deleteForm.action = deleteUrl;

            if (deleteProductName) {
                deleteProductName.textContent = productName;
            }

            deleteModal.classList.add('is-open');
            deleteModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');

            window.requestAnimationFrame(() => {
                deleteCancel?.focus();
            });
        };

const closeDeleteModal = ({ restoreFocus = true } = {}) => {
            deleteModal?.classList.remove('is-open');
            deleteModal?.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');

            if (deleteForm) deleteForm.action = '';

            pendingDeleteCard = null;

            if (restoreFocus) {
                deleteTrigger?.focus();
            }

            deleteTrigger = null;
        };

        const handlePermanentDelete = async (event) => {
            event.preventDefault();

            if (
                !deleteForm ||
                !deleteConfirm ||
                !pendingDeleteCard ||
                !deleteForm.action
            ) {
                return;
            }

            const card = pendingDeleteCard;
            const originalText = deleteConfirm.textContent;

            deleteConfirm.disabled = true;
            deleteConfirm.setAttribute('aria-busy', 'true');
            deleteConfirm.textContent = 'Deleting...';

            try {
                const response = await fetch(deleteForm.action, {
                    method: deleteForm.method || 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(deleteForm),
                    credentials: 'same-origin',
                });

                const payload = await parseResponse(response);

                if (!response.ok) {
                    const firstError = payload?.errors
                        ? Object.values(payload.errors)?.[0]?.[0]
                        : null;

                    throw new Error(
                        firstError ||
                        payload?.message ||
                        'Unable to permanently delete this product.'
                    );
                }

                closeDeleteModal({ restoreFocus: false });
                removeCard(card);

                showToast(
                    'success',
                    payload?.message || 'Product permanently deleted.'
                );
            } catch (error) {
                showToast(
                    'error',
                    error?.message || 'Unable to permanently delete this product.'
                );
            } finally {
                deleteConfirm.disabled = false;
                deleteConfirm.removeAttribute('aria-busy');
                deleteConfirm.textContent = originalText;
            }
        };

        document.querySelectorAll('[data-archive-delete-open]').forEach((button) => {
            button.addEventListener('click', () => openDeleteModal(button));
        });

        deleteCancel?.addEventListener('click', () => closeDeleteModal());

        deleteForm?.addEventListener('submit', handlePermanentDelete);

        deleteModal?.addEventListener('click', (event) => {
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });

        deleteDialog?.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;

            if (deleteModal?.classList.contains('is-open')) {
                closeDeleteModal();
                return;
            }

            closeHistoryMenu();
        }, { signal });

        const cleanup = () => {
            shell.dataset.archiveInitialized = '0';
            lifecycle.abort();

            if (window.__SARI_SELLER_ARCHIVE_LIFECYCLE__ === lifecycle) {
                window.__SARI_SELLER_ARCHIVE_LIFECYCLE__ = null;
            }
        };

        document.addEventListener('livewire:navigating', cleanup, { once: true, signal });
        window.addEventListener('beforeunload', cleanup, { once: true, signal });

        /* --------------------------------------------------------------
           Initial state
           -------------------------------------------------------------- */
        syncHistoryDropdown(typeFilter?.value || '');
        filterArchive();
    };

    if (window.__SARI_SELLER_AFTER_PAINT__) {
        window.__SARI_SELLER_AFTER_PAINT__(initArchivePage);
    } else if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initArchivePage, { once: true });
    } else {
        initArchivePage();
    }
})();
</script>
@endpush
