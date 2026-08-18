@extends('layouts.seller')

@section('title', 'Archived Products — SARI Seller')
@section('page-title', 'Archived Products')

@section('content')
<div class="mx-auto w-full max-w-[1800px]">

    @if (session('success'))
        <div class="mb-5 rounded-[18px] border border-[#d5e5dc] bg-[#f3f8f5] px-5 py-4 text-[11px] font-medium text-[#56816a]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-[18px] border border-[#ead7d7] bg-[#fdf5f5] px-5 py-4 text-[11px] text-[#a45f5f]">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="overflow-hidden rounded-[22px] border border-[#e8ddc9] bg-[#fffdf9] p-6 shadow-[inset_0_4px_0_#c99128] sm:p-7">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-[#eadfc9] bg-[#fbf5e9] px-3 py-1.5 text-[9px] font-semibold uppercase tracking-[0.12em] text-[#a8731f]">
                    Product History
                </div>
                <h2 class="mt-3 text-[24px] font-bold tracking-[-0.03em] text-[#211c16]">Archived Products</h2>
                <p class="mt-2 max-w-[760px] text-[12px] leading-6 text-[#81786c]">
                    Archived and deleted products are kept here so you can restore them later and so compliance history is never lost.
                </p>
            </div>

            <a href="{{ route('seller.dashboard') }}" class="inline-flex h-11 w-fit items-center justify-center rounded-xl border border-[#e3dbd0] bg-white px-4 text-[10px] font-semibold text-[#62594e] transition hover:border-[#d3bd92] hover:bg-[#fcf8f1]">
                ← Back to Dashboard
            </a>
        </div>
    </section>

    <section class="mt-5 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">
        <div class="flex items-center justify-between border-b border-[#eee8df] p-5">
            <div>
                <h3 class="text-[16px] font-bold text-[#28221b]">Product Archive</h3>
                <p class="mt-1 text-[10px] text-[#91887d]">{{ $products->count() }} archived item{{ $products->count() === 1 ? '' : 's' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
            @forelse ($products as $product)
                <article class="overflow-hidden rounded-[18px] border border-[#e9e3da] bg-white transition duration-300 hover:-translate-y-1 hover:border-[#d9c8a8] hover:shadow-[0_14px_34px_rgba(68,52,28,0.08)]">
                    <div class="h-[160px] overflow-hidden bg-[#f5f2ed]">
                        @if ($product->image_path)
                            <img src="{{ route('seller.products.image', $product) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="grid h-full place-items-center text-[#a79d91]">
                                <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                    <path d="m4 17 5-5 4 4 2-2 5 4"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-[12px] font-bold text-[#312b25]">{{ $product->name }}</p>
                                <p class="mt-1 text-[9px] text-[#978e83]">{{ $product->category }} • {{ $product->sku }}</p>
                            </div>

                            <span class="shrink-0 rounded-full {{ $product->archive_reason === 'deleted' ? 'bg-[#faeeee] text-[#a96565]' : 'bg-[#fbf5e9] text-[#a8731f]' }} px-2.5 py-1 text-[8px] font-semibold">
                                {{ $product->archive_reason === 'deleted' ? 'Deleted' : 'Archived' }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-[#fcfaf7] p-3">
                                <p class="text-[8px] text-[#958c80]">Price</p>
                                <p class="mt-1 text-[11px] font-bold text-[#a8731f]">₱{{ number_format((float) $product->price, 2) }}</p>
                            </div>
                            <div class="rounded-xl bg-[#fcfaf7] p-3">
                                <p class="text-[8px] text-[#958c80]">Archived</p>
                                <p class="mt-1 text-[10px] font-semibold text-[#514a42]">{{ $product->archived_at?->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-[#eee8df] pt-4">
                            <span class="text-[8px] font-medium text-[#91887d]">Status: {{ ucfirst($product->moderation_status) }}</span>

                            <form method="POST" action="{{ route('seller.products.restore', $product) }}">
                                @csrf
                                <button type="submit" @if ($seller->isSuspended()) disabled @endif class="rounded-lg bg-[#c99128] px-3 py-2 text-[9px] font-semibold text-white transition hover:bg-[#b47e1e] disabled:cursor-not-allowed disabled:opacity-40">
                                    Restore
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-[18px] border border-dashed border-[#dfd6c8] bg-[#fcfaf7] px-6 py-14 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#fbf5e9] text-[#a8731f]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M6 7v12h12V7"></path></svg>
                    </div>
                    <p class="mt-4 text-[12px] font-bold text-[#4f473e]">Your archive is empty</p>
                    <p class="mt-1 text-[10px] text-[#958c80]">Archived or deleted products will appear here.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
