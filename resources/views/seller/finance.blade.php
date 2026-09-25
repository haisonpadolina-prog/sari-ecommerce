@extends('layouts.seller')
@section('title', 'Finance & Earnings — SARI Seller')
@section('page-title', 'Finance & Earnings')
@section('content')
<div class="mx-auto w-full max-w-[1700px] font-['Poppins',sans-serif]">
    @if(session('success'))
        <div class="mb-4 rounded-2xl border border-[#cfe4d7] bg-[#f2faf5] px-4 py-3 text-[10px] font-medium text-[#4f7d63]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-2xl border border-[#efcece] bg-[#fff5f5] px-4 py-3 text-[10px] font-medium text-[#a65353]">{{ $errors->first() }}</div>
    @endif

    <div><p class="text-[8px] font-bold uppercase tracking-[.15em] text-[#b97805]">Business Finance</p><h1 class="mt-2 text-[30px] font-semibold tracking-[-.04em] text-[#202124]">Finance & Earnings</h1><p class="mt-2 text-[10px] text-[#8A919B]">Delivered-and-paid orders only. Settlements are internal ledger records, not proof of an external bank transfer.</p></div>
    <div class="mt-5 grid grid-cols-2 gap-3 xl:grid-cols-5">
        @foreach([['Gross Sales',$stats['gross']],['Platform Commission',$stats['commission']],['Net Earnings',$stats['net']],['Pending Settlement',$stats['pending']],['Paid/Released',$stats['paid']]] as [$label,$value])
        <div class="rounded-[18px] border border-[#E7E9EE] bg-white p-5"><p class="text-[8px] font-semibold text-[#8A919B]">{{ $label }}</p><p class="mt-2 text-[20px] font-semibold text-[#202124]">₱{{ number_format($value,2) }}</p></div>
        @endforeach
    </div>
    <section class="mt-5 overflow-hidden rounded-[20px] border border-[#E7E9EE] bg-white">
        <div class="border-b border-[#E7E9EE] px-5 py-4"><h2 class="text-[13px] font-semibold">Settlement Ledger</h2></div>
        <div class="overflow-x-auto"><table class="w-full min-w-[900px] text-left text-[8px]"><thead><tr><th class="p-4">Order</th><th>Eligible</th><th>Gross</th><th>Commission</th><th>Withholding</th><th>Net</th><th>Status</th></tr></thead><tbody>
        @forelse($settlements as $row)<tr class="border-t border-[#eef0f2]"><td class="p-4 font-semibold">{{ $row->order?->order_number }}</td><td>{{ $row->eligible_at?->format('M d, Y') }}</td><td>₱{{ number_format((float)$row->merchandise_amount,2) }}</td><td>₱{{ number_format((float)$row->platform_commission_amount,2) }}</td><td>₱{{ number_format((float)$row->withholding_tax_amount,2) }}</td><td class="font-semibold text-[#4f7d63]">₱{{ number_format((float)$row->seller_net_amount,2) }}</td><td>{{ ucfirst($row->status) }}</td></tr>
        @empty<tr><td colspan="7" class="p-10 text-center text-[#8A919B]">No financial settlements yet.</td></tr>@endforelse
        </tbody></table></div>
        @if($settlements->hasPages())<div class="border-t border-[#E7E9EE] p-4">{{ $settlements->links() }}</div>@endif
    </section>
</div>
@endsection
