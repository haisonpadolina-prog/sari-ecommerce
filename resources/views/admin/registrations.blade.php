@extends('layouts.admin')

@section('title', 'Account Registrations — SARI Admin')
@section('page-title', 'Account Registrations')

@section('content')
<style>
    .reg-card {
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .reg-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(57, 48, 38, .06);
        border-color: #ddd5ca;
    }

    .reg-control {
        color: #302a24 !important;
        -webkit-text-fill-color: #302a24 !important;
        background: #fff !important;
    }

    .reg-control::placeholder {
        color: #a59c91 !important;
        -webkit-text-fill-color: #a59c91 !important;
    }

    .reg-control:focus {
        outline: none;
        border-color: #c99128 !important;
        box-shadow: 0 0 0 4px rgba(201, 145, 40, .08);
    }

    [data-application-card][hidden] {
        display: none !important;
    }
</style>

<div class="mx-auto w-full max-w-[1800px]">

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-[16px] border border-[#cfe2d5] bg-[#f4faf6] px-4 py-3.5">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-white text-[#56816a] shadow-sm">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="m8 12 2.5 2.5L16 9"></path>
                </svg>
            </span>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.10em] text-[#56816a]">Success</p>
                <p class="mt-1 text-[10px] leading-5 text-[#55705f]">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 flex items-start gap-3 rounded-[16px] border border-[#ead0d0] bg-[#fff6f6] px-4 py-3.5">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-white text-[#a65f5f] shadow-sm">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 8v5"></path>
                    <path d="M12 16.5h.01"></path>
                </svg>
            </span>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-[.10em] text-[#a65f5f]">Action Required</p>
                <p class="mt-1 text-[10px] leading-5 text-[#8d5f5f]">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    {{-- HEADER --}}
    <section class="rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6 lg:p-7">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-[15px] border border-[#eadfc9] bg-[#fff9ee] text-[#b67d18]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="8" r="3"></circle>
                        <path d="M3 20a6 6 0 0 1 12 0"></path>
                        <path d="m16 12 2 2 4-4"></path>
                    </svg>
                </div>

                <div>
                    <span class="inline-flex rounded-full border border-[#e8e1d7] bg-[#fcfaf7] px-3 py-1 text-[8px] font-bold uppercase tracking-[.12em] text-[#847a6e]">
                        Registration Review Center
                    </span>
                    <h2 class="mt-3 text-[22px] font-bold tracking-[-.035em] text-[#211c16] sm:text-[26px]">
                        Review account applications
                    </h2>
                    <p class="mt-2 max-w-[720px] text-[10px] leading-5 text-[#81786c] sm:text-[11px] sm:leading-6">
                        Verify applicant details and required documents before approving Buyer, Seller, or Courier accounts.
                    </p>
                </div>
            </div>

            <div class="max-w-[390px] rounded-[15px] border border-[#e8e2d9] bg-[#fcfbf8] px-4 py-3">
                <div class="flex items-start gap-2.5">
                    <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-[#8a7b62]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="5" y="10" width="14" height="10" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                    </svg>
                    <div>
                        <p class="text-[8px] font-bold text-[#5d554b]">Private verification files</p>
                        <p class="mt-1 text-[8px] leading-4 text-[#8b8175]">
                            IDs, business permits, and OR/CR files are opened only through the protected Admin document route.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SUMMARY CARDS --}}
    <section class="mt-4 grid grid-cols-2 gap-3 xl:grid-cols-4">
        @php
            $cards = [
                ['Total Applications', $stats['total'], 'bg-[#f2f6f8] text-[#657f94] border-[#dfe7ec]', 'users'],
                ['Pending Review', $stats['pending'], 'bg-[#fff7e9] text-[#a8731f] border-[#eee0c5]', 'clock'],
                ['Approved', $stats['approved'], 'bg-[#eef7f1] text-[#56816a] border-[#d9e8dd]', 'check'],
                ['Rejected', $stats['rejected'], 'bg-[#fff1f1] text-[#a65d5d] border-[#ecdada]', 'x'],
            ];
        @endphp

        @foreach($cards as [$label, $value, $tone, $icon])
            <article class="rounded-[17px] border bg-white p-4 sm:p-5 {{ Str::after($tone, 'border-') ? '' : '' }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[8px] font-medium text-[#91887d]">{{ $label }}</p>
                        <p class="mt-2 text-[22px] font-bold tracking-[-.03em] text-[#28221b]">{{ $value }}</p>
                    </div>

                    <div class="grid h-9 w-9 place-items-center rounded-xl border {{ $tone }}">
                        @if($icon === 'users')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="9" cy="7" r="3"></circle>
                                <path d="M3 20a6 6 0 0 1 12 0"></path>
                                <path d="M17 6a3 3 0 0 1 0 6"></path>
                                <path d="M18 15a5 5 0 0 1 3 5"></path>
                            </svg>
                        @elseif($icon === 'clock')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg>
                        @elseif($icon === 'check')
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m8 12 2.5 2.5L16 9"></path>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m9 9 6 6"></path>
                                <path d="m15 9-6 6"></path>
                            </svg>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    {{-- APPLICATIONS --}}
    <section class="mt-4 overflow-hidden rounded-[22px] border border-[#ebe4da] bg-white">
        {{-- FILTER BAR --}}
        <div class="border-b border-[#eee8df] p-4 sm:p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                    <select name="status" class="reg-control h-10 rounded-xl border border-[#e5ddd2] px-3 text-[9px]">
                        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                            <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="role" class="reg-control h-10 rounded-xl border border-[#e5ddd2] px-3 text-[9px]">
                        <option value="">All Roles</option>
                        <option value="buyer" {{ $role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                        <option value="seller" {{ $role === 'seller' ? 'selected' : '' }}>Seller</option>
                        <option value="courier" {{ $role === 'courier' ? 'selected' : '' }}>Courier</option>
                    </select>

                    <button class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#c99128] px-4 text-[9px] font-semibold text-white transition hover:bg-[#b88020]">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M4 6h16"></path>
                            <path d="M7 12h10"></path>
                            <path d="M10 18h4"></path>
                        </svg>
                        Apply Filter
                    </button>

                    <a href="{{ route('admin.registrations') }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-[#e4ddd3] px-4 text-[9px] font-semibold text-[#6f665b] hover:bg-[#faf8f4]">
                        Reset
                    </a>
                </form>

                <div class="relative w-full xl:max-w-[320px]">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#a0978c]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.4-3.4"></path>
                    </svg>
                    <input
                        id="applicationSearch"
                        type="search"
                        placeholder="Search name or email..."
                        class="reg-control h-10 w-full rounded-xl border border-[#e5ddd2] pl-10 pr-4 text-[9px]"
                    >
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <span class="rounded-full border border-[#dde7ef] bg-[#f5f9fc] px-3 py-1.5 text-[8px] font-semibold text-[#587ca0]">Buyer pending: {{ $stats['buyers'] }}</span>
                <span class="rounded-full border border-[#eee0c5] bg-[#fff9ef] px-3 py-1.5 text-[8px] font-semibold text-[#a8731f]">Seller pending: {{ $stats['sellers'] }}</span>
                <span class="rounded-full border border-[#dbe6e3] bg-[#f3f8f7] px-3 py-1.5 text-[8px] font-semibold text-[#56796f]">Courier pending: {{ $stats['couriers'] }}</span>
                <span id="visibleCount" class="ml-auto hidden rounded-full bg-[#f4f2ef] px-3 py-1.5 text-[8px] font-semibold text-[#746d64] sm:inline-flex">Showing {{ $applications->count() }}</span>
            </div>
        </div>

        {{-- CARDS --}}
        <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 2xl:grid-cols-2">
            @forelse($applications as $application)
                @php
                    $roleTone = match($application->role) {
                        'buyer' => 'border-[#dae7f2] bg-[#f4f8fc] text-[#537a9f]',
                        'seller' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]',
                        'courier' => 'border-[#d9e7e3] bg-[#f2f8f6] text-[#527b6f]',
                        default => 'border-[#e3ded7] bg-[#f7f5f2] text-[#746d64]',
                    };

                    $statusTone = match($application->status) {
                        'approved' => 'border-[#cfe1d5] bg-[#f2f8f4] text-[#4f7c60]',
                        'rejected' => 'border-[#e8cccc] bg-[#fff3f3] text-[#a55a5a]',
                        default => 'border-[#eadfc9] bg-[#fffaf2] text-[#a8731f]',
                    };

                    $initials = mb_strtoupper(mb_substr($application->first_name ?? '', 0, 1))
                        . mb_strtoupper(mb_substr($application->last_name ?? '', 0, 1));

                    $requiredDocuments = match($application->role) {
                        'seller' => [
                            ['label' => 'Valid ID', 'available' => (bool) $application->id_path],
                            ['label' => 'Business Permit', 'available' => (bool) $application->business_permit_path],
                        ],
                        'courier' => [
                            ['label' => 'ID / Driver’s License', 'available' => (bool) $application->id_path],
                            ['label' => 'OR / CR', 'available' => (bool) $application->orcr_path],
                        ],
                        default => [
                            ['label' => 'Valid ID', 'available' => (bool) $application->id_path],
                        ],
                    };

                    $allDocumentsAvailable = collect($requiredDocuments)->every(fn ($doc) => $doc['available']);
                @endphp

                <article
                    class="reg-card rounded-[19px] border border-[#ebe4da] bg-white p-4 sm:p-5"
                    data-application-card
                    data-search="{{ strtolower($application->fullName() . ' ' . $application->email . ' ' . $application->contact_no . ' ' . $application->role) }}"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-[13px] border border-[#e6dfd6] bg-[#faf8f4] text-[10px] font-bold text-[#6e655a]">
                                {{ $initials ?: 'SA' }}
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-[12px] font-bold text-[#312b25] sm:text-[13px]">{{ $application->fullName() }}</h3>
                                    <span class="rounded-full border px-2.5 py-1 text-[7px] font-bold {{ $roleTone }}">{{ strtoupper($application->role) }}</span>
                                </div>

                                <div class="mt-2 flex flex-col gap-1 text-[8px] text-[#81786c] sm:flex-row sm:flex-wrap sm:gap-x-4">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                            <path d="m3 7 9 6 9-6"></path>
                                        </svg>
                                        {{ $application->email }}
                                    </span>

                                    <span class="inline-flex items-center gap-1.5">
                                        <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M5 4h4l2 5-2.5 1.5a15 15 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2C9.7 21 3 14.3 3 6a2 2 0 0 1 2-2Z"></path>
                                        </svg>
                                        {{ $application->contact_no }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 sm:flex-col sm:items-end">
                            <span class="rounded-full border px-2.5 py-1 text-[7px] font-bold {{ $statusTone }}">{{ strtoupper($application->status) }}</span>
                            <span class="text-[7px] text-[#9b9389]">Submitted {{ $application->created_at?->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        <div class="rounded-xl bg-[#faf9f6] p-3">
                            <p class="text-[7px] text-[#958c80]">Sex</p>
                            <p class="mt-1.5 text-[9px] font-semibold text-[#403a33]">{{ $application->sex }}</p>
                        </div>
                        <div class="rounded-xl bg-[#faf9f6] p-3">
                            <p class="text-[7px] text-[#958c80]">Age</p>
                            <p class="mt-1.5 text-[9px] font-semibold text-[#403a33]">{{ $application->age }} years old</p>
                        </div>
                        <div class="col-span-2 rounded-xl bg-[#faf9f6] p-3 sm:col-span-1">
                            <p class="text-[7px] text-[#958c80]">Birthday</p>
                            <p class="mt-1.5 text-[9px] font-semibold text-[#403a33]">{{ $application->birthday?->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-3 rounded-xl border border-[#eee7de] bg-[#fcfbf8] p-3.5">
                        <div class="flex items-start gap-2.5">
                            <svg viewBox="0 0 24 24" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-[#988c7c]" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                <circle cx="12" cy="10" r="2.5"></circle>
                            </svg>
                            <div>
                                <p class="text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80]">Registered Address</p>
                                <p class="mt-1.5 text-[9px] leading-5 text-[#5f574e]">
                                    {{ $application->street_address }},
                                    {{ $application->barangay_name }},
                                    {{ $application->municipality_name }},
                                    {{ $application->province_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($application->role === 'seller')
                        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <div class="rounded-xl border border-[#eee7de] p-3.5">
                                <p class="text-[7px] text-[#958c80]">Business Name</p>
                                <p class="mt-1.5 text-[9px] font-bold text-[#403a33]">{{ $application->business_name }}</p>
                            </div>
                            <div class="rounded-xl border border-[#eee7de] p-3.5">
                                <p class="text-[7px] text-[#958c80]">Line of Business</p>
                                <p class="mt-1.5 text-[9px] font-bold text-[#403a33]">{{ $application->line_of_business }}</p>
                            </div>
                        </div>
                    @elseif($application->role === 'courier')
                        <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <div class="rounded-xl border border-[#eee7de] p-3.5">
                                <p class="text-[7px] text-[#958c80]">Vehicle</p>
                                <p class="mt-1.5 text-[9px] font-bold text-[#403a33]">{{ $application->vehicle_type }}</p>
                            </div>
                            <div class="rounded-xl border border-[#eee7de] p-3.5">
                                <p class="text-[7px] text-[#958c80]">Plate Number</p>
                                <p class="mt-1.5 text-[9px] font-bold tracking-[.04em] text-[#403a33]">{{ $application->plate_number }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- DOCUMENTS --}}
                    <div class="mt-3 rounded-xl border border-[#eee7de] p-3.5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#807568]" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M7 3h7l4 4v14H7z"></path>
                                        <path d="M14 3v5h5"></path>
                                    </svg>
                                    <p class="text-[8px] font-bold text-[#514a42]">Verification Documents</p>
                                </div>

                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($requiredDocuments as $document)
                                        <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[7px] font-semibold {{ $document['available'] ? 'border-[#d5e5da] bg-[#f3f9f5] text-[#56816a]' : 'border-[#ead2d2] bg-[#fff5f5] text-[#a65d5d]' }}">
                                            {{ $document['available'] ? '✓' : '!' }} {{ $document['label'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @if($application->id_path)
                                    <a href="{{ route('admin.registrations.document', [$application, 'id']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 items-center gap-2 rounded-lg border border-[#ddd7ce] px-3 text-[8px] font-semibold text-[#62594e] hover:bg-[#faf8f4]">
                                        <svg viewBox="0 0 24 24" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="1.9">
                                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                            <circle cx="12" cy="12" r="2.5"></circle>
                                        </svg>
                                        View ID
                                    </a>
                                @endif

                                @if($application->business_permit_path)
                                    <a href="{{ route('admin.registrations.document', [$application, 'permit']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 items-center gap-2 rounded-lg border border-[#eadfc9] bg-[#fffaf2] px-3 text-[8px] font-semibold text-[#a8731f]">
                                        Business Permit
                                    </a>
                                @endif

                                @if($application->orcr_path)
                                    <a href="{{ route('admin.registrations.document', [$application, 'orcr']) }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 items-center gap-2 rounded-lg border border-[#d9e2ea] bg-[#f6f9fb] px-3 text-[8px] font-semibold text-[#57758c]">
                                        View OR / CR
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($application->admin_note)
                        <div class="mt-3 rounded-xl border border-[#eee7de] bg-[#fcfaf7] p-3.5">
                            <p class="text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80]">Admin Note</p>
                            <p class="mt-1 text-[8px] leading-5 text-[#756d63]">{{ $application->admin_note }}</p>
                        </div>
                    @endif

                    @if($application->status === 'pending')
                        <div class="mt-4 border-t border-[#eee8df] pt-4">
                            @if(!$allDocumentsAvailable)
                                <div class="mb-3 rounded-lg border border-[#ead2d2] bg-[#fff7f7] px-3 py-2.5 text-[8px] leading-4 text-[#8d6262]">
                                    Some required documents are missing. Review carefully before making a decision.
                                </div>
                            @endif

                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <form method="POST" action="{{ route('admin.registrations.approve', $application) }}" class="rounded-xl border border-[#dce7df] bg-[#f8fbf9] p-3">
                                    @csrf
                                    <p class="mb-2 text-[8px] font-bold text-[#52715d]">Approve Account</p>
                                    <input type="text" name="admin_note" maxlength="1500" placeholder="Optional approval note..." class="reg-control mb-2 h-9 w-full rounded-lg border border-[#dce4de] px-3 text-[8px]">
                                    <button class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-[#56816a] text-[8px] font-bold text-white hover:bg-[#496f5b]">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="m7 12 3 3 7-7"></path>
                                        </svg>
                                        Approve Registration
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.registrations.reject', $application) }}" class="rounded-xl border border-[#eadada] bg-[#fffafa] p-3">
                                    @csrf
                                    <p class="mb-2 text-[8px] font-bold text-[#8e5d5d]">Reject Application</p>
                                    <input type="text" name="admin_note" required minlength="5" maxlength="1500" placeholder="Reason for rejection..." class="reg-control mb-2 h-9 w-full rounded-lg border border-[#ead9d9] px-3 text-[8px]">
                                    <button class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-[#a85c54] text-[8px] font-bold text-white hover:bg-[#934f48]">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="m8 8 8 8"></path>
                                            <path d="m16 8-8 8"></path>
                                        </svg>
                                        Reject Registration
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </article>
            @empty
                <div class="col-span-full rounded-[18px] border border-dashed border-[#ded5c9] bg-[#fcfbf8] p-12 text-center">
                    <div class="mx-auto grid h-11 w-11 place-items-center rounded-full border border-[#e6dfd5] bg-white text-[#958a7c]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.4-3.4"></path>
                        </svg>
                    </div>
                    <p class="mt-4 text-[10px] font-bold text-[#514a42]">No matching registration applications</p>
                    <p class="mx-auto mt-1 max-w-[360px] text-[8px] leading-4 text-[#8d8479]">Change the status or role filter to review another group.</p>
                </div>
            @endforelse
        </div>

        <div id="searchEmpty" hidden class="border-t border-[#eee8df] p-8 text-center">
            <p class="text-[9px] font-semibold text-[#5f574e]">No applicant matches your search.</p>
        </div>
    </section>
</div>

<script>
(function () {
    const search = document.getElementById('applicationSearch');
    const cards = Array.from(document.querySelectorAll('[data-application-card]'));
    const empty = document.getElementById('searchEmpty');
    const count = document.getElementById('visibleCount');

    function filterCards() {
        const query = (search?.value || '').trim().toLowerCase();
        let visible = 0;

        cards.forEach(function (card) {
            const haystack = (card.dataset.search || '').toLowerCase();
            const match = !query || haystack.includes(query);
            card.hidden = !match;
            if (match) visible++;
        });

        if (empty) {
            empty.hidden = visible !== 0 || cards.length === 0;
        }

        if (count) {
            count.textContent = 'Showing ' + visible;
        }
    }

    search?.addEventListener('input', filterCards);
    filterCards();
})();
</script>

@endsection
