@extends('layouts.seller')



@section('title', 'Inventory — SARI Seller')

@section('page-title', 'Inventory')



@push('styles')
<link rel="stylesheet" href="{{ asset('css/seller/inventory.css') }}?v=20261007-1">
@endpush

@section('content')

@php

    $goodStock = max((int) ($stats['products'] ?? 0) - (int) ($stats['low_stock'] ?? 0) - (int) ($stats['out_of_stock'] ?? 0), 0);

    $lowStock = (int) ($stats['low_stock'] ?? 0);

    $outStock = (int) ($stats['out_of_stock'] ?? 0);

    $totalProducts = max((int) ($stats['products'] ?? 0), 1);



    $goodPercent = round(($goodStock / $totalProducts) * 100);

    $lowPercent = round(($lowStock / $totalProducts) * 100);

    $outPercent = max(0, 100 - $goodPercent - $lowPercent);



    $stockChartItems = [

        ['label' => 'Healthy', 'value' => $goodStock, 'class' => 'healthy'],

        ['label' => 'Low', 'value' => $lowStock, 'class' => 'low'],

        ['label' => 'Out', 'value' => $outStock, 'class' => 'out'],

    ];

    $maxChartValue = max(1, $goodStock, $lowStock, $outStock);

@endphp









<div class="inventory-shell">

    @if(session('success'))

        <div class="mb-4 rounded-[12px] border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[9px] font-medium text-[#4f7d63]">{{ session('success') }}</div>

    @endif



    @if($errors->any())

        <div class="mb-4 rounded-[12px] border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[9px] font-medium text-[#a65353]">{{ $errors->first() }}</div>

    @endif



    <header class="inventory-head inventory-head--single">

        <div>

            <p class="inventory-eyebrow">Seller Catalog</p>

            <h1 class="inventory-title">Inventory</h1>

            <p class="inventory-subtitle">

                Monitor product stock, update quantities, and manage inventory across your catalog.

                Track product images, variants, and adjustment history all in one place.

            </p>

        </div>

    </header>



    <section class="inventory-stat-grid">

        <div class="inventory-stat-card">

            <span class="inventory-stat-icon inventory-stat-icon--products">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"></path><path d="m4.5 7.8 7.5 4.3 7.5-4.3"></path><path d="M12 12v9"></path></svg>

            </span>

            <div>

                <p class="inventory-stat-label">Products</p>

                <p class="inventory-stat-value">{{ number_format($stats['products'] ?? 0) }}</p>

                <p class="inventory-stat-note">Tracked product listings</p>

            </div>

        </div>



        <div class="inventory-stat-card">

            <span class="inventory-stat-icon inventory-stat-icon--units">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 8 4-8 4-8-4 8-4Z"></path><path d="m4 12 8 4 8-4"></path><path d="m4 17 8 4 8-4"></path></svg>

            </span>

            <div>

                <p class="inventory-stat-label">Total Units</p>

                <p class="inventory-stat-value">{{ number_format($stats['units'] ?? 0) }}</p>

                <p class="inventory-stat-note">Combined available stock</p>

            </div>

        </div>



        <div class="inventory-stat-card">

            <span class="inventory-stat-icon inventory-stat-icon--low">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 4 3.8 18.5a1.2 1.2 0 0 0 1 1.8h14.4a1.2 1.2 0 0 0 1-1.8L12 4Z"></path><path d="M12 9v5"></path><path d="M12 17h.01"></path></svg>

            </span>

            <div>

                <p class="inventory-stat-label">Low Stock</p>

                <p class="inventory-stat-value">{{ number_format($stats['low_stock'] ?? 0) }}</p>

                <p class="inventory-stat-note">Needs replenishment soon</p>

            </div>

        </div>



        <div class="inventory-stat-card">

            <span class="inventory-stat-icon inventory-stat-icon--out">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="m9 9 6 6"></path><path d="m15 9-6 6"></path></svg>

            </span>

            <div>

                <p class="inventory-stat-label">Out of Stock</p>

                <p class="inventory-stat-value">{{ number_format($stats['out_of_stock'] ?? 0) }}</p>

                <p class="inventory-stat-note">Currently unavailable</p>

            </div>

        </div>

    </section>



    <section class="inventory-insight-grid">

        <div class="inventory-panel">

            <div class="inventory-panel-head">

                <span class="inventory-panel-icon">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 19V9"></path><path d="M10 19V5"></path><path d="M15 19v-7"></path><path d="M20 19V3"></path></svg>

                </span>

                <div>

                    <h2 class="inventory-panel-title">Stock Overview</h2>

                    <p class="inventory-panel-subtitle">Quick comparison of healthy, low, and out-of-stock listings.</p>

                </div>

            </div>



            <div class="inventory-health-cards">

                <div class="inventory-health-card">

                    <div class="inventory-health-label"><span class="inventory-health-dot inventory-health-dot--healthy"></span>Healthy</div>

                    <div class="inventory-health-value"><strong>{{ number_format($goodStock) }}</strong><span>listings</span></div>

                    <div class="inventory-progress-row">

                        <div class="inventory-progress inventory-progress--healthy"><span style="width: {{ $goodPercent }}%;"></span></div>

                        <span class="inventory-progress-percent">{{ $goodPercent }}%</span>

                    </div>

                </div>



                <div class="inventory-health-card">

                    <div class="inventory-health-label"><span class="inventory-health-dot inventory-health-dot--low"></span>Low Stock</div>

                    <div class="inventory-health-value"><strong>{{ number_format($lowStock) }}</strong><span>listings</span></div>

                    <div class="inventory-progress-row">

                        <div class="inventory-progress inventory-progress--low"><span style="width: {{ $lowPercent }}%;"></span></div>

                        <span class="inventory-progress-percent">{{ $lowPercent }}%</span>

                    </div>

                </div>



                <div class="inventory-health-card">

                    <div class="inventory-health-label"><span class="inventory-health-dot inventory-health-dot--out"></span>Out of Stock</div>

                    <div class="inventory-health-value"><strong>{{ number_format($outStock) }}</strong><span>listings</span></div>

                    <div class="inventory-progress-row">

                        <div class="inventory-progress inventory-progress--out"><span style="width: {{ $outPercent }}%;"></span></div>

                        <span class="inventory-progress-percent">{{ $outPercent }}%</span>

                    </div>

                </div>

            </div>

        </div>



        <div class="inventory-panel">

            <div class="inventory-panel-head">

                <span class="inventory-panel-icon">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v9h9"></path><path d="M19.4 16.5A8 8 0 1 1 8 4.1"></path></svg>

                </span>

                <div>

                    <h2 class="inventory-panel-title">Stock Health</h2>

                    <p class="inventory-panel-subtitle">Donut summary based on all tracked products.</p>

                </div>

            </div>



            <div class="inventory-donut-body">

                <div class="inventory-donut" style="--good: {{ $goodPercent }}; --low: {{ $lowPercent }};">

                    <div class="inventory-donut-center">

                        <div>

                            <strong>{{ $goodPercent }}%</strong>

                            <span>Healthy stock</span>

                        </div>

                    </div>

                </div>



                <div class="inventory-legend">

                    <div class="inventory-legend-row">

                        <span class="inventory-health-dot inventory-health-dot--healthy"></span>

                        <span>Healthy stock</span>

                        <strong>{{ number_format($goodStock) }}</strong>

                        <em>{{ $goodPercent }}%</em>

                    </div>

                    <div class="inventory-legend-row">

                        <span class="inventory-health-dot inventory-health-dot--low"></span>

                        <span>Low stock</span>

                        <strong>{{ number_format($lowStock) }}</strong>

                        <em>{{ $lowPercent }}%</em>

                    </div>

                    <div class="inventory-legend-row">

                        <span class="inventory-health-dot inventory-health-dot--out"></span>

                        <span>Out of stock</span>

                        <strong>{{ number_format($outStock) }}</strong>

                        <em>{{ $outPercent }}%</em>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <section class="inventory-table-panel">

        <div class="inventory-table-head">

            <div class="inventory-table-head-copy">

                <span class="inventory-table-head-icon">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"></path><path d="m4.5 7.8 7.5 4.3 7.5-4.3"></path><path d="M12 12v9"></path></svg>

                </span>

                <div>

                    <h2 class="inventory-table-title">Stock by Product</h2>

                    <p class="inventory-table-subtitle">View product images, search items, and adjust stock with quick updates.</p>

                </div>

            </div>



            <div class="inventory-table-search-wrap">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>

                <input id="inventoryTableSearch" class="inventory-table-search" type="search" autocomplete="off" placeholder="Search products...">

            </div>

        </div>



        <div class="inventory-grid-head">

            <span>Product</span>

            <span>SKU</span>

            <span>Status</span>

            <span>Current Stock</span>

            <span>Variant</span>

            <span>Adjust Quantity</span>

            <span>Actions</span>

        </div>



        <div id="inventoryProductList">

            @forelse($products as $product)

                @php

                    $currentStock = (int) ($product->stock ?? 0);

                    $threshold = (int) ($product->low_stock_threshold ?? 0);

                    $state = $currentStock <= 0 ? 'out' : ($currentStock <= $threshold ? 'low' : 'healthy');

                    $stateLabel = $currentStock <= 0 ? 'Out of Stock' : ($currentStock <= $threshold ? 'Low Stock' : 'Healthy');



                    $variantSearch = $product->activeVariants->map(function ($variant) {

                        return trim(($variant->sku ?: ('#' . $variant->id)) . ' ' . ($variant->name ?? '') . ' ' . ($variant->option_summary ?? ''));

                    })->implode(' ');



                    $imagePath = $product->cover_image_url

                        ?? $product->cover_image_path

                        ?? $product->image_url

                        ?? $product->image_path

                        ?? optional($product->images->first())->path

                        ?? optional($product->galleryImages->first())->path

                        ?? null;



                    $imageUrl = null;

                    if ($imagePath) {

                        $imageUrl = \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://', '/storage/', '/images/', '/uploads/'])

                            ? $imagePath

                            : asset('storage/' . ltrim($imagePath, '/'));

                    }



                    $productCategory = $product->category ?? $product->custom_category ?? 'Product listing';

                @endphp



                <div

                    class="inventory-row inventory-searchable-row"

                    data-search="{{ strtolower(trim($product->name . ' ' . ($product->sku ?: '') . ' ' . $productCategory . ' ' . $variantSearch)) }}"

                    data-product-stock="{{ $currentStock }}"

                    data-low-stock-threshold="{{ $threshold }}"

                >

                    <div class="inventory-product-cell">

                        <div class="inventory-product-thumb">

                            @if($imageUrl)

                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" decoding="async">

                            @else

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="3"></rect><path d="m7 15 3-3 3 3 4-5 3 4"></path><circle cx="9" cy="9" r="1.2" fill="currentColor" stroke="none"></circle></svg>

                            @endif

                        </div>



                        <div class="min-w-0">

                            <p class="inventory-product-name">{{ $product->name }}</p>

                            <p class="inventory-product-category">{{ $productCategory }}</p>

                        </div>

                    </div>



                    <div class="inventory-sku">{{ $product->sku ?: '—' }}</div>



                    <div>

                        <span class="inventory-badge {{ $state }}" data-inventory-status-badge>{{ $stateLabel }}</span>

                    </div>



                    <div class="inventory-stock-number" data-inventory-current-stock>{{ number_format($currentStock) }}</div>



                    <form method="POST" action="{{ route('seller.inventory.adjust', $product) }}" class="contents">

                        @csrf



                        <div>

                            @if($product->activeVariants->isNotEmpty())

                                <select name="variant_id" required class="inventory-select" data-inventory-variant-select>

                                    <option value="" data-stock="">Select variant</option>

                                    @foreach($product->activeVariants as $variant)

                                        <option

                                            value="{{ $variant->id }}"

                                            data-stock="{{ (int) $variant->stock }}"

                                        >

                                            {{ $variant->sku ?: '#'.$variant->id }} · stock {{ $variant->stock }}

                                        </option>

                                    @endforeach

                                </select>

                            @else

                                <input type="hidden" name="variant_id" value="">

                                <select class="inventory-select" disabled>

                                    <option>No variants</option>

                                </select>

                            @endif

                        </div>



                        <div>

                            <input

                                name="quantity"

                                type="number"

                                min="0"

                                required

                                value="{{ $product->activeVariants->isEmpty() ? $currentStock : '' }}"

                                placeholder="New stock"

                                class="inventory-qty"

                                data-inventory-quantity

                            >

                            <input type="hidden" name="reason" value="manual_adjustment">

                        </div>



                        <div>

                            <button

                                class="inventory-update-btn"

                                type="submit"

                                data-inventory-update

                                @disabled($product->activeVariants->isNotEmpty())

                            >

                                Update

                            </button>

                        </div>

                    </form>

                </div>

            @empty

                <div class="inventory-empty">No active products found in inventory.</div>

            @endforelse

        </div>



        <div id="inventoryNoSearchResults" class="inventory-empty inventory-hidden">

            No matching products found.

        </div>



        @if($products->hasPages())

            <div class="border-t border-[#e7ebf0] p-4">{{ $products->links() }}</div>

        @endif

    </section>



    <section class="inventory-activity-panel">

        <div class="inventory-activity-head">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v5l3 2"></path></svg>

            <div>

                <h2 class="inventory-activity-title">Recent Inventory Adjustments</h2>

                <p class="inventory-activity-subtitle">View the latest manual stock changes and system updates.</p>

            </div>

        </div>



        @if($movements->isEmpty())

            <div class="inventory-activity-empty">

                <div>

                    <span class="inventory-activity-empty-icon">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 3h10l3 3v15H6z"></path><path d="M16 3v4h4"></path><path d="M9 11h7"></path><path d="M9 15h7"></path></svg>

                    </span>

                    <strong>No recent adjustments</strong>

                    <span>No manual adjustments yet. Any stock changes you make will appear here.</span>

                </div>

            </div>

        @else

            <div class="inventory-activity-list">

                @foreach($movements as $movement)

                    <div class="inventory-activity-item">

                        <div>

                            <div class="inventory-activity-name">Product #{{ $movement->seller_product_id }} · {{ str_replace('_', ' ', ucfirst($movement->reason)) }}</div>

                            <div class="inventory-activity-meta">{{ optional($movement->created_at)->format('M d, Y · h:i A') ?: 'Recent update' }}</div>

                        </div>

                        <div class="inventory-activity-value">{{ $movement->quantity_before }} → {{ $movement->quantity_after }}</div>

                    </div>

                @endforeach

            </div>

        @endif

    </section>

</div>

@endsection





@push('scripts')

<script>

(() => {

    const bootInventoryPage = () => {

        const tableSearch = document.getElementById('inventoryTableSearch');

        const rows = Array.from(document.querySelectorAll('.inventory-searchable-row'));

        const emptyState = document.getElementById('inventoryNoSearchResults');



        const applyFilter = (value = '') => {

            if (!emptyState) return;



            const keyword = String(value || '').trim().toLowerCase();

            let visible = 0;



            rows.forEach((row) => {

                const haystack = row.getAttribute('data-search') || '';

                const matched = !keyword || haystack.includes(keyword);

                row.classList.toggle('inventory-hidden', !matched);

                if (matched) visible += 1;

            });



            emptyState.classList.toggle('inventory-hidden', visible !== 0);

        };



        tableSearch?.addEventListener('input', () => {

            applyFilter(tableSearch.value);

        }, { passive: true });



        const setStatusBadge = (badge, stock, threshold) => {

            if (!badge) return;



            badge.classList.remove('healthy', 'low', 'out');



            if (stock <= 0) {

                badge.classList.add('out');

                badge.textContent = 'Out of Stock';

                return;

            }



            if (stock <= threshold) {

                badge.classList.add('low');

                badge.textContent = 'Low Stock';

                return;

            }



            badge.classList.add('healthy');

            badge.textContent = 'Healthy';

        };



        rows.forEach((row) => {

            const variantSelect = row.querySelector('[data-inventory-variant-select]');

            const quantityInput = row.querySelector('[data-inventory-quantity]');

            const updateButton = row.querySelector('[data-inventory-update]');

            const currentStockNode = row.querySelector('[data-inventory-current-stock]');

            const statusBadge = row.querySelector('[data-inventory-status-badge]');



            if (!variantSelect) return;



            const productStock = Number(row.dataset.productStock || 0);

            const threshold = Number(row.dataset.lowStockThreshold || 0);



            const syncVariantState = () => {

                const selectedOption = variantSelect.options[variantSelect.selectedIndex];

                const hasVariant = Boolean(variantSelect.value);

                const selectedStock = hasVariant

                    ? Number(selectedOption?.dataset?.stock || 0)

                    : productStock;



                if (currentStockNode) {

                    currentStockNode.textContent = selectedStock.toLocaleString('en-PH');

                }



                setStatusBadge(statusBadge, selectedStock, threshold);



                if (quantityInput) {

                    if (hasVariant) {

                        quantityInput.value = String(selectedStock);

                        quantityInput.disabled = false;

                        quantityInput.focus({ preventScroll: true });

                        quantityInput.select?.();

                    } else {

                        quantityInput.value = '';

                        quantityInput.disabled = true;

                    }

                }



                if (updateButton) {

                    updateButton.disabled = !hasVariant;

                }

            };



            quantityInput && (quantityInput.disabled = true);

            variantSelect.addEventListener('change', syncVariantState);

            syncVariantState();

        });

    };



    if (window.__SARI_SELLER_AFTER_PAINT__) {

        window.__SARI_SELLER_AFTER_PAINT__(bootInventoryPage);

    } else {

        window.requestAnimationFrame(() => window.requestAnimationFrame(bootInventoryPage));

    }

})();

</script>

@endpush
