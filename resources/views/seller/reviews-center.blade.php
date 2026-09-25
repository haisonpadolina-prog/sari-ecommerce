@extends('layouts.seller')
@section('title', 'Reviews & Ratings — SARI Seller')
@section('page-title', 'Reviews & Ratings')
@section('content')
<div class="mx-auto w-full max-w-[1700px] font-['Poppins',sans-serif]">
    @if(session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[10px] font-medium text-[#4f7d63]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[10px] font-medium text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <div><p class="text-[8px] font-bold uppercase tracking-[.15em] text-[#b97805]">Business Reputation</p><h1 class="mt-2 text-[30px] font-semibold tracking-[-.04em] text-[#202124]">Reviews & Ratings</h1><p class="mt-2 text-[10px] text-[#8A919B]">Monitor verified product feedback and publish one Seller response per review.</p></div>
    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([['Average Rating',number_format($stats['average'],2)],['Total Reviews',$stats['count']],['5-Star Reviews',$stats['five']],['1–2 Star Reviews',$stats['low']]] as [$label,$value])
        <div class="rounded-[18px] border border-[#E7E9EE] bg-white p-5"><p class="text-[8px] text-[#8A919B]">{{ $label }}</p><p class="mt-2 text-[23px] font-semibold">{{ $value }}</p></div>@endforeach
    </div>
    <section class="mt-5 space-y-3">
        @forelse($reviews as $review)
        <article class="rounded-[18px] border border-[#E7E9EE] bg-white p-5">
            <div class="flex justify-between gap-4"><div><p class="text-[10px] font-semibold">{{ $review->product?->name ?: 'Product' }}</p><p class="mt-1 text-[9px] tracking-[.08em] text-[#D89B10]">{{ str_repeat('★',(int)$review->rating) }}{{ str_repeat('☆',5-(int)$review->rating) }}</p></div><p class="text-[7px] text-[#8A919B]">{{ $review->created_at?->format('M d, Y') }}</p></div>
            <p class="mt-3 text-[9px] leading-5 text-[#4B5563]">{{ $review->comment ?: 'Buyer left a rating without a written comment.' }}</p>
            <form method="POST" action="{{ route('seller.reviews-center.reply',$review) }}" class="mt-4 flex gap-2 border-t border-[#eef0f2] pt-4">@csrf<input name="reply" required value="{{ $review->sellerReply?->reply }}" placeholder="Write a professional Seller response..." class="h-10 min-w-0 flex-1 rounded-xl border border-[#E7E9EE] px-3 text-[8px]"><button class="h-10 rounded-xl bg-[#202124] px-4 text-[8px] font-semibold text-white">{{ $review->sellerReply ? 'Update Reply' : 'Reply' }}</button></form>
        </article>
        @empty<div class="rounded-[18px] border border-dashed border-[#dfe3e8] bg-white p-12 text-center text-[9px] text-[#8A919B]">No Buyer reviews yet.</div>@endforelse
        @if($reviews->hasPages())<div>{{ $reviews->links() }}</div>@endif
    </section>
</div>
@endsection
