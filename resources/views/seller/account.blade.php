@extends('layouts.seller')

@section('title', 'Account Management — SARI Seller')
@section('page-title', 'Account Management')

@section('content')
@php
    $fullName = trim(collect([
        $account->first_name,
        $account->middle_initial ? $account->middle_initial . '.' : null,
        $account->last_name,
    ])->filter()->implode(' '));

    $fullName = $fullName !== '' ? $fullName : ($account->store_name ?: 'SARI Seller');
    $initials = collect(preg_split('/\s+/', $fullName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('') ?: 'SS';

    $registrationAddress = collect([
        $account->street_address,
        $account->barangay_name,
        $account->municipality_name,
        $account->province_name,
    ])->filter()->implode(', ');

    $accountStatus = strtolower((string) ($account->account_status ?: 'active'));
    $registrationStatus = strtolower((string) ($account->registration_status ?: 'approved'));
@endphp

<style>
    .seller-settings-page { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; color: #302a24; }
    .seller-settings-card { border: 1px solid #e9e3da; background: #fff; border-radius: 18px; box-shadow: 0 8px 26px rgba(55,45,32,.035); }
    .seller-settings-control { width: 100%; min-height: 44px; border: 1px solid #e3ddd4; border-radius: 12px; background: #fff; padding: 0 14px; color: #302a24; font-size: 12px; transition: border-color .18s ease, box-shadow .18s ease; }
    textarea.seller-settings-control { min-height: 92px; padding-top: 12px; padding-bottom: 12px; }
    .seller-settings-control:focus { outline: none; border-color: #d49a2d; box-shadow: 0 0 0 4px rgba(212,154,45,.08); }
    .seller-settings-label { display:block; margin-bottom:7px; color:#625a50; font-size:11px; font-weight:600; }
    .seller-settings-help { margin-top:6px; color:#978e83; font-size:10px; line-height:1.5; }
</style>

<div class="seller-settings-page mx-auto w-full max-w-[1500px] pb-8">
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 rounded-[14px] border border-[#d5e6da] bg-[#f4faf6] px-4 py-3 text-[11px] text-[#51715d]">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-[#4f8061]">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-[14px] border border-[#ead2d2] bg-[#fff7f7] px-4 py-3 text-[11px] text-[#955f5f]">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="seller-settings-card overflow-hidden">
        <div class="flex flex-col gap-5 px-5 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-[18px] border border-[#eadfc9] bg-[#fff8eb]">
                    @if($profilePhotoUrl)
                        <img
                            src="{{ $profilePhotoUrl }}"
                            alt="{{ $fullName }} profile photo"
                            class="h-full w-full object-cover"
                        >
                    @else
                        <div class="grid h-full w-full place-items-center text-[20px] font-bold text-[#b77c18]">{{ $initials }}</div>
                    @endif
                </div>

                <div class="min-w-0">
                    <p class="text-[9px] font-bold uppercase tracking-[.14em] text-[#a8731f]">Seller Account</p>
                    <h1 class="mt-1 truncate text-[25px] font-bold tracking-[-.035em] text-[#211c16] sm:text-[30px]">{{ $fullName }}</h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[10px] text-[#81786c]">
                        <span>{{ $account->email }}</span>
                        <span class="h-1 w-1 rounded-full bg-[#d5cdc2]"></span>
                        <span>{{ $account->store_name ?: 'Store name not set' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <span class="inline-flex h-8 items-center gap-2 rounded-full border border-[#d6e5dc] bg-[#f4f8f5] px-3 text-[10px] font-semibold text-[#56816a]">
                    <span class="h-2 w-2 rounded-full bg-[#67a17c]"></span>
                    {{ ucfirst($accountStatus) }}
                </span>
                <span class="inline-flex h-8 items-center rounded-full border border-[#eadfc9] bg-[#fffaf3] px-3 text-[10px] font-semibold text-[#a8731f]">
                    Registration {{ ucfirst($registrationStatus) }}
                </span>
            </div>
        </div>
    </section>

    <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_minmax(340px,.65fr)]">
        <div class="space-y-4">
            <form
                method="POST"
                action="{{ route('seller.account.update') }}"
                enctype="multipart/form-data"
                class="seller-settings-card overflow-hidden"
            >
                @csrf
                @method('PATCH')

                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <p class="text-[9px] font-bold uppercase tracking-[.13em] text-[#a8731f]">Profile & Identity</p>
                    <h2 class="mt-1 text-[16px] font-bold text-[#302a24]">Personal information</h2>
                    <p class="mt-1 text-[10px] leading-5 text-[#91887d]">Update the seller identity and contact details attached to your account.</p>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="seller-settings-label" for="sellerFirstName">First name</label>
                            <input id="sellerFirstName" name="first_name" required value="{{ old('first_name', $account->first_name) }}" class="seller-settings-control">
                        </div>
                        <div>
                            <label class="seller-settings-label" for="sellerLastName">Last name</label>
                            <input id="sellerLastName" name="last_name" required value="{{ old('last_name', $account->last_name) }}" class="seller-settings-control">
                        </div>
                        <div>
                            <label class="seller-settings-label" for="sellerMiddleInitial">Middle initial</label>
                            <input id="sellerMiddleInitial" name="middle_initial" maxlength="5" value="{{ old('middle_initial', $account->middle_initial) }}" class="seller-settings-control">
                        </div>
                        <div>
                            <label class="seller-settings-label" for="sellerSex">Sex</label>
                            <select id="sellerSex" name="sex" required class="seller-settings-control">
                                <option value="Male" @selected(old('sex', $account->sex) === 'Male')>Male</option>
                                <option value="Female" @selected(old('sex', $account->sex) === 'Female')>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="seller-settings-label" for="sellerContact">Contact number</label>
                            <input id="sellerContact" name="contact_no" required value="{{ old('contact_no', $account->contact_no) }}" class="seller-settings-control">
                        </div>
                        <div>
                            <label class="seller-settings-label" for="sellerBirthday">Birthday</label>
                            <input id="sellerBirthday" type="date" name="birthday" required value="{{ old('birthday', optional($account->birthday)->format('Y-m-d') ?: $account->getRawOriginal('birthday')) }}" class="seller-settings-control">
                        </div>
                    </div>

                    <div class="mt-5 rounded-[14px] border border-[#e9e3da] bg-[#fcfbf9] p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-[16px] border border-[#e4ddd3] bg-white">
                                @if($profilePhotoUrl)
                                    <img id="sellerProfilePreview" src="{{ $profilePhotoUrl }}" alt="Current profile photo" class="h-full w-full object-cover">
                                @else
                                    <img id="sellerProfilePreview" src="" alt="Selected profile preview" class="hidden h-full w-full object-cover">
                                    <div id="sellerProfileFallback" class="grid h-full w-full place-items-center text-[18px] font-bold text-[#b77c18]">{{ $initials }}</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="seller-settings-label" for="sellerProfileImage">Profile photo</label>
                                <input id="sellerProfileImage" type="file" name="profile_image" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-xl border border-[#e3ddd4] bg-white p-3 text-[10px] text-[#625a50]">
                                <p class="seller-settings-help">JPG, PNG, or WEBP. Maximum 5 MB. Your approved registration photo is shown automatically if one was uploaded.</p>
                                @if($profilePhotoUrl)
                                    <label class="mt-3 inline-flex items-center gap-2 text-[10px] font-medium text-[#8f6257]">
                                        <input type="checkbox" name="remove_profile_image" value="1" class="rounded border-[#d8cec2] text-[#b86b50]">
                                        Remove current profile photo
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end border-t border-[#eee8df] pt-4">
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#d48f08] px-5 text-[10px] font-bold text-white transition hover:bg-[#bd7d05]">Save personal information</button>
                    </div>
                </div>
            </form>

            <section class="seller-settings-card overflow-hidden">
                <div class="border-b border-[#eee8df] px-5 py-4 sm:px-6">
                    <p class="text-[9px] font-bold uppercase tracking-[.13em] text-[#a8731f]">Verification</p>
                    <h2 class="mt-1 text-[16px] font-bold text-[#302a24]">Approved registration requirements</h2>
                    <p class="mt-1 text-[10px] leading-5 text-[#91887d]">These are the private documents submitted during registration. Only your account and authorized administrators can open them.</p>
                </div>

                <div class="grid gap-3 p-5 sm:grid-cols-2 sm:p-6">
                    <div class="rounded-[14px] border border-[#e9e3da] bg-[#fcfbf9] p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-bold text-[#403930]">Government Valid ID</p>
                                <p class="mt-1 text-[9px] text-[#958c80]">Identity requirement submitted with registration.</p>
                            </div>
                            <span class="rounded-full px-2 py-1 text-[8px] font-bold {{ $documentAvailability['id'] ? 'bg-[#eef6f1] text-[#56816a]' : 'bg-[#fff1ef] text-[#a85f50]' }}">
                                {{ $documentAvailability['id'] ? 'Available' : 'Missing' }}
                            </span>
                        </div>
                        @if($documentAvailability['id'])
                            <a href="{{ route('seller.account.documents.show', 'id') }}" target="_blank" rel="noopener" class="mt-4 inline-flex h-9 items-center justify-center rounded-lg border border-[#ded7cd] bg-white px-3 text-[9px] font-bold text-[#625a50] hover:bg-[#fffaf2]">View secure document</a>
                        @else
                            <p class="mt-4 text-[9px] leading-5 text-[#9a6b5c]">The stored Valid ID file could not be found. Contact SARI Admin so the compliance record can be repaired.</p>
                        @endif
                    </div>

                    <div class="rounded-[14px] border border-[#e9e3da] bg-[#fcfbf9] p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-bold text-[#403930]">Business Permit</p>
                                <p class="mt-1 text-[9px] text-[#958c80]">Business requirement used for seller approval.</p>
                            </div>
                            <span class="rounded-full px-2 py-1 text-[8px] font-bold {{ $documentAvailability['permit'] ? 'bg-[#eef6f1] text-[#56816a]' : 'bg-[#fff1ef] text-[#a85f50]' }}">
                                {{ $documentAvailability['permit'] ? 'Available' : 'Missing' }}
                            </span>
                        </div>
                        @if($documentAvailability['permit'])
                            <a href="{{ route('seller.account.documents.show', 'permit') }}" target="_blank" rel="noopener" class="mt-4 inline-flex h-9 items-center justify-center rounded-lg border border-[#ded7cd] bg-white px-3 text-[9px] font-bold text-[#625a50] hover:bg-[#fffaf2]">View secure document</a>
                        @else
                            <p class="mt-4 text-[9px] leading-5 text-[#9a6b5c]">The stored Business Permit file could not be found. Contact SARI Admin so the compliance record can be repaired.</p>
                        @endif
                    </div>

                    <div class="sm:col-span-2 rounded-[14px] border border-[#eadfc9] bg-[#fffaf3] p-4">
                        <p class="text-[10px] font-bold text-[#7d622f]">Verified registration address</p>
                        <p class="mt-1 text-[10px] leading-5 text-[#8e7c61]">{{ $registrationAddress ?: 'No verified registration address is currently stored.' }}</p>
                        <p class="mt-2 text-[9px] leading-5 text-[#9a8b70]">This compliance address is intentionally read-only here. Your operational pickup location is managed separately under Store Management.</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="space-y-4">
            <section class="seller-settings-card p-5">
                <p class="text-[9px] font-bold uppercase tracking-[.13em] text-[#a8731f]">Account Details</p>
                <dl class="mt-4 space-y-3 text-[10px]">
                    <div class="flex items-start justify-between gap-4 border-b border-[#eee8df] pb-3"><dt class="text-[#91887d]">Login email</dt><dd class="max-w-[220px] break-all text-right font-semibold text-[#403930]">{{ $account->email }}</dd></div>
                    <div class="flex items-center justify-between gap-4 border-b border-[#eee8df] pb-3"><dt class="text-[#91887d]">Seller ID</dt><dd class="font-semibold text-[#403930]">SLR-{{ str_pad((string) $account->id, 6, '0', STR_PAD_LEFT) }}</dd></div>
                    <div class="flex items-center justify-between gap-4 border-b border-[#eee8df] pb-3"><dt class="text-[#91887d]">Line of business</dt><dd class="text-right font-semibold text-[#403930]">{{ $account->line_of_business ?: '—' }}</dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-[#91887d]">Approved</dt><dd class="font-semibold text-[#403930]">{{ optional($account->approved_at)->format('M d, Y') ?: '—' }}</dd></div>
                </dl>
                <p class="mt-4 rounded-xl border border-[#e7e1d8] bg-[#faf9f7] p-3 text-[9px] leading-5 text-[#8b8278]">Login email and approved business classification are protected account fields. Contact SARI Admin when those verified details need correction.</p>
            </section>

            <form method="POST" action="{{ route('seller.account.password.update') }}" class="seller-settings-card overflow-hidden">
                @csrf
                @method('PATCH')
                <div class="border-b border-[#eee8df] px-5 py-4">
                    <p class="text-[9px] font-bold uppercase tracking-[.13em] text-[#a8731f]">Security</p>
                    <h2 class="mt-1 text-[15px] font-bold text-[#302a24]">Change password</h2>
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        <label class="seller-settings-label" for="currentPassword">Current password</label>
                        <input id="currentPassword" type="password" name="current_password" autocomplete="current-password" required class="seller-settings-control">
                        @error('current_password', 'password')<p class="seller-settings-help !text-[#a65f57]">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="seller-settings-label" for="newPassword">New password</label>
                        <input id="newPassword" type="password" name="password" autocomplete="new-password" required minlength="8" class="seller-settings-control">
                        @error('password', 'password')<p class="seller-settings-help !text-[#a65f57]">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="seller-settings-label" for="newPasswordConfirmation">Confirm new password</label>
                        <input id="newPasswordConfirmation" type="password" name="password_confirmation" autocomplete="new-password" required minlength="8" class="seller-settings-control">
                    </div>
                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-[#d48f08] px-5 text-[10px] font-bold text-white transition hover:bg-[#bd7d05]">Update password</button>
                </div>
            </form>

            <section class="seller-settings-card p-5">
                <p class="text-[9px] font-bold uppercase tracking-[.13em] text-[#a8731f]">Store Operations</p>
                <h2 class="mt-1 text-[15px] font-bold text-[#302a24]">Public store settings live separately</h2>
                <p class="mt-2 text-[10px] leading-5 text-[#91887d]">Store name, public contact details, Open/Paused status, pickup address, and pickup instructions are managed in Store Management.</p>
                <a href="{{ route('seller.store.index') }}" wire:navigate.hover class="mt-4 inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-[#dfcfac] bg-[#fffaf1] px-4 text-[10px] font-bold text-[#966516] transition hover:bg-[#fff4dc]">Open Store Management</a>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initSellerAccountProfilePreview() {
        const input = document.getElementById('sellerProfileImage');
        if (!input || input.dataset.previewBound === '1') return;
        input.dataset.previewBound = '1';

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const preview = document.getElementById('sellerProfilePreview');
            const fallback = document.getElementById('sellerProfileFallback');
            if (!preview) return;

            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.classList.remove('hidden');
            fallback?.classList.add('hidden');
            preview.onload = () => URL.revokeObjectURL(url);
        });
    }

    document.addEventListener('DOMContentLoaded', initSellerAccountProfilePreview, { once: true });
    document.addEventListener('livewire:navigated', initSellerAccountProfilePreview);
})();
</script>
@endpush
