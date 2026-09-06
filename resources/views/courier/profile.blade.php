@extends('layouts.courier')

@section('title', 'Account Management')
@section('header-title', 'Account Management')
@section('header-subtitle', 'Courier Profile & Vehicle')


@push('styles')
<style>
    .page-card {
        border: 1px solid #eee4d3;
        background: #fffdf9;
        box-shadow: 0 8px 24px rgba(75, 59, 30, .035);
    }
    .page-card-hover {
        transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
    }
    .page-card-hover:hover {
        transform: translateY(-2px);
        border-color: #dbc89f;
        box-shadow: 0 14px 32px rgba(75, 59, 30, .07);
    }
</style>
@endpush


@section('content')
<div class="mx-auto max-w-[1620px]">

    <section class="reveal mb-5 border-b border-[#eee4d3] pb-5">
        <h2 class="text-[15px] font-bold text-[#211d17]">Courier Account</h2>
        <p class="mt-2 text-[10px] leading-5 text-[#817769]">
            Manage courier profile information, delivery vehicle, and account verification details.
        </p>
    </section>

    <section class="grid gap-5 xl:grid-cols-[320px_minmax(0,1fr)]">

        <aside class="reveal page-card rounded-2xl p-5">
            <div class="text-center">
                <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-[#3C6E91] text-[22px] font-bold text-white">
                    {{ strtoupper(substr($profile['full_name'],0,1)) }}
                </div>
                <h3 class="mt-4 text-[13px] font-bold text-[#211d17]">{{ $profile['full_name'] }}</h3>
                <p class="mt-1 text-[8px] text-[#918677]">Verified SARI Courier</p>

                <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-[8px] font-semibold text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Account Active
                </div>
            </div>

            <div class="mt-6 space-y-3 border-t border-[#eee4d3] pt-5">
                <div class="flex items-center justify-between">
                    <span class="text-[8px] text-[#8c8274]">Rating</span>
                    <strong class="text-[10px] text-[#302a23]">★ {{ $profile['rating'] }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[8px] text-[#8c8274]">Deliveries</span>
                    <strong class="text-[10px] text-[#302a23]">{{ $profile['completed_deliveries'] }}</strong>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[8px] text-[#8c8274]">Joined</span>
                    <strong class="text-[9px] text-[#302a23]">{{ $profile['joined'] }}</strong>
                </div>
            </div>
        </aside>

        <div class="space-y-5">
            <section class="reveal page-card rounded-2xl p-5">
                <div class="flex items-center justify-between gap-4 border-b border-[#eee4d3] pb-4">
                    <div>
                        <h3 class="text-[12px] font-bold text-[#211d17]">Personal Information</h3>
                        <p class="mt-1 text-[8px] text-[#918677]">Basic courier contact details.</p>
                    </div>
                    <button type="button" class="edit-demo rounded-xl border border-[#e6dccb] bg-white px-4 py-2.5 text-[8px] font-semibold text-[#62594d]">Edit</button>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @foreach([
                        ['Full Name',$profile['full_name']],
                        ['Email Address',$profile['email']],
                        ['Phone Number',$profile['phone']],
                        ['Address',$profile['address']],
                    ] as [$label,$value])
                        <div>
                            <p class="text-[7px] uppercase tracking-[.11em] text-[#9d9283]">{{ $label }}</p>
                            <p class="mt-1.5 text-[9px] font-semibold text-[#373129]">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="reveal page-card rounded-2xl p-5">
                <div class="flex items-center justify-between gap-4 border-b border-[#eee4d3] pb-4">
                    <div>
                        <h3 class="text-[12px] font-bold text-[#211d17]">Vehicle Information</h3>
                        <p class="mt-1 text-[8px] text-[#918677]">Registered vehicle used for SARI deliveries.</p>
                    </div>
                    <span class="rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-[7px] font-semibold text-blue-700">Verified Vehicle</span>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach([
                        ['Vehicle Type',$profile['vehicle_type']],
                        ['Vehicle Model',$profile['vehicle_model']],
                        ['Plate Number',$profile['plate_number']],
                        ['License Number',$profile['license_number']],
                    ] as [$label,$value])
                        <div class="rounded-xl border border-[#eee4d3] bg-[#fbf8f1] p-4">
                            <p class="text-[7px] uppercase tracking-[.11em] text-[#9d9283]">{{ $label }}</p>
                            <p class="mt-1.5 text-[9px] font-semibold text-[#373129]">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="reveal page-card rounded-2xl p-5">
                <div>
                    <h3 class="text-[12px] font-bold text-[#211d17]">Security & Account</h3>
                    <p class="mt-1 text-[8px] text-[#918677]">Courier account security settings.</p>
                </div>

                <div class="mt-5 divide-y divide-[#eee4d3] rounded-xl border border-[#eee4d3] bg-white">
                    <div class="flex items-center justify-between gap-4 p-4">
                        <div>
                            <p class="text-[9px] font-semibold text-[#352f28]">Password</p>
                            <p class="mt-1 text-[8px] text-[#8a8072]">Change your courier login password.</p>
                        </div>
                        <button type="button" class="edit-demo rounded-xl border border-[#e6dccb] px-3 py-2 text-[8px] font-semibold text-[#62594d]">Change</button>
                    </div>
                    <div class="flex items-center justify-between gap-4 p-4">
                        <div>
                            <p class="text-[9px] font-semibold text-[#352f28]">Availability Status</p>
                            <p class="mt-1 text-[8px] text-[#8a8072]">Control whether you receive new delivery requests.</p>
                        </div>
                        <button id="availabilityToggle" type="button" class="rounded-full bg-emerald-500 px-4 py-2 text-[8px] font-semibold text-white">Online</button>
                    </div>
                </div>
            </section>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.edit-demo').forEach(button => {
        button.addEventListener('click', () => {
            window.SariCourierToast?.('Account settings', 'Editing can be connected to the courier_accounts table after the UI is approved.');
        });
    });

    const toggle = document.getElementById('availabilityToggle');
    toggle?.addEventListener('click', () => {
        const online = toggle.textContent.trim() === 'Online';
        toggle.textContent = online ? 'Offline' : 'Online';
        toggle.className = online
            ? 'rounded-full bg-slate-500 px-4 py-2 text-[8px] font-semibold text-white'
            : 'rounded-full bg-emerald-500 px-4 py-2 text-[8px] font-semibold text-white';

        window.SariCourierToast?.(
            online ? 'Courier offline' : 'Courier online',
            online ? 'New delivery requests are paused in this UI demo.' : 'You are available for new delivery requests.'
        );
    });
});
</script>
@endpush
