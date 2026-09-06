@php
    $compact = $compact ?? false;

    $flexTablesReady =
        \Illuminate\Support\Facades\Schema::hasTable('seller_product_options')
        && \Illuminate\Support\Facades\Schema::hasTable('seller_product_option_values')
        && \Illuminate\Support\Facades\Schema::hasTable('seller_product_variants')
        && \Illuminate\Support\Facades\Schema::hasTable('seller_product_specifications');

    $flexOptions = collect();
    $flexVariants = collect();
    $flexSpecifications = collect();

    if ($flexTablesReady) {
        $flexOptions = \App\Models\SellerProductOption::query()
            ->with('values')
            ->where('seller_product_id', $product->id)
            ->orderBy('position')
            ->get();

        $flexVariants = \App\Models\SellerProductVariant::query()
            ->where('seller_product_id', $product->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $flexSpecifications = \App\Models\SellerProductSpecification::query()
            ->where('seller_product_id', $product->id)
            ->orderBy('position')
            ->get();
    }

    $hasFlexibleDetails =
        $flexOptions->isNotEmpty()
        || $flexVariants->isNotEmpty()
        || $flexSpecifications->isNotEmpty();

    $variantMinPrice = $flexVariants->isNotEmpty()
        ? (float) $flexVariants->min('price')
        : null;

    $variantMaxPrice = $flexVariants->isNotEmpty()
        ? (float) $flexVariants->max('price')
        : null;

    $variantTotalStock = $flexVariants->sum('stock');
@endphp

@if (!$flexTablesReady)
    <div class="{{ $compact ? 'mt-2' : 'mt-4' }} rounded-xl border border-[#eadfc9] bg-[#fffaf2] px-3 py-2.5">
        <div class="flex items-start gap-2">
            <svg viewBox="0 0 24 24" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 8v5"></path>
                <path d="M12 16.5h.01"></path>
            </svg>

            <div>
                <p class="text-[8px] font-bold text-[#8d681f]">
                    Product options database is not ready
                </p>
                <p class="mt-1 text-[7px] leading-4 text-[#8c7960]">
                    Run the flexible-product migrations before reviewing Seller options and variants.
                </p>
            </div>
        </div>
    </div>
@elseif ($hasFlexibleDetails)
    <details
        {{ $compact ? '' : 'open' }}
        class="{{ $compact ? 'mt-2' : 'mt-4' }} overflow-hidden rounded-[14px] border border-[#e5ddd2] bg-[#fcfbf8]"
    >
        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3.5 py-3">
            <div class="flex min-w-0 items-center gap-2.5">
                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#fff8ed] text-[#b67d18]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16"></path>
                        <path d="M4 12h16"></path>
                        <path d="M4 17h10"></path>
                        <circle cx="18" cy="17" r="2"></circle>
                    </svg>
                </div>

                <div class="min-w-0">
                    <p class="text-[9px] font-bold text-[#4d463e]">
                        Product Options & Specifications
                    </p>

                    <p class="mt-0.5 text-[7px] text-[#91887d]">
                        {{ $flexOptions->count() }} option{{ $flexOptions->count() === 1 ? '' : 's' }}
                        • {{ $flexVariants->count() }} variant{{ $flexVariants->count() === 1 ? '' : 's' }}
                        • {{ $flexSpecifications->count() }} detail{{ $flexSpecifications->count() === 1 ? '' : 's' }}
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if ($flexVariants->isNotEmpty())
                    <span class="hidden rounded-full bg-[#eef6f1] px-2 py-1 text-[6.5px] font-bold text-[#56816a] sm:inline-flex">
                        {{ number_format($variantTotalStock) }} total stock
                    </span>
                @endif

                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#8e8579]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m7 10 5 5 5-5"></path>
                </svg>
            </div>
        </summary>

        <div class="border-t border-[#eee7de] p-3.5">
            {{-- OPTIONS --}}
            @if ($flexOptions->isNotEmpty())
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#a8731f]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 6h14"></path>
                            <path d="M5 12h14"></path>
                            <path d="M5 18h14"></path>
                            <circle cx="8" cy="6" r="1.5" fill="currentColor" stroke="none"></circle>
                            <circle cx="15" cy="12" r="1.5" fill="currentColor" stroke="none"></circle>
                            <circle cx="11" cy="18" r="1.5" fill="currentColor" stroke="none"></circle>
                        </svg>
                        <p class="text-[8px] font-bold uppercase tracking-[.08em] text-[#786f64]">
                            Buyer-selectable Options
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach ($flexOptions as $option)
                            <div class="rounded-xl border border-[#eee7de] bg-white p-3">
                                <p class="text-[8px] font-bold text-[#4e473f]">
                                    {{ $option->name }}
                                </p>

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach ($option->values as $value)
                                        <span class="rounded-full border border-[#e7dfd4] bg-[#faf8f4] px-2 py-1 text-[7px] font-semibold text-[#6c6359]">
                                            {{ $value->value }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- SPECIFICATIONS --}}
            @if ($flexSpecifications->isNotEmpty())
                <div class="{{ $flexOptions->isNotEmpty() ? 'mt-4 border-t border-[#eee7de] pt-4' : '' }}">
                    <div class="mb-2 flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#667f94]" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M7 3h7l4 4v14H7z"></path>
                            <path d="M14 3v5h5"></path>
                            <path d="M10 13h5"></path>
                            <path d="M10 17h4"></path>
                        </svg>

                        <p class="text-[8px] font-bold uppercase tracking-[.08em] text-[#786f64]">
                            Product Specifications
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($flexSpecifications as $specification)
                            <div class="rounded-xl border border-[#e7e5e2] bg-white px-3 py-2.5">
                                <p class="text-[6.5px] uppercase tracking-[.06em] text-[#9a9186]">
                                    {{ $specification->name }}
                                </p>
                                <p class="mt-1 text-[8px] font-semibold text-[#504941]">
                                    {{ $specification->value }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- VARIANTS --}}
            @if ($flexVariants->isNotEmpty())
                <div class="mt-4 border-t border-[#eee7de] pt-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#56816a]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="4" y="4" width="6" height="6" rx="1"></rect>
                                <rect x="14" y="4" width="6" height="6" rx="1"></rect>
                                <rect x="4" y="14" width="6" height="6" rx="1"></rect>
                                <rect x="14" y="14" width="6" height="6" rx="1"></rect>
                            </svg>

                            <p class="text-[8px] font-bold uppercase tracking-[.08em] text-[#786f64]">
                                Generated Variants
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-1.5">
                            <span class="rounded-full bg-[#f3f6f8] px-2 py-1 text-[6.5px] font-semibold text-[#657f94]">
                                {{ $flexVariants->count() }} combinations
                            </span>

                            <span class="rounded-full bg-[#eef6f1] px-2 py-1 text-[6.5px] font-semibold text-[#56816a]">
                                Stock {{ number_format($variantTotalStock) }}
                            </span>

                            @if ($variantMinPrice !== null)
                                <span class="rounded-full bg-[#fff8ed] px-2 py-1 text-[6.5px] font-semibold text-[#a8731f]">
                                    @if ($variantMinPrice === $variantMaxPrice)
                                        ₱{{ number_format($variantMinPrice, 2) }}
                                    @else
                                        ₱{{ number_format($variantMinPrice, 2) }} – ₱{{ number_format($variantMaxPrice, 2) }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-2 overflow-x-auto rounded-xl border border-[#e9e3da] bg-white">
                        <table class="w-full min-w-[540px] text-left">
                            <thead>
                                <tr class="border-b border-[#eee8df] bg-[#faf8f4] text-[6.5px] font-bold uppercase tracking-[.06em] text-[#968d82]">
                                    <th class="px-3 py-2.5">Variant</th>
                                    <th class="px-3 py-2.5">SKU</th>
                                    <th class="px-3 py-2.5 text-right">Price</th>
                                    <th class="px-3 py-2.5 text-right">Stock</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($flexVariants as $variant)
                                    @php
                                        $variantValues = is_array($variant->option_values)
                                            ? $variant->option_values
                                            : (json_decode((string) $variant->option_values, true) ?: []);

                                        $variantLabel = collect($variantValues)
                                            ->map(function ($value, $key) {
                                                if (is_array($value)) {
                                                    $name = $value['name'] ?? $key;
                                                    $text = $value['value'] ?? '';
                                                    return trim($name . ': ' . $text);
                                                }

                                                return is_string($key)
                                                    ? $key . ': ' . $value
                                                    : (string) $value;
                                            })
                                            ->filter()
                                            ->implode(' / ');
                                    @endphp

                                    <tr class="border-b border-[#f1ece5] last:border-0">
                                        <td class="px-3 py-2.5">
                                            <p class="text-[7.5px] font-semibold text-[#4d463e]">
                                                {{ $variantLabel ?: $variant->combination_key }}
                                            </p>
                                        </td>

                                        <td class="px-3 py-2.5 text-[7px] text-[#81786c]">
                                            {{ $variant->sku ?: 'Auto / none' }}
                                        </td>

                                        <td class="px-3 py-2.5 text-right text-[7.5px] font-bold text-[#3e3831]">
                                            ₱{{ number_format((float) $variant->price, 2) }}
                                        </td>

                                        <td class="px-3 py-2.5 text-right">
                                            <span class="rounded-full px-2 py-1 text-[6.5px] font-bold
                                                {{ (int) $variant->stock <= 2
                                                    ? 'bg-[#fff0f0] text-[#b65353]'
                                                    : ((int) $variant->stock <= 5
                                                        ? 'bg-[#fff8ec] text-[#a8731f]'
                                                        : 'bg-[#eef6f1] text-[#56816a]') }}"
                                            >
                                                {{ $variant->stock }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-3 flex items-start gap-2 rounded-lg border border-[#e2e5ed] bg-[#f8f9fc] px-3 py-2">
                <svg viewBox="0 0 24 24" class="mt-0.5 h-3 w-3 shrink-0 text-[#697087]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 11v5"></path>
                    <path d="M12 8h.01"></path>
                </svg>

                <p class="text-[6.5px] leading-4 text-[#74798c]">
                    Review custom option names, option values, and specifications together with the product name, category, description, and image before approving the listing.
                </p>
            </div>
        </div>
    </details>
@elseif (!$compact)
    <div class="mt-4 rounded-xl border border-[#e8e2da] bg-[#faf9f6] px-3 py-2.5">
        <p class="text-[7.5px] text-[#8d8479]">
            This listing does not use custom options, variants, or additional specifications.
        </p>
    </div>
@endif
