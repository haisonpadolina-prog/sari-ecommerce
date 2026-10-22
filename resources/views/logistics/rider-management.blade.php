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

    <div class="rounded-[18px] border border-[#eee4d3] bg-white p-4">
        <div class="mb-4"><h2 class="text-[13px] font-bold text-[#302a22]">Add Rider</h2><p class="mt-1 text-[8px] text-[#978d80]">Creates a Pending Activation account and emails a secure 24-hour activation link.</p></div>
        @if(session('success'))<p class="mb-3 rounded-xl bg-[#eef7f1] px-3 py-2 text-[9px] text-[#56816a]">{{ session('success') }}</p>@endif
        @if($errors->any())<div class="mb-3 rounded-xl bg-[#fff1f1] px-3 py-2 text-[9px] text-[#a65d5d]">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('logistics.rider-management.store') }}" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            @csrf
            @foreach([['first_name','First Name','text'],['last_name','Last Name','text'],['birthday','Date of Birth','date'],['email','Email','email'],['contact_no','Mobile Number','text'],['license_number',"Driver's License No.",'text'],['license_expiry_date','License Expiry Date','date'],['plate_number','Plate Number','text']] as [$name,$label,$type])
                <label class="text-[8px] font-semibold text-[#62594f]">{{ $label }} *<input type="{{ $type }}" name="{{ $name }}" value="{{ old($name) }}" required class="mt-1 h-10 w-full rounded-xl border border-[#e5ddd2] px-3 text-[9px]"></label>
            @endforeach
            <label class="text-[8px] font-semibold text-[#62594f]">Vehicle Type *<select name="vehicle_type" required class="mt-1 h-10 w-full rounded-xl border border-[#e5ddd2] px-3 text-[9px]"><option value="">Select</option>@foreach(['Motorcycle','Car','Van'] as $vehicle)<option value="{{ $vehicle }}" @selected(old('vehicle_type')===$vehicle)>{{ $vehicle }}</option>@endforeach</select></label>
            <label class="text-[8px] font-semibold text-[#62594f]">Driver's License *<input type="file" name="driver_license_document" accept=".jpg,.jpeg,.png,.pdf" required class="mt-1 block w-full text-[8px]"></label>
            <label class="text-[8px] font-semibold text-[#62594f]">Vehicle OR/CR *<input type="file" name="orcr_document" accept=".jpg,.jpeg,.png,.pdf" required class="mt-1 block w-full text-[8px]"></label>
            <div class="flex items-end"><button type="submit" class="h-10 rounded-xl bg-[#302a22] px-5 text-[9px] font-bold text-white">Save Rider</button></div>
        </form>
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
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 font-semibold {{ $rider->account_status === 'active' ? 'bg-[#eef7f1] text-[#56816a]' : 'bg-[#fff1f1] text-[#a65d5d]' }}">{{ str($rider->account_status)->replace('_', ' ')->title() }}</span>@if($rider->account_status === 'pending_activation')<form method="POST" action="{{ route('logistics.rider-management.resend-activation', $rider) }}" class="mt-2">@csrf<button class="text-[7px] font-semibold underline">Resend activation</button></form>@endif</td>
                        <td class="px-4 py-3">{{ ucfirst($rider->availability_status ?: 'online') }}</td>
                        <td class="px-4 py-3">{{ $activeOrder ? $activeOrder->order_number.' · '.$activeOrder->statusLabel() : (($rider->availability_status ?: 'online') === 'online' ? 'Available' : 'Offline') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-[9px] text-[#8c8275]">No Riders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
