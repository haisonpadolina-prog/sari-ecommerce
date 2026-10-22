@extends('layouts.admin')

@section('title', 'Users — SARI Admin')
@section('page-title', 'User Management')
@include('admin.partials.users.summary-icon-styles')
@section('content')
@include('admin.partials.users.page-styles')
@include('admin.partials.users.table-styles')
@include('admin.partials.users.modal-styles')

@php
    $metricCards = [
        ['Total Accounts', $stats['total'] ?? 0, 'All accounts', 'all', 'text-[#b98112] bg-[#fff7e8] border-[#efdfbf]', 'users'],
        ['Buyers', $stats['buyers'] ?? 0, 'Buyer accounts', 'Buyer', 'text-[#298b53] bg-[#eef8f1] border-[#d8ebde]', 'user'],
        ['Sellers', $stats['sellers'] ?? 0, 'Seller accounts', 'Seller', 'text-[#377ab7] bg-[#eef6fd] border-[#d9e7f3]', 'store'],
        ['Riders', $stats['riders'] ?? 0, 'Rider accounts', 'Rider', 'text-[#7154c7] bg-[#f4f0fd] border-[#e5ddf6]', 'rider'],
        ['Logistics', $stats['logistics'] ?? 0, 'Logistics accounts', 'Logistics', 'text-[#c26f20] bg-[#fff5ed] border-[#f0dfd0]', 'truck'],
    ];

    $roleTone = fn ($role) => 'user-role-badge ' . match (strtolower((string) $role)) {
        'buyer' => 'user-role-badge--buyer',
        'seller' => 'user-role-badge--seller',
        'rider', 'courier' => 'user-role-badge--rider',
        'logistics' => 'user-role-badge--logistics',
        default => 'user-role-badge--other',
    };
@endphp

<div class="user-page mx-auto w-full max-w-[1880px] pb-8">
    @if(session('success'))
        <div class="mb-4 flex items-start gap-3 rounded-[14px] border border-[#d5e6da] bg-[#f4faf6] px-4 py-3 shadow-[0_8px_22px_rgba(50,95,66,.05)]">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-[#45805b]">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 2.5 2.5L16 9"></path></svg>
            </span>
            <div><p class="text-[9px] font-bold text-[#426b50]">Success</p><p class="mt-0.5 text-[9px] text-[#617668]">{{ session('success') }}</p></div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-[14px] border border-[#ead2d2] bg-[#fff7f7] px-4 py-3 text-[9px] text-[#8d6262] shadow-[0_8px_22px_rgba(120,60,60,.04)]">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PAGE HEADER — SELLER COMPLIANCE STYLE --}}
    <section class="user-page-header flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="user-page-header-main">
            <span class="user-page-header-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="9" cy="8" r="3"></circle>
                    <path d="M3 20a6 6 0 0 1 12 0"></path>
                    <path d="M17 11h4"></path>
                    <path d="M19 9v4"></path>
                </svg>
            </span>

            <div class="min-w-0">
                <p class="user-page-eyebrow">User Management</p>

                <h2 class="user-page-title">
                    <span class="user-page-title-base">Approved</span>
                    <span class="user-page-title-accent">Accounts</span>
                </h2>

                <p class="user-page-subtitle">
                    View profiles, manage account access, leave admin notes, and review recent activity.
                </p>
            </div>
        </div>

        <button
            id="exportUsers"
            type="button"
            class="user-page-export inline-flex items-center justify-center gap-2 self-start border border-[#e6ddd2] bg-white text-[#5f574e] transition hover:border-[#d8c4a3] hover:bg-[#fffaf2] hover:text-[#a8731f]"
        >
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 3v12"></path>
                <path d="m8 11 4 4 4-4"></path>
                <path d="M5 19h14"></path>
            </svg>
            Export List
        </button>
    </section>

    {{-- SUMMARY --}}
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        @foreach($metricCards as [$label, $value, $helper, $role, $tone, $icon])
            <button type="button" data-summary-role="{{ $role }}" class="metric-card group relative min-h-[108px] p-4 pr-20 text-left sm:p-[18px] sm:pr-20">
                <div class="flex h-full items-center gap-4">
                    <span class="absolute right-4 top-4 grid h-12 w-12 shrink-0 place-items-center rounded-[12px] border {{ $tone }} sm:right-[18px] sm:top-[18px]">
                        @if($icon === 'users')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="7" r="3"></circle><path d="M3 20a6 6 0 0 1 12 0"></path><path d="M17 6a3 3 0 0 1 0 6"></path><path d="M18 15a5 5 0 0 1 3 5"></path></svg>
                        @elseif($icon === 'user')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"></circle><path d="M5 20a7 7 0 0 1 14 0"></path></svg>
                        @elseif($icon === 'store')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 9h16"></path><path d="M5 4h14l1 5v2a3 3 0 0 1-3 3 3 3 0 0 1-3-3 3 3 0 0 1-6 0 3 3 0 0 1-3 3V9Z"></path><path d="M6 14v6h12v-6"></path></svg>
                        @elseif($icon === 'rider')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="6" cy="17" r="3"></circle><circle cx="18" cy="17" r="3"></circle><path d="M9 17h6"></path><path d="m10 10 3 4h3l2-4"></path><path d="M8 10h4"></path></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h11v11H3z"></path><path d="M14 10h4l3 3v4h-7z"></path><circle cx="7" cy="18" r="2"></circle><circle cx="18" cy="18" r="2"></circle></svg>
                        @endif
                    </span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-medium text-[#7d746a]">{{ $label }}</p>
                        <p class="mt-1 text-[24px] font-bold leading-none tracking-[-.04em] text-[#1c1712]">{{ $value }}</p>
                        <p class="mt-2 truncate text-[8px] text-[#9b9288]">{{ $helper }}</p>
                    </div>
                </div>
            </button>
        @endforeach
    </section>

    {{-- FILTER BAR --}}
    <section class="user-surface mt-4 p-3">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
            <div class="relative min-w-0 flex-1">
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9d8f7e]" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg>
                <input id="userSearch" type="search" placeholder="Search name, email, contact, or role..." class="clean-control h-11 w-full rounded-[12px] pl-11 pr-4 text-[10px]">
            </div>

            <div class="relative xl:w-[185px]" data-role-dropdown>
                <button type="button" data-role-toggle class="clean-control flex h-11 w-full items-center justify-between rounded-[12px] px-4 text-[9px] font-medium"><span data-role-label>All Roles</span><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#8b8175]" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 10 5 5 5-5"></path></svg></button>
                <div class="custom-menu absolute left-0 right-0 top-full z-40 rounded-[14px] border border-[#e7dfd4] bg-white p-1.5 shadow-[0_18px_40px_rgba(47,37,25,.13)]" data-role-menu>
                    @foreach(['all' => 'All Roles', 'Buyer' => 'Buyer', 'Seller' => 'Seller', 'Rider' => 'Rider', 'Logistics' => 'Logistics'] as $value => $label)
                        <button type="button" data-role-option="{{ $value }}" class="flex w-full items-center justify-between rounded-[10px] px-3 py-2.5 text-left text-[9px] font-medium text-[#5c534a] transition hover:bg-[#fff7e8] hover:text-[#a8731f]">{{ $label }}<span class="h-1.5 w-1.5 rounded-full bg-[#d99500] opacity-0" data-check></span></button>
                    @endforeach
                </div>
            </div>

            <div class="relative xl:w-[175px]" data-status-dropdown>
                <button type="button" data-status-toggle class="clean-control flex h-11 w-full items-center justify-between rounded-[12px] px-4 text-[9px] font-medium"><span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#3f9a61]"></span><span data-status-label>All Status</span></span><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#8b8175]" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m7 10 5 5 5-5"></path></svg></button>
                <div class="custom-menu absolute left-0 right-0 top-full z-40 rounded-[14px] border border-[#e7dfd4] bg-white p-1.5 shadow-[0_18px_40px_rgba(47,37,25,.13)]" data-status-menu>
                    @foreach(['all' => 'All Status', 'active' => 'Active', 'deactivated' => 'Suspended', 'banned' => 'Banned'] as $value => $label)
                        <button type="button" data-status-option="{{ $value }}" class="flex w-full items-center justify-between rounded-[10px] px-3 py-2.5 text-left text-[9px] font-medium text-[#5c534a] transition hover:bg-[#fff7e8] hover:text-[#a8731f]">{{ $label }}<span class="h-1.5 w-1.5 rounded-full bg-[#d99500] opacity-0" data-check></span></button>
                    @endforeach
                </div>
            </div>

            <button id="applyFilter" type="button" class="inline-flex h-11 items-center justify-center gap-2 rounded-[12px] bg-[#d99500] px-5 text-[9px] font-semibold text-white shadow-[0_10px_22px_rgba(217,149,0,.18)] transition hover:bg-[#bd8205]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>Apply Filter</button>
            <button id="resetFilter" type="button" class="inline-flex h-11 items-center justify-center rounded-[12px] border border-[#e6ddd2] bg-white px-4 text-[9px] font-semibold text-[#6f665b] transition hover:bg-[#faf8f4]">Reset</button>
        </div>
    </section>

    {{-- TABLE --}}
    <section class="user-surface mt-4 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1060px] border-collapse text-left">
                <thead><tr class="border-b border-[#eee8df] bg-[#fcfbf8]">

                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Account</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Role</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Contact</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Status</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Last sign-in</th>
                    <th class="px-4 py-3.5 text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Joined</th>
                    <th class="px-5 py-3.5 text-right text-[9px] font-bold uppercase tracking-[.08em] text-[#847b70]">Actions</th>
                </tr></thead>
                <tbody id="userRows" class="divide-y divide-[#f0ebe4]">
                @forelse($accounts as $account)
                    @php
                        $name = (string) ($account['name'] ?? 'Unnamed User');
                        $role = (string) ($account['role'] ?? 'User');
                        $roleKey = (string) ($account['role_key'] ?? strtolower($role));
                        $email = (string) ($account['email'] ?? '');
                        $status = strtolower((string) ($account['status'] ?? 'active'));
                        $id = $account['id'] ?? null;
                        $initials = collect(preg_split('/\s+/', trim($name)) ?: [])->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                        $created = $account['created_at'] ?? null;
                        $joinedDate = $created?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? '—';
                        $joinedTime = $created?->copy()->timezone('Asia/Manila')->format('h:i A') ?? '';
                        $reference = $account['account_reference'] ?? strtoupper($roleKey) . '-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
                        $photoUrl = $account['profile_photo_url'] ?? null;
                        $lastSignIn = $account['last_sign_in_at'] ?? null;
                        $lastSignInDate = $lastSignIn?->copy()->timezone('Asia/Manila')->format('M d, Y');
                        $lastSignInTime = $lastSignIn?->copy()->timezone('Asia/Manila')->format('h:i A');
                        $activityPayload = collect($account['activity'] ?? [])->map(fn ($event) => [
                            'action' => $event['action'] ?? 'activity',
                            'description' => $event['description'] ?? 'Account activity',
                            'time' => optional($event['created_at'] ?? null)->format('M d, Y h:i A'),
                            'relative' => optional($event['created_at'] ?? null)->diffForHumans(),
                        ])->values();
                    @endphp
                    <tr
                        data-user-row
                        data-name="{{ strtolower($name) }}"
                        data-display-name="{{ $name }}"
                        data-account-reference="{{ $reference }}"
                        data-photo-url="{{ $photoUrl }}"
                        data-last-sign-in="{{ $lastSignIn ? $lastSignInDate . ' ' . $lastSignInTime : 'No recorded sign-in' }}"
                        data-role="{{ $role }}"
                        data-role-key="{{ $roleKey }}"
                        data-email="{{ strtolower($email) }}"
                        data-email-display="{{ $email }}"
                        data-contact="{{ $account['contact_no'] ?? '' }}"
                        data-status="{{ $status }}"
                        data-id="{{ $id }}"
                        data-joined="{{ $joinedDate }}"
                        data-first-name="{{ $account['first_name'] ?? '' }}"
                        data-last-name="{{ $account['last_name'] ?? '' }}"
                        data-store-name="{{ $account['store_name'] ?? '' }}"
                        data-update-url="{{ route('admin.users.update', [$roleKey, $id]) }}"
                        data-suspend-url="{{ route('admin.users.suspend', [$roleKey, $id]) }}"
                        data-restore-url="{{ route('admin.users.restore', [$roleKey, $id]) }}"
                        data-ban-url="{{ route('admin.users.ban', [$roleKey, $id]) }}"
                        data-unban-url="{{ route('admin.users.unban', [$roleKey, $id]) }}"
                        data-delete-url="{{ route('admin.users.destroy', [$roleKey, $id]) }}"
                        data-note-url="{{ route('admin.users.note', [$roleKey, $id]) }}"
                        data-message-url="{{ $roleKey === 'seller' ? route('admin.messages', ['seller' => $id]) : '' }}"
                        data-profile='@json($account['profile'] ?? [])'
                        data-activity='@json($activityPayload)'
                    >
                        <td class="px-4 py-4">
                            <div class="user-account-identity">
                                <span class="user-account-avatar"><span aria-hidden="true">{{ $initials ?: 'U' }}</span>
                                    @if($photoUrl)<img src="{{ $photoUrl }}" alt="Profile photo of {{ $name }}" loading="eager" decoding="async" data-user-photo>@endif
                                </span>
                                <div class="min-w-0"><p class="user-account-name">{{ $name }}</p><p class="user-account-reference">{{ $reference }}</p></div>
                            </div>
                        </td>
                        <td class="px-4 py-4"><span class="inline-flex rounded-full border px-3 py-1.5 text-[8px] font-semibold {{ $roleTone($role) }}">{{ $role }}</span></td>
                        <td class="px-4 py-4"><p class="max-w-[230px] truncate text-[9px] font-medium text-[#514a42]">{{ $email ?: 'No email' }}</p><p class="mt-0.5 text-[8px] text-[#958c80]">{{ $account['contact_no'] ?: 'No contact number' }}</p></td>
                        <td class="px-4 py-4">
                            @if($status === 'active')
                                <span class="inline-flex items-center gap-2 rounded-full border border-[#d6e9dc] bg-[#eef8f1] px-3 py-1.5 text-[8px] font-semibold text-[#36805a]"><span class="h-1.5 w-1.5 rounded-full bg-[#2f9b5c]"></span>Active</span>
                            @elseif($status === 'banned')
                                <span class="inline-flex items-center gap-2 rounded-full border border-[#efc8c3] bg-[#fff1ef] px-3 py-1.5 text-[8px] font-semibold text-[#bd4438]"><span class="h-1.5 w-1.5 rounded-full bg-[#d94f42]"></span>Banned</span>
                            @else
                                <span class="inline-flex items-center gap-2 rounded-full border border-[#ead5d0] bg-[#fff4f1] px-3 py-1.5 text-[8px] font-semibold text-[#a95d45]"><span class="h-1.5 w-1.5 rounded-full bg-[#bd6a4e]"></span>Suspended</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 user-account-last-login">
                            @if($lastSignIn)
                                <p>{{ $lastSignInDate }}</p><span>{{ $lastSignInTime }} PHT</span>
                            @else
                                <p class="user-account-no-login">No recorded sign-in</p>
                            @endif
                        </td>
                        <td class="px-4 py-4"><p class="text-[9px] font-medium text-[#514a42]">{{ $joinedDate }}</p>@if($joinedTime)<p class="mt-0.5 text-[8px] text-[#958c80]">{{ $joinedTime }}</p>@endif</td>
                        <td class="px-5 py-4">
                            <div class="user-actions-cell">
                                <button type="button" class="user-actions-trigger" popovertarget="user-actions-{{ $roleKey }}-{{ $id }}" aria-label="Actions for {{ $name }}" aria-expanded="false">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="19" cy="12" r="1.7"/></svg>
                                </button>
                                <div id="user-actions-{{ $roleKey }}-{{ $id }}" class="user-actions-menu" popover="auto" aria-label="Account actions for {{ $name }}">

                                {{-- View profile --}}
                                <button type="button" data-view-user class="account-action" title="View profile" aria-label="View profile">
                                    
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                            <circle cx="12" cy="12" r="2.5"></circle>
                                        </svg>
                                <span>View profile</span></button>

                                {{-- Message: current backend supports Admin ↔ Seller messaging --}}
                                @if($roleKey === 'seller')
                                    <a href="{{ route('admin.messages', ['seller' => $id]) }}" class="account-action account-action--message" title="Message seller" aria-label="Message seller">
                                        
                                            <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M20 15a4 4 0 0 1-4 4H8l-4 2V7a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v8Z"></path>
                                                <path d="M8 9h8"></path>
                                                <path d="M8 13h5"></path>
                                            </svg>
                                    <span>Message seller</span>
                                    </a>
                                @endif

                                {{-- Recent activity --}}
                                <button type="button" data-activity-user class="account-action account-action--activity" title="Recent activity" aria-label="Recent activity">
                                    
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8"></circle>
                                            <path d="M12 7v5l3 2"></path>
                                            <path d="M5 5 3 8h4"></path>
                                        </svg>
                                <span>Recent activity</span></button>

                                {{-- Internal admin note --}}
                                <button type="button" data-note-user class="account-action account-action--note" title="Add admin note" aria-label="Add admin note">
                                    
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M6 3h12v18H6z"></path>
                                            <path d="M9 8h6"></path>
                                            <path d="M9 12h6"></path>
                                            <path d="M9 16h4"></path>
                                        </svg>
                                <span>Add admin note</span></button>

                                {{-- Suspend / Restore --}}
                                @if($status === 'active')
                                    <button type="button" data-access-user data-access-mode="suspend" class="account-action account-action--suspend" title="Suspend account" aria-label="Suspend account">
                                        
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8"></circle>
                                            <path d="m6.5 6.5 11 11"></path>
                                        </svg>
                                    <span>Suspend account</span></button>
                                @else
                                    <button type="button" data-access-user data-access-mode="restore" class="account-action account-action--restore" title="Restore account" aria-label="Restore account">
                                        
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M20 7v5h-5"></path>
                                            <path d="M18 16a7 7 0 1 1 1-8l1 4"></path>
                                        </svg>
                                    <span>Restore account</span></button>
                                @endif

                                @if($status === 'banned')
                                    <button type="button" data-ban-user data-ban-mode="unban" class="account-action account-action--restore" title="Cancel ban" aria-label="Cancel ban">
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 7v5h-5"></path><path d="M18 16a7 7 0 1 1 1-8l1 4"></path></svg>
                                    <span>Cancel ban</span></button>
                                @else
                                    <button type="button" data-ban-user data-ban-mode="ban" class="account-action account-action--suspend" title="Ban email and account" aria-label="Ban email and account">
                                        <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 5l14 14"></path><circle cx="12" cy="12" r="9"></circle></svg>
                                    <span>Ban email and account</span></button>
                                @endif

                                <button type="button" data-delete-user class="account-action" style="color:#d95745" title="Delete account" aria-label="Delete account">
                                    <svg viewBox="0 0 24 24" class="account-action-svg" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M7 7l1 13h8l1-13"></path><path d="M10 11v5M14 11v5"></path></svg>
                                <span>Delete account</span></button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-16 text-center"><div class="mx-auto grid h-12 w-12 place-items-center rounded-full border border-[#e7dfd5] bg-[#fcfbf8] text-[#958a7c]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.4-3.4"></path></svg></div><p class="mt-4 text-[11px] font-bold text-[#514a42]">No approved accounts yet</p><p class="mt-1 text-[9px] text-[#8d8479]">Approved platform users will appear here.</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="user-table-footer">
            <p id="resultCount" aria-live="polite">Showing {{ count($accounts) }} results</p>
            <div class="user-pagination" aria-label="Account table pagination">
                <label for="userPageSize">Rows</label><select id="userPageSize"><option value="10">10</option><option value="20" selected>20</option><option value="50">50</option></select>
                <button type="button" id="userPreviousPage" aria-label="Previous page">Previous</button>
                <span id="userPageNumber" aria-live="polite">1 / 1</span>
                <button type="button" id="userNextPage" aria-label="Next page">Next</button>
            </div>
        </div>
    </section>
</div>

@include('admin.partials.users.account-modal')

<script>
(function () {
    const rows = Array.from(document.querySelectorAll('[data-user-row]'));
    const search = document.getElementById('userSearch');
    const resultCount = document.getElementById('resultCount');
    const summaryCards = Array.from(document.querySelectorAll('[data-summary-role]'));
    let selectedRole = 'all';
    let selectedStatus = 'all';
    let currentPage = 1;
    let filteredRows = rows.slice();
    const pageSizeControl = document.getElementById('userPageSize');

    const normalize = value => (value || '').toString().trim().toLowerCase();

    /*
     * PERFORMANCE:
     * Build the searchable text once instead of reconstructing and
     * lower-casing it again for every row on every keystroke.
     */
    const rowIndex = rows.map(row => ({
        row,
        role: normalize(row.dataset.role),
        status: normalize(row.dataset.status),
        searchText: normalize([
            row.dataset.name,
            row.dataset.accountReference,
            row.dataset.emailDisplay,
            row.dataset.contact,
            row.dataset.role
        ].join(' '))
    }));

    function debounce(callback, wait = 130) {
        let timer = null;

        return function (...args) {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => callback.apply(this, args), wait);
        };
    }

    let filterFrame = 0;

    function applyFilters(resetPage = true) {
        if (resetPage) currentPage = 1;
        const query = normalize(search?.value);
        const normalizedRole = normalize(selectedRole);
        const normalizedStatus = normalize(selectedStatus);

        window.cancelAnimationFrame(filterFrame);

        filterFrame = window.requestAnimationFrame(() => {
            filteredRows = rowIndex.filter(item =>
                (normalizedRole === 'all' || item.role === normalizedRole)
                && (normalizedStatus === 'all' || item.status === normalizedStatus)
                && (query === '' || item.searchText.includes(query))
            ).map(item => item.row);
            const pageSize = Number(pageSizeControl?.value || 20);
            const pageCount = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(currentPage, 1), pageCount);
            const offset = (currentPage - 1) * pageSize;
            const visibleSet = new Set(filteredRows.slice(offset, offset + pageSize));
            rows.forEach(row => { row.hidden = !visibleSet.has(row); });
            if (resultCount) resultCount.textContent = filteredRows.length
                ? `Showing ${offset + 1}–${Math.min(offset + pageSize, filteredRows.length)} of ${filteredRows.length} results`
                : 'No matching accounts';
            document.getElementById('userPageNumber').textContent = `${currentPage} / ${pageCount}`;
            document.getElementById('userPreviousPage').disabled = currentPage <= 1;
            document.getElementById('userNextPage').disabled = currentPage >= pageCount;

            summaryCards.forEach(card => {
                card.classList.toggle(
                    'is-active',
                    normalize(card.dataset.summaryRole) === normalizedRole
                );
            });
        });
    }

    const debouncedApplyFilters = debounce(applyFilters, 130);

    search?.addEventListener('input', debouncedApplyFilters, { passive: true });

    summaryCards.forEach(card => card.addEventListener('click', () => {
        selectedRole = card.dataset.summaryRole || 'all';
        syncRoleDropdown();
        applyFilters();
    }));

    function setupDropdown(rootSelector, toggleSelector, menuSelector, optionSelector, labelSelector, onSelect) {
        const root = document.querySelector(rootSelector);
        const toggle = root?.querySelector(toggleSelector);
        const menu = root?.querySelector(menuSelector);
        const label = root?.querySelector(labelSelector);
        const options = Array.from(root?.querySelectorAll(optionSelector) || []);
        toggle?.addEventListener('click', event => { event.stopPropagation(); menu?.classList.toggle('is-open'); });
        options.forEach(option => option.addEventListener('click', () => { onSelect(option, label, options); menu?.classList.remove('is-open'); applyFilters(); }));
        document.addEventListener('click', event => { if (!event.target.closest(rootSelector)) menu?.classList.remove('is-open'); });
        return { root, label, options };
    }

    const roleDropdown = setupDropdown('[data-role-dropdown]', '[data-role-toggle]', '[data-role-menu]', '[data-role-option]', '[data-role-label]', (option, label, options) => {
        selectedRole = option.dataset.roleOption || 'all';
        if (label) label.textContent = option.textContent.trim();
        options.forEach(o => o.querySelector('[data-check]')?.classList.toggle('opacity-0', o !== option));
    });

    const statusDropdown = setupDropdown('[data-status-dropdown]', '[data-status-toggle]', '[data-status-menu]', '[data-status-option]', '[data-status-label]', (option, label, options) => {
        selectedStatus = option.dataset.statusOption || 'all';
        if (label) label.textContent = option.textContent.trim();
        options.forEach(o => o.querySelector('[data-check]')?.classList.toggle('opacity-0', o !== option));
    });

    function syncRoleDropdown() {
        roleDropdown.options.forEach(option => {
            const active = normalize(option.dataset.roleOption) === normalize(selectedRole);
            option.querySelector('[data-check]')?.classList.toggle('opacity-0', !active);
            if (active && roleDropdown.label) roleDropdown.label.textContent = option.textContent.trim();
        });
    }

    document.getElementById('applyFilter')?.addEventListener('click', applyFilters);
    document.getElementById('resetFilter')?.addEventListener('click', () => {
        selectedRole = 'all'; selectedStatus = 'all'; if (search) search.value = '';
        roleDropdown.options[0]?.click(); statusDropdown.options[0]?.click(); applyFilters();
    });

    document.getElementById('exportUsers')?.addEventListener('click', () => {
        const visibleRows = filteredRows;
        const csv = [['Account reference','Name','Role','Email','Contact','Status','Last sign-in','Joined'], ...visibleRows.map(row => [row.dataset.accountReference || '', row.dataset.displayName || '', row.dataset.role || '', row.dataset.emailDisplay || '', row.dataset.contact || '', row.dataset.status || '', row.dataset.lastSignIn || '', row.dataset.joined || ''])].map(cols => cols.map(value => '"' + String(value).replaceAll('"','""') + '"').join(',')).join('\n');
        const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'}); const url = URL.createObjectURL(blob); const link = document.createElement('a'); link.href = url; link.download = 'sari-user-accounts.csv'; document.body.appendChild(link); link.click(); link.remove(); URL.revokeObjectURL(url);
    });

    pageSizeControl?.addEventListener('change', () => applyFilters());
    document.getElementById('userPreviousPage')?.addEventListener('click', () => { currentPage--; applyFilters(false); });
    document.getElementById('userNextPage')?.addEventListener('click', () => { currentPage++; applyFilters(false); });
    document.querySelectorAll('[data-user-photo]').forEach(image => {
        image.addEventListener('error', () => { image.hidden = true; });
        image.addEventListener('load', () => { image.hidden = false; });
        // A deferred image can be complete with zero width before a request starts.
        // Hide only after an actual error event, never while it is waiting to load.
        if (image.complete && image.naturalWidth > 0) image.hidden = false;
    });
    document.querySelectorAll('.user-actions-menu').forEach(menu => {
        const trigger = menu.parentElement.querySelector('[popovertarget]');
        menu.addEventListener('beforetoggle', event => {
            const open = event.newState === 'open';
            trigger?.setAttribute('aria-expanded', String(open));
            if (!open || !trigger) return;
            const rect = trigger.getBoundingClientRect();
            menu.style.left = Math.max(8, Math.min(window.innerWidth - 224, rect.right - 216)) + 'px';
            menu.style.top = 'auto'; menu.style.bottom = 'auto';
            if (window.innerHeight - rect.bottom > 310) menu.style.top = rect.bottom + 5 + 'px';
            else menu.style.bottom = Math.max(8, window.innerHeight - rect.top + 5) + 'px';
        });
        menu.addEventListener('click', event => {
            if (event.target.closest('button, a')) menu.hidePopover();
        });
    });

    syncRoleDropdown(); applyFilters();
})();
</script>
<!-- Action icons are inline SVGs to avoid an extra render-blocking icon-font stylesheet. -->
@include('admin.partials.users.modal-script')
@endsection
