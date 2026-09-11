@extends('layouts.logistics')

@section('title', 'Rider Applications | SARI Logistics')
@section('page-title', 'Rider Applications')

@section('content')
<div class="mx-auto w-full max-w-[1800px]">
    @if (session('success'))
        <div class="mb-4 rounded-[16px] border border-[#cfe2d5] bg-[#f4faf6] px-4 py-3 text-[10px] font-semibold text-[#55705f]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-[16px] border border-[#ead0d0] bg-[#fff7f7] px-4 py-3 text-[10px] font-semibold text-[#a55555]">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6 lg:p-7">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.12em] text-[#b97805]">Rider Recruitment</p>
                <h2 class="mt-2 text-[22px] font-bold tracking-[-.035em] text-[#211c16] sm:text-[26px]">Review Rider applications</h2>
                <p class="mt-2 max-w-[720px] text-[10px] leading-5 text-[#81786c] sm:text-[11px]">
                    Only Rider applications submitted specifically to <strong>{{ $logistics->displayName() }}</strong> appear here. Other Logistics providers cannot review or approve these applicants.
                </p>
            </div>

            <form method="GET" class="flex items-center gap-2">
                <select name="status" class="h-10 rounded-xl border border-[#e5ddd2] bg-white px-3 text-[9px] text-[#403a33]">
                    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="h-10 rounded-xl bg-[#c99128] px-4 text-[9px] font-semibold text-white hover:bg-[#b88020]">Apply</button>
            </form>
        </div>
    </section>

    <section class="mt-4 grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach([
            ['Total', $stats['total']],
            ['Pending', $stats['pending']],
            ['Approved', $stats['approved']],
            ['Rejected', $stats['rejected']],
        ] as [$label, $value])
            <article class="rounded-[17px] border border-[#ebe4da] bg-white p-4 sm:p-5">
                <p class="text-[8px] text-[#91887d]">{{ $label }}</p>
                <p class="mt-2 text-[22px] font-bold text-[#28221b]">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-4 grid grid-cols-1 gap-4 2xl:grid-cols-2">
        @forelse($applications as $application)
            <article class="rounded-[20px] border border-[#ebe4da] bg-white p-5 shadow-[0_10px_30px_rgba(76,58,34,0.04)]">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        @if($application->profile_image_path)
                            <img
                                src="{{ asset('storage/' . $application->profile_image_path) }}"
                                alt="{{ $application->fullName() }}"
                                class="h-11 w-11 shrink-0 rounded-[12px] border border-[#e5ded4] object-cover"
                            >
                        @else
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] bg-[#f6f3ee] text-[#9f720e]" aria-hidden="true">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="8" r="3"></circle>
                                    <path d="M5 20c.5-4 3-6.5 7-6.5s6.5 2.5 7 6.5"></path>
                                </svg>
                            </span>
                        @endif

                        <div class="min-w-0">
                            <h3 class="truncate text-[13px] font-bold text-[#312b25]">{{ $application->fullName() }}</h3>
                            <p class="mt-1 truncate text-[9px] text-[#81786c]">{{ $application->email }} · {{ $application->contact_no }}</p>
                            <p class="mt-1 text-[8px] text-[#9b9389]">Submitted {{ $application->created_at?->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <span class="w-fit rounded-full border px-2.5 py-1 text-[7px] font-bold
                        {{ $application->status === 'approved' ? 'border-[#cfe1d5] bg-[#f2f8f4] text-[#4f7c60]' : ($application->status === 'rejected' ? 'border-[#e8cccc] bg-[#fff3f3] text-[#a55a5a]' : 'border-[#eadfc9] bg-[#fffaf2] text-[#a8731f]') }}">
                        {{ strtoupper($application->status) }}
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <div class="rounded-xl bg-[#faf9f6] p-3">
                        <p class="text-[7px] text-[#958c80]">Vehicle</p>
                        <p class="mt-1 text-[9px] font-semibold text-[#403a33]">{{ $application->vehicle_type }}</p>
                    </div>
                    <div class="rounded-xl bg-[#faf9f6] p-3">
                        <p class="text-[7px] text-[#958c80]">Plate Number</p>
                        <p class="mt-1 text-[9px] font-semibold text-[#403a33]">{{ $application->plate_number }}</p>
                    </div>
                </div>

                <div class="mt-3 rounded-xl border border-[#eee7de] bg-[#fcfbf8] p-3.5">
                    <p class="text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80]">Address</p>
                    <p class="mt-1.5 text-[9px] leading-5 text-[#5f574e]">
                        {{ $application->street_address }}, {{ $application->barangay_name }}, {{ $application->municipality_name }}, {{ $application->province_name }}
                    </p>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    @if($application->id_path)
                        <a href="{{ route('logistics.rider-applications.document', [$application, 'id']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 items-center rounded-lg border border-[#ddd7ce] px-3 text-[8px] font-semibold text-[#62594e] hover:bg-[#faf8f4]">
                            View ID / License
                        </a>
                    @endif
                    @if($application->orcr_path)
                        <a href="{{ route('logistics.rider-applications.document', [$application, 'orcr']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 items-center rounded-lg border border-[#d9e2ea] bg-[#f6f9fb] px-3 text-[8px] font-semibold text-[#57758c]">
                            View OR / CR
                        </a>
                    @endif
                </div>

                @if($application->admin_note)
                    <div class="mt-3 rounded-xl border border-[#eee7de] bg-[#fcfaf7] p-3.5">
                        <p class="text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80]">Review Note</p>
                        <p class="mt-1 text-[8px] leading-5 text-[#756d63]">{{ $application->admin_note }}</p>
                    </div>
                @endif

                @if($application->status === 'pending')
                    <div class="mt-4 grid grid-cols-1 gap-2 border-t border-[#eee8df] pt-4 sm:grid-cols-2">
                        <form method="POST" action="{{ route('logistics.rider-applications.approve', $application) }}" class="rounded-xl border border-[#dce7df] bg-[#f8fbf9] p-3">
                            @csrf
                            <p class="mb-2 text-[8px] font-bold text-[#52715d]">Approve Rider</p>
                            <input type="text" name="review_note" maxlength="1500" placeholder="Optional review note..." class="mb-2 h-9 w-full rounded-lg border border-[#dce4de] bg-white px-3 text-[8px] text-[#403a33]">
                            <button class="h-10 w-full rounded-xl bg-[#56816a] text-[8px] font-bold text-white hover:bg-[#496f5b]">Approve Application</button>
                        </form>

                        <form method="POST" action="{{ route('logistics.rider-applications.reject', $application) }}" class="rounded-xl border border-[#eadada] bg-[#fffafa] p-3">
                            @csrf
                            <p class="mb-2 text-[8px] font-bold text-[#8e5d5d]">Reject Rider</p>
                            <input type="text" name="review_note" required minlength="5" maxlength="1500" placeholder="Reason for rejection..." class="mb-2 h-9 w-full rounded-lg border border-[#ead9d9] bg-white px-3 text-[8px] text-[#403a33]">
                            <button class="h-10 w-full rounded-xl bg-[#a85c54] text-[8px] font-bold text-white hover:bg-[#934f48]">Reject Application</button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <div class="2xl:col-span-2 rounded-[20px] border border-dashed border-[#ded5c9] bg-white p-12 text-center">
                <p class="text-[11px] font-bold text-[#514a42]">No {{ $status }} Rider applications</p>
                <p class="mt-1 text-[9px] text-[#8d8479]">New Rider applications submitted to {{ $logistics->displayName() }} will automatically appear here.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection
