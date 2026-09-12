@extends('layouts.logistics')

@section('title', 'Rider Management | SARI Logistics')
@section('page-title', 'Rider Management')

@section('content')
<section class="space-y-4">
    <div class="grid gap-3 sm:grid-cols-3">
        @foreach([
            ['Approved Riders', $stats['total']],
            ['Active Accounts', $stats['active']],
            ['Busy Now', $stats['busy']],
        ] as [$label,$value])
            <div class="rounded-[18px] border border-[#eee4d3] bg-white p-4">
                <p class="text-[8px] uppercase tracking-[.08em] text-[#978d80]">{{ $label }}</p>
                <p class="mt-2 text-[22px] font-bold text-[#302a22]">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-[18px] border border-[#eee4d3] bg-white overflow-x-auto">
        <table class="w-full min-w-[900px] text-left">
            <thead class="bg-[#fcfaf6] text-[8px] uppercase tracking-[.08em] text-[#978d80]">
                <tr><th class="px-4 py-3">Rider</th><th class="px-4 py-3">Vehicle</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Account</th><th class="px-4 py-3">Availability</th><th class="px-4 py-3">Current Work</th></tr>
            </thead>
            <tbody class="divide-y divide-[#f0e9df]">
                @forelse($riders as $rider)
                    @php($activeOrder = $activeOrders->get(strtolower((string) $rider->email)))
                    <tr class="text-[9px] text-[#5f574e]">
                        <td class="px-4 py-3"><p class="font-bold text-[#332c24]">{{ trim($rider->first_name.' '.$rider->last_name) }}</p><p class="mt-1 text-[7px] text-[#92877a]">{{ $rider->email }}</p></td>
                        <td class="px-4 py-3">{{ $rider->vehicle_type }} · {{ $rider->plate_number }}</td>
                        <td class="px-4 py-3">{{ $rider->contact_no }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 font-semibold {{ $rider->account_status === 'active' ? 'bg-[#eef7f1] text-[#56816a]' : 'bg-[#fff1f1] text-[#a65d5d]' }}">{{ ucfirst($rider->account_status) }}</span></td>
                        <td class="px-4 py-3">{{ ucfirst($rider->availability_status ?: 'online') }}</td>
                        <td class="px-4 py-3">{{ $activeOrder ? $activeOrder->order_number.' · '.$activeOrder->statusLabel() : (($rider->availability_status ?: 'online') === 'online' ? 'Available' : 'Offline') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-[9px] text-[#8c8275]">No approved Riders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
