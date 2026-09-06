@extends('layouts.seller')

@section('title', 'Product Reviews — SARI Seller')
@section('page-title', 'Product Reviews')

@section('content')
<div class="mx-auto w-full max-w-[1600px]">
    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-[8px] font-semibold uppercase tracking-[.14em] text-[#56816a]">Delivered-order feedback</p><h1 class="mt-2 text-[22px] font-bold tracking-[-.03em] text-[#28221b]">Buyer Product Reviews</h1><p class="mt-1 text-[9px] text-[#91887d]">Only buyers with delivered orders can submit these reviews.</p></div><a href="{{ route('seller.orders') }}" class="text-[8px] font-semibold text-[#a8731f]">← Back to Orders</a></div>
    </section>

    <section class="mt-4 grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-[#ebe4da] bg-white p-4"><p class="text-[8px] text-[#91887d]">Average Rating</p><p class="mt-2 text-[22px] font-bold text-[#b97805]">{{ number_format($average, 2) }} / 5</p></div>
        <div class="rounded-2xl border border-[#ebe4da] bg-white p-4"><p class="text-[8px] text-[#91887d]">Total Reviews</p><p class="mt-2 text-[22px] font-bold text-[#302a24]">{{ $reviews->count() }}</p></div>
        <div class="rounded-2xl border border-[#ebe4da] bg-white p-4"><p class="text-[8px] text-[#91887d]">5-Star Reviews</p><p class="mt-2 text-[22px] font-bold text-[#4F7D63]">{{ $reviews->where('rating', 5)->count() }}</p></div>
    </section>

    <section class="mt-4 grid gap-4 lg:grid-cols-2">
        @forelse ($reviews as $review)
            <article class="rounded-[20px] border border-[#ebe4da] bg-white p-5">
                <div class="flex items-start justify-between gap-3"><div><p class="text-[10px] font-bold text-[#403a33]">{{ $review->product?->name ?: 'Product' }}</p><p class="mt-1 text-[8px] text-[#91887d]">Order {{ $review->order?->order_number }}</p></div><span class="text-[10px] font-semibold text-[#b97805]">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span></div>
                <p class="mt-4 text-[9px] leading-5 text-[#62594e]">{{ $review->comment ?: 'Buyer submitted a rating without a written comment.' }}</p>
                <p class="mt-4 border-t border-[#eee8df] pt-3 text-[7px] text-[#91887d]">Buyer: {{ $review->buyer ? trim($review->buyer->first_name . ' ' . $review->buyer->last_name) : ($review->socialBuyer?->name ?: 'SARI Buyer') }} • {{ $review->created_at?->format('M d, Y h:i A') }}</p>
            </article>
        @empty
            <div class="lg:col-span-2 rounded-[20px] border border-dashed border-[#ded5c9] bg-white p-10 text-center text-[9px] text-[#91887d]">No delivered-order reviews yet.</div>
        @endforelse
    </section>
</div>
@endsection
