@extends('layouts.seller')
@section('title', 'Returns & Refunds — SARI Seller')
@section('page-title', 'Returns & Refunds')
@section('content')
<div class="mx-auto w-full max-w-[1700px] font-['Poppins',sans-serif]">
    @if(session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[10px] font-medium text-[#4f7d63]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[10px] font-medium text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <div><p class="text-[8px] font-bold uppercase tracking-[.15em] text-[#b97805]">Orders & Fulfillment</p><h1 class="mt-2 text-[30px] font-semibold tracking-[-.04em] text-[#202124]">Returns & Refunds</h1><p class="mt-2 text-[10px] text-[#8A919B]">Review Buyer return requests, record returned parcels, and post internal refund ledger entries.</p></div>
    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([['All',(int)$stats->total],['Requested',(int)$stats->requested],['Approved',(int)$stats->approved],['Refunded',(int)$stats->refunded]] as [$label,$value])
        <div class="rounded-[18px] border border-[#E7E9EE] bg-white p-5"><p class="text-[8px] text-[#8A919B]">{{ $label }}</p><p class="mt-2 text-[23px] font-semibold">{{ $value }}</p></div>@endforeach
    </div>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach(['all','requested','approved','returned','refunded','rejected'] as $tab)<a href="{{ route('seller.returns.index',['status'=>$tab]) }}" class="rounded-xl px-3 py-2 text-[8px] font-semibold {{ $status===$tab ? 'bg-[#202124] text-white' : 'border border-[#E7E9EE] bg-white text-[#6b7280]' }}">{{ ucfirst($tab) }}</a>@endforeach
    </div>
    <section class="mt-4 space-y-3">
        @forelse($returns as $return)
        <article class="rounded-[18px] border border-[#E7E9EE] bg-white p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:justify-between">
                <div><div class="flex items-center gap-2"><p class="text-[11px] font-semibold">{{ $return->order?->order_number }}</p><span class="rounded-full bg-[#FAFAFB] px-2 py-1 text-[7px] font-bold uppercase text-[#6b7280]">{{ $return->status }}</span></div><p class="mt-2 text-[9px] text-[#4B5563]">{{ $return->reason }}</p><p class="mt-1 max-w-[760px] text-[8px] leading-4 text-[#8A919B]">{{ $return->details ?: 'No additional details.' }}</p></div>
                <div class="text-right"><p class="text-[8px] text-[#8A919B]">Requested refund</p><p class="mt-1 text-[16px] font-bold text-[#202124]">₱{{ number_format((float)$return->requested_amount,2) }}</p></div>
            </div>
            @if($return->status==='requested')
            <form method="POST" action="{{ route('seller.returns.review',$return) }}" class="mt-4 grid gap-2 border-t border-[#eef0f2] pt-4 md:grid-cols-[1fr_150px_150px_auto]">@csrf
                <input name="seller_response" required placeholder="Seller response / decision notes" class="h-10 rounded-xl border border-[#E7E9EE] px-3 text-[8px]">
                <input name="approved_amount" type="number" step=".01" min="0" max="{{ $return->requested_amount }}" value="{{ $return->requested_amount }}" class="h-10 rounded-xl border border-[#E7E9EE] px-3 text-[8px]">
                <select name="decision" class="h-10 rounded-xl border border-[#E7E9EE] px-3 text-[8px]"><option value="approve">Approve</option><option value="reject">Reject</option></select>
                <button class="h-10 rounded-xl bg-[#202124] px-4 text-[8px] font-semibold text-white">Submit Review</button>
            </form>
            @elseif($return->status==='approved')
            <form method="POST" action="{{ route('seller.returns.returned',$return) }}" class="mt-4 border-t border-[#eef0f2] pt-4">@csrf<button class="h-10 rounded-xl bg-[#D89B10] px-4 text-[8px] font-semibold text-white">Confirm Returned Item Received</button></form>
            @elseif($return->status==='returned')
            <form method="POST" action="{{ route('seller.returns.refund',$return) }}" class="mt-4 border-t border-[#eef0f2] pt-4">@csrf<button class="h-10 rounded-xl bg-[#4f7d63] px-4 text-[8px] font-semibold text-white">Record Refund</button><p class="mt-2 text-[7px] text-[#8A919B]">Records an internal refund ledger transaction only.</p></form>
            @elseif($return->seller_response)
            <div class="mt-4 rounded-xl bg-[#FAFAFB] p-3 text-[8px] text-[#6b7280]">{{ $return->seller_response }}</div>
            @endif
        </article>
        @empty<div class="rounded-[18px] border border-dashed border-[#dfe3e8] bg-white p-12 text-center text-[9px] text-[#8A919B]">No return requests found.</div>@endforelse
        @if($returns->hasPages())<div>{{ $returns->links() }}</div>@endif
    </section>
</div>
@endsection
