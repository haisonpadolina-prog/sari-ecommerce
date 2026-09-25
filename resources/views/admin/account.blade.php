@extends('layouts.admin')

@section('title','Account — SARI Admin')
@section('page-title','Account Management')

@push('styles')
<style>
    .sari-account-page{font-family:'Poppins',sans-serif;color:#26211d;}
    .sari-account-page input:-webkit-autofill,
    .sari-account-page input:-webkit-autofill:hover,
    .sari-account-page input:-webkit-autofill:focus{
        -webkit-box-shadow:0 0 0 1000px #fff inset !important;
        -webkit-text-fill-color:#2f2924 !important;
    }
    .sari-account-page .account-card{box-shadow:0 8px 26px rgba(55,45,32,.035);}
    .sari-account-page .field-input:focus{border-color:#d6b363;box-shadow:0 0 0 4px rgba(201,145,40,.07);outline:none;}
</style>
@endpush

@section('content')
@php
    $roleLabel = match($adminAccount->role ?? 'super_admin') {
        'super_admin' => 'Super Administrator',
        'admin' => 'Administrator',
        default => ucwords(str_replace('_', ' ', (string) ($adminAccount->role ?? 'Administrator'))),
    };

    $accessLabel = $adminAccount->canManagePlatformSettings()
        ? 'Full Platform Access'
        : 'Standard Admin Access';

    $profileUpdated = $adminAccount->profile_updated_at ?? $adminAccount->updated_at;
    $passwordChanged = $adminAccount->password_changed_at;
    $lastLogin = $adminAccount->last_login_at;
@endphp

<div class="sari-account-page mx-auto max-w-[1180px] pb-8">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex items-start gap-3.5">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-[12px] border border-[#eadfc8] bg-[#fff8e9] text-[#a66f16]">
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4.8 20c.9-4 3.4-6 7.2-6s6.3 2 7.2 6"></path>
                </svg>
            </div>
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.18em] text-[#aa7a25]">Administration</p>
                <h1 class="mt-1 text-[28px] font-bold leading-none tracking-[-.035em] text-[#25211d]">Account <span class="text-[#c99128]">Management</span></h1>
                <p class="mt-2 max-w-[620px] text-[10px] leading-5 text-[#7e766d]">Manage administrator identity, password security, access information, and account activity.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 flex items-center gap-2.5 rounded-[13px] border border-[#dbe8df] bg-[#f5faf6] px-4 py-3 text-[9px] font-medium text-[#51715d]">
            <span class="grid h-6 w-6 place-items-center rounded-full bg-white text-[#568269]">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
            </span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-[minmax(0,1.45fr)_minmax(310px,.75fr)]">
        <section class="account-card rounded-[16px] border border-[#e8e3dc] bg-white p-5 sm:p-6">
            <div class="flex flex-col gap-4 border-b border-[#eee9e3] pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-3.5">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full border border-[#dce5e1] bg-[#eef3f1] text-[11px] font-bold text-[#60756e]">
                        {{ collect(preg_split('/\s+/', trim($adminAccount->name)))->filter()->map(fn($part) => strtoupper(substr($part,0,1)))->take(2)->implode('') ?: 'SA' }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-[15px] font-bold text-[#2b2622]">{{ $adminAccount->name }}</h2>
                        <div class="mt-1 flex flex-wrap items-center gap-2">
                            <span class="inline-flex h-5 items-center rounded-full border border-[#ead39d] bg-[#fff8e9] px-2 text-[7px] font-bold text-[#a26e17]">{{ $roleLabel }}</span>
                            <span class="inline-flex h-5 items-center gap-1.5 rounded-full border border-[#dce7df] bg-[#f5f9f6] px-2 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Active</span>
                        </div>
                    </div>
                </div>
                <div class="rounded-[11px] border border-[#ece7e0] bg-[#faf9f7] px-3 py-2 text-right">
                    <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#9a9187]">Account ID</p>
                    <p class="mt-1 text-[9px] font-semibold text-[#4d463f]">ADM-{{ str_pad((string) $adminAccount->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            <div class="mt-5">
                <div class="mb-4">
                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Administrator Profile</p>
                    <p class="mt-1 text-[9px] text-[#91887e]">Update the identity used for your SARI administrator account.</p>
                </div>

                <form method="POST" action="{{ route('admin.account.profile.update') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PATCH')

                    <label class="block">
                        <span class="text-[8px] font-semibold text-[#5f574f]">Full name</span>
                        <input name="name" value="{{ old('name', $adminAccount->name) }}" autocomplete="name" class="field-input mt-2 h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 text-[9px] font-medium text-[#332d28] transition" required>
                        @error('name', 'profile')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror
                    </label>

                    <label class="block">
                        <span class="text-[8px] font-semibold text-[#5f574f]">Email address</span>
                        <input name="email" type="email" value="{{ old('email', $adminAccount->email) }}" autocomplete="email" class="field-input mt-2 h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 text-[9px] font-medium text-[#332d28] transition" required>
                        @error('email', 'profile')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror
                    </label>

                    <div class="sm:col-span-2 flex justify-end pt-1">
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-[10px] bg-[#c99128] px-4 text-[9px] font-bold text-white shadow-[0_6px_14px_rgba(166,112,18,.14)] transition hover:bg-[#b6811f]">
                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M5 12l4 4L19 6"/></svg>
                            Save profile
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <aside class="account-card rounded-[16px] border border-[#e8e3dc] bg-[#fbfaf8] p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Account Access</p>
                    <p class="mt-1 text-[9px] text-[#91887e]">Current platform authorization.</p>
                </div>
                <span class="inline-flex h-6 items-center gap-1.5 rounded-full border border-[#dce7df] bg-white px-2.5 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Active</span>
            </div>

            <dl class="mt-5 space-y-3">
                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Role</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $roleLabel }}</dd></div>
                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Access level</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $accessLabel }}</dd></div>
                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Authentication</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">Password</dd></div>
                <div class="flex items-center justify-between gap-4 border-b border-[#ece7e0] pb-3"><dt class="text-[8px] text-[#8f867d]">Platform settings</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ $adminAccount->canManagePlatformSettings() ? 'Allowed' : 'Restricted' }}</dd></div>
                <div class="flex items-center justify-between gap-4"><dt class="text-[8px] text-[#8f867d]">Account created</dt><dd class="text-right text-[8px] font-semibold text-[#413a34]">{{ optional($adminAccount->created_at)->format('M d, Y') ?? '—' }}</dd></div>
            </dl>
        </aside>
    </div>

    <section class="account-card mt-4 rounded-[16px] border border-[#e8e3dc] bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Account Security</p>
                    <span class="inline-flex h-5 items-center gap-1.5 rounded-full border border-[#dce7df] bg-[#f5f9f6] px-2 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Secured</span>
                </div>
                <p class="mt-1.5 text-[9px] text-[#91887e]">Change your administrator password. Your current password is required.</p>
            </div>
            <div class="rounded-[10px] border border-[#eee7da] bg-[#fffaf0] px-3 py-2 text-[7px] text-[#8b6a32]">Minimum 8 characters</div>
        </div>

        <form method="POST" action="{{ route('admin.account.password.update') }}" class="mt-5 grid gap-4 lg:grid-cols-3">
            @csrf
            @method('PATCH')

            @foreach([
                ['current_password', 'Current password', 'current-password'],
                ['password', 'New password', 'new-password'],
                ['password_confirmation', 'Confirm new password', 'new-password'],
            ] as [$field, $label, $autocomplete])
                <label class="block">
                    <span class="text-[8px] font-semibold text-[#5f574f]">{{ $label }}</span>
                    <div class="relative mt-2">
                        <input id="{{ $field }}" name="{{ $field }}" type="password" autocomplete="{{ $autocomplete }}" class="field-input h-11 w-full rounded-[11px] border border-[#ddd7cf] bg-white px-3.5 pr-10 text-[9px] font-medium text-[#332d28] transition" required>
                        <button type="button" data-password-toggle="{{ $field }}" class="absolute right-2 top-1/2 grid h-7 w-7 -translate-y-1/2 place-items-center rounded-lg text-[#8b837a] transition hover:bg-[#f5f2ed] hover:text-[#76551d]" aria-label="Show or hide password">
                            <svg data-eye-open viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                        </button>
                    </div>
                    @error($field, 'password')<span class="mt-1.5 block text-[7px] text-[#a65f57]">{{ $message }}</span>@enderror
                </label>
            @endforeach

            <div class="lg:col-span-3 flex flex-col gap-3 border-t border-[#eee9e3] pt-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1 sm:max-w-[420px]">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-[7px] font-semibold text-[#756d64]">Password strength</span>
                        <span id="passwordStrengthLabel" class="text-[7px] font-bold text-[#9a9187]">Enter a new password</span>
                    </div>
                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-[#eeeae4]"><div id="passwordStrengthBar" class="h-full w-0 rounded-full bg-[#c99128] transition-all duration-200"></div></div>
                </div>

                <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-[10px] border border-[#d7b86f] bg-[#fff9ec] px-4 text-[9px] font-bold text-[#97691d] transition hover:bg-[#fff3d8]">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    Update password
                </button>
            </div>
        </form>
    </section>

    <section class="account-card mt-4 rounded-[16px] border border-[#e8e3dc] bg-[#fbfaf8] p-5 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.14em] text-[#8f806d]">Login & Account Activity</p>
                <p class="mt-1 text-[9px] text-[#91887e]">Security information recorded by the administrator account.</p>
            </div>
            <span class="inline-flex w-fit h-6 items-center gap-1.5 rounded-full border border-[#dce7df] bg-white px-2.5 text-[7px] font-bold text-[#617568]"><span class="h-1.5 w-1.5 rounded-full bg-[#4a9a68]"></span>Current session active</span>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">
                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Current session</p>
                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $sessionInfo['browser'] }} · {{ $sessionInfo['platform'] }}</p>
                <p class="mt-1 text-[7px] text-[#8f867d]">Active now</p>
            </div>
            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">
                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Last sign-in</p>
                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $lastLogin ? $lastLogin->format('M d, Y · h:i A') : 'Not recorded yet' }}</p>
                <p class="mt-1 text-[7px] text-[#8f867d]">Login count: {{ number_format((int) ($adminAccount->login_count ?? 0)) }}</p>
            </div>
            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">
                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Profile updated</p>
                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $profileUpdated ? $profileUpdated->format('M d, Y') : 'Not recorded' }}</p>
                <p class="mt-1 text-[7px] text-[#8f867d]">Identity and email changes</p>
            </div>
            <div class="rounded-[12px] border border-[#e8e3dc] bg-white p-4">
                <p class="text-[6.5px] font-bold uppercase tracking-[.12em] text-[#a0968c]">Password changed</p>
                <p class="mt-2 text-[9px] font-bold text-[#413a34]">{{ $passwordChanged ? $passwordChanged->format('M d, Y') : 'Not recorded yet' }}</p>
                <p class="mt-1 text-[7px] text-[#8f867d]">Security credential history</p>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
(function () {
    function initAccountPage() {
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            if (button.dataset.bound === '1') return;
            button.dataset.bound = '1';

            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.passwordToggle);
                if (!input) return;
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });

        const password = document.getElementById('password');
        const bar = document.getElementById('passwordStrengthBar');
        const label = document.getElementById('passwordStrengthLabel');

        if (password && bar && label && password.dataset.strengthBound !== '1') {
            password.dataset.strengthBound = '1';

            const updateStrength = function () {
                const value = password.value;
                let score = 0;
                if (value.length >= 8) score++;
                if (value.length >= 12) score++;
                if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
                if (/\d/.test(value)) score++;
                if (/[^A-Za-z0-9]/.test(value)) score++;

                if (!value) {
                    bar.style.width = '0%';
                    label.textContent = 'Enter a new password';
                    label.style.color = '#9a9187';
                    return;
                }

                const percent = Math.max(20, Math.min(100, score * 20));
                bar.style.width = percent + '%';

                if (score <= 2) {
                    label.textContent = 'Basic';
                    label.style.color = '#9a6a45';
                } else if (score <= 4) {
                    label.textContent = 'Good';
                    label.style.color = '#9a741f';
                } else {
                    label.textContent = 'Strong';
                    label.style.color = '#577660';
                }
            };

            password.addEventListener('input', updateStrength);
            updateStrength();
        }
    }

    document.addEventListener('DOMContentLoaded', initAccountPage, { once: true });
    document.addEventListener('livewire:navigated', initAccountPage);
})();
</script>
@endpush
@endsection
